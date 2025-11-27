# 📧 Configuración de Correos Reales - MediCitas

## ✅ CONFIGURADO PARA ENVIAR CORREOS REALES

---

## 🔧 Configuración Actual en `.env`

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-contraseña-de-aplicacion
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@medicitas.com"
MAIL_FROM_NAME="MediCitas - Sistema de Citas Médicas"
```

---

## 📝 INSTRUCCIONES PARA GMAIL

### Paso 1: Editar el archivo `.env`

Reemplaza estas dos líneas con tu información:
```
MAIL_USERNAME=tu-email@gmail.com        ← TU EMAIL DE GMAIL AQUÍ
MAIL_PASSWORD=tu-contraseña-de-aplicacion  ← CONTRASEÑA DE APLICACIÓN AQUÍ
```

### Paso 2: Obtener Contraseña de Aplicación de Gmail

⚠️ **IMPORTANTE:** No uses tu contraseña normal de Gmail. Debes generar una "Contraseña de Aplicación".

#### Opción A: Si tienes verificación en 2 pasos activada (Recomendado)

1. Ve a tu cuenta de Google: https://myaccount.google.com/
2. Clic en "Seguridad" (menú izquierdo)
3. En "Cómo inicias sesión en Google", busca **"Contraseñas de aplicaciones"**
4. Es posible que te pida verificar tu identidad
5. Selecciona "Correo" y "Otro (nombre personalizado)"
6. Escribe "MediCitas Laravel"
7. Clic en "Generar"
8. Copia la contraseña de 16 caracteres (sin espacios)
9. Pégala en `MAIL_PASSWORD=` en tu archivo `.env`

#### Opción B: Si NO tienes verificación en 2 pasos

1. Ve a: https://myaccount.google.com/security
2. Activa "Verificación en 2 pasos"
3. Sigue los pasos de la Opción A

#### Opción C: Acceso de aplicaciones menos seguras (NO recomendado)

Si no quieres usar verificación en 2 pasos:
1. Ve a: https://myaccount.google.com/lesssecureapps
2. Activa "Permitir aplicaciones menos seguras"
3. Usa tu contraseña normal de Gmail en `MAIL_PASSWORD`

---

## 🎯 Ejemplo Completo de Configuración

```dotenv
# Ejemplo con Gmail real
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=juan.perez@gmail.com
MAIL_PASSWORD=abcd efgh ijkl mnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@medicitas.com"
MAIL_FROM_NAME="MediCitas - Sistema de Citas Médicas"
```

---

## 🧪 Cómo Probar que Funciona

### 1. Limpiar cache (ya hecho):
```bash
php artisan config:clear
```

### 2. Crear una cita de prueba:

1. Abre: http://127.0.0.1:8001/explorar
2. Selecciona cualquier médico
3. Elige una fecha y hora disponible
4. Completa el formulario con **TU EMAIL REAL**:
   ```
   Nombre: Test Paciente
   Email: TU-EMAIL@gmail.com  ← AQUÍ PON TU EMAIL
   Cédula: 1234567890
   Teléfono: 0999999999
   ```
5. Envía el formulario
6. **¡Revisa tu bandeja de entrada!** 📨

### 3. Probar otros emails:

**Confirmar cita:**
1. Login como admin: http://127.0.0.1:8001/login
   - Email: admin@hospital.com
   - Password: admin123
2. Ve a: http://127.0.0.1:8001/admin/appointments
3. Busca la cita que creaste
4. Clic en el nombre de la cita
5. Clic en botón "Aceptar Cita"
6. **¡Revisa tu email! Llegará el correo de confirmación** ✅

**Reagendar cita:**
1. Desde la vista de la cita, clic en "Reagendar"
2. Selecciona nueva fecha/hora
3. Confirma
4. **¡Llegará email con el cambio!** 📅

**Cancelar cita:**
1. Desde la vista de la cita, clic en "Cancelar Cita"
2. Confirma la cancelación
3. **¡Llegará email de cancelación!** ❌

---

## 🔍 Verificar si hay Errores

Si NO llegan los correos, revisa el log:

```bash
tail -f storage/logs/laravel.log
```

**Errores comunes:**

### Error: "Invalid credentials"
```
Solution: Verifica que tu contraseña de aplicación sea correcta
```

### Error: "Could not authenticate"
```
Solution: 
1. Verifica que tengas verificación en 2 pasos activa
2. Genera una nueva contraseña de aplicación
3. NO uses tu contraseña normal de Gmail
```

### Error: "Connection refused"
```
Solution: Verifica tu conexión a internet
```

---

## 🌐 Alternativas a Gmail

### Opción 1: Mailtrap (Para Testing - No envía emails reales)
```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu-usuario-mailtrap
MAIL_PASSWORD=tu-password-mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@medicitas.com"
MAIL_FROM_NAME="MediCitas"
```

Regístrate gratis en: https://mailtrap.io/

### Opción 2: SendGrid (Para Producción - Gratis hasta 100 emails/día)
```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=tu-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@medicitas.com"
MAIL_FROM_NAME="MediCitas"
```

Regístrate en: https://sendgrid.com/

### Opción 3: Mailgun (Para Producción)
```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=postmaster@tu-dominio.mailgun.org
MAIL_PASSWORD=tu-password-mailgun
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@medicitas.com"
MAIL_FROM_NAME="MediCitas"
```

---

## ⚡ Comandos Útiles

```bash
# Limpiar cache de config (ejecutar después de cambiar .env)
php artisan config:clear

# Ver la configuración actual de mail
php artisan tinker
>>> config('mail')

# Probar envío de email desde tinker
php artisan tinker
>>> Mail::raw('Test email', function($msg) { $msg->to('tu-email@gmail.com')->subject('Test'); });
```

---

## 📊 Resumen del Estado

| Componente | Estado |
|------------|--------|
| Configuración SMTP | ✅ Configurado para Gmail |
| Mail Classes | ✅ Todas creadas |
| Email Templates | ✅ Diseño profesional |
| Integración Controllers | ✅ Implementado |
| Cache limpiado | ✅ Listo |
| **Falta por hacer** | ⚠️ **Configurar TU email y contraseña en .env** |

---

## 🎯 ACCIÓN REQUERIDA

### ⚠️ PARA QUE FUNCIONE, DEBES:

1. **Abrir el archivo:** `/Applications/XAMPP/xamppfiles/htdocs/proyecto-final-laravel/.env`

2. **Buscar las líneas:**
   ```
   MAIL_USERNAME=tu-email@gmail.com
   MAIL_PASSWORD=tu-contraseña-de-aplicacion
   ```

3. **Reemplazar con TU información:**
   ```
   MAIL_USERNAME=TU-EMAIL@gmail.com
   MAIL_PASSWORD=TU-CONTRASEÑA-DE-APLICACION-GMAIL
   ```

4. **Guardar el archivo**

5. **Ejecutar:**
   ```bash
   php artisan config:clear
   ```

6. **Probar creando una cita** con tu email real

---

## 📱 Capturas de Pantalla de Cómo Obtener Contraseña de Gmail

### 1. Ve a Google Account → Seguridad
![](https://support.google.com/accounts/answer/185833)

### 2. Busca "Contraseñas de aplicaciones"
En la sección "Cómo inicias sesión en Google"

### 3. Genera nueva contraseña
- Tipo: Correo
- Dispositivo: Otro (nombre personalizado)
- Nombre: "MediCitas Laravel"

### 4. Copia la contraseña de 16 caracteres
Formato: `xxxx xxxx xxxx xxxx`
Úsala sin espacios en el .env

---

**Última actualización:** 27 de Noviembre, 2025  
**Estado:** ⚠️ Configurado - Requiere credenciales de Gmail del usuario
