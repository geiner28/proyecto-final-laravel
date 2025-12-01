# 🔍 Sistema de Filtros - Dashboard Médico

## 📋 Resumen Rápido

**IMPORTANTE:** Los filtros son **COMPLETAMENTE INDEPENDIENTES** del calendario semanal.

---

## 🎯 Concepto Clave

### El Dashboard tiene 2 Sistemas Separados:

#### 1. 📅 CALENDARIO SEMANAL (Vista temporal)
- **Propósito:** Ver la distribución de citas en una semana específica
- **Navegación:** ← (semana anterior) | Hoy | → (semana siguiente)
- **Alcance:** Solo muestra 7 días (lunes a domingo)
- **No filtra datos:** Siempre muestra la semana seleccionada completa

#### 2. 🔍 FILTROS DE BÚSQUEDA (Búsqueda global)
- **Propósito:** Encontrar citas específicas en TODA la historia
- **Alcance:** Busca en TODAS las fechas (pasadas, presentes, futuras)
- **Independiente del calendario:** No importa qué semana estés viendo
- **Resultados:** Muestra TODAS las citas que coincidan con los criterios

---

## 🔎 Tipos de Filtros Disponibles

### 1. Búsqueda por Texto (Campo: "Buscar paciente")
**Busca en:**
- Nombre del paciente
- Email del paciente
- Cédula del paciente

**Cómo usar:**
```
1. Escribe en el campo "Buscar paciente"
2. Puede ser cualquier parte del nombre, email o cédula
3. Click en "Buscar"
```

**Ejemplos:**
| Escribes | Encuentra |
|----------|-----------|
| `Juan` | Todas las citas de pacientes llamados Juan (cualquier fecha) |
| `1234` | Todas las citas con cédulas que contengan 1234 |
| `@gmail` | Todas las citas de pacientes con email Gmail |
| `Maria Perez` | Todas las citas de Maria Perez |

### 2. Filtro por Estado (Dropdown: "Estado")
**Estados disponibles:**
- Todos los estados (opción por defecto)
- Pendiente (amarillo)
- Confirmada (verde)
- Completada (azul)
- Rechazada (rojo)
- Cancelada (gris)

**Cómo usar:**
```
1. Selecciona un estado del dropdown
2. Automáticamente filtra al seleccionar
```

**Ejemplo:**
- Seleccionas "Confirmada"
- Resultado: TODAS las citas confirmadas del médico (cualquier fecha)

### 3. Filtro por Fecha (Campo: "Fecha específica")
**Busca citas de una fecha exacta**

**Cómo usar:**
```
1. Click en el campo de fecha
2. Selecciona una fecha del calendario popup
3. Automáticamente filtra al seleccionar
```

**Ejemplo:**
- Seleccionas "15 de noviembre de 2025"
- Resultado: Solo citas del 15 de noviembre

---

## 🔗 Combinar Filtros

Puedes usar **múltiples filtros simultáneamente**:

### Ejemplos de Combinaciones:

#### Ejemplo 1: Búsqueda + Estado
```
Búsqueda: "Juan"
Estado: "Confirmada"
Fecha: (vacío)

Resultado: Todas las citas confirmadas de pacientes llamados Juan
```

#### Ejemplo 2: Cédula + Fecha
```
Búsqueda: "1234567890"
Estado: (vacío)
Fecha: "20 de diciembre de 2025"

Resultado: Citas del paciente con cédula 1234567890 el 20 de diciembre
```

#### Ejemplo 3: Estado + Fecha
```
Búsqueda: (vacío)
Estado: "Pendiente"
Fecha: "1 de diciembre de 2025"

Resultado: Todas las citas pendientes del 1 de diciembre
```

#### Ejemplo 4: Todos los filtros
```
Búsqueda: "Maria"
Estado: "Completada"
Fecha: "15 de noviembre de 2025"

Resultado: Citas completadas de Maria el 15 de noviembre
```

---

## 🎬 Flujos de Uso

### Flujo 1: Buscar todas las citas de un paciente específico

```
Paso 1: Encuentra la cédula del paciente
        (Por ejemplo: 1234567890)

Paso 2: Escríbela en el campo "Buscar paciente"

Paso 3: Click en "Buscar"

✅ RESULTADO:
   - Ve TODAS las citas de ese paciente
   - Incluye citas pasadas, presentes y futuras
   - No importa qué semana estés viendo en el calendario
```

### Flujo 2: Ver todas las citas pendientes

```
Paso 1: Abre el dropdown "Estado"

Paso 2: Selecciona "Pendiente"

✅ RESULTADO:
   - Ve TODAS las citas pendientes del médico
   - De todas las fechas (pasadas y futuras)
   - Útil para saber qué citas necesitan atención
```

### Flujo 3: Verificar citas de una fecha específica

```
Paso 1: Click en el campo "Fecha específica"

Paso 2: Selecciona la fecha en el calendario

✅ RESULTADO:
   - Ve TODAS las citas de esa fecha
   - Todos los estados incluidos
   - Útil para planificar un día específico
```

### Flujo 4: Volver a la vista normal

```
Paso 1: Click en el botón "Limpiar"

✅ RESULTADO:
   - Todos los filtros se resetean
   - Vuelves a la vista principal
   - Ves el calendario semanal y las citas de hoy
```

---

## ❓ Preguntas Frecuentes

### ¿Por qué no veo las citas de un paciente en el calendario?

**R:** El calendario solo muestra la semana actual. Si las citas del paciente son de otras semanas:
1. Usa los filtros para buscar al paciente (verás TODAS sus citas)
2. O navega el calendario a la semana correcta

### ¿Puedo buscar mientras navego el calendario?

**R:** ¡Sí! Son sistemas independientes:
- Los filtros muestran resultados globales (sección separada)
- El calendario sigue mostrando la semana seleccionada
- Ambos son visibles al mismo tiempo

### ¿Cómo busco citas de hace 2 meses?

**R:** Dos opciones:
1. **Usar el filtro de fecha:** Selecciona la fecha específica
2. **Buscar al paciente:** Si recuerdas nombre/cédula, búscalo directamente

### ¿Los filtros afectan las estadísticas?

**R:** No. Las estadísticas siempre muestran:
- Total de citas de hoy
- Total de citas de la semana actual
- Total de citas pendientes/confirmadas/completadas (todas las fechas)
- Total general de citas

### ¿Puedo exportar los resultados filtrados?

**R:** Actualmente no, pero es una mejora futura planificada.

---

## 🎨 Interfaz Visual

### Estados de la Vista:

#### Estado A: Sin Filtros Activos
```
┌─────────────────────────────────────────┐
│ 📊 Estadísticas (6 cards)               │
├─────────────────────────────────────────┤
│ 🔍 Panel de Filtros (vacío)             │
├─────────────────────────────────────────┤
│ 📅 Calendario Semanal                   │
│    (← Lun Mar Mié Jue Vie Sáb Dom →)   │
├─────────────────────────────────────────┤
│ ⭐ Citas de Hoy                         │
│    - Lista de citas del día actual      │
└─────────────────────────────────────────┘
```

#### Estado B: Con Filtros Activos
```
┌─────────────────────────────────────────┐
│ 📊 Estadísticas (6 cards)               │
├─────────────────────────────────────────┤
│ 🔍 Panel de Filtros (activos)           │
│    [Búsqueda] [Estado] [Fecha] [Limpiar]│
├─────────────────────────────────────────┤
│ 📅 Calendario Semanal                   │
│    (← Lun Mar Mié Jue Vie Sáb Dom →)   │
├─────────────────────────────────────────┤
│ 🔎 Resultados de Búsqueda (X encontrados)│
│    - Lista de TODAS las citas filtradas │
│    - Puede ser de cualquier fecha       │
└─────────────────────────────────────────┘
```

---

## 🔧 Casos de Uso Prácticos

### Caso 1: Paciente llama para saber su cita
```
Situación: Paciente llama y dice "Soy Juan Pérez, cédula 1234567890"
          Quiere saber cuándo tiene su cita

Solución:
1. Escribe "1234567890" en búsqueda
2. Click "Buscar"
3. Ves todas sus citas
4. Le informas fecha y hora
```

### Caso 2: Revisar citas del mes pasado
```
Situación: Necesitas revisar las citas de noviembre

Solución:
1. Click en "Fecha específica"
2. Selecciona cualquier día de noviembre
3. Repite para varios días si es necesario
O:
1. Navega el calendario con ← a las semanas de noviembre
```

### Caso 3: Ver qué citas faltan por confirmar
```
Situación: Quieres contactar pacientes con citas pendientes

Solución:
1. Selecciona "Pendiente" en el dropdown Estado
2. Ve TODAS las citas pendientes
3. Llama a los pacientes para confirmar
```

### Caso 4: Preparar tu semana
```
Situación: Es lunes y quieres ver tu semana completa

Solución:
1. No uses filtros (déjalos vacíos)
2. El calendario muestra automáticamente la semana actual
3. Navega con → para ver semanas futuras si es necesario
```

---

## ✅ Checklist de Funcionalidad

Verifica que todo funcione correctamente:

- [ ] Buscar por nombre encuentra todas las citas
- [ ] Buscar por cédula encuentra todas las citas
- [ ] Buscar por email encuentra todas las citas
- [ ] Filtro de estado funciona correctamente
- [ ] Filtro de fecha específica funciona
- [ ] Combinar filtros (nombre + estado) funciona
- [ ] Botón "Limpiar" resetea todos los filtros
- [ ] Calendario navega entre semanas correctamente
- [ ] Con filtros activos, el calendario sigue navegable
- [ ] Resultados de búsqueda muestran contador correcto
- [ ] "Citas de hoy" solo aparece sin filtros
- [ ] "Resultados de búsqueda" solo aparece con filtros
- [ ] Estadísticas siempre muestran totales correctos

---

**Última actualización:** 1 de Diciembre, 2025  
**Estado:** ✅ FILTROS INDEPENDIENTES IMPLEMENTADOS  
**Versión:** 2.1 - Sistema de Filtros Mejorado
