# Instrucciones para Administradores - Sistema de Citas Médicas

## 🔐 Acceso al Panel de Administración

**Email:** `admin@hospital.com`  
**Contraseña:** `admin123`

## 👨‍⚕️ Crear Nuevo Médico

### Paso 1: Acceder al Panel
1. Inicia sesión como administrador
2. En el menú, haz clic en **"Médicos"** o ve a `/admin/doctors`

### Paso 2: Crear el Médico
1. Haz clic en el botón **"+ Crear Médico"** (esquina superior derecha)
2. Completa el formulario:

#### Datos Personales
- **Nombre completo:** Nombre del médico (requerido)
- **Especialidad:** Especialidad médica (requerido)

#### Credenciales de Acceso
- **Correo electrónico:** Email único para iniciar sesión (requerido)
- **Contraseña:** Mínimo 8 caracteres (requerido)
- **Confirmar contraseña:** Debe coincidir con la contraseña (requerido)

3. Haz clic en **"Crear médico"**

### Resultado
- ✅ Se crea un **usuario** con rol `doctor` en el sistema
- ✅ Se crea el **perfil del médico** vinculado al usuario
- ✅ El médico podrá iniciar sesión con el email y contraseña proporcionados
- ✅ El médico tendrá acceso a su propio panel con:
  - Calendario de citas
  - Gestión de disponibilidad horaria
  - Aceptar/rechazar citas
  - Ver solo sus propias citas

## ✏️ Editar Médico Existente

### Desde la Lista de Médicos
1. Ve a **Médicos** (`/admin/doctors`)
2. En la tabla verás:
   - **Nombre** del médico
   - **Especialidad**
   - **Email** de acceso
   - **Estado:** 
     - ✓ Con acceso (tiene usuario vinculado)
     - Sin acceso (no tiene usuario)
3. Haz clic en **"Editar"** en la fila del médico

### Formulario de Edición
Puedes modificar:

#### Datos Personales
- **Nombre completo**
- **Especialidad**

#### Credenciales de Acceso
- **Correo electrónico:** Se actualizará en el usuario vinculado
- **Nueva contraseña:** (opcional) Deja en blanco si no deseas cambiarla
- **Confirmar nueva contraseña:** Solo si cambiaste la contraseña

4. Haz clic en **"Actualizar médico"**

## 📋 Lista de Médicos

La tabla muestra todos los médicos con:
- 📊 **Columna "Estado"**
  - Badge verde "✓ Con acceso": El médico tiene credenciales y puede iniciar sesión
  - Badge gris "Sin acceso": Médico sin usuario (médicos antiguos sin credenciales)

## 🔒 Seguridad

### Validaciones Implementadas
- ✅ Email único: No se puede duplicar correo electrónico
- ✅ Contraseña segura: Mínimo 8 caracteres
- ✅ Confirmación de contraseña obligatoria
- ✅ Solo los administradores pueden crear/editar médicos
- ✅ Las contraseñas se almacenan encriptadas (bcrypt)

### Roles del Sistema
- **Admin:** Acceso completo, puede crear médicos con credenciales
- **Doctor:** Solo ve su propia información y citas
- **Patient:** Usuario público sin panel (solo consulta de citas por cédula)

## ⚠️ Notas Importantes

1. **Email único:** El correo electrónico debe ser único en todo el sistema
2. **Contraseña inicial:** Guarda la contraseña inicial y compártela de forma segura con el médico
3. **Cambio de contraseña:** Los médicos pueden cambiar su contraseña desde su perfil
4. **Eliminación:** Si eliminas un médico, también se elimina su usuario asociado

## 🎯 Flujo Completo

```
1. Admin crea médico → 
2. Sistema crea usuario + perfil médico → 
3. Médico recibe credenciales → 
4. Médico inicia sesión → 
5. Médico configura disponibilidad → 
6. Pacientes agendan citas → 
7. Médico acepta/rechaza citas
```

## 📧 Notificaciones

El sistema envía emails automáticos cuando:
- ✅ Se crea una nueva cita (al médico y paciente)
- ✅ El médico acepta una cita (al paciente)
- ✅ El médico rechaza una cita (al paciente)

## 🆘 Solución de Problemas

### "El email ya está en uso"
- Verifica que el correo no esté registrado en otro usuario
- Busca en la lista de médicos si ya existe

### "El médico no puede iniciar sesión"
- Verifica que el email sea correcto
- Verifica que la contraseña tenga al menos 8 caracteres
- Revisa que el médico tenga el badge "✓ Con acceso" en la lista

### "Olvidé la contraseña del médico"
- Edita el médico desde el panel admin
- Ingresa una nueva contraseña en el campo "Nueva contraseña"
- Confirma la nueva contraseña
- Guarda los cambios
