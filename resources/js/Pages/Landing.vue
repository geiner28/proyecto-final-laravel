<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 overflow-hidden">
    <!-- Navbar mejorada con efecto glass -->
    <nav class="glass bg-white/90 backdrop-blur-xl shadow-sm sticky top-0 z-50 border-b border-white/20 transition-all duration-300">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3 group cursor-pointer">
            <div class="relative">
              <div class="w-12 h-12 bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg transform group-hover:scale-110 group-hover:rotate-12 transition-all duration-300">
                <i class="fas fa-heartbeat text-white text-xl group-hover:animate-pulse"></i>
              </div>
              <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl blur opacity-30 group-hover:opacity-60 transition-opacity -z-10"></div>
            </div>
            <div>
              <h1 class="font-bold text-2xl bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">MediCitas</h1>
              <p class="text-xs text-gray-500">Tu Salud, Nuestra Prioridad</p>
            </div>
          </div>
          
          <div class="hidden md:flex items-center gap-8">
            <a href="#servicios" class="relative text-sm font-medium text-gray-700 hover:text-blue-600 transition-all duration-300 group">
              <span>Servicios</span>
              <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300"></span>
            </a>
            <a href="#como-funciona" class="relative text-sm font-medium text-gray-700 hover:text-blue-600 transition-all duration-300 group">
              <span>Cómo Funciona</span>
              <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300"></span>
            </a>
            <a href="#beneficios" class="relative text-sm font-medium text-gray-700 hover:text-blue-600 transition-all duration-300 group">
              <span>Beneficios</span>
              <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300"></span>
            </a>
            <a href="/login" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-full font-medium transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-xl">
              Acceso Médico
            </a>
          </div>

          <!-- Mobile menu button -->
          <button class="md:hidden p-2 rounded-lg bg-white/50 backdrop-blur-sm hover:bg-white/80 transition-all">
            <i class="fas fa-bars text-gray-700"></i>
          </button>
        </div>
      </div>
    </nav>

    <!-- Hero Section Revolucionario con Split Layout -->
    <section class="relative min-h-screen flex items-center py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
      <!-- Elementos decorativos animados -->
      <div class="absolute top-20 left-10 w-72 h-72 bg-blue-200/20 rounded-full blur-3xl animate-blob"></div>
      <div class="absolute top-40 right-20 w-96 h-96 bg-indigo-200/20 rounded-full blur-3xl animate-blob animation-delay-2000"></div>
      <div class="absolute bottom-20 left-1/3 w-80 h-80 bg-purple-200/20 rounded-full blur-3xl animate-blob animation-delay-4000"></div>
      
      <!-- Grid Pattern Background -->
      <div class="absolute inset-0 bg-grid-gray-100/50 [mask-image:linear-gradient(0deg,white,rgba(255,255,255,0.5))] -z-10"></div>
      
      <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 lg:gap-16 items-center relative z-10">
        <!-- Contenido Izquierdo -->
        <div class="text-center lg:text-left space-y-8 fade-in-left">
          <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 border border-blue-100 rounded-full mb-4">
            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
            <span class="text-sm font-medium text-blue-700">Sistema Activo 24/7</span>
          </div>
          
          <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold text-gray-900 leading-tight">
            Tu Cita Médica
            <span class="block mt-2 text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 animate-gradient-x">
              En 3 Clics
            </span>
          </h1>
          
          <p class="text-xl text-gray-600 leading-relaxed max-w-xl">
            Olvídate de las largas esperas telefónicas. Agenda, consulta y gestiona tus citas médicas de forma inteligente y sin complicaciones.
          </p>

          <!-- Stats -->
          <div class="grid grid-cols-3 gap-6 py-6">
            <div class="text-center">
              <div class="text-3xl font-bold text-blue-600 mb-1">24/7</div>
              <div class="text-sm text-gray-600">Disponible</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold text-indigo-600 mb-1">&lt;30s</div>
              <div class="text-sm text-gray-600">Tiempo Reserva</div>
            </div>
            <div class="text-center">
              <div class="text-3xl font-bold text-purple-600 mb-1">100%</div>
              <div class="text-sm text-gray-600">Seguro</div>
            </div>
          </div>

          <!-- CTA Buttons -->
          <div class="flex flex-col sm:flex-row gap-4 pt-4">
            <button 
              @click="goPaciente" 
              class="group relative px-8 py-4 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 hover:from-blue-700 hover:via-indigo-700 hover:to-purple-700 text-white rounded-2xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-xl hover:shadow-2xl overflow-hidden"
            >
              <span class="relative z-10 flex items-center justify-center gap-3">
                <i class="fas fa-calendar-plus group-hover:rotate-12 transition-transform"></i>
                Agendar Cita Ahora
              </span>
              <div class="absolute inset-0 bg-gradient-to-r from-blue-700 via-indigo-700 to-purple-700 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
            </button>
            
            <Link 
              :href="route('public.consultar.form')"
              class="group px-8 py-4 bg-white border-2 border-gray-200 hover:border-blue-300 text-gray-700 hover:text-blue-600 rounded-2xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-xl flex items-center justify-center gap-3"
            >
              <i class="fas fa-search group-hover:scale-110 transition-transform"></i>
              Consultar Mi Cita
            </Link>
          </div>
        </div>

        <!-- Imagen Derecha con Efecto 3D -->
        <div class="relative lg:block fade-in-right">
          <div class="relative rounded-3xl overflow-hidden shadow-2xl transform hover:scale-[1.02] transition-transform duration-500">
            <!-- Placeholder image - reemplaza con tu URL -->
            <img 
              src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=800" 
              alt="Médico profesional" 
              class="w-full h-[500px] object-cover"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-blue-900/50 via-transparent to-transparent"></div>
            
            <!-- Floating Cards -->
            <div class="absolute top-6 right-6 bg-white/95 backdrop-blur-sm rounded-2xl p-4 shadow-xl animate-float">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                  <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div>
                  <div class="text-sm font-bold text-gray-900">Cita Confirmada</div>
                  <div class="text-xs text-gray-600">Dr. Carlos Ramírez</div>
                </div>
              </div>
            </div>
            
            <div class="absolute bottom-6 left-6 bg-white/95 backdrop-blur-sm rounded-2xl p-4 shadow-xl animate-float animation-delay-2000">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                  <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                </div>
                <div>
                  <div class="text-sm font-bold text-gray-900">Próxima Cita</div>
                  <div class="text-xs text-gray-600">Mañana 10:00 AM</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Elementos decorativos 3D -->
          <div class="absolute -top-6 -right-6 w-32 h-32 bg-gradient-to-br from-blue-400 to-indigo-400 rounded-full blur-2xl opacity-40 animate-pulse"></div>
          <div class="absolute -bottom-6 -left-6 w-40 h-40 bg-gradient-to-br from-purple-400 to-pink-400 rounded-full blur-2xl opacity-40 animate-pulse animation-delay-1000"></div>
        </div>
      </div>
    </section>

    <!-- Servicios / Módulos principales -->
    <section id="servicios" class="py-16 px-4 sm:px-6 relative">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16 fade-in-up">
          <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Nuestros Servicios</h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">Descubre cómo podemos ayudarte a gestionar tu salud de manera eficiente</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
          
          <!-- Módulo 1: Agendar Cita -->
          <div class="group glass bg-white/60 backdrop-blur-lg rounded-3xl p-8 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-white/20">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 transform group-hover:scale-110 transition-transform duration-300 shadow-lg">
              <i class="fas fa-calendar-plus text-white text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Agendar Cita</h3>
            <p class="text-gray-600 mb-6 leading-relaxed">
              Explora médicos disponibles, revisa sus horarios y reserva tu cita en minutos con nuestra interfaz intuitiva.
            </p>
            <button 
              @click="goPaciente" 
              class="w-full px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-semibold transition-all duration-300 transform group-hover:scale-105 flex items-center justify-center gap-3 shadow-lg"
            >
              <i class="fas fa-search"></i>
              Explorar Médicos
            </button>
          </div>

          <!-- Módulo 2: Consultar Mi Cita -->
          <div class="group glass bg-white/60 backdrop-blur-lg rounded-3xl p-8 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-white/20">
            <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-6 transform group-hover:scale-110 transition-transform duration-300 shadow-lg">
              <i class="fas fa-search-location text-white text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Consultar Mi Cita</h3>
            <p class="text-gray-600 mb-6 leading-relaxed">
              Ingresa tu cédula y verifica el estado de tus citas médicas sin necesidad de iniciar sesión.
            </p>
            <Link 
              :href="route('public.consultar.form')"
              class="w-full px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white rounded-xl font-semibold transition-all duration-300 transform group-hover:scale-105 flex items-center justify-center gap-3 shadow-lg"
            >
              <i class="fas fa-id-card"></i>
              Consultar por Cédula
            </Link>
          </div>

          <!-- Módulo 3: Panel Profesional -->
          <div class="group glass bg-white/60 backdrop-blur-lg rounded-3xl p-8 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-white/20">
            <div class="w-20 h-20 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl flex items-center justify-center mb-6 transform group-hover:scale-110 transition-transform duration-300 shadow-lg">
              <i class="fas fa-user-md text-white text-3xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Panel Médico/Admin</h3>
            <p class="text-gray-600 mb-6 leading-relaxed">
              Gestiona tus citas, revisa solicitudes pendientes y administra tu calendario semanal de manera eficiente.
            </p>
            <a 
              href="/login" 
              class="w-full px-6 py-4 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white rounded-xl font-semibold transition-all duration-300 transform group-hover:scale-105 flex items-center justify-center gap-3 shadow-lg"
            >
              <i class="fas fa-stethoscope"></i>
              Iniciar Sesión
            </a>
          </div>

        </div>
      </div>
    </section>

    <!-- Cómo funciona -->
    <section id="como-funciona" class="py-20 px-4 sm:px-6 bg-white/30 backdrop-blur-sm relative overflow-hidden">
      <!-- Elementos decorativos -->
      <div class="absolute -top-20 -right-20 w-40 h-40 bg-blue-100/50 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-indigo-100/50 rounded-full blur-3xl"></div>
      
      <div class="max-w-6xl mx-auto relative z-10">
        <div class="text-center mb-16 fade-in-up">
          <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">¿Cómo Funciona?</h2>
          <p class="text-xl text-gray-600">Proceso simple en 4 pasos para agendar tu cita</p>
        </div>
        
        <div class="grid md:grid-cols-4 gap-8">
          <!-- Paso 1 -->
          <div class="text-center group">
            <div class="relative mx-auto mb-6">
              <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto transform group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <span class="text-3xl font-bold text-blue-600">1</span>
              </div>
              <div class="absolute -inset-2 bg-blue-200/30 rounded-full blur-md group-hover:blur-lg transition-all duration-300 -z-10"></div>
            </div>
            <h3 class="font-bold text-xl mb-3 text-gray-800">Elige tu Médico</h3>
            <p class="text-gray-600">Explora nuestros profesionales y selecciona el especialista que necesitas</p>
          </div>

          <!-- Paso 2 -->
          <div class="text-center group">
            <div class="relative mx-auto mb-6">
              <div class="w-24 h-24 bg-indigo-100 rounded-full flex items-center justify-center mx-auto transform group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <span class="text-3xl font-bold text-indigo-600">2</span>
              </div>
              <div class="absolute -inset-2 bg-indigo-200/30 rounded-full blur-md group-hover:blur-lg transition-all duration-300 -z-10"></div>
            </div>
            <h3 class="font-bold text-xl mb-3 text-gray-800">Selecciona Horario</h3>
            <p class="text-gray-600">Revisa la disponibilidad y elige el día y hora que mejor te convenga</p>
          </div>

          <!-- Paso 3 -->
          <div class="text-center group">
            <div class="relative mx-auto mb-6">
              <div class="w-24 h-24 bg-emerald-100 rounded-full flex items-center justify-center mx-auto transform group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <span class="text-3xl font-bold text-emerald-600">3</span>
              </div>
              <div class="absolute -inset-2 bg-emerald-200/30 rounded-full blur-md group-hover:blur-lg transition-all duration-300 -z-10"></div>
            </div>
            <h3 class="font-bold text-xl mb-3 text-gray-800">Completa tus Datos</h3>
            <p class="text-gray-600">Proporciona tu información personal y cédula para confirmar la reserva</p>
          </div>

          <!-- Paso 4 -->
          <div class="text-center group">
            <div class="relative mx-auto mb-6">
              <div class="w-24 h-24 bg-purple-100 rounded-full flex items-center justify-center mx-auto transform group-hover:scale-110 transition-transform duration-300 shadow-lg">
                <span class="text-3xl font-bold text-purple-600">4</span>
              </div>
              <div class="absolute -inset-2 bg-purple-200/30 rounded-full blur-md group-hover:blur-lg transition-all duration-300 -z-10"></div>
            </div>
            <h3 class="font-bold text-xl mb-3 text-gray-800">Confirmación</h3>
            <p class="text-gray-600">Recibe confirmación por email y consulta tu cita cuando quieras</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Features -->
    <section class="py-20 px-4 sm:px-6 relative overflow-hidden">
      <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-b from-white/50 to-transparent"></div>
      
      <div class="max-w-6xl mx-auto">
        <div class="text-center mb-16 fade-in-up">
          <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Características del Sistema</h2>
          <p class="text-xl text-gray-600">Diseñado para ofrecer la mejor experiencia a pacientes y profesionales</p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div class="glass bg-white/50 backdrop-blur-lg rounded-2xl p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-white/20 group">
            <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center mb-4 transform group-hover:scale-110 transition-transform">
              <i class="fas fa-clock text-blue-600 text-2xl"></i>
            </div>
            <h3 class="font-bold text-lg mb-2 text-gray-800">Disponibilidad 24/7</h3>
            <p class="text-sm text-gray-600">Reserva citas en cualquier momento del día</p>
          </div>

          <div class="glass bg-white/50 backdrop-blur-lg rounded-2xl p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-white/20 group">
            <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center mb-4 transform group-hover:scale-110 transition-transform">
              <i class="fas fa-shield-alt text-green-600 text-2xl"></i>
            </div>
            <h3 class="font-bold text-lg mb-2 text-gray-800">Datos Seguros</h3>
            <p class="text-sm text-gray-600">Tu información está protegida y encriptada</p>
          </div>

          <div class="glass bg-white/50 backdrop-blur-lg rounded-2xl p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-white/20 group">
            <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center mb-4 transform group-hover:scale-110 transition-transform">
              <i class="fas fa-envelope text-indigo-600 text-2xl"></i>
            </div>
            <h3 class="font-bold text-lg mb-2 text-gray-800">Notificaciones</h3>
            <p class="text-sm text-gray-600">Recibe confirmaciones por correo electrónico</p>
          </div>

          <div class="glass bg-white/50 backdrop-blur-lg rounded-2xl p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-white/20 group">
            <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center mb-4 transform group-hover:scale-110 transition-transform">
              <i class="fas fa-mobile-alt text-purple-600 text-2xl"></i>
            </div>
            <h3 class="font-bold text-lg mb-2 text-gray-800">Responsive</h3>
            <p class="text-sm text-gray-600">Funciona en cualquier dispositivo móvil</p>
          </div>

          <div class="glass bg-white/50 backdrop-blur-lg rounded-2xl p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-white/20 group">
            <div class="w-14 h-14 bg-teal-100 rounded-2xl flex items-center justify-center mb-4 transform group-hover:scale-110 transition-transform">
              <i class="fas fa-calendar-check text-teal-600 text-2xl"></i>
            </div>
            <h3 class="font-bold text-lg mb-2 text-gray-800">Gestión Eficiente</h3>
            <p class="text-sm text-gray-600">Calendario inteligente sin conflictos</p>
          </div>

          <div class="glass bg-white/50 backdrop-blur-lg rounded-2xl p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-white/20 group">
            <div class="w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center mb-4 transform group-hover:scale-110 transition-transform">
              <i class="fas fa-user-check text-orange-600 text-2xl"></i>
            </div>
            <h3 class="font-bold text-lg mb-2 text-gray-800">Fácil Consulta</h3>
            <p class="text-sm text-gray-600">Verifica tus citas solo con tu cédula</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gradient-to-br from-gray-900 to-gray-800 text-white py-12 px-4 sm:px-6 relative overflow-hidden">
      <!-- Elementos decorativos -->
      <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
      <div class="absolute -top-20 -right-20 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl"></div>
      <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl"></div>
      
      <div class="max-w-7xl mx-auto relative z-10">
        <div class="grid md:grid-cols-3 gap-8 mb-8">
          <div>
            <div class="flex items-center gap-3 mb-4">
              <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center">
                <i class="fas fa-heartbeat text-white"></i>
              </div>
              <h3 class="font-bold text-xl">MediCitas</h3>
            </div>
            <p class="text-gray-400 text-sm leading-relaxed">Sistema integral de gestión de citas médicas para mejorar la atención al paciente y optimizar el trabajo de los profesionales.</p>
          </div>
          
          <div>
            <h4 class="font-semibold mb-4 text-lg">Enlaces Rápidos</h4>
            <ul class="space-y-3 text-sm text-gray-400">
              <li><a href="#servicios" class="hover:text-white transition-all duration-300 hover:translate-x-1 inline-block">Servicios</a></li>
              <li><Link :href="route('public.consultar.form')" class="hover:text-white transition-all duration-300 hover:translate-x-1 inline-block">Consultar Cita</Link></li>
              <li><a href="#como-funciona" class="hover:text-white transition-all duration-300 hover:translate-x-1 inline-block">Cómo Funciona</a></li>
            </ul>
          </div>
          
          <div>
            <h4 class="font-semibold mb-4 text-lg">Contacto</h4>
            <div class="space-y-3 text-sm text-gray-400">
              <p class="flex items-center gap-2">
                <i class="fas fa-envelope text-blue-400"></i>
                contacto@medicitas.com
              </p>
              <p class="flex items-center gap-2">
                <i class="fas fa-phone text-green-400"></i>
                +593 99 999 9999
              </p>
              <p class="flex items-center gap-2">
                <i class="fas fa-map-marker-alt text-red-400"></i>
                Quito, Ecuador
              </p>
            </div>
          </div>
        </div>
        
        <div class="border-t border-gray-800 pt-6 text-center text-sm text-gray-400">
          &copy; {{ new Date().getFullYear() }} MediCitas - Sistema de Citas Médicas. Todos los derechos reservados.
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { router, Link } from '@inertiajs/vue3'

const props = defineProps({
  firstDoctorSlug: String,
})

function goPaciente() {
  router.get('/explorar')
}
</script>

<style scoped>
@keyframes gradient-x {
  0%, 100% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
}

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

@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-20px);
  }
}

@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
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

.animate-gradient-x {
  background-size: 200% 200%;
  animation: gradient-x 3s ease infinite;
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

.animation-delay-1000 {
  animation-delay: 1s;
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

.fade-in-up {
  animation: fade-in-up 0.8s ease-out;
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
</style>

<style scoped>
/* Animaciones personalizadas */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes gradient {
  0% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
  100% {
    background-position: 0% 50%;
  }
}

.fade-in-up {
  animation: fadeInUp 0.8s ease-out;
}

.fade-in-up.delay-300 {
  animation-delay: 0.3s;
  animation-fill-mode: both;
}

.animate-gradient {
  background-size: 200% 200%;
  animation: gradient 6s ease infinite;
}

/* Efectos glassmorphism */
.glass {
  background: rgba(255, 255, 255, 0.25);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
}

/* Mejoras de hover para todos los dispositivos */
@media (min-width: 768px) {
  .group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
  }
  
  .group:hover .group-hover\:scale-105 {
    transform: scale(1.05);
  }
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