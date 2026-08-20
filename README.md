# 📦 InventoryFlow - Sistema de Gestion de Inventarios y Ventas

![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)
![PHPUnit](https://img.shields.io/badge/PHPUnit-10.x-9B8B15?style=flat&logo=phpunit&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green)
![Version](https://img.shields.io/badge/Version-1.0.0-blue)

Sistema completo de gestion de inventarios y ventas construido con PHP 8.1+ siguiendo arquitectura MVC, bases de datos MySQL, autenticacion segura y dashboard interactivo.

## ✨ Caracteristicas

### Modulos Principales
- **Dashboard** - Estadisticas en tiempo real, graficos de ventas, alertas de stock bajo
- **Productos** - CRUD completo con gestion de precios, stock, imagenes y categorias
- **Categorias** - Organizacion jerarquica de productos
- **Proveedores** - Gestion de contactos y relacion con productos
- **Clientes** - Base de datos de clientes con historial de compras
- **Ventas** - Sistema de POS con carrito, descuentos e Impuesto al Valor Agregado (IVA)
- **Reportes** - Generacion de reportes de ventas, inventario y rendimiento

### Funcionalidades Tecnicas
- Arquitectura MVC personalizada sin framework
- Router interno con soporte para parametros dinamicos
- Sistema de autenticacion con sesiones y hashing bcrypt
- Proteccion CSRF en formularios
- Validacion de datos server-side y client-side
- Sistema de paginacion, busqueda y filtrado
- Manejo de errores y logging
- Base de datos con migraciones
- Seeds para datos de prueba
- Tests unitarios con PHPUnit

## 🛠️ Requisitos

- PHP 8.1 o superior
- MySQL 8.0 o MariaDB 10.5+
- Composer
- XAMPP / WAMP / LAMP stack

## 🚀 Instalacion

### 1. Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/inventoryflow.git
cd inventoryflow
```

### 2. Instalar dependencias

```bash
composer install
```

### 3. Configurar base de datos

```bash
# Copiar archivo de configuracion
cp config/database.example.php config/database.php

# Editar con tus credenciales de MySQL
```

### 4. Ejecutar migraciones

```bash
php database/migrate.php
```

### 5. Cargar datos de prueba (opcional)

```bash
php database/seed.php
```

### 6. Iniciar servidor de desarrollo

```bash
php -S localhost:8000 -t public
```

### 7. Acceder al sistema

```
URL: http://localhost:8000
Usuario: admin@inventoryflow.com
Password: Admin123!
```

## 📁 Estructura del Proyecto

```
inventoryflow/
├── assets/                 # Recursos estaticos
│   ├── css/
│   │   └── style.css      # Estilos principales
│   ├── js/
│   │   └── app.js         # JavaScript principal
│   └── img/               # Imagenes
├── config/
│   └── database.php       # Configuracion de BD
├── database/
│   ├── migrations/        # Migraciones SQL
│   │   └── 001_create_tables.sql
│   ├── migrate.php        # Ejecutor de migraciones
│   └── seed.php           # Datos de prueba
├── public/
│   └── index.php          # Punto de entrada
├── src/
│   ├── Controllers/       # Controladores
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── ProductController.php
│   │   ├── CategoryController.php
│   │   ├── SupplierController.php
│   │   ├── CustomerController.php
│   │   ├── SaleController.php
│   │   └── ReportController.php
│   ├── Core/              # Nucleo del framework
│   │   ├── Database.php   # Conexion PDO
│   │   ├── Router.php     # Enrutador
│   │   ├── Controller.php # Controlador base
│   │   └── Model.php      # Modelo base
│   ├── Helpers/           # Utilidades
│   │   ├── Auth.php       # Autenticacion
│   │   ├── CSRF.php       # Proteccion CSRF
│   │   ├── Validator.php  # Validaciones
│   │   └── Pagination.php # Paginacion
│   └── Models/            # Modelos de datos
│       ├── User.php
│       ├── Product.php
│       ├── Category.php
│       ├── Supplier.php
│       ├── Customer.php
│       └── Sale.php
├── tests/                 # Pruebas unitarias
│   ├── Unit/
│   │   ├── ValidatorTest.php
│   │   ├── ProductTest.php
│   │   ├── CartTest.php
│   │   └── AuthTest.php
│   └── bootstrap.php
├── views/                 # Plantillas PHP
│   ├── layout/
│   │   ├── header.php
│   │   └── footer.php
│   ├── auth/
│   │   ├── login.php
│   │   └── register.php
│   ├── dashboard/
│   │   └── index.php
│   ├── products/
│   │   ├── index.php
│   │   ├── create.php
│   │   ├── edit.php
│   │   └── show.php
│   ├── categories/
│   │   ├── index.php
│   │   └── form.php
│   ├── suppliers/
│   │   ├── index.php
│   │   └── form.php
│   ├── customers/
│   │   ├── index.php
│   │   └── form.php
│   ├── sales/
│   │   ├── index.php
│   │   └── create.php
│   └── reports/
│       └── index.php
├── composer.json
├── phpunit.xml
└── README.md
```

## 📊 Base de Datos

### Diagrama Entidad-Relacion

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│    users    │     │  categories │     │  suppliers  │
├─────────────┤     ├─────────────┤     ├─────────────┤
│ id (PK)     │     │ id (PK)     │     │ id (PK)     │
│ name        │     │ name        │     │ name        │
│ email       │     │ description │     │ contact     │
│ password    │     │ parent_id   │     │ email       │
│ role        │     │ created_at  │     │ phone       │
│ created_at  │     └─────────────┘     │ address     │
└─────────────┘            │            └─────────────┘
                           │                   │
                           ▼                   │
                    ┌─────────────┐            │
                    │  products   │◄───────────┘
                    ├─────────────┤
                    │ id (PK)     │
                    │ name        │
                    │ sku         │
                    │ description │
                    │ price       │
                    │ cost        │
                    │ stock       │
                    │ min_stock   │
                    │ category_id │
                    │ supplier_id │
                    │ image       │
                    │ status      │
                    │ created_at  │
                    └─────────────┘
                           │
                           │
              ┌────────────┴───────────┐
              │                        │
              ▼                        ▼
    ┌─────────────┐           ┌─────────────┐
    │   sales     │           │  customers  │
    ├─────────────┤           ├─────────────┤
    │ id (PK)     │           │ id (PK)     │
    │ customer_id │◄──────────│ name        │
    │ user_id     │           │ email       │
    │ total       │           │ phone       │
    │ discount    │           │ address     │
    │ tax         │           │ rfc         │
    │ subtotal    │           │ created_at  │
    │ status      │           └─────────────┘
    │ created_at  │
    └─────────────┘
              │
              ▼
    ┌─────────────┐
    │ sale_items  │
    ├─────────────┤
    │ id (PK)     │
    │ sale_id(FK) │
    │ product_id  │
    │ quantity    │
    │ price       │
    │ subtotal    │
    └─────────────┘
```

### Tablas Principales

| Tabla | Registros | Descripcion |
|-------|-----------|-------------|
| users | - | Usuarios del sistema |
| categories | - | Categorias jerarquicas |
| suppliers | - | Proveedores |
| products | - | Productos del inventario |
| customers | - | Clientes registrados |
| sales | - | Ventas realizadas |
| sale_items | - | Detalle de ventas |

## 🧪 Ejecutar Pruebas

```bash
# Ejecutar todas las pruebas
vendor/bin/phpunit

# Ejecutar con cobertura
vendor/bin/phpunit --coverage-html coverage/

# Ejecutar un archivo especifico
vendor/bin/phpunit tests/Unit/ValidatorTest.php
```

## 📝 Uso del Sistema

### Crear Producto

```
1. Ir a Productos > Nuevo Producto
2. Completar formulario (nombre, SKU, precio, stock inicial)
3. Seleccionar categoria y proveedor
4. Guardar
```

### Registrar Venta

```
1. Ir a Ventas > Nueva Venta
2. Agregar productos al carrito
3. Aplicar descuento (opcional)
4. Seleccionar cliente (opcional)
5. Confirmar venta
6. El stock se actualiza automaticamente
```

### Generar Reportes

```
1. Ir a Reportes
2. Seleccionar tipo de reporte
3. Definir rango de fechas
4. Exportar a PDF/CSV
```

## 🔒 Seguridad

- Passwords hasheados con bcrypt
- Tokens CSRF en todos los formularios
- Prepared statements para queries SQL
- Sanitizacion de inputs
- Headers de seguridad HTTP
- Rate limiting en login

## 🎨 Personalizacion

### Variables CSS

```css
:root {
    --primary-color: #3B82F6;
    --secondary-color: #10B981;
    --danger-color: #EF4444;
    --warning-color: #F59E0B;
    --sidebar-width: 250px;
}
```

## 📄 Licencia

MIT License - Ver archivo [LICENSE](LICENSE) para detalles.

## 👨‍💻 Autor

Desarrollado como proyecto de portafolio para demostrar habilidades en desarrollo PHP, bases de datos y arquitectura de software.

---

**¡Star este repo si te fue util!** ⭐
