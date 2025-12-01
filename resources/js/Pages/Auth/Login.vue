<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Iniciar Sesión - MediCitas" />

    <!-- Container Principal -->
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 overflow-hidden relative flex items-center justify-center p-4">
        <!-- Elementos decorativos animados -->
        <div class="absolute top-20 left-10 w-72 h-72 bg-blue-200/20 rounded-full blur-3xl animate-blob"></div>
        <div class="absolute top-40 right-20 w-96 h-96 bg-indigo-200/20 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-20 left-1/3 w-80 h-80 bg-purple-200/20 rounded-full blur-3xl animate-blob animation-delay-4000"></div>
        
        <!-- Grid Pattern Background -->
        <div class="absolute inset-0 bg-grid-gray-100/50 [mask-image:linear-gradient(0deg,white,rgba(255,255,255,0.5))] -z-10"></div>

        <!-- Navbar -->
        <nav class="glass bg-white/90 backdrop-blur-xl shadow-sm fixed top-0 left-0 right-0 z-50 border-b border-white/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <Link href="/" class="flex items-center gap-3 group cursor-pointer">
                        <div class="relative">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg transform group-hover:scale-110 group-hover:rotate-12 transition-all duration-300">
                                <i class="fas fa-heartbeat text-white text-xl group-hover:animate-pulse"></i>
                            </div>
                            <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl blur opacity-30 group-hover:opacity-60 transition-opacity -z-10"></div>
                        </div>
                        <div>
                            <h1 class="font-bold text-2xl bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">MediCitas</h1>
                            <p class="text-xs text-gray-500">Panel Profesional</p>
                        </div>
                    </Link>
                    
                    <Link href="/" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 transition-all duration-300 flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        Volver al Inicio
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Login Card -->
        <div class="w-full max-w-6xl mx-auto grid lg:grid-cols-2 gap-8 items-center mt-20 relative z-10">
            <!-- Columna Izquierda - Información -->
            <div class="hidden lg:block space-y-6 fade-in-left">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 border border-blue-100 rounded-full mb-4">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <span class="text-sm font-medium text-blue-700">Sistema Seguro</span>
                </div>

                <h1 class="text-5xl font-extrabold text-gray-900 leading-tight">
                    Bienvenido a
                    <span class="block mt-2 text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600">
                        MediCitas Pro
                    </span>
                </h1>

                <p class="text-lg text-gray-600 leading-relaxed">
                    Accede a tu panel profesional para gestionar citas, pacientes y optimizar tu práctica médica.
                </p>

                <!-- Features -->
                <div class="space-y-4 pt-6">
                    <div class="flex items-center gap-4 p-4 bg-white/60 backdrop-blur-lg rounded-2xl border border-white/20 shadow-sm hover:shadow-md transition-all">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-calendar-check text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Gestión Inteligente</h3>
                            <p class="text-sm text-gray-600">Administra tus citas de forma eficiente</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-4 bg-white/60 backdrop-blur-lg rounded-2xl border border-white/20 shadow-sm hover:shadow-md transition-all">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Estadísticas en Tiempo Real</h3>
                            <p class="text-sm text-gray-600">Visualiza el rendimiento de tu práctica</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-4 bg-white/60 backdrop-blur-lg rounded-2xl border border-white/20 shadow-sm hover:shadow-md transition-all">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-lock text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Máxima Seguridad</h3>
                            <p class="text-sm text-gray-600">Datos protegidos y encriptados</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha - Formulario de Login -->
            <div class="fade-in-right">
                <div class="glass bg-white/90 backdrop-blur-xl rounded-3xl shadow-2xl p-8 sm:p-10 border border-white/20">
                    <!-- Header del formulario -->
                    <div class="text-center mb-8">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 rounded-3xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-user-md text-white text-3xl"></i>
                        </div>
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Iniciar Sesión</h2>
                        <p class="text-gray-600">Accede a tu panel profesional</p>
                    </div>

                    <!-- Mensaje de estado -->
                    <div v-if="status" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-2xl flex items-center gap-3">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        <span class="text-sm font-medium text-green-800">{{ status }}</span>
                    </div>

                    <!-- Formulario -->
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-envelope text-blue-600 mr-2"></i>
                                Correo Electrónico
                            </label>
                            <div class="relative">
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="w-full px-4 py-4 bg-white/80 border-2 border-gray-200 rounded-2xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 outline-none text-gray-900 placeholder-gray-400"
                                    placeholder="tu@email.com"
                                    required
                                    autofocus
                                    autocomplete="username"
                                />
                                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none">
                                    <i class="fas fa-at text-gray-400"></i>
                                </div>
                            </div>
                            <p v-if="form.errors.email" class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-lock text-blue-600 mr-2"></i>
                                Contraseña
                            </label>
                            <div class="relative">
                                <input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    class="w-full px-4 py-4 bg-white/80 border-2 border-gray-200 rounded-2xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 outline-none text-gray-900 placeholder-gray-400"
                                    placeholder="••••••••"
                                    required
                                    autocomplete="current-password"
                                />
                                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none">
                                    <i class="fas fa-key text-gray-400"></i>
                                </div>
                            </div>
                            <p v-if="form.errors.password" class="mt-2 text-sm text-red-600 flex items-center gap-2">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <!-- Remember & Forgot -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center cursor-pointer group">
                                <input
                                    v-model="form.remember"
                                    type="checkbox"
                                    name="remember"
                                    class="w-5 h-5 text-blue-600 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-blue-500/20 transition-all cursor-pointer"
                                />
                                <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-blue-600 transition-colors">Recordarme</span>
                            </label>

                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors hover:underline"
                            >
                                ¿Olvidaste tu contraseña?
                            </Link>
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="group relative w-full px-6 py-4 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 hover:from-blue-700 hover:via-indigo-700 hover:to-purple-700 text-white rounded-2xl font-bold text-lg transition-all duration-300 transform hover:scale-[1.02] shadow-xl hover:shadow-2xl disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none overflow-hidden"
                        >
                            <span class="relative z-10 flex items-center justify-center gap-3">
                                <i class="fas fa-sign-in-alt group-hover:translate-x-1 transition-transform"></i>
                                <span v-if="!form.processing">Iniciar Sesión</span>
                                <span v-else class="flex items-center gap-2">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    Iniciando sesión...
                                </span>
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-700 via-indigo-700 to-purple-700 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                        </button>
                    </form>

                    <!-- Footer del formulario -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <p class="text-center text-sm text-gray-600">
                            ¿Eres paciente? 
                            <Link href="/" class="font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                                Ir a Agendar Cita
                            </Link>
                        </p>
                    </div>
                </div>

                <!-- Info adicional debajo del formulario -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600 flex items-center justify-center gap-2">
                        <i class="fas fa-shield-alt text-green-600"></i>
                        Conexión segura y encriptada
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes blob {
  0%, 100% {
    transform: translate(0, 0) scale(1);
  }
  33% {
    transform: translate(30px, -50px) scale(1.1);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }
}

@keyframes fade-in-left {
  from {
    opacity: 0;
    transform: translateX(-30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes fade-in-right {
  from {
    opacity: 0;
    transform: translateX(30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}

.fade-in-left {
  animation: fade-in-left 0.8s ease-out;
}

.fade-in-right {
  animation: fade-in-right 0.8s ease-out;
}

.glass {
  backdrop-filter: blur(16px) saturate(180%);
  -webkit-backdrop-filter: blur(16px) saturate(180%);
}

.bg-grid-gray-100\/50 {
  background-image: 
    linear-gradient(to right, rgb(243 244 246 / 0.5) 1px, transparent 1px),
    linear-gradient(to bottom, rgb(243 244 246 / 0.5) 1px, transparent 1px);
  background-size: 50px 50px;
}

/* Asegurar que las animaciones no se ejecuten en dispositivos que prefieren reducir movimiento */
@media (prefers-reduced-motion: reduce) {
  * {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
</style>
