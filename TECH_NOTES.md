# Tech Notes

## Contexto general

- Backend en Laravel con Inertia para una SPA, usando Vue 3 en el frontend.
- Vite se encarga del hot reload durante el desarrollo.
- La infraestructura local se levanta con Docker para estandarizar entorno.

## Arquitectura y patron

- Use una separacion por capas tipo Clean/Hexagonal para el modulo de Orders.
- La capa de Dominio define contratos y modelos base.
- La capa de Aplicacion concentra los casos de uso (por ejemplo crear y listar ordenes).
- La capa de Infraestructura adapta HTTP y base de datos a traves de repositorios.
- El objetivo fue reducir el acoplamiento y mantener la logica de negocio aislada.

## Acceso a datos

- Los repositorios implementan los contratos y encapsulan el acceso a la base.
- Se usan procedimientos almacenados para crear y consultar ordenes, manteniendo la logica SQL centralizada y reutilizable.

## Frontend SPA

- Inertia permite una experiencia SPA sin duplicar la logica de routing, aprovechando Laravel como orquestador.
- Vue 3 se usa para las vistas y componentes, con un flujo de desarrollo rapido via Vite.

## Docker

- Contenedores para app, MySQL, Vite y un servidor de correo local.
- Esto asegura un arranque consistente para revisores y evita configuraciones manuales en cada equipo.
