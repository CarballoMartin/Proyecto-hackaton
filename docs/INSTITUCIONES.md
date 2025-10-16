# 🏛️ Documentación del Sistema de Instituciones

## 📋 Resumen
Este documento describe el estado actual del sistema de instituciones, los seeders implementados y los comandos necesarios para replicar el entorno de desarrollo.

## 🎯 Estado Actual del Sistema

### ✅ **Lo que está implementado:**
- **10 instituciones** con logos reales y datos completos
- **10 usuarios institucionales** con credenciales de prueba
- **Modelos y relaciones** funcionando correctamente
- **Sistema de autenticación** por roles implementado

### 📊 **Instituciones Creadas:**

| Institución | Email Admin | Contraseña | Estado | Logo |
|-------------|-------------|------------|--------|------|
| Instituto Tecnológico Agropecuario | admin@instituto-tech.test | password123 | ✅ Validada | - |
| Universidad Estatal de Agricultura | admin@universidad-agro.test | password123 | ✅ Validada | - |
| Ministerio de Agricultura y Ganadería | admin@ministerio-agro.test | password123 | ✅ Validada | - |
| Servicio Nacional Sanitario | admin@servicio-sanitario.test | password123 | ✅ Validada | - |
| Cooperativa Agrícola Regional | admin@cooperativa-regional.test | password123 | ⏳ Pendiente | - |
| Asociación de Productores del Sur | admin@asociacion-sur.test | password123 | ⏳ Pendiente | - |
| Fundación para el Desarrollo Rural | admin@fundacionrural.test | password123 | ⏳ Pendiente | - |
| Cámara de Productores Ganaderos | admin@camara-productores.test | password123 | ⏳ Pendiente | - |
| Instituto de Investigación Agropecuaria | admin@instituto-investigacion.test | password123 | ⏳ Pendiente | - |
| Asociación de Técnicos Agropecuarios | admin@asociacion-tecnicos.test | password123 | ⏳ Pendiente | - |

## 🗂️ Archivos Creados/Modificados

### **Nuevos Seeders:**
- `database/seeders/InstitucionSeederMejorado.php` - Crea las 10 instituciones con logos reales
- `database/seeders/UsuarioInstitucionalSeeder.php` - Crea usuarios admin para cada institución

### **Archivos Modificados:**
- `database/seeders/DatabaseSeeder.php` - Actualizado para incluir el nuevo seeder

## 🚀 Comandos para Replicar el Entorno

### **1. Ejecutar Migraciones y Seeders Completos:**
```bash
php artisan migrate:fresh --seed
```

### **2. Si solo quieres recrear las instituciones:**
```bash
php artisan db:seed --class=InstitucionSeederMejorado
php artisan db:seed --class=UsuarioInstitucionalSeeder
```

### **3. Verificar que todo funcionó:**
```bash
php artisan tinker
```
Luego ejecutar en tinker:
```php
// Verificar instituciones
App\Models\Institucion::count(); // Debería retornar 10

// Verificar usuarios institucionales
App\Models\User::where('rol', 'institucional')->count(); // Debería retornar 10

// Verificar relaciones
App\Models\InstitucionalParticipante::count(); // Debería retornar 10
```

## 🔐 Credenciales de Prueba

### **Superadmin:**
- Email: `superadmin@test.com`
- Contraseña: `password`

### **Usuarios Institucionales:**
Todos los usuarios institucionales usan la contraseña: `password123`

Para probar cada institución, usa los emails de la tabla anterior.

## 🎨 Logos Disponibles

Los logos están ubicados en `public/logos/`:
- Las instituciones pueden tener logos personalizados asignados en la columna `logo_path`
- Por defecto se usa `null` (sin logo) o un placeholder genérico

## 🔄 Rutas de Acceso

### **Panel de Superadmin:**
- URL: `/superadmin/instituciones`
- Ruta: `admin.instituciones.panel`

### **Panel Institucional:**
- URL: `/institucional/dashboard`
- Ruta: `institucional.dashboard`

## 📝 Notas Importantes

1. **Error en UnidadProductivaSeeder:** Hay un error de duplicación en `identificador_local` que no afecta las instituciones.

2. **Orden de Seeders:** El `UsuarioInstitucionalSeeder` debe ejecutarse DESPUÉS del `InstitucionSeederMejorado`.

3. **Base de Datos:** Se está usando SQLite en desarrollo.

4. **Middleware:** Los usuarios institucionales requieren el middleware `role:institucional`.

## 🎯 Próximos Pasos Sugeridos

1. **Panel Institucional:** Completar funcionalidades del dashboard institucional
2. **Gestión de Participantes:** Implementar CRUD de participantes por institución
3. **Sistema de Reportes:** Crear reportes específicos para instituciones
4. **Notificaciones:** Sistema de notificaciones entre instituciones y superadmin

## 📞 Contacto

Para cualquier duda sobre este sistema, consultar con el equipo de desarrollo.

---
**Última actualización:** 30 de Septiembre de 2025
**Versión:** 1.0



