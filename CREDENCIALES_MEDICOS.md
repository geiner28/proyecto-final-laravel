# Credenciales de Acceso - Sistema de Citas Médicas

El sistema soporta 3 roles de usuario: **Admin**, **Médico** y **Paciente**.

## 👨‍💼 Usuario Administrador

- **Email:** `admin@hospital.com`
- **Contraseña:** `admin123`
- **Rol:** Administrador
- **Permisos:** Acceso total al sistema

## 🩺 Usuarios Médicos

Puedes iniciar sesión como médico con cualquiera de estas cuentas:

### 1. Dr. Carlos Ramírez
- **Email:** `carlos@medicos.com`
- **Contraseña:** `password`
- **Especialidad:** Neumología

### 2. Dra. María González
- **Email:** `maria@medicos.com`
- **Contraseña:** `password`
- **Especialidad:** Neumología

### 3. Dr. Juan Pérez
- **Email:** `juan@medicos.com`
- **Contraseña:** `password`
- **Especialidad:** Neumología

## 👤 Usuario Paciente

- **Email:** `paciente@example.com`
- **Contraseña:** `password`
- **Rol:** Paciente

## 🔐 Funcionalidades del Sistema

### Para Administradores:
- Dashboard con estadísticas completas
- Gestión de médicos (CRUD completo)
- Gestión de usuarios y roles
- Ver todas las citas de todos los médicos
- Reportes y análisis
- Control total del sistema

### Para Médicos:
- Ver su calendario semanal con citas y disponibilidad
- Gestionar citas pendientes (aceptar/rechazar)
- Ver resumen de citas próximas
- Administrar su perfil y disponibilidad
- Solo ven sus propias citas

### Para Pacientes (Público):
- Explorar médicos disponibles
- Ver calendario de disponibilidad semanal
- Agendar citas proporcionando:
  - Nombre completo
  - Email
  - **Cédula / Documento de identidad**
  - Teléfono (opcional)
  - Notas (opcional)
- **Consultar sus citas por cédula** (sin necesidad de login)

## 🆕 Nueva Funcionalidad: Consulta de Citas por Cédula

Los pacientes pueden consultar sus citas ingresando su número de cédula en:
**http://localhost:8000/consultar-cita**

Esto les permite ver:
- Estado de la cita (Pendiente, Confirmada, Rechazada, Completada)
- Médico asignado
- Fecha y hora
- Notas adicionales

## 🚀 Acceso

1. Visita: http://localhost:8000
2. Elige tu rol:
   - **Paciente:** Explora disponibilidad y agenda citas
   - **Médico/Admin:** Haz clic en "Ir al Panel" e inicia sesión
3. Usa las credenciales según tu rol

## 📝 Cambios Implementados (Última Actualización)

### Base de Datos:
- ✅ Campo `cedula_paciente` agregado a appointments (requerido, indexado)
- ✅ Campo `telefono_paciente` agregado a appointments (opcional)
- ✅ Campo `role` con valores: 'admin', 'doctor', 'patient'

### Backend:
- ✅ Middlewares `IsAdmin` y `IsDoctor` para protección de rutas
- ✅ Validación de cédula en creación de citas
- ✅ Sistema de consulta pública de citas por cédula
- ✅ Controladores actualizados con filtrado automático por rol

### Frontend:
- ✅ Formulario de reserva actualizado con cédula y teléfono
- ✅ Nueva página de consulta de citas (ConsultarCita.vue)
- ✅ Enlace en navegación principal para consultar citas
- ✅ Mejoras visuales en formularios y feedback

### Seguridad:
- ✅ Roles verificados con middlewares
- ✅ Médicos solo ven sus propias citas
- ✅ Admin tiene acceso completo
- ✅ Consulta de citas protegida (solo con cédula registrada)

## 🔧 Problemas Resueltos

1. **Error 404 después de login:** Ruta de redirección corregida
2. **Citas no aparecían en calendario:** Lógica de filtrado mejorada
3. **Falta de identificación de pacientes:** Sistema de cédula implementado
4. **Consulta de citas:** Ahora los pacientes pueden ver sus citas sin login
