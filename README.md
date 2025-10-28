# 🌱 Campo Verde - Sistema de Gestión Ganadera Ambiental

Sistema web integral para la gestión ganadera con enfoque ambiental, desarrollado con Laravel 12 y Livewire 3. Incluye monitoreo satelital, análisis de suelo y certificación ambiental.

## 🚀 Características Principales

### 🐑 Gestión Ganadera
- **Panel de Productor**: Dashboard personalizado con estadísticas
- **Gestión de Unidades Productivas**: CRUD completo de campos ganaderos
- **Stock Animal**: Inventario detallado por especie, raza y categoría
- **Reportes Avanzados**: Filtros dinámicos y exportación PDF/Excel
- **Gráficos Interactivos**: Visualizaciones con Chart.js

### 🌍 Módulo Ambiental (NUEVO)
- **Monitoreo Satelital**: Índices de vegetación (NDVI) desde Sentinel-2
- **Análisis de Suelo**: Características químicas y físicas desde FAO SoilGrids
- **Datos Climáticos**: Integración con NASA POWER y Open-Meteo
- **Alertas Inteligentes**: Sistema automático de alertas ambientales
- **Dashboard Integrado**: Métricas consolidadas de clima, vegetación y suelo
- **Certificación Ambiental**: Sistema de puntos Campo Verde (400 puntos máximos)

### 🏢 Panel Institucional
- **Gestión de Participantes**: Administración de productores
- **Sistema de Solicitudes**: Verificación y aprobación de solicitudes
- **Reportes Institucionales**: Estadísticas avanzadas con filtros
- **Configuración**: Gestión de parámetros del sistema

## 🛠️ Tecnologías

### Backend
- **Framework**: Laravel 12.x
- **Componentes**: Livewire 3.x
- **Base de Datos**: MySQL/SQLite
- **Autenticación**: Laravel Fortify, Sanctum
- **APIs Externas**: Copernicus/Sentinel-2, FAO SoilGrids, NASA POWER

### Frontend
- **CSS**: Tailwind CSS 3.x
- **JavaScript**: Alpine.js 3.x
- **Gráficos**: Chart.js 4.x
- **Templates**: Blade Templates

### Servicios Externos
- **Satelital**: Copernicus Sentinel Hub API
- **Suelo**: FAO SoilGrids REST API
- **Clima**: NASA POWER API, Open-Meteo API

## 📋 Requisitos

- PHP 8.2 o superior
- Composer
- Node.js 18+ y npm
- MySQL 5.7+ o SQLite 3
- Git
- Servidor web (Apache/Nginx)

## 🔧 Instalación

### 1. Clonar el repositorio
```bash
git clone https://github.com/CarballoMartin/Proyecto-hackaton.git
cd Proyecto-hackaton
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
Edita el archivo `.env`:
```env
DB_CONNECTION=sqlite
# O para MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=campo_verde
# DB_USERNAME=tu_usuario
# DB_PASSWORD=tu_password
```

### 6. Configurar APIs Ambientales (Opcional)
```env
# APIs Ambientales
COPERNICUS_ENABLED=true
COPERNICUS_CLIENT_ID=tu_client_id
COPERNICUS_CLIENT_SECRET=tu_client_secret
SOILGRIDS_ENABLED=true
NASA_POWER_ENABLED=true
OPEN_METEO_ENABLED=true

# Alertas
ALERTAS_EMAIL_ENABLED=true
ALERTAS_SMS_ENABLED=false
```

### 7. Ejecutar migraciones y seeders
```bash
php artisan migrate:fresh --seed
```

### 8. Compilar assets
```bash
npm run build
```

### 9. Iniciar servidor
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

### Institucional
- **Email**: institucional@test.com
- **Password**: password
- **Rol**: institucional

## 📁 Estructura del Proyecto

```
app/
├── Livewire/
│   ├── Productor/
│   │   ├── Dashboard.php
│   │   ├── Ambiental/
│   │   │   ├── DashboardIntegrado.php
│   │   │   ├── Ndvi.php
│   │   │   ├── Suelo.php
│   │   │   └── General.php
│   │   ├── UnidadesProductivas/
│   │   └── Stock/
│   ├── Institucional/
│   │   ├── Reportes.php
│   │   ├── Solicitudes.php
│   │   └── Participantes.php
│   └── Admin/
├── Models/
│   ├── Productor.php
│   ├── UnidadProductiva.php
│   ├── IndiceVegetacion.php
│   ├── CaracteristicaSuelo.php
│   ├── AlertaAmbiental.php
│   └── ...
├── Services/
│   ├── SatelitalApi/
│   │   └── CopernicusApiService.php
│   ├── SueloApi/
│   │   └── SoilGridsApiService.php
│   ├── ClimaApi/
│   │   └── NasaPowerService.php
│   ├── DashboardAmbientalService.php
│   └── AlertaAmbientalService.php
└── Console/Commands/
    ├── ActualizarNDVICommand.php
    ├── ActualizarDatosSueloCommand.php
    └── GenerarAlertasAmbientalesCommand.php

resources/
└── views/
    └── livewire/
        ├── productor/
        │   ├── ambiental/
        │   │   ├── dashboard-integrado.blade.php
        │   │   ├── ndvi.blade.php
        │   │   └── suelo.blade.php
        │   └── ...
        └── institucional/

database/
├── migrations/
│   ├── 2025_01_27_000001_create_indices_vegetacion_table.php
│   ├── 2025_01_27_000002_create_caracteristicas_suelo_table.php
│   └── 2025_01_27_000003_create_alertas_ambientales_table.php
└── seeders/

config/
├── ambiental.php
└── ganaderia.php
```

## 🎯 Funcionalidades por Módulo

### ✅ Módulo Ganadero
- **Dashboard**: Panel principal con estadísticas
- **Unidades Productivas**: CRUD completo con ubicación GPS
- **Stock Animal**: Inventario por especie, raza y categoría
- **Reportes**: Filtros avanzados y exportación PDF/Excel
- **Gráficos**: Visualizaciones interactivas con Chart.js

### ✅ Módulo Ambiental (NUEVO)
- **Fase 1**: Datos climáticos básicos
- **Fase 2**: Sistema de alertas ambientales
- **Fase 3**: Índices de vegetación (NDVI) satelitales
- **Fase 4**: Análisis de suelo con FAO SoilGrids
- **Fase 5**: Dashboard integrado con métricas consolidadas

### ✅ Módulo Institucional
- **Participantes**: Gestión de productores asociados
- **Solicitudes**: Sistema de verificación y aprobación
- **Reportes**: Estadísticas avanzadas con filtros
- **Configuración**: Parámetros del sistema

## 🌱 Módulo Ambiental Detallado

### 📡 Monitoreo Satelital
- **Integración**: Copernicus Sentinel Hub API
- **Datos**: NDVI, EVI, EVI2 desde Sentinel-2
- **Frecuencia**: Actualización cada 5 días
- **Cobertura**: Global con resolución 10m

### 🌍 Análisis de Suelo
- **Fuente**: FAO SoilGrids REST API
- **Propiedades**: pH, materia orgánica, textura, nutrientes
- **Profundidad**: 0-30cm estándar
- **Recomendaciones**: Automáticas según características

### 🌡️ Datos Climáticos
- **NASA POWER**: Temperatura, precipitación, humedad
- **Open-Meteo**: Pronósticos y datos históricos
- **Alertas**: Sequía, tormenta, estrés térmico, helada

### 🚨 Sistema de Alertas
- **Generación**: Automática basada en umbrales
- **Tipos**: Sequía, tormenta, estrés térmico, helada, viento, NDVI bajo, suelo degradado
- **Severidad**: Baja, media, alta, crítica
- **Notificaciones**: Email, SMS, dashboard

### 🏆 Certificación Ambiental
- **Puntos Máximos**: 400 puntos
- **Categorías**: Agua, biodiversidad, eficiencia, sostenibilidad, NDVI, clima/alertas
- **Cálculo**: Automático por productor
- **Niveles**: Inicial, intermedio, avanzado

## 🔐 Seguridad

- **Autenticación**: Laravel Fortify
- **Autorización**: Middleware de roles personalizado
- **Validación**: Reglas de validación en todos los formularios
- **Sanitización**: Escape automático de datos
- **CSRF**: Protección CSRF habilitada
- **Rate Limiting**: Límites en APIs externas

## 📊 Base de Datos

### Tablas Principales
- `users` - Usuarios del sistema
- `productors` - Información de productores
- `unidades_productivas` - Campos ganaderos con GPS
- `stock_animals` - Inventario animal
- `indices_vegetacion` - Datos NDVI satelitales
- `caracteristicas_suelo` - Análisis de suelo
- `alertas_ambientales` - Sistema de alertas

### Tablas Institucionales
- `institucions` - Instituciones participantes
- `institucional_participantes` - Participantes institucionales
- `solicitud_verificacions` - Solicitudes de verificación

## 🚀 Comandos Artisan

### Módulo Ambiental
```bash
# Actualizar datos NDVI
php artisan satelital:actualizar-ndvi

# Sincronizar datos de suelo
php artisan suelo:sincronizar-fao

# Generar alertas ambientales
php artisan ambiental:generar-alertas
```

### Comandos Generales
```bash
# Limpiar caché
php artisan cache:clear

# Optimizar para producción
php artisan optimize

# Ver estado de migraciones
php artisan migrate:status
```

## 🌐 APIs Externas

### Copernicus Sentinel Hub
- **Propósito**: Datos satelitales NDVI
- **Autenticación**: OAuth2 Client Credentials
- **Límites**: Según plan de suscripción
- **Documentación**: [Sentinel Hub API](https://docs.sentinel-hub.com/api/)

### FAO SoilGrids
- **Propósito**: Datos de suelo globales
- **Autenticación**: No requerida
- **Límites**: Sin límites conocidos
- **Documentación**: [SoilGrids API](https://rest.isric.org/soilgrids/)

### NASA POWER
- **Propósito**: Datos climáticos históricos
- **Autenticación**: No requerida
- **Límites**: 1000 requests/día
- **Documentación**: [NASA POWER API](https://power.larc.nasa.gov/api/)

## 🚀 Despliegue

### Para desarrollo:
```bash
php artisan serve
npm run dev
```

### Para producción:
1. Configurar variables de entorno de producción
2. Ejecutar `composer install --optimize-autoloader --no-dev`
3. Ejecutar `npm run build`
4. Configurar servidor web (Apache/Nginx)
5. Configurar base de datos de producción
6. Configurar credenciales de APIs externas

### Docker (Opcional)
```bash
docker-compose up -d
```

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
- **Email**: soporte@campo-verde.com
- **GitHub Issues**: [Issues del proyecto](https://github.com/CarballoMartin/Proyecto-hackaton/issues)
- **Documentación**: [Wiki del proyecto](https://github.com/CarballoMartin/Proyecto-hackaton/wiki)

## 🔄 Changelog

### v2.0.0 (2025-01-27) - Módulo Ambiental Completo
- ✅ **Fase 3**: Índices de vegetación (NDVI) satelitales
- ✅ **Fase 4**: Análisis de suelo con FAO SoilGrids
- ✅ **Fase 5**: Dashboard integrado ambiental
- ✅ **Sistema de Alertas**: Generación automática
- ✅ **Certificación Ambiental**: Sistema de puntos mejorado
- ✅ **Branding Campo Verde**: Logo y identidad visual
- ✅ **APIs Externas**: Integración completa

### v1.0.0 (2024-12-XX) - Versión Base
- ✅ Módulo Ganadero completo
- ✅ Módulo Institucional básico
- ✅ Sistema de autenticación y roles
- ✅ Dashboard y reportes básicos

## 🏆 Reconocimientos

- **NASA POWER**: Datos climáticos históricos
- **FAO SoilGrids**: Datos de suelo globales
- **Copernicus**: Datos satelitales Sentinel-2
- **Laravel Community**: Framework y ecosistema
- **Tailwind CSS**: Sistema de diseño

---

**Campo Verde** - Transformando la ganadería con tecnología ambiental 🌱🐑