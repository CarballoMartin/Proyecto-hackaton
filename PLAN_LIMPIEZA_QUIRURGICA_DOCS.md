# 🔍 PLAN DE LIMPIEZA QUIRÚRGICA - DOCUMENTACIÓN

**Objetivo:** Limpiar SOLO la información sensible, manteniendo el contenido técnico valioso

---

## 📄 ARCHIVOS DE DOCUMENTACIÓN A LIMPIAR

### 1️⃣ `docs/INSTITUCIONES.md` ✅ MANTENER (Limpiar tabla)

**Contenido:** Documentación técnica sobre sistema de instituciones (seeders, comandos, etc.)

**QUÉ LIMPIAR:**

#### Tabla de Instituciones (líneas 16-27)
```markdown
❌ ANTES:
| INTA - Instituto Nacional de Tecnología Agropecuaria | admin@inta.misiones.test | password123 | ✅ Validada | inta1.png |
| Universidad Nacional de Misiones | admin@unam.test | password123 | ✅ Validada | unam.jpg |
| Ministerio del Agro y la Producción de Misiones | admin@agro.misiones.test | password123 | ✅ Validada | candelaria.png |
| SENASA - Servicio Nacional de Sanidad y Calidad Agroalimentaria | admin@senasa.misiones.test | password123 | ✅ Validada | logoovinos.png |
| Cooperativa Agrícola de Misiones | admin@coopmisiones.test | password123 | ⏳ Pendiente | todostenemos.jpg |
| Asociación de Ganaderos del Sur | admin@ganaderossur.test | password123 | ⏳ Pendiente | Logo SRM.jpg |
| Fundación para el Desarrollo Rural | admin@fundacionrural.test | password123 | ⏳ Pendiente | efa-sancristobal.jpg |
| Cámara de Productores Ovino-Caprinos | admin@camaraovinocaprina.test | password123 | ⏳ Pendiente | logoovinos.png |
| Instituto de Investigación Agropecuaria Regional | admin@iiar.test | password123 | ⏳ Pendiente | efa-sancristobal.jpg |
| Asociación de Técnicos Agropecuarios | admin@atecnicos.test | password123 | ⏳ Pendiente | efa-sancristobal.jpg |

✅ DESPUÉS:
| Instituto Tecnológico Agropecuario | admin@instituto-tech.test | password123 | ✅ Validada | logo-placeholder.png |
| Universidad Estatal de Agricultura | admin@universidad-agro.test | password123 | ✅ Validada | logo-placeholder.png |
| Ministerio de Agricultura y Ganadería | admin@ministerio-agro.test | password123 | ✅ Validada | logo-placeholder.png |
| Servicio Nacional Sanitario | admin@servicio-sanitario.test | password123 | ✅ Validada | logo-placeholder.png |
| Cooperativa Agrícola Regional | admin@cooperativa-regional.test | password123 | ⏳ Pendiente | logo-placeholder.png |
| Asociación de Productores del Sur | admin@asociacion-sur.test | password123 | ⏳ Pendiente | logo-placeholder.png |
| Fundación para el Desarrollo Rural | admin@fundacionrural.test | password123 | ⏳ Pendiente | logo-placeholder.png |
| Cámara de Productores | admin@camara-productores.test | password123 | ⏳ Pendiente | logo-placeholder.png |
| Instituto de Investigación Agropecuaria | admin@instituto-investigacion.test | password123 | ⏳ Pendiente | logo-placeholder.png |
| Asociación de Técnicos Agropecuarios | admin@asociacion-tecnicos.test | password123 | ⏳ Pendiente | logo-placeholder.png |
```

#### Sección Logos (líneas 78-87)
```markdown
❌ ANTES:
- `inta1.png` - INTA
- `unam.jpg` - Universidad Nacional de Misiones
- `candelaria.png` - Ministerio (usando logo de municipio)
- `logoovinos.png` - SENASA y Cámara Ovino-Caprinos
- `todostenemos.jpg` - Cooperativa
- `Logo SRM.jpg` - Ganaderos del Sur
- `efa-sancristobal.jpg` - Fundación, IIAR, ATA

✅ DESPUÉS:
- `logo-placeholder.png` - Logos genéricos para todas las instituciones
```

**QUÉ MANTENER:**
- ✅ Todo el contenido técnico sobre seeders
- ✅ Comandos de Laravel
- ✅ Explicación de rutas y middleware
- ✅ Estructura de tablas
- ✅ Próximos pasos sugeridos

---

### 2️⃣ `docs/COMANDOS_INSTITUCIONES.txt` ✅ MANTENER (Limpiar credenciales)

**Contenido:** Comandos rápidos para trabajar con instituciones (muy útil para desarrollo)

**QUÉ LIMPIAR:**

#### Credenciales de Instituciones (líneas 50-60)
```bash
❌ ANTES:
- INTA: admin@inta.misiones.test
- UNaM: admin@unam.test
- Ministerio: admin@agro.misiones.test
- SENASA: admin@senasa.misiones.test
- Cooperativa: admin@coopmisiones.test
- Ganaderos: admin@ganaderossur.test
- Fundación: admin@fundacionrural.test
- Cámara: admin@camaraovinocaprina.test
- IIAR: admin@iiar.test
- ATA: admin@atecnicos.test

✅ DESPUÉS:
- Instituto Tecnológico: admin@instituto-tech.test
- Universidad Agricultura: admin@universidad-agro.test
- Ministerio Agricultura: admin@ministerio-agro.test
- Servicio Sanitario: admin@servicio-sanitario.test
- Cooperativa Regional: admin@cooperativa-regional.test
- Asociación Sur: admin@asociacion-sur.test
- Fundación Rural: admin@fundacionrural.test
- Cámara Productores: admin@camara-productores.test
- Instituto Investigación: admin@instituto-investigacion.test
- Asoc. Técnicos: admin@asociacion-tecnicos.test
```

**QUÉ MANTENER:**
- ✅ TODOS los comandos Laravel (migrate:fresh, db:seed, tinker, etc.)
- ✅ Código PHP de ejemplos
- ✅ URLs de prueba
- ✅ Troubleshooting
- ✅ Notas técnicas

---

### 3️⃣ `docs/desarrollo_territorial.txt` ⚠️ DECISIÓN NECESARIA

**Contenido:** Documento extenso sobre desarrollo territorial con contexto socioeconómico

**PROBLEMA:** 
- Contiene 60+ menciones de instituciones reales en contexto explicativo/histórico
- Es un documento de CONTEXTO, no técnico del sistema
- Muchas referencias están en explicaciones narrativas

**OPCIONES:**

#### OPCIÓN A: ARCHIVAR COMPLETO ⭐ Recomendada
```bash
mv docs/desarrollo_territorial.txt archive/docs-contexto/
```
**Razón:** Es contexto histórico/regional, no documentación técnica del sistema.

#### OPCIÓN B: LIMPIAR ABREVIATURAS (líneas 39-59)
Solo limpiar la sección de abreviaturas:
```
❌ ANTES:
INTA: Instituto Nacional de Tecnología Agropecuaria.
SENASA: Servicio Nacional de Sanidad y Calidad Agroalimentaria.
EEA INTA Cerro Azul: Estación Experimental Agropecuaria INTA Cerro Azul.
INTA AER Posadas: INTA Agencia de Extensión Rural Posadas.

✅ DESPUÉS:
ITA: Instituto Tecnológico Agropecuario.
SNS: Servicio Nacional Sanitario.
EEA Regional: Estación Experimental Agropecuaria Regional.
AER Provincial: Agencia de Extensión Rural Provincial.
```

**Pero:** Quedarían 50+ menciones en el texto narrativo que serían difíciles de limpiar sin perder sentido.

#### OPCIÓN C: AÑADIR DISCLAIMER AL INICIO
```markdown
---
**NOTA:** Este documento contiene información de contexto histórico regional.
Las referencias a instituciones específicas son únicamente con fines educativos
y de contexto, y no representan afiliaciones o asociaciones del proyecto.
---
```

**MI RECOMENDACIÓN:** OPCIÓN A (Archivar) + OPCIÓN C (Disclaimer en otros docs si es necesario)

---

## 📊 RESUMEN DE CAMBIOS EN DOCUMENTACIÓN

| Archivo | Acción | Cambios | Mantiene Contenido Técnico |
|---------|--------|---------|---------------------------|
| **INSTITUCIONES.md** | ✂️ Limpiar | Tabla (12 líneas) + Logos (7 líneas) | ✅ 100% |
| **COMANDOS_INSTITUCIONES.txt** | ✂️ Limpiar | Credenciales (11 líneas) | ✅ 100% |
| **desarrollo_territorial.txt** | 📁 Archivar | Todo el archivo | N/A (es contexto, no técnico) |

**Total de líneas a modificar:** ~30 líneas en 2 archivos  
**Tiempo estimado:** 10 minutos

---

## 🛠️ PROCESO DE LIMPIEZA

### FASE 1: Backup
```bash
cp docs/INSTITUCIONES.md archive/docs-originales/INSTITUCIONES.md.bak
cp docs/COMANDOS_INSTITUCIONES.txt archive/docs-originales/COMANDOS_INSTITUCIONES.txt.bak
```

### FASE 2: Limpiar INSTITUCIONES.md
- ✂️ Reemplazar tabla de instituciones (líneas 16-27)
- ✂️ Reemplazar sección de logos (líneas 80-87)
- ✅ Mantener TODO lo demás

### FASE 3: Limpiar COMANDOS_INSTITUCIONES.txt
- ✂️ Reemplazar credenciales (líneas 50-60)
- ✅ Mantener TODO lo demás

### FASE 4: Archivar desarrollo_territorial.txt
```bash
mv docs/desarrollo_territorial.txt archive/docs-contexto/
```

---

## ✅ VERIFICACIÓN

Después de la limpieza, buscar menciones restantes:
```bash
grep -n "INTA\|UNaM\|SENASA" docs/INSTITUCIONES.md
grep -n "INTA\|UNaM\|SENASA" docs/COMANDOS_INSTITUCIONES.txt
```

Debe retornar: **0 resultados**

---

## 🎯 RESULTADO ESPERADO

✅ **INSTITUCIONES.md:**
- Tabla con instituciones genéricas
- Todos los comandos y explicaciones técnicas intactas
- Referencias a logos actualizadas

✅ **COMANDOS_INSTITUCIONES.txt:**
- Credenciales anonimizadas
- Todos los comandos Laravel funcionando
- Ejemplos de código intactos

✅ **desarrollo_territorial.txt:**
- Archivado en `archive/docs-contexto/`
- Disponible para consulta si es necesario
- No forma parte del código público del proyecto

---

## ⏱️ TIEMPO ESTIMADO

- Backup: 1 min
- INSTITUCIONES.md: 5 min
- COMANDOS_INSTITUCIONES.txt: 3 min
- Archivar desarrollo_territorial: 1 min
- **TOTAL: 10 minutos**

---

**¿Procedemos con esta limpieza quirúrgica?** 🔬


