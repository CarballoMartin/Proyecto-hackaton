# 🐑 Sistema de Gestión Ovino-Caprino

Sistema web para la gestión integral de producción ganadera (ovinos y caprinos), desarrollado con Laravel 12 y Livewire 3.

## 🚀 Características

- **Panel de Productor**: Dashboard personalizado con estadísticas
- **Gestión de Campos**: CRUD completo de campos ganaderos
- **Stock Animal**: Inventario detallado por especie, raza y categoría
- **Reportes Avanzados**: Filtros dinámicos y exportación PDF/Excel
- **Gráficos Interactivos**: Visualizaciones con Chart.js
- **Roles y Permisos**: Superadmin, Institucional y Productor

## 🛠️ Tecnologías

- **Backend**: Laravel 12.x, Livewire 3.x, MySQL/SQLite
- **Frontend**: Tailwind CSS, Blade Templates, Chart.js
- **Exportación**: Laravel DomPDF, Laravel Excel
- **Autenticación**: Laravel Fortify, Sanctum

## 📋 Requisitos

- PHP 8.2 o superior
- Composer
- Node.js y npm
- MySQL 5.7 o superior
- Git

## 🔧 Instalación

### 1. Clonar el repositorio
```bash
git clone https://github.com/tu-usuario/proyecto-ovino-caprinos.git
cd proyecto-ovino-caprinos
```

### 2. Instalar dependencias PHP
```bash
composer install
```

### 3. Instalar dependencias Node.js
```bash
npm install
```

### 4. Configurar variables de entorno
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configurar base de datos
Edita el archivo `.env` con tus credenciales de base de datos:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ovino_caprinos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password
```

### 6. Ejecutar migraciones y seeders
```bash
php artisan migrate:fresh --seed
```

### 7. Compilar assets
```bash
npm run build
```

### 8. Iniciar servidor
```bash
php artisan serve
```

## 👥 Usuarios de Prueba

### Productor
- **Email**: productor@test.com
- **Password**: password
- **Rol**: productor

### Superadmin
- **Email**: admin@test.com
- **Password**: password
- **Rol**: superadmin

## 📁 Estructura del Proyecto

```
app/
├── Livewire/
│   ├── Productor/
│   │   ├── Dashboard.php
│   │   ├── Perfil.php
│   │   ├── Campos/
│   │   ├── Stock/
│   │   └── Reportes/
│   └── Admin/
├── Models/
│   ├── Productor.php
│   ├── Campo.php
│   ├── StockAnimal.php
│   └── ...
└── Http/
    ├── Responses/
    └── Middleware/

resources/
└── views/
    └── livewire/
        └── productor/
            ├── dashboard.blade.php
            ├── perfil.blade.php
            ├── campos/
            ├── stock/
            └── reportes/

database/
├── migrations/
└── seeders/
```

## 🎯 Funcionalidades por Fase

### ✅ Fase 1: Dashboard y Perfil
- Panel principal del productor
- Gestión de perfil personal
- Estadísticas básicas

### ✅ Fase 2: Gestión de Campos
- CRUD completo de campos
- Filtros por ubicación y características
- Modales para ver/eliminar

### ✅ Fase 3: Gestión de Stock Animal
- Inventario por especie, raza y categoría
- Registros con fechas y observaciones
- Estadísticas por tipo

### ✅ Fase 4: Formularios CRUD de Stock
- Crear, editar, ver y eliminar registros
- Filtros dinámicos dependientes
- Validaciones completas

### ✅ Fase 5: Reportes y Estadísticas
- Filtros avanzados por múltiples criterios
- Exportación a PDF y Excel
- Métricas detalladas y resúmenes

### 🔄 Fase 6: Gráficos Interactivos
- Visualizaciones con Chart.js
- Gráficos de barras, líneas y circulares
- Dashboard con gráficos en tiempo real

## 🔐 Seguridad

- **Autenticación**: Laravel Fortify
- **Autorización**: Middleware de roles personalizado
- **Validación**: Reglas de validación en todos los formularios
- **Sanitización**: Escape automático de datos
- **CSRF**: Protección CSRF habilitada

## 📊 Base de Datos

### Tablas Principales
- `users` - Usuarios del sistema
- `productors` - Información de productores
- `campos` - Campos ganaderos
- `stock_animals` - Inventario animal
- `especies` - Catálogo de especies
- `razas` - Catálogo de razas
- `categoria_animals` - Categorías animales

### Seeders Incluidos
- `UserSeeder` - Usuarios de prueba
- `ProductorSeeder` - Productores de ejemplo
- `EspecieSeeder` - Especies comunes
- `RazaSeeder` - Razas por especie
- `CategoriaAnimalSeeder` - Categorías por especie
- `StockAnimalSeeder` - Datos de ejemplo

## 🚀 Despliegue

### Para producción:
1. Configurar variables de entorno de producción
2. Ejecutar `composer install --optimize-autoloader --no-dev`
3. Ejecutar `npm run build`
4. Configurar servidor web (Apache/Nginx)
5. Configurar base de datos de producción

## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 📞 Soporte

Para soporte técnico o consultas:
- Email: soporte@ovino-caprinos.com
- Documentación: [Wiki del proyecto]

## 🔄 Changelog

### v1.0.0 (2024-01-XX)
- ✅ Fase 1: Dashboard y Perfil
- ✅ Fase 2: Gestión de Campos
- ✅ Fase 3: Gestión de Stock Animal
- ✅ Fase 4: Formularios CRUD de Stock
- ✅ Fase 5: Reportes y Estadísticas
- 🔄 Fase 6: Gráficos Interactivos (En desarrollo)
