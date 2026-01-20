# Finanzas Personales (Laravel)

Aplicación web para gestionar finanzas personales: billeteras, categorías y movimientos, con exportación a Excel y autenticación básica.

## Requisitos
- PHP 8.2+
- Composer
- Node.js 18+
- Base de datos MySQL/MariaDB

## Instalación
1. Clonar el repositorio y entrar al proyecto.
2. Crear archivo de entorno y clave de la app:
```bash
cp .env.example .env
php artisan key:generate
```
3. Configurar conexión a base de datos en `.env` (host, usuario, contraseña, base de datos).
4. Instalar dependencias y preparar front-end:
```bash
composer install
npm install
```
5. Ejecutar migraciones:
```bash
php artisan migrate
```
6. Iniciar servidores (en dos terminales):
```bash
php artisan serve
npm run dev
```

## Uso rápido
- Registro e inicio de sesión.
- Crear y gestionar billeteras.
- Definir categorías.
- Registrar movimientos (ingresos/egresos) y verlos en el dashboard.
- Transferencias entre billeteras.
- Exportar movimientos a Excel.
- Perfil de usuario: actualizar nombre/correo y cambiar contraseña.

## Rutas principales
- `GET /` dashboard
- `GET/POST /login` autenticación
- `GET/POST /register` registro
- `POST /logout` cerrar sesión
- `GET /billeteras` CRUD billeteras
- `GET /movimientos` CRUD movimientos + `GET /movimientos/export`
- `GET/PUT /perfil` ver/editar perfil y `PUT /perfil/password` contraseña

## Estructura destacada
- `app/Models`: `User`, `Billetera`, `Categoria`, `Movimiento`.
- `app/Http/Controllers`: controladores de auth, dashboard, billeteras, movimientos, transferencias y perfil.
- `resources/views`: vistas Blade (layouts, dashboard, billeteras, movimientos, auth, user).
- `database/migrations`: tablas base y relaciones.
- `app/Exports/MovimientosExport.php`: exportación a Excel (Maatwebsite/Excel).

## Pruebas
```bash
php artisan test
```

## Despliegue
- Ejecutar `npm run build` para assets de producción.
- Configurar variables de entorno (`APP_ENV`, `APP_KEY`, `APP_URL`, `DB_*`).
- Asegurar permisos de `storage/` y `bootstrap/cache`.

## Mejoras futuras sugeridas
- Reportes avanzados: gráficos mensuales, comparativas, presupuesto.
- Importación CSV/Excel y API pública (token-based).
- Etiquetas y notas en movimientos; búsquedas y filtros mejorados.
- Seguridad: verificación email, 2FA, política de contraseñas.
- Auditoría y respaldo: historial de cambios y exportación/backup.
- Internacionalización y accesibilidad.
- Docker y CI/CD; cobertura de pruebas y seeding de datos demo.
