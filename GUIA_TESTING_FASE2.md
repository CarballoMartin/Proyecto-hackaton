# 🧪 GUÍA DE TESTING: Fase 2 - Alertas Ambientales

**Fecha:** 17 de Octubre de 2025  
**Estado:** ✅ Fase 2 Completada - Lista para probar  
**Tiempo de Testing:** 10-15 minutos

---

## 📋 CHECKLIST DE VERIFICACIÓN

### ✅ Pre-requisitos

- [x] Servidor corriendo
- [x] Base de datos con datos
- [x] Migraciones ejecutadas
- [x] 6 alertas creadas en BD
- [x] Caché limpiada

---

## 🧪 TEST 1: Verificar Backend Funciona

### Comando 1: Ver alertas en BD

```bash
php artisan tinker --execute="echo App\Models\AlertaAmbiental::count() . ' alertas en BD';"
```

**Resultado esperado:**
```
6 alertas en BD
```

✅ Si ves esto, el backend funciona

---

### Comando 2: Ejecutar detección de alertas

```bash
php artisan alertas:detectar
```

**Resultado esperado:**
```
🚨 Detectando alertas ambientales...

📊 Resultados:
   • Unidades analizadas: 86
   • Alertas creadas: X (puede ser 0 si ya existen)
   • Alertas desactivadas: X

✅ Proceso completado en X segundos
```

✅ Si ves esto, el comando funciona

---

## 🌐 TEST 2: Verificar Frontend en Navegador

### Paso 1: Abrir el Sistema

```
URL: http://localhost:8000
```

**Si no está corriendo:**
```bash
php artisan serve
# En otra terminal:
npm run dev
```

---

### Paso 2: Login como Productor

**Credenciales de prueba:**
```
Email: productor@test.com
Password: password
```

O cualquier usuario con rol "productor"

---

### Paso 3: Verificar Campana en Header

**Ubicación:** Arriba a la derecha del dashboard

**Deberías ver:**
```
🌍 [Botón Ecoganadería]
🔔 (6) [Campana con contador rojo] ← ESTO ES NUEVO ⭐
🔔 [Notificaciones]
👤 [Usuario]
```

**Verificaciones:**
- ✅ La campana tiene un ícono de campana SVG
- ✅ Hay un contador rojo con número (6)
- ✅ El contador tiene animación (pulse)
- ✅ Al pasar el mouse cambia de color

---

### Paso 4: Click en la Campana

**Al hacer click deberías ver:**

1. **Dropdown elegante** que se abre suavemente
2. **Header** con "🚨 Alertas Ambientales"
3. **Lista de alertas** con:
   - Emoji grande (🔴, ⛈️, 🌡️, ❄️)
   - Título en negrita
   - Badge de "Nuevo" si no está leída
   - Mensaje descriptivo
   - Nombre del campo (📍)
   - Datos de contexto (temperatura, lluvia, etc.)
   - Fecha relativa ("hace 3 horas")
   - Botón ✓ para marcar como leída
4. **Recomendaciones expandibles** (💡 Ver recomendaciones)
5. **Footer** con contador total

**Verificaciones:**
- ✅ Se abre el dropdown
- ✅ Ves las alertas
- ✅ Cada alerta tiene diferente color según nivel
- ✅ Puedes expandir recomendaciones

---

### Paso 5: Marcar como Leída

**Acción:** Click en el botón ✓ de una alerta

**Resultado esperado:**
- ✅ El badge "Nuevo" desaparece
- ✅ El fondo cambia de azul a blanco
- ✅ El contador en la campana disminuye
- ✅ El botón ✓ desaparece

---

### Paso 6: Verificar Panel en Dashboard

**Ubicación:** Columna derecha, después del widget de clima

**Deberías ver:**

```
┌─────────────────────────────────┐
│ 🌦️ Clima Actual                │
│ 19°C                            │
│ Parcialmente nublado            │
└─────────────────────────────────┘

┌─────────────────────────────────┐
│ 🚨 Alertas Activas        (6)  │ ← ESTO ES NUEVO ⭐
│                                  │
│ [Card de alerta 1]              │
│ [Card de alerta 2]              │
│ [Card de alerta 3]              │
│                                  │
│ [Ver todas las alertas (6)]     │
└─────────────────────────────────┘

┌─────────────────────────────────┐
│ Noticias del Sector             │
└─────────────────────────────────┘
```

**Verificaciones:**
- ✅ El panel está visible
- ✅ Muestra máximo 3 alertas
- ✅ Cada alerta tiene colores diferentes
- ✅ Hay botón "Ver todas" si hay más de 3

---

### Paso 7: Expandir Recomendaciones

**Acción:** Click en "💡 Ver recomendaciones"

**Resultado esperado:**
- ✅ Se expande una lista
- ✅ Muestra 3-4 recomendaciones
- ✅ Con bullets y formato bonito

---

## 🎨 TEST 3: Verificar Diseño Responsive

### Desktop (>1024px)
- ✅ Panel a la derecha
- ✅ Campana visible en header
- ✅ Todo legible

### Tablet (768-1024px)
- ✅ Panel se ajusta
- ✅ Dropdown funciona

### Móvil (<768px)
- ✅ Panel ocupa todo el ancho
- ✅ Campana accesible

**Cómo probar:** Resize del navegador o F12 → Responsive

---

## 🔍 TEST 4: Verificar Seguridad

### Test de Permisos

1. Login como productor1
2. Ver sus alertas (deben ser solo de sus UPs)
3. Marcar como leída
4. ✅ Solo debería poder marcar sus propias alertas

**Logging de seguridad:**
```bash
tail -f storage/logs/laravel.log
```

Si alguien intenta marcar alerta ajena, verás warning

---

## 🐛 TEST 5: Casos Edge

### Sin Alertas

**Simular:**
```bash
php artisan tinker
>>> App\Models\AlertaAmbiental::query()->update(['activa' => false])
>>> exit
```

**Refresh navegador**

**Deberías ver:**
- Campana sin contador
- Al abrir: "No hay alertas activas" con ícono ✓
- Panel no aparece (solo si hay alertas)

**Revertir:**
```bash
php artisan alertas:detectar
```

---

### Con Muchas Alertas

**Crear más alertas:**
```bash
php artisan db:seed --class=AlertasAmbientalesDemoSeeder
```

**Deberías ver:**
- Contador actualizado
- Panel muestra máximo 3
- Botón "Ver todas" aparece

---

## ✅ CHECKLIST FINAL DE TESTING

```
Backend:
├─ [✓] Comando ejecuta sin errores
├─ [✓] Alertas se crean en BD
├─ [✓] Alertas se desactivan cuando mejora
├─ [✓] No crea duplicados
└─ [✓] Logging funciona

Frontend - Campana:
├─ [✓] Campana visible en header
├─ [✓] Contador muestra número correcto
├─ [✓] Contador tiene animación pulse
├─ [✓] Dropdown se abre al click
├─ [✓] Alertas se muestran correctamente
├─ [✓] Colores según nivel de gravedad
├─ [✓] Emojis correctos por tipo
├─ [✓] Marcar como leída funciona
├─ [✓] Contador se actualiza
└─ [✓] Recomendaciones expandibles

Frontend - Panel:
├─ [✓] Panel visible en dashboard
├─ [✓] Muestra máximo 3 alertas
├─ [✓] Diseño por niveles (colores)
├─ [✓] Datos de contexto visibles
├─ [✓] Botón "Ver todas" si > 3
└─ [✓] Recomendaciones expandibles

Responsive:
├─ [✓] Desktop (>1024px)
├─ [✓] Tablet (768-1024px)
└─ [✓] Móvil (<768px)

Seguridad:
├─ [✓] Solo ve sus propias alertas
├─ [✓] Solo marca sus propias alertas
└─ [✓] Logging de intentos inválidos
```

---

## 🎯 RESULTADO ESPERADO

### En el Navegador Deberías Ver:

```
Dashboard del Productor:
┌────────────────────────────────────────────────────────┐
│  Header:                                               │
│  🌍  🔔(6)  🔔  👤                                     │
└────────────────────────────────────────────────────────┘

Columna Derecha:
┌─────────────────────┐
│ 🌦️ Clima Actual    │
│ 19°C                │
│ Parcialmente nublado│
│ 📍 Campo - Posadas  │
│ Pronóstico 7 días... │
└─────────────────────┘

┌─────────────────────┐
│ 🚨 Alertas (6)      │ ← NUEVO ⭐
│                     │
│ 🔴 Sequía           │
│ 📍 Campo La Esperanza│
│ Temp: 35°C          │
│ 15 días sin lluvia  │
│ [Ver recomendaciones]│
│                     │
│ ⛈️ Tormenta         │
│ 📍 Campo San José   │
│ 65mm esperados      │
│                     │
│ [Ver todas (6)]     │
└─────────────────────┘

Columna Central:
[Gráficos y stats...]
```

---

## 🚀 COMANDOS PARA PROBAR

### 1. Limpiar todo y preparar

```bash
php artisan view:clear
php artisan config:clear
php artisan route:clear
```

### 2. Asegurar que hay alertas

```bash
php artisan alertas:detectar --forzar
```

### 3. Ver en consola

```bash
php artisan tinker
>>> $alertas = App\Models\AlertaAmbiental::with('unidadProductiva')->get()
>>> foreach($alertas as $a) {
...     echo $a->obtenerEmoji() . ' ' . $a->tipo . ' - ' . $a->unidadProductiva->nombre . PHP_EOL;
... }
>>> exit
```

### 4. Abrir navegador

```
http://localhost:8000/productor/panel
```

---

## 📊 SI ALGO NO FUNCIONA

### Problema: No veo la campana

**Solución:**
```bash
php artisan view:clear
# Ctrl + F5 en navegador (recarga forzada)
```

---

### Problema: Contador en 0

**Solución:**
```bash
# Verificar que hay alertas
php artisan tinker --execute="echo App\Models\AlertaAmbiental::activas()->noLeidas()->count();"

# Si es 0, crear más alertas
php artisan db:seed --class=AlertasAmbientalesDemoSeeder
```

---

### Problema: Panel no aparece

**Verificar:**
1. Que haya alertas activas
2. Que estés en la ruta correcta: `/productor/panel`
3. Limpiar caché: `php artisan view:clear`

---

### Problema: Error al marcar como leída

**Ver logs:**
```bash
tail -f storage/logs/laravel.log
```

---

## ✅ PRUEBA COMPLETA PASO A PASO

### Testing Completo (15 minutos)

**1. Verificar Backend (3 min)**
```bash
✓ php artisan alertas:detectar
✓ Verificar logs
✓ Contar alertas en BD
```

**2. Abrir Sistema (2 min)**
```bash
✓ http://localhost:8000
✓ Login como productor
✓ Ir a dashboard
```

**3. Verificar Campana (3 min)**
```bash
✓ Ver campana 🔔 con contador
✓ Click en campana
✓ Ver dropdown con alertas
✓ Marcar una como leída
✓ Ver contador disminuir
```

**4. Verificar Panel (3 min)**
```bash
✓ Scroll en dashboard
✓ Ver panel de alertas
✓ Ver 3 alertas destacadas
✓ Expandir recomendaciones
✓ Click "Ver todas"
```

**5. Casos Edge (4 min)**
```bash
✓ Marcar todas como leídas
✓ Ver estado vacío
✓ Crear nuevas alertas
✓ Verificar actualización
```

---

## 🎉 CRITERIOS DE ÉXITO

### Fase 2 está 100% completa si:

- ✅ Comando `alertas:detectar` funciona
- ✅ Se crean alertas en BD
- ✅ Campana visible con contador
- ✅ Dropdown se abre y muestra alertas
- ✅ Panel visible en dashboard
- ✅ Marcar como leída funciona
- ✅ Colores correctos por nivel
- ✅ Recomendaciones se expanden
- ✅ No hay errores en consola

---

## 📸 CAPTURAS SUGERIDAS

Para tu presentación, captura:

1. **Campana con contador** (header)
2. **Dropdown abierto** con alertas
3. **Panel en dashboard** con 3 alertas
4. **Recomendaciones expandidas**
5. **Estado vacío** (sin alertas)
6. **Consola** mostrando `php artisan alertas:detectar`

---

## 🚀 PRÓXIMOS PASOS DESPUÉS DE TESTING

### Si Todo Funciona ✅

```bash
# 1. Crear checkpoint actualizado
# 2. Opcional: Merge a rama Fase 1
git checkout feat/modulo-ambiental-fase1
git merge feat/modulo-ambiental-fase2

# 3. O continuar con Fase 3 (NDVI Satelital)
```

### Si Hay Problemas ⚠️

```bash
# 1. Anotar qué no funciona
# 2. Ver logs: storage/logs/laravel.log
# 3. Hacer debug
# 4. Corregir
# 5. Re-testear
```

---

## 📝 REPORTE DE TESTING

Completa esto después de probar:

```
Testing de Fase 2 - Alertas Ambientales
Fecha: _______________
Probador: Martin

Backend:
[ ] Comando funciona
[ ] Alertas en BD
[ ] Schedule configurado

Frontend - Campana:
[ ] Visible en header
[ ] Contador correcto
[ ] Dropdown funciona
[ ] Marcar leída funciona

Frontend - Panel:
[ ] Visible en dashboard
[ ] Muestra alertas
[ ] Diseño correcto
[ ] Recomendaciones funcionan

Problemas Encontrados:
_______________________
_______________________

Tiempo de testing: _______ minutos

Estado Final: [ ] ✅ TODO FUNCIONA  [ ] ⚠️ HAY PROBLEMAS
```

---

**¡Ahora sí! Vamos a probar todo funcionando! 🧪🚀**

