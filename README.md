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
5. Ejecutar seeders para datos de prueba
```bash
php artisan db:seed
```
7. Iniciar servidores:
```bash
php artisan serve
```

## Uso rápido
- Registro e inicio de sesión.
- Crear y gestionar billeteras.
- Definir categorías.
- Registrar movimientos (ingresos/egresos) y verlos en el dashboard.
- Transferencias entre billeteras.
- Exportar movimientos a Excel.
- Perfil de usuario: actualizar nombre/correo y cambiar contraseña.

## Mejoras futuras
- Reportes avanzados: gráficos mensuales, comparativas, presupuesto.
- Importación CSV/Excel y API pública (token-based).
- Etiquetas y notas en movimientos; búsquedas y filtros mejorados.
- Seguridad: verificación email, 2FA, política de contraseñas.
- Auditoría y respaldo: historial de cambios y exportación/backup.

