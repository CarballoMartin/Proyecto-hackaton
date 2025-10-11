# Sistema de Gestión de Ovinos y Caprinos

## Descripción General

Este sistema es una aplicación web desarrollada en Laravel que permite la gestión integral de productores de ovinos y caprinos, así como la administración de instituciones relacionadas con el sector ganadero. El sistema está diseñado para facilitar el registro, seguimiento y análisis de datos relacionados con la producción ganadera.

## Arquitectura del Sistema

### Tecnologías Utilizadas
- **Backend**: Laravel 10 (PHP)
- **Frontend**: Livewire, Tailwind CSS, Alpine.js
- **Base de Datos**: SQLite (desarrollo)
- **Autenticación**: Laravel Jetstream con Fortify
- **Autenticación de API**: Laravel Sanctum

### Estructura de Roles
El sistema implementa un sistema de roles con tres niveles principales:

1. **Superadmin**: Acceso completo al sistema
2. **Institucional**: Gestión de instituciones y solicitudes
3. **Productor**: Acceso limitado a sus propios datos

## Funcionalidades Actuales del Sistema

### 1. Panel de Administración (Superadmin)

#### Dashboard Principal
- **Estadísticas Generales**:
  - Total de productores registrados
  - Total de instituciones
  - Solicitudes pendientes de verificación
  - Total de campos registrados

#### Gestión de Productores
- **Registrar Productor**: Formulario manual para agregar nuevos productores
- **Importar Productores**: Carga masiva desde archivos CSV/Excel
- **Listar y Modificar**: Funcionalidad pendiente de implementación

#### Gestión de Instituciones
- **Registrar Institución**: Creación manual de instituciones
- **Gestionar Solicitudes**: Aprobación/rechazo de solicitudes de incorporación

### 2. Sistema de Autenticación y Autorización
- Registro y login de usuarios
- Verificación de email
- Gestión de perfiles de usuario
- Middleware de roles para control de acceso

### 3. Gestión de Solicitudes
- Sistema de solicitudes de incorporación para instituciones
- Notificaciones por email
- Estados de solicitud (pendiente, aprobada, rechazada)

## Modelos de Datos Principales

### Productor
- Información personal (nombre, DNI, fecha de nacimiento)
- Datos de contacto (celular, email)
- Ubicación (municipio, paraje, dirección, coordenadas)
- RNSPA (Registro Nacional Sanitario de Productores Agropecuarios)
- Condición de tenencia
- Estado activo/inactivo

### Campo
- Ubicación geográfica (latitud, longitud)
- Localidad
- Fuentes de agua (humano y animal)
- Tipo de pasto predominante
- Tipo de suelo predominante
- Dispositivos asociados
- Superficies

### Stock Animal
- Especie (ovino, caprino)
- Raza
- Categoría animal
- Tipo de registro
- Período de actualización

### Instituciones
- Datos de la institución
- Participantes institucionales
- Estado de verificación

## Estado Actual del Sistema

### ✅ Funcionalidades Implementadas
1. **Autenticación y autorización completa**
2. **Panel de administración con estadísticas**
3. **Registro manual de productores**
4. **Importación masiva de productores**
5. **Gestión de instituciones**
6. **Sistema de solicitudes**
7. **Notificaciones por email**
8. **Base de datos completa con relaciones**

### ⚠️ Funcionalidades Pendientes
1. **Panel de productor** (vista específica para productores)
2. **Listado y modificación de productores**
3. **Gestión de campos por productor**
4. **Registro de stock animal**
5. **Reportes y estadísticas**
6. **Panel institucional completo**

## Propuesta para la Sección de Productores

### Panel de Productor (Vista Principal)

#### 1. Dashboard del Productor
- **Resumen de datos personales**
- **Estadísticas de producción**:
  - Total de campos registrados
  - Total de animales por especie
  - Última actualización de datos
- **Alertas y notificaciones**
- **Acciones rápidas**

#### 2. Gestión de Perfil
- **Datos personales**:
  - Información básica (nombre, DNI, fecha nacimiento)
  - Datos de contacto (celular, email)
  - Ubicación (municipio, paraje, dirección)
  - Coordenadas GPS
  - RNSPA
- **Condición de tenencia**
- **Cambio de contraseña**

#### 3. Gestión de Campos
- **Listado de campos** con información básica
- **Agregar nuevo campo**:
  - Ubicación (latitud, longitud, localidad)
  - Fuentes de agua (humano y animal)
  - Tipo de pasto predominante
  - Tipo de suelo predominante
  - Dispositivos disponibles
- **Editar campo existente**
- **Eliminar campo**
- **Vista de mapa** con ubicación de campos

#### 4. Gestión de Stock Animal
- **Listado de animales** por campo
- **Agregar animales**:
  - Especie (ovino/caprino)
  - Raza
  - Categoría (machos, hembras, crías, etc.)
  - Cantidad
  - Tipo de registro
- **Editar stock existente**
- **Eliminar registros**
- **Filtros por especie, raza, categoría**

#### 5. Reportes y Estadísticas
- **Resumen de producción** por período
- **Evolución del stock** (gráficos)
- **Distribución por especie y raza**
- **Exportar datos** (PDF, Excel)

#### 6. Configuración
- **Preferencias de notificaciones**
- **Configuración de actualizaciones**
- **Historial de cambios**

### Funcionalidades Adicionales Sugeridas

#### 1. Sistema de Alertas
- **Recordatorios de actualización** de datos
- **Alertas sanitarias** (vacunación, desparasitación)
- **Notificaciones de eventos** importantes

#### 2. Integración con GPS
- **Captura automática** de coordenadas
- **Validación de ubicación** de campos
- **Mapas interactivos** con ubicación de campos

#### 3. Gestión de Documentos
- **Subida de documentos** (RNSPA, certificados)
- **Almacenamiento seguro** de archivos
- **Historial de documentos**

#### 4. Comunicación
- **Mensajes internos** con instituciones
- **Notificaciones del sistema**
- **Soporte técnico**

## Estructura de Archivos Sugerida

```
app/Livewire/Productor/
├── Dashboard.php
├── Perfil.php
├── Campos/
│   ├── ListarCampos.php
│   ├── CrearCampo.php
│   └── EditarCampo.php
├── StockAnimal/
│   ├── ListarStock.php
│   ├── CrearStock.php
│   └── EditarStock.php
├── Reportes/
│   ├── ResumenProduccion.php
│   └── Estadisticas.php
└── Configuracion.php
```

## Próximos Pasos Recomendados

### Fase 1: Panel Básico del Productor
1. Crear el componente `Dashboard` del productor
2. Implementar gestión de perfil
3. Crear listado básico de campos

### Fase 2: Gestión Completa de Campos
1. Implementar CRUD completo de campos
2. Agregar validación de coordenadas
3. Integrar mapas básicos

### Fase 3: Gestión de Stock Animal
1. Implementar CRUD de stock animal
2. Crear filtros y búsquedas
3. Agregar validaciones específicas

### Fase 4: Reportes y Estadísticas
1. Implementar reportes básicos
2. Agregar gráficos y visualizaciones
3. Funcionalidad de exportación

### Fase 5: Funcionalidades Avanzadas
1. Sistema de alertas
2. Integración GPS
3. Gestión de documentos
4. Comunicación interna

## Consideraciones Técnicas

### Seguridad
- Validación de datos en frontend y backend
- Control de acceso por roles
- Sanitización de inputs
- Protección CSRF

### Rendimiento
- Paginación en listados grandes
- Carga lazy de componentes
- Optimización de consultas
- Caché de datos frecuentes

### Usabilidad
- Interfaz responsive
- Navegación intuitiva
- Mensajes de confirmación
- Validación en tiempo real

## Conclusión

El sistema actual tiene una base sólida con autenticación, autorización y gestión administrativa implementada. La próxima prioridad debería ser desarrollar el panel específico para productores, comenzando por el dashboard básico y la gestión de perfil, para luego expandir hacia la gestión completa de campos y stock animal.

La arquitectura modular del sistema permite un desarrollo incremental sin afectar las funcionalidades existentes, lo que facilita la implementación de nuevas características de manera controlada y segura.
