# 🧹 PLAN DE LIMPIEZA AJUSTADO - REVISIÓN COMPLETA

**Fecha:** 12 de Octubre de 2025  
**Versión:** 2.0 - Ajustado tras revisión  
**Status:** ✅ Aprobado para ejecución

---

## ✅ CONFIRMACIONES DE LA REVISIÓN

### 1. PRODUCTORES - ✅ NO TOCAR
**Verificado:** Todos los productores son datos FALSOS de seeders

```php
✅ Nombres genéricos: "Juan Carlos Productor", "María Elena Productora"
✅ Emails de prueba: @test.com
✅ DNIs ficticios: 12345678, 87654321, etc.
✅ Teléfonos genéricos: 0297-1234567, etc.
```

**Decisión:** ✅ **NO MODIFICAR** los seeders de productores

### 2. LOCALIDADES/MUNICIPIOS - ✅ NO TOCAR
**Decisión del usuario:** NO es necesario anonimizar ubicaciones geográficas

```
✅ MANTENER: Aristóbulo del Valle, Candelaria, Oberá, Posadas, etc.
✅ MANTENER: Parajes, municipios, provincias
✅ MANTENER: municipios.geojson con coordenadas
```

**Decisión:** ✅ **NO MODIFICAR** referencias geográficas

---

## 🎯 OBJETIVO REAL - SOLO INSTITUCIONES

**Limpieza enfocada en:**
1. ❌ Instituciones reales (INTA, UNaM, SENASA, SRM)
2. ❌ Logos de organizaciones reales
3. ❌ Documentación con referencias a organizaciones específicas
4. ❌ Base de datos actual (posible contenido real)
5. ❌ Archivos .bak obsoletos

---

## 📋 INVENTARIO AJUSTADO

### 1. LOGOS - ALTA PRIORIDAD ❌

**Ubicación:** `public/logos/`

```bash
❌ ELIMINAR COMPLETAMENTE:
- inta1.png                    # Logo INTA Real
- unam.jpg                     # Logo Universidad Real
- Logo SRM.jpg                 # Logo Sociedad Rural Real
- logoovinos.png               # Logo SENASA/Cámara Real
- efa-sancristobal.jpg         # Logo EFA Real
- todostenemos.jpg             # Logo Cooperativa Real
- municipios/candelaria.png    # Logo Municipio Real
- municipios/fachinal.jpg      # Logo Municipio Real
- municipios/profundidad.jpg   # Logo Municipio Real

✅ CREAR GENÉRICOS:
- logo-instituto-1.png         # Placeholder genérico
- logo-universidad-2.png       # Placeholder genérico
- logo-cooperativa-3.png       # Placeholder genérico
- logo-placeholder.png         # Genérico por defecto
```

### 2. SEEDERS DE INSTITUCIONES - ALTA PRIORIDAD ❌

**Archivos a modificar:**

#### A) `database/seeders/InstitucionSeeder.php`
```php
❌ REEMPLAZAR:
'INTA'                                    → 'Instituto Tecnológico Agropecuario'
'Universidad Nacional de Misiones'        → 'Universidad Estatal de Agricultura'
'Cooperativa Agrícola de Misiones'        → 'Cooperativa Agrícola Regional'
'Asociación de Ganaderos del Sur'         → 'Asociación de Productores'

❌ CUITs reales                            → CUITs ficticios válidos
❌ Emails reales                           → admin@instituto-tech.test
❌ Logos reales                            → logo-placeholder.png
```

#### B) `database/seeders/InstitucionSeederMejorado.php`
```php
❌ REEMPLAZAR las mismas instituciones
❌ 'INTA - Instituto Nacional...'         → 'ITA - Instituto Tecnológico...'
❌ 'Universidad Nacional de Misiones'     → 'Universidad Estatal de Agricultura'
❌ 'Ministerio del Agro...de Misiones'    → 'Ministerio de Agricultura y Ganadería'
❌ 'SENASA - Servicio Nacional...'        → 'SNS - Servicio Nacional Sanitario'
❌ 'Cooperativa Agrícola de Misiones'     → 'Cooperativa Agrícola Regional'
❌ 'Sociedad Rural de Misiones'           → 'Sociedad Rural Provincial'
```

#### C) `database/seeders/UsuarioInstitucionalSeeder.php`
```php
❌ REEMPLAZAR:
'admin@inta.misiones.test'              → 'admin@instituto-tech.test'
'admin@unam.test'                       → 'admin@universidad-agro.test'
'admin@agro.misiones.test'              → 'admin@ministerio-agro.test'
'admin@senasa.misiones.test'            → 'admin@servicio-sanitario.test'
'admin@coopmisiones.test'               → 'admin@cooperativa-regional.test'

❌ Nombres de usuarios:
'Admin INTA Misiones'                   → 'Admin Instituto Tecnológico'
'Admin UNaM'                            → 'Admin Universidad Agricultura'
'Admin Ministerio Agro'                 → 'Admin Ministerio Agricultura'
'Admin SENASA Misiones'                 → 'Admin Servicio Sanitario'
```

### 3. DOCUMENTACIÓN - MEDIA PRIORIDAD ⚠️

**Acción:** ARCHIVAR documentos muy específicos, NO modificarlos

```bash
📁 MOVER A /archive/docs-cuenca/:

- desarrollo_territorial.txt              # 60+ menciones INTA, UNaM, SENASA
- INSTITUCIONES.md                        # Lista instituciones reales
- COMANDOS_INSTITUCIONES.txt              # Emails instituciones reales
```

**Documentos a MANTENER (son técnicos/genéricos):**
```bash
✅ MANTENER:
- ANALISIS_COMPLETO_PROYECTO_2025.md
- ANALISIS_GAPS.md
- DOCUMENTACION_TECNICA_BACKEND.md
- PLAN_DE_REFACTORIZACION.md
- etc. (la mayoría son técnicos)
```

### 4. README.md - BAJA PRIORIDAD ⚠️

**Cambios mínimos:**

```markdown
❌ ANTES:
Sistema web para la gestión integral de producción ovina y caprina, 
desarrollado con Laravel 10 y Livewire 3.

✅ DESPUÉS:
Sistema web para la gestión integral de producción ganadera (ovinos y caprinos),
desarrollado con Laravel 12 y Livewire 3.
```

**Mantener todo lo demás igual** (es ya bastante genérico)

### 5. BASE DE DATOS - ALTA PRIORIDAD ❌

```bash
❌ ACCIÓN:
1. Backup: mv database/database.sqlite archive/database-backup/database.sqlite.bak
2. Eliminar: rm database/database.sqlite (o se auto-crea vacío)
3. Regenerar: php artisan migrate:fresh --seed
```

### 6. ARCHIVOS .BAK - MEDIA PRIORIDAD 🗑️

```bash
❌ ELIMINAR:
- routes/web.php.bak
- app/Livewire/Productor/Parajes/CrearParajeModal.php.bak
- app/Livewire/Productor/UnidadesProductivas/CrearUnidadProductiva.php.bak
```

---

## 🛠️ PLAN DE EJECUCIÓN AJUSTADO

### FASE 1: BACKUP (3 min)

```bash
# Crear estructura de archive
mkdir -p archive/logos-originales
mkdir -p archive/docs-cuenca
mkdir -p archive/database-backup

# Backup de logos
cp -r public/logos/* archive/logos-originales/

# Backup de documentos específicos
cp docs/desarrollo_territorial.txt archive/docs-cuenca/
cp docs/INSTITUCIONES.md archive/docs-cuenca/
cp docs/COMANDOS_INSTITUCIONES.txt archive/docs-cuenca/

# Backup de base de datos
cp database/database.sqlite archive/database-backup/database.sqlite.bak 2>/dev/null || echo "No existe database.sqlite"
```

### FASE 2: ELIMINAR LOGOS (2 min)

```bash
# Eliminar logos de instituciones reales
rm public/logos/inta1.png
rm public/logos/unam.jpg
rm "public/logos/Logo SRM.jpg"
rm public/logos/logoovinos.png
rm public/logos/efa-sancristobal.jpg
rm public/logos/todostenemos.jpg
rm public/logos/municipios/candelaria.png
rm public/logos/municipios/fachinal.jpg
rm public/logos/municipios/profundidad.jpg

# Crear placeholder genérico
# (Usaremos un logo placeholder simple o genérico)
```

### FASE 3: MODIFICAR SEEDERS DE INSTITUCIONES (20 min)

**Archivos a editar:**
1. ✅ `database/seeders/InstitucionSeeder.php`
2. ✅ `database/seeders/InstitucionSeederMejorado.php`
3. ✅ `database/seeders/UsuarioInstitucionalSeeder.php`

**Cambios específicos documentados en sección 2**

### FASE 4: LIMPIAR ARCHIVOS .BAK (1 min)

```bash
rm routes/web.php.bak
rm app/Livewire/Productor/Parajes/CrearParajeModal.php.bak
rm app/Livewire/Productor/UnidadesProductivas/CrearUnidadProductiva.php.bak
```

### FASE 5: MOVER DOCUMENTACIÓN ESPECÍFICA (2 min)

```bash
mv docs/desarrollo_territorial.txt archive/docs-cuenca/
mv docs/INSTITUCIONES.md archive/docs-cuenca/
mv docs/COMANDOS_INSTITUCIONES.txt archive/docs-cuenca/
```

### FASE 6: ACTUALIZAR README (5 min)

```bash
# Edición manual de README.md
# Cambios mínimos según sección 4
```

### FASE 7: REGENERAR BASE DE DATOS (3 min)

```bash
# Eliminar BD actual
rm database/database.sqlite

# Regenerar con seeders anonimizados
php artisan migrate:fresh --seed
```

### FASE 8: VERIFICACIÓN (5 min)

```bash
# Buscar menciones de instituciones reales en código activo
grep -r "INTA\|UNaM\|SENASA" database/seeders/ --exclude-dir=archive
grep -r "admin@inta\|admin@unam\|admin@senasa" database/seeders/ --exclude-dir=archive

# Verificar que logos genéricos existan
ls -la public/logos/

# Verificar BD regenerada
php artisan db:show
```

---

## ⏱️ TIEMPO ESTIMADO AJUSTADO

| Fase | Tiempo | Complejidad |
|------|--------|-------------|
| Fase 1: Backup | 3 min | Baja |
| Fase 2: Logos | 2 min | Baja |
| Fase 3: Seeders | 20 min | Media |
| Fase 4: .BAK | 1 min | Baja |
| Fase 5: Docs | 2 min | Baja |
| Fase 6: README | 5 min | Baja |
| Fase 7: BD | 3 min | Baja |
| Fase 8: Verificación | 5 min | Baja |
| **TOTAL** | **~41 min** | **⚡ Rápido** |

---

## ✅ CHECKLIST AJUSTADO

### ALTA PRIORIDAD (Obligatorio)
- [ ] Backup de logos originales
- [ ] Eliminar 9 logos de instituciones reales
- [ ] Modificar `InstitucionSeeder.php`
- [ ] Modificar `InstitucionSeederMejorado.php`
- [ ] Modificar `UsuarioInstitucionalSeeder.php`
- [ ] Eliminar 3 archivos .bak
- [ ] Regenerar database.sqlite

### MEDIA PRIORIDAD (Recomendado)
- [ ] Mover 3 documentos específicos a archive/
- [ ] Actualizar README.md con cambios mínimos
- [ ] Verificación de menciones a instituciones

### BAJA PRIORIDAD (Opcional)
- [ ] Crear logo placeholder genérico

---

## 📊 RESUMEN DE CAMBIOS

### LO QUE SE LIMPIA ❌
1. ✅ **Logos de instituciones reales** (9 archivos)
2. ✅ **Nombres de instituciones en seeders** (3 archivos)
3. ✅ **Emails institucionales en seeders** (3 archivos)
4. ✅ **Base de datos actual** (1 archivo)
5. ✅ **Archivos .bak obsoletos** (3 archivos)
6. ✅ **Documentos muy específicos** (3 archivos → archive)

### LO QUE SE MANTIENE ✅
1. ✅ **Productores** - Ya son datos falsos
2. ✅ **Localidades/Municipios** - No sensibles según contexto
3. ✅ **Parajes y ubicaciones** - No sensibles
4. ✅ **Documentación técnica** - Es genérica
5. ✅ **Funcionalidad completa** - Sin cambios
6. ✅ **Estructura del proyecto** - Intacta

---

## 🎯 RESULTADO ESPERADO

Al finalizar:
1. ✅ Sin logos de instituciones reales
2. ✅ Sin nombres de organizaciones específicas en seeders
3. ✅ Base de datos limpia con datos genéricos
4. ✅ Código funcional 100%
5. ✅ Datos de prueba realistas pero anónimos
6. ✅ Documentación sensible archivada (no eliminada)

---

## 🚀 LISTO PARA EJECUTAR

**Confirmación:** Este plan está ajustado según las especificaciones:
- ✅ NO toca productores (ya son falsos)
- ✅ NO toca localidades (no es necesario)
- ✅ SÍ limpia instituciones reales
- ✅ SÍ archiva documentación específica
- ✅ Tiempo reducido: ~41 minutos

**¿Proceder con la ejecución?** 🚀

---

**Fin del Plan Ajustado**


