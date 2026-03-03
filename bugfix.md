# Bugfixes

## 1) SQL Injection

### Fragmento vulnerable (ejemplo)
```php
// No hacer esto: concatena input sin validar
$sql = "CALL sp_get_orders('{$dateFrom}', '{$dateTo}', '{$minTotal}')";
$rows = DB::select($sql);
```

**Problema**: el input entra directo a la cadena SQL y permite inyeccion.

### Version corregida
```php
// Usar placeholders y binding
$rows = DB::select("CALL sp_get_orders(?,?,?)", [$dateFrom, $dateTo, $minTotal]);
```

**Notas**:
- Esto ya se aplica en los repositorios actuales (bindings en `DB::select` o `PDO::prepare`).
- Si necesitas filtrar por columnas dinamicas (por ejemplo `sortBy`), valida contra una lista blanca antes de interpolar.

---

## 2) N+1 en listado de pedidos

### Como detectarlo
- Activa logging de queries y revisa multiples consultas repetidas por pedido.
- Ejemplo de sintoma: 1 query para pedidos + N queries para items (una por pedido).

### Ejemplo del problema (hipotetico)
```php
$orders = DB::select("CALL sp_get_orders(?,?,?)", [$dateFrom, $dateTo, $minTotal]);

foreach ($orders as &$order) {
    $order->items = DB::select("CALL sp_get_order_items(?)", [$order->id]);
}
```

### Solucion (como implementarlo)
**Opcion A: query unica con JOIN**
- Crear un procedimiento que retorne pedidos e items en una sola consulta.
- Luego agrupar en PHP por `order_id`.

**Opcion B: batch IN**
```php
$orders = DB::select("CALL sp_get_orders(?,?,?)", [$dateFrom, $dateTo, $minTotal]);
$orderIds = array_map(static fn ($o) => $o->id, $orders);

if ($orderIds) {
    $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
    $items = DB::select("SELECT * FROM order_items WHERE order_id IN ($placeholders)", $orderIds);
}
```

**Opcion C: si usas Eloquent**
- Definir relaciones y usar `with('items')` para eager loading.

**Recomendacion rapida**
- Para este proyecto, la opcion B es simple y evita cambiar SPs.
- Si deseas optimizar a nivel DB, la opcion A es la mas limpia.
