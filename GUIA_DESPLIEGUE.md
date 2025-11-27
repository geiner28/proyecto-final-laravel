# 🚀 GUÍA DE DESPLIEGUE - MediCitas

## 🎯 OPCIÓN RECOMENDADA: Railway.app (10 minutos)

Railway es la forma MÁS FÁCIL de desplegar tu proyecto Laravel con PostgreSQL.

---

## ✅ VENTAJAS DE RAILWAY

- 🆓 **Gratis:** $5 USD/mes de crédito incluido (suficiente para proyectos pequeños)
- ⚡ **Rápido:** Deploy en minutos
- 🔧 **Cero Config:** Detecta Laravel automáticamente
- 🗄️ **PostgreSQL:** Incluido sin costo adicional
- 🔒 **SSL:** Certificado HTTPS gratis
- 🌐 **Dominio:** URL automática: `tu-proyecto.up.railway.app`
- 🔄 **Auto-deploy:** Cada push a GitHub despliega automáticamente

---

## 📝 PASO A PASO: DESPLIEGUE EN RAILWAY

### Paso 1: Preparar el repositorio Git

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/proyecto-final-laravel

# Inicializar git (si no lo tienes)
git init

# Agregar todos los archivos
git add .

# Hacer commit
git commit -m "Proyecto final Laravel - Sistema de Citas Médicas"

# Crear repositorio en GitHub y subir
git remote add origin https://github.com/TU-USUARIO/medicitas.git
git branch -M main
git push -u origin main
```

---

### Paso 2: Crear cuenta en Railway

1. Ve a: **https://railway.app**
2. Click en **"Login"**
3. Selecciona **"Login with GitHub"**
4. Autoriza Railway

---

### Paso 3: Crear nuevo proyecto

1. Click en **"New Project"**
2. Selecciona **"Deploy from GitHub repo"**
3. Autoriza acceso a tu repositorio
4. Selecciona el repositorio `medicitas`
5. Railway detectará Laravel automáticamente

---

### Paso 4: Agregar base de datos PostgreSQL

1. En tu proyecto, click **"+ New"**
2. Selecciona **"Database"**
3. Elige **"Add PostgreSQL"**
4. Railway creará la BD automáticamente

---

### Paso 5: Configurar variables de entorno

1. Click en tu servicio web (Laravel)
2. Ve a la pestaña **"Variables"**
3. Click **"+ New Variable"** y agrega cada una:

```env
APP_NAME=MediCitas
APP_ENV=production
APP_KEY=base64:Z393+L556Gr9AeP/Ydsl5RRzYk9AH4j39tWAYsLGCJ0=
APP_DEBUG=false
APP_URL=https://tu-proyecto.up.railway.app

DB_CONNECTION=pgsql
DB_HOST=${{Postgres.PGHOST}}
DB_PORT=${{Postgres.PGPORT}}
DB_DATABASE=${{Postgres.PGDATABASE}}
DB_USERNAME=${{Postgres.PGUSER}}
DB_PASSWORD=${{Postgres.PGPASSWORD}}

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=martinezstiven815@gmail.com
MAIL_PASSWORD="wzcl ztlw aify cgda"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@medicitas.com
MAIL_FROM_NAME="MediCitas"

APPOINTMENT_DURATION_MINUTES=20
```

**Nota:** Las variables `${{Postgres.XXX}}` se llenan automáticamente con los datos de tu BD PostgreSQL.

---

### Paso 6: Generar nueva APP_KEY

Railway necesita una APP_KEY válida:

```bash
# En tu terminal local:
php artisan key:generate --show
```

Copia la salida y úsala en `APP_KEY` en Railway.

---

### Paso 7: Deploy y migración

1. Railway desplegará automáticamente
2. Una vez desplegado, ve a **"Settings"** → **"Deploy"**
3. En **"Custom Start Command"** pon:
   ```
   php artisan migrate --force --seed && php artisan config:cache && php artisan serve --host=0.0.0.0 --port=$PORT
   ```
4. O simplemente ejecuta manualmente desde la terminal de Railway:
   ```bash
   php artisan migrate --force --seed
   ```

---

### Paso 8: Obtener tu URL

1. Ve a **"Settings"** → **"Networking"**
2. Click en **"Generate Domain"**
3. Railway te dará una URL como: `medicitas-production.up.railway.app`
4. Copia esta URL y actualiza `APP_URL` en las variables de entorno

---

## 🎉 ¡LISTO! Tu proyecto está en producción

Accede a: `https://tu-proyecto.up.railway.app`

### Usuarios de prueba:
- **Admin:** admin@hospital.com / admin123
- **Doctor:** carlos@medicos.com / password
- **Paciente:** Crear desde formulario público

---

## 🔧 COMANDOS ÚTILES EN RAILWAY

Railway tiene una terminal integrada:

```bash
# Limpiar cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Ver migraciones
php artisan migrate:status

# Crear nuevo usuario admin
php artisan tinker
>>> User::create(['name'=>'Admin', 'email'=>'admin@test.com', 'password'=>bcrypt('pass123'), 'role'=>'admin']);

# Ver logs
tail -f storage/logs/laravel.log
```

---

## 🐛 SOLUCIÓN DE PROBLEMAS COMUNES

### Error 500 - Internal Server Error
```bash
# En Railway terminal:
php artisan config:clear
php artisan cache:clear
chmod -R 775 storage bootstrap/cache
```

### Error de base de datos
- Verifica que las variables `${{Postgres.XXX}}` estén correctamente referenciadas
- Ejecuta `php artisan migrate:fresh --seed --force`

### CSS/JS no carga
```bash
# En local, antes de hacer push:
npm run build
git add public/build
git commit -m "Build assets"
git push
```

### Emails no llegan
- Verifica `MAIL_USERNAME` y `MAIL_PASSWORD` en variables de entorno
- Asegúrate que la contraseña esté entre comillas si tiene espacios

---

## 💰 COSTOS ESTIMADOS

### Railway Plan Hobby (Gratis)
- **$5 USD/mes de crédito incluido** 🎁
- Suficiente para:
  - 500 horas de ejecución/mes
  - Base de datos PostgreSQL
  - SSL gratuito
  - Dominio incluido

**Para proyectos de clase/prueba: COMPLETAMENTE GRATIS** ✅

### Si necesitas más:
- **Plan Pro:** $20 USD/mes (recursos ilimitados)

---

## 🌍 ALTERNATIVAS A RAILWAY

### 1. **Heroku** (Similar a Railway)
- Plan gratuito limitado
- PostgreSQL gratis
- Más conocido pero más lento

### 2. **Vercel + Neon** (Serverless)
- Frontend gratis ilimitado
- PostgreSQL serverless gratis
- Mejor para apps con poco tráfico

### 3. **DigitalOcean App Platform**
- $5 USD/mes
- Más control
- Requiere más configuración

### 4. **VPS Tradicional** (Avanzado)
- DigitalOcean Droplet: $6/mes
- AWS EC2: Variable
- Requiere configurar servidor completo

---

## 📊 COMPARACIÓN RÁPIDA

| Plataforma | Dificultad | Tiempo | Costo/mes | PostgreSQL |
|------------|------------|--------|-----------|------------|
| **Railway** | ⭐ Muy Fácil | 10 min | Gratis* | ✅ Incluido |
| Heroku | ⭐⭐ Fácil | 15 min | Gratis* | ✅ Addon |
| Vercel + Neon | ⭐⭐ Media | 20 min | Gratis | ✅ Separado |
| DigitalOcean | ⭐⭐⭐ Media | 30 min | $5 | Opcional |
| VPS Ubuntu | ⭐⭐⭐⭐ Difícil | 1-2 hrs | $6+ | Manual |

*Con limitaciones de uso

---

## ✅ CHECKLIST PRE-DEPLOY

Antes de desplegar, asegúrate de:

- [ ] Archivo `Procfile` creado
- [ ] `composer.json` actualizado
- [ ] `.gitignore` configurado (no subir `.env`)
- [ ] Assets compilados (`npm run build`)
- [ ] Código en GitHub
- [ ] Contraseña de Gmail válida
- [ ] `APP_DEBUG=false` en producción
- [ ] `APP_ENV=production`

---

## 🎯 RESUMEN

### Para tu proyecto de clase:

**✅ USA RAILWAY.APP**

**Razones:**
1. Gratis para proyectos de clase
2. Configuración en 10 minutos
3. PostgreSQL incluido
4. Auto-deploy desde GitHub
5. SSL y dominio gratis
6. No requiere tarjeta de crédito

**Alternativa si Railway no funciona:** Heroku

---

## 📞 SOPORTE

Si tienes problemas:
1. Revisa los logs en Railway Dashboard
2. Verifica variables de entorno
3. Ejecuta `php artisan config:clear` en Railway terminal
4. Revisa la documentación: https://docs.railway.app/guides/laravel

---

**Última actualización:** 27 de Noviembre, 2025  
**Dificultad de despliegue:** ⭐ MUY FÁCIL (10 minutos con Railway)
