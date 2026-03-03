# Orders App

## Requisitos

- Docker
- Docker Compose

## Instalacion de Docker

- Docker Engine (Linux): https://docs.docker.com/engine/install/
- Docker Desktop (Windows/Mac): https://www.docker.com/products/docker-desktop/

## Instalacion

1. Copia el archivo de entorno y ajusta variables si lo necesitas:

```bash
cp .env.example .env
```

2. Levanta los contenedores:

```bash
docker compose up -d --build
```

3. Instala dependencias de PHP dentro del contenedor:

```bash
docker compose exec app composer install
```

4. Genera la clave de la aplicacion:

```bash
docker compose exec app php artisan key:generate
```

5. Ejecuta migraciones y seeders:

```bash
docker compose exec app php artisan migrate --seed
```

6. Ejecuta los procedimientos almacenados (sp):

```bash
docker exec -i orders-mysql mysql -u root -ppassword orders_db < database/sql/procedures.sql
```

## Accesos

- Aplicacion: http://localhost:8000
- Vite (hot reload): http://localhost:5173

## Credenciales

- Email: admin@example.com
- Password: password

## Detener

```bash
docker compose down
```
