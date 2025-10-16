# 🧹 PLAN DE LIMPIEZA Y ANONIMIZACIÓN DEL PROYECTO

**Fecha:** 12 de Octubre de 2025  
**Objetivo:** Eliminar toda información sensible de instituciones y productores reales  
**Status:** 📋 Pendiente de aprobación

---

## 🎯 OBJETIVOS

1. ✅ Remover referencias a instituciones reales (INTA, UNaM, SENASA, SRM, etc.)
2. ✅ Eliminar datos de productores reales
3. ✅ Anonimizar información geográfica específica (Misiones, Posadas, etc.)
4. ✅ Remover logos y recursos de organizaciones reales
5. ✅ Generalizar documentación técnica
6. ✅ Mantener funcionalidad completa con datos ficticios

---

## 📋 INVENTARIO DE DATOS A LIMPIAR

### 1. LOGOS Y RECURSOS VISUALES

**Ubicación:** `public/logos/`

```
❌ REMOVER:
- inta1.png                    → Logo INTA (Instituto Nacional)
- unam.jpg                     → Logo Universidad Nacional de Misiones
- Logo SRM.jpg                 → Logo Sociedad Rural de Misiones
- logoovinos.png               → Logo SENASA/Cámara Ovino-Caprinos
- efa-sancristobal.jpg         → Logo EFA San Cristóbal
- todostenemos.jpg             → Logo Cooperativa
- municipios/candelaria.png    → Municipio real
- municipios/fachinal.jpg      → Municipio real
- municipios/profundidad.jpg   → Municipio real

✅ REEMPLAZAR CON:
- logo-institucion-1.png       → Logo genérico placeholder
- logo-institucion-2.png       → Logo genérico placeholder
- logo-institucion-3.png       → Logo genérico placeholder
- logo-cooperativa.png         → Logo genérico
- logo-educativo.png           → Logo genérico
- municipios/municipio-1.png   → Logos genéricos
```

### 2. SEEDERS DE INSTITUCIONES

**Archivos:**
- `database/seeders/InstitucionSeeder.php`
- `database/seeders/InstitucionSeederMejorado.php`
- `database/seeders/UsuarioInstitucionalSeeder.php`

**Datos actuales (SENSIBLES):**
```
❌ INTA - Instituto Nacional de Tecnología Agropecuaria
❌ Universidad Nacional de Misiones (UNaM)
❌ Ministerio del Agro y la Producción de Misiones
❌ SENASA - Servicio Nacional de Sanidad y Calidad Agroalimentaria
❌ Cooperativa Agrícola de Misiones
❌ Asociación de Ganaderos del Sur
❌ Sociedad Rural de Misiones
```

**Reemplazo propuesto (GENÉRICO):**
```
✅ Instituto Tecnológico Agropecuario (ITA)
✅ Universidad Estatal de Agricultura (UEA)
✅ Ministerio de Agricultura y Ganadería (MAG)
✅ Servicio Nacional Sanitario (SNS)
✅ Cooperativa Agrícola Regional (CAR)
✅ Asociación de Productores del Sur (APS)
✅ Sociedad Rural Provincial (SRP)
```

### 3. UBICACIONES GEOGRÁFICAS

**Archivos afectados:**
- Seeders (múltiples)
- Vistas (6 archivos)
- Documentación (28+ archivos)
- `municipios.geojson`

**Datos actuales (SENSIBLES):**
```
❌ Misiones (Provincia real)
❌ Posadas (Capital real)
❌ Apóstoles (Municipio real)
❌ Oberá (Municipio real)
❌ Candelaria (Municipio real)
❌ Fachinal (Localidad real)
❌ Profundidad (Localidad real)
❌ Aristóbulo del Valle (Municipio real)
❌ San José (Municipio real)
❌ Picada Paraguay (Paraje real)
```

**Reemplazo propuesto (GENÉRICO):**
```
✅ Provincia del Valle Verde
✅ Capital Provincial
✅ Ciudad del Norte
✅ Ciudad del Sur
✅ Ciudad del Este
✅ Villa Central
✅ Pueblo del Lago
✅ Valle del Río
✅ San Martín
✅ Paraje Los Aromos
```

### 4. SEEDERS DE PRODUCTORES

**Archivos:**
- `database/seeders/ProductorSeeder.php`
- `database/seeders/ProductorSeederMejorado.php`
- `database/seeders/UnidadProductivaSeederMejorado.php`
- `database/seeders/StockAnimalSeederMejorado.php`

**Acción:**
- ✅ Reemplazar nombres reales por genéricos: "Juan Pérez", "María González", etc.
- ✅ Usar DNIs/CUITs ficticios válidos en formato pero inexistentes
- ✅ Emails de prueba: productor1@test.com, productor2@test.com
- ✅ Teléfonos genéricos: 011-1234-5678
- ✅ Direcciones genéricas

### 5. DOCUMENTACIÓN TÉCNICA

**Archivos en `docs/`:**

```
⚠️ REVISAR Y ANONIMIZAR:
- desarrollo_territorial.txt          → Menciona cuenca, INTA, SENASA, UNaM 60+ veces
- INSTITUCIONES.md                    → Lista instituciones reales
- COMANDOS_INSTITUCIONES.txt          → Emails de instituciones reales
- ANALISIS_COMPLETO_PROYECTO_2025.md  → Menciona Misiones
- RESUMEN_EJECUTIVO_ANALISIS.md       → Menciona región específica

✅ ACCIÓN:
- Reemplazar referencias específicas por genéricas
- Mantener estructura y contenido técnico
- O MOVER a carpeta /archive/ si es muy específico
```

### 6. ARCHIVO GEOJSON

**Archivo:** `municipios.geojson`

```
❌ CONTIENE: Datos geográficos reales de Misiones
✅ OPCIONES:
  A) Eliminar completamente
  B) Reemplazar con datos ficticios genéricos
  C) Usar archivo de ejemplo de otra región genérica
```

### 7. VISTAS CON REFERENCIAS

**Archivos:**
- `resources/views/pages/cuenca-misiones.blade.php`  → Nombre específico
- `resources/views/livewire/institucional/configuracion.blade.php`
- `resources/views/livewire/institucional/mapa.blade.php`
- `resources/views/layouts/partials/navigation/landing-nav.blade.php`
- `resources/views/layouts/partials/footer.blade.php`

**Acción:**
- ✅ Reemplazar textos específicos por genéricos
- ✅ Renombrar archivo `cuenca-misiones.blade.php` → `region-productiva.blade.php`

### 8. BASE DE DATOS SQLITE

**Archivo:** `database/database.sqlite`

```
⚠️ CONTIENE: Posiblemente datos reales cargados

✅ ACCIÓN RECOMENDADA:
1. Backup actual (mover a /archive/)
2. Eliminar database.sqlite
3. Regenerar con: php artisan migrate:fresh --seed
4. Nuevos seeders ya estarán anonimizados
```

### 9. README Y DOCUMENTACIÓN PRINCIPAL

**Archivos:**
- `README.md`
- `DESPLIEGUE.md`

**Acción:**
- ✅ Generalizar descripción del proyecto
- ✅ Remover referencias a región específica
- ✅ Mantener características técnicas

---

## 📝 ARCHIVOS ESPECÍFICOS A MODIFICAR

### ALTA PRIORIDAD (Contienen datos sensibles directos)

1. ✅ `database/seeders/InstitucionSeeder.php`
2. ✅ `database/seeders/InstitucionSeederMejorado.php`
3. ✅ `database/seeders/UsuarioInstitucionalSeeder.php`
4. ✅ `database/seeders/ProductorSeeder.php`
5. ✅ `database/seeders/ProductorSeederMejorado.php`
6. ✅ `database/seeders/UnidadProductivaSeederMejorado.php`
7. ✅ `database/seeders/StockAnimalSeederMejorado.php`
8. ✅ `database/seeders/ClimaSeeder.php`
9. ✅ Todos los logos en `public/logos/`
10. ✅ `database/database.sqlite`

### MEDIA PRIORIDAD (Referencias en código funcional)

11. ✅ `resources/views/pages/cuenca-misiones.blade.php`
12. ✅ `resources/views/livewire/institucional/configuracion.blade.php`
13. ✅ `resources/views/livewire/institucional/mapa.blade.php`
14. ✅ `resources/views/layouts/partials/navigation/landing-nav.blade.php`
15. ✅ `resources/views/layouts/partials/footer.blade.php`
16. ✅ `README.md`

### BAJA PRIORIDAD (Documentación técnica)

17. ⚠️ `docs/desarrollo_territorial.txt` (60+ menciones)
18. ⚠️ `docs/INSTITUCIONES.md`
19. ⚠️ `docs/COMANDOS_INSTITUCIONES.txt`
20. ⚠️ Otros docs con menciones específicas

**RECOMENDACIÓN:** Mover documentación muy específica a `/archive/` en lugar de modificarla.

---

## 🛠️ PLAN DE EJECUCIÓN

### FASE 1: BACKUP Y PREPARACIÓN (5 min)

```bash
# Crear carpeta archive
mkdir archive
mkdir archive/logos-originales
mkdir archive/docs-especificos
mkdir archive/database-backup

# Backup de archivos originales
cp -r public/logos/* archive/logos-originales/
cp database/database.sqlite archive/database-backup/ 2>/dev/null || true
```

### FASE 2: LIMPIEZA DE RECURSOS (10 min)

1. ✅ Eliminar logos de instituciones reales
2. ✅ Crear/agregar logos genéricos placeholder

### FASE 3: ANONIMIZAR SEEDERS (30 min)

1. ✅ Modificar `InstitucionSeeder.php`
2. ✅ Modificar `InstitucionSeederMejorado.php`
3. ✅ Modificar `UsuarioInstitucionalSeeder.php`
4. ✅ Modificar `ProductorSeeder.php`
5. ✅ Modificar `ProductorSeederMejorado.php`
6. ✅ Modificar `UnidadProductivaSeederMejorado.php`
7. ✅ Modificar `StockAnimalSeederMejorado.php`
8. ✅ Modificar `ClimaSeeder.php`

### FASE 4: LIMPIAR VISTAS (20 min)

1. ✅ Renombrar y modificar `cuenca-misiones.blade.php`
2. ✅ Actualizar referencias en vistas
3. ✅ Actualizar navegación y footer

### FASE 5: ACTUALIZAR DOCUMENTACIÓN (15 min)

1. ✅ Actualizar `README.md`
2. ✅ Mover docs específicos a `/archive/`
3. ✅ Actualizar referencias en rutas si es necesario

### FASE 6: REGENERAR BASE DE DATOS (5 min)

```bash
# Eliminar BD actual
rm database/database.sqlite

# Regenerar con datos anonimizados
php artisan migrate:fresh --seed

# Verificar
php artisan db:seed --class=InstitucionSeeder
```

### FASE 7: VERIFICACIÓN (10 min)

1. ✅ Verificar que no queden referencias a instituciones reales
2. ✅ Verificar que logos genéricos carguen correctamente
3. ✅ Probar login con usuarios de prueba
4. ✅ Verificar vistas principales
5. ✅ Buscar en todo el proyecto menciones a:
   - "INTA"
   - "UNaM"
   - "SENASA"
   - "Misiones"
   - "Posadas"

```bash
# Búsqueda final de verificación
grep -r "INTA\|UNaM\|SENASA\|Misiones" app/ database/ resources/ --exclude-dir=vendor
```

---

## ⏱️ TIEMPO ESTIMADO TOTAL

| Fase | Tiempo | Complejidad |
|------|--------|-------------|
| Fase 1: Backup | 5 min | Baja |
| Fase 2: Logos | 10 min | Baja |
| Fase 3: Seeders | 30 min | Media |
| Fase 4: Vistas | 20 min | Media |
| Fase 5: Docs | 15 min | Baja |
| Fase 6: BD | 5 min | Baja |
| Fase 7: Verificación | 10 min | Baja |
| **TOTAL** | **~95 min** | **1.5 horas** |

---

## ✅ CHECKLIST FINAL

Después de completar la limpieza, verificar:

- [ ] No hay logos de instituciones reales en `public/logos/`
- [ ] Todos los seeders usan datos ficticios
- [ ] No hay menciones a "INTA", "UNaM", "SENASA" en código activo
- [ ] No hay referencias a "Misiones", "Posadas" en código activo
- [ ] Base de datos regenerada con datos anónimos
- [ ] README.md es genérico
- [ ] Aplicación funciona correctamente con datos ficticios
- [ ] Tests pasan correctamente
- [ ] Documentación sensible archivada en `/archive/`

---

## 🎯 RESULTADO ESPERADO

Al finalizar la limpieza:

1. ✅ Proyecto 100% genérico y reutilizable
2. ✅ Sin datos sensibles de instituciones reales
3. ✅ Sin información geográfica específica comprometedora
4. ✅ Funcionalidad completa mantenida
5. ✅ Datos de prueba realistas pero ficticios
6. ✅ Listo para desarrollo público/compartido
7. ✅ Listo para actualizaciones de dependencias

---

## 📌 NOTAS IMPORTANTES

- ⚠️ Los datos originales se mantendrán en `/archive/` por seguridad
- ⚠️ Este proceso es **IRREVERSIBLE** en git si se hace commit
- ⚠️ Se recomienda crear un **branch** antes: `git checkout -b limpieza-anonimizacion`
- ⚠️ La documentación técnica en `/docs/` muy específica se archivará, no se modificará

---

## 🚀 ¿PROCEDER CON LA LIMPIEZA?

**Opciones:**

1. ✅ **PROCEDER COMPLETO**: Ejecutar todas las fases (95 min)
2. ⚠️ **PROCEDER PARCIAL**: Solo alta prioridad (40 min)
3. 📝 **REVISAR**: Ajustar plan antes de ejecutar
4. ❌ **CANCELAR**: No realizar limpieza

---

**Fin del Plan de Limpieza**  
**Esperando confirmación para proceder...**


