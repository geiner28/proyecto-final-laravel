# 📊 Datos de Prueba - Sistema de Citas Médicas

## ✅ Base de Datos Generada Exitosamente

El sistema ha sido poblado con datos de prueba completos para facilitar las pruebas.

### 📈 Resumen de Datos Generados:
- **9 Usuarios** (1 Admin + 5 Pacientes + 3 Médicos)
- **3 Médicos** con especialidades diferentes
- **39 Citas** en diferentes estados y fechas
- **9 Diagnósticos completos** con medicamentos, procedimientos y remisiones

---

## 👥 Credenciales de Acceso

### 🔑 Administrador
- **Email:** `admin@hospital.com`
- **Contraseña:** `admin123`
- **Acceso:** Panel completo de administración

### 🩺 Médicos

#### 1. Dr. Carlos Ramírez - Neumología
- **Email:** `carlos@medicos.com`
- **Contraseña:** `password`
- **Cédulas de pacientes con citas:** 1234567890, 0987654321, 1122334455, etc.

#### 2. Dra. María González - Cardiología
- **Email:** `maria@medicos.com`
- **Contraseña:** `password`
- **Cédulas de pacientes con citas:** 5544332211, 6677889900, etc.

#### 3. Dr. Juan Pérez - Medicina General
- **Email:** `juan@medicos.com`
- **Contraseña:** `password`
- **Cédulas de pacientes con citas:** 9988776655, 3344556677, etc.

---

## 📋 Estados de Citas Generadas

### Citas de Ayer (Completadas con Diagnósticos) ✅
Cada médico tiene **3 citas completadas** de ayer con diagnósticos detallados:
- Incluyen: Diagnóstico, medicamentos, procedimientos, observaciones
- Algunas incluyen remisiones a especialistas
- **Total: 9 diagnósticos** disponibles para consultar

### Citas de Hoy 📅
- **2 citas por médico** (10:00 AM y 3:00 PM)
- Estados: Pendiente o Confirmada
- Listas para ser completadas y agregar diagnósticos

### Citas de Mañana ⏭️
- **2 citas por médico** 
- Estado: Confirmada
- Horarios: 9:00 AM y 11:00 AM

### Citas de la Próxima Semana 📆
- **4 citas por médico** (días 2-5)
- Estados variados: Pendiente y Confirmada

### Citas Canceladas/Rechazadas ❌
- **2 citas por médico** en estos estados
- Para probar filtros y visualización de estados

---

## 👨‍⚕️ Pacientes de Prueba

### Pacientes con Cédulas para Consultar:

1. **Juan Pérez García**
   - Cédula: `1234567890`
   - Email: juan.perez@example.com
   - Teléfono: 555-0100

2. **María Rodríguez**
   - Cédula: `0987654321`
   - Email: maria.rodriguez@example.com
   - Teléfono: 555-0200

3. **Carlos López**
   - Cédula: `1122334455`
   - Email: carlos.lopez@example.com
   - Teléfono: 555-0300

4. **Ana Martínez**
   - Cédula: `5544332211`
   - Email: ana.martinez@example.com
   - Teléfono: 555-0400

5. **Pedro Sánchez**
   - Cédula: `6677889900`
   - Email: pedro.sanchez@example.com
   - Teléfono: 555-0500

6. **Laura Torres**
   - Cédula: `9988776655`
   - Email: laura.torres@example.com
   - Teléfono: 555-0600

7. **Roberto Díaz**
   - Cédula: `3344556677`
   - Email: roberto.diaz@example.com
   - Teléfono: 555-0700

8. **Carmen Ruiz**
   - Cédula: `7766554433`
   - Email: carmen.ruiz@example.com
   - Teléfono: 555-0800

---

## 🧪 Ejemplos de Diagnósticos Generados

Los diagnósticos incluyen casos médicos realistas:

### 1. Infección Respiratoria Aguda (IRA)
- **Medicamentos:** Paracetamol, Loratadina, Amoxicilina
- **Procedimientos:** Reposo, hidratación, inhalaciones
- **Remisión:** Otorrinolaringología (si persiste)

### 2. Hipertensión Arterial
- **Medicamentos:** Losartán, Amlodipino, Ácido Acetilsalicílico
- **Procedimientos:** Control de PA, dieta hiposódica, ejercicio
- **Remisión:** Cardiología para evaluación integral

### 3. Gastritis Aguda
- **Medicamentos:** Omeprazol, Sucralfato, Dimenhidrinato
- **Procedimientos:** Dieta blanda, evitar irritantes
- **Observaciones:** Sin remisión necesaria

### 4. Cefalea Tensional
- **Medicamentos:** Ibuprofeno, Complejo B, Relajante muscular
- **Procedimientos:** Compresas calientes, ejercicios de estiramiento
- **Remisión:** Neurología si persiste

### 5. Lumbalgia Mecánica
- **Medicamentos:** Diclofenaco, Vitamina B12, Gel antiinflamatorio
- **Procedimientos:** Reposo relativo, compresas, fisioterapia
- **Remisión:** Medicina Física y Rehabilitación

---

## 🔍 Cómo Probar el Sistema

### 1. Como Administrador:
```
1. Ir a http://localhost:8000
2. Clic en "Ir al Panel"
3. Login: admin@hospital.com / admin123
4. Explorar:
   - Dashboard con estadísticas
   - Lista de todas las citas (39 citas)
   - Gestión de médicos
   - Lista de diagnósticos (9 diagnósticos)
   - Widget de búsqueda rápida de diagnósticos (ingresa cualquier cédula)
```

### 2. Como Médico:
```
1. Login con cualquier médico (ej: carlos@medicos.com / password)
2. Ver:
   - Dashboard con calendario semanal
   - Citas de hoy (2 citas)
   - Citas pendientes de aceptar/rechazar
   - Historia clínica con diagnósticos previos
3. Completar citas pendientes:
   - Ir a "Citas" → Citas de hoy
   - Clic en "Completar" en una cita confirmada
   - Llenar formulario de diagnóstico
4. Ver diagnósticos:
   - Ir a "Historial Clínico"
   - Ver 3 diagnósticos completados de ayer
   - Clic en "Ver Detalles" para ver diagnóstico completo
   - Botón "Descargar PDF" disponible
```

### 3. Como Paciente (Sin Login):
```
1. Consultar cita por cédula:
   - Ir a http://localhost:8000/consultar-cita
   - Ingresar cédula: 1234567890
   - Ver todas las citas del paciente

2. Consultar diagnóstico:
   - Ir a http://localhost:8000/consultar-diagnostico
   - Ingresar cédula: 1234567890
   - Ver diagnósticos con opción de descargar PDF

3. Agendar nueva cita:
   - Explorar médicos disponibles
   - Ver calendario semanal
   - Agendar cita proporcionando datos
```

---

## 📅 Disponibilidad de Médicos

**Todos los médicos tienen disponibilidad:**
- **Lunes a Viernes**
- **Horarios:**
  - Mañana: 8:00 AM - 12:00 PM
  - Tarde: 2:00 PM - 6:00 PM
- **Duración de citas:** 20 minutos

---

## 🎯 Funcionalidades a Probar

### ✅ Sistema de Citas:
- [x] Agendar citas como paciente
- [x] Ver citas en calendario semanal
- [x] Aceptar/rechazar citas (médico)
- [x] Completar citas (médico)
- [x] Consultar citas por cédula (paciente)

### ✅ Sistema de Diagnósticos:
- [x] Crear diagnóstico al completar cita
- [x] Ver diagnóstico antes de descargar (modal/vista)
- [x] Descargar PDF del diagnóstico
- [x] Búsqueda rápida en dashboard admin
- [x] Consulta pública de diagnósticos por cédula
- [x] Botón "Ver Diagnóstico" en citas completadas

### ✅ Notificaciones por Email:
- [x] Email al crear cita
- [x] Email al aceptar cita
- [x] Email al rechazar cita
- [x] Email al reagendar cita
- [x] Email al cancelar cita

### ✅ Permisos y Roles:
- [x] Admin ve todas las citas
- [x] Médicos solo ven sus citas
- [x] Pacientes consultan por cédula
- [x] Protección de rutas por middleware

---

## 🔄 Regenerar Datos

Si necesitas regenerar los datos de prueba:

```bash
php artisan migrate:fresh --seed
```

Esto borrará todos los datos actuales y creará nuevos datos de prueba.

---

## 📝 Notas Importantes

1. **Todas las contraseñas de médicos y pacientes son:** `password`
2. **La contraseña del admin es:** `admin123`
3. **Las citas se generan dinámicamente basadas en la fecha actual**
4. **Los diagnósticos incluyen información médica realista**
5. **Cada médico tiene su propia especialidad diferente**
6. **Los emails de notificación se envían automáticamente**

---

## 🎨 Características Especiales

### Widget de Búsqueda Rápida (Admin Dashboard)
- Búsqueda instantánea de diagnósticos por cédula
- Diseño morado con gradiente
- Resultados en tiempo real

### Vista Previa de Diagnósticos
- **Nueva funcionalidad:** Ver diagnóstico completo antes de descargar
- Todos los campos organizados en secciones
- Botón prominente "Descargar PDF"
- Disponible para admin y médicos

### Landing Page Pública
- Sección destacada para consultar diagnósticos
- Información clara para pacientes
- Acceso sin necesidad de login

---

¡Disfruta probando el sistema! 🚀
