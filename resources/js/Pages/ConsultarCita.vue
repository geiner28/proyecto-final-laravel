<script setup>
import { ref, computed } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import HomeButton from '@/Components/HomeButton.vue';
import PatientCalendar from '@/Components/PatientCalendar.vue';

const props = defineProps({
    appointments: {
        type: Array,
        default: () => []
    },
    cedula: {
        type: String,
        default: ''
    }
});

const form = useForm({
    cedula: props.cedula || ''
});

const searchAppointments = () => {
    form.post(route('public.consultar'), {
        preserveState: true,
        preserveScroll: true
    });
};

// Estado para expandir detalles
const showingDetails = ref(null);

// Estado para cancelar
const cancelModal = ref({
    show: false,
    appointment: null,
    loading: false
});

// Estado para reagendar
const rescheduleModal = ref({
    show: false,
    appointment: null,
    availability: [],
    duration: 30,
    newSlot: null,
    loading: false
});

// Funciones de fecha
const formatDate = (datetime) => {
    const date = new Date(datetime);
    return date.toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatTime = (datetime) => {
    const date = new Date(datetime);
    return date.toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit'
    });
};

const isPast = (datetime) => {
    return new Date(datetime) < new Date();
};

const isToday = (datetime) => {
    const today = new Date();
    const date = new Date(datetime);
    return today.toDateString() === date.toDateString();
};

// Validaciones
const canCancel = (appointment) => {
    return (appointment.status === 'pendiente' || appointment.status === 'confirmada') && !isPast(appointment.start_at);
};

const canReschedule = (appointment) => {
    return (appointment.status === 'pendiente' || appointment.status === 'confirmada') && !isPast(appointment.start_at);
};

// Toggle detalles
const toggleDetails = (id) => {
    showingDetails.value = showingDetails.value === id ? null : id;
};

// Status helpers
const getStatusText = (status) => {
    const map = {
        'pendiente': 'Pendiente',
        'confirmada': 'Confirmada',
        'rechazada': 'Rechazada',
        'completada': 'Completada',
        'cancelada': 'Cancelada'
    };
    return map[status] || status;
};

const getStatusClass = (status) => {
    const map = {
        'pendiente': 'bg-yellow-100 text-yellow-800 border-yellow-300',
        'confirmada': 'bg-green-100 text-green-800 border-green-300',
        'rechazada': 'bg-red-100 text-red-800 border-red-300',
        'completada': 'bg-blue-100 text-blue-800 border-blue-300',
        'cancelada': 'bg-gray-100 text-gray-800 border-gray-300'
    };
    return map[status] || 'bg-gray-100 text-gray-800';
};

const getStatusIcon = (status) => {
    const map = {
        'pendiente': 'fa-clock',
        'confirmada': 'fa-check-circle',
        'rechazada': 'fa-times-circle',
        'completada': 'fa-check-double',
        'cancelada': 'fa-ban'
    };
    return map[status] || 'fa-question-circle';
};

// Cancelar cita
const openCancelModal = (appointment) => {
    cancelModal.value = {
        show: true,
        appointment: appointment,
        loading: false
    };
};

const closeCancelModal = () => {
    cancelModal.value = {
        show: false,
        appointment: null,
        loading: false
    };
};

const confirmCancel = () => {
    cancelModal.value.loading = true;
    router.post(route('public.appointments.cancel', cancelModal.value.appointment.slug), {}, {
        preserveState: true,
        onSuccess: () => {
            closeCancelModal();
        },
        onError: () => {
            cancelModal.value.loading = false;
        }
    });
};

// Reagendar cita
const openRescheduleModal = async (appointment) => {
    rescheduleModal.value.loading = true;
    rescheduleModal.value.show = true;
    rescheduleModal.value.appointment = appointment;
    
    try {
        // Obtener disponibilidad del doctor via fetch
        const response = await fetch(route('public.appointments.reschedule-form', appointment.slug));
        const data = await response.json();
        
        if (data.success) {
            rescheduleModal.value.availability = data.availability || [];
            rescheduleModal.value.duration = data.duration || 30;
        }
    } catch (error) {
        console.error('Error al cargar disponibilidad:', error);
        alert('Error al cargar la disponibilidad del doctor');
        closeRescheduleModal();
    } finally {
        rescheduleModal.value.loading = false;
    }
};

const closeRescheduleModal = () => {
    rescheduleModal.value = {
        show: false,
        appointment: null,
        availability: [],
        duration: 30,
        newSlot: null,
        loading: false
    };
};

const confirmReschedule = () => {
    if (!rescheduleModal.value.newSlot) return;
    
    rescheduleModal.value.loading = true;
    router.post(route('public.appointments.reschedule', rescheduleModal.value.appointment.slug), {
        new_start: rescheduleModal.value.newSlot
    }, {
        preserveState: true,
        onSuccess: () => {
            closeRescheduleModal();
        },
        onError: () => {
            rescheduleModal.value.loading = false;
        }
    });
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 relative overflow-x-hidden">
        <!-- Blobs animados -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="blob blob-1"></div>
            <div class="blob blob-2"></div>
            <div class="blob blob-3"></div>
        </div>

        <!-- Contenido principal -->
        <div class="relative z-10 py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="text-center mb-8 animate-fade-in-up">
                    <HomeButton />
                    <h1 class="text-4xl sm:text-5xl font-extrabold bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent mb-4">
                        Consultar mi cita
                    </h1>
                    <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                        Ingresa tu número de cédula para ver el estado de tus citas médicas
                    </p>
                </div>

                <!-- Formulario de búsqueda -->
                <div class="glass rounded-2xl p-6 sm:p-8 mb-8 max-w-2xl mx-auto animate-fade-in-up" style="animation-delay: 0.1s">
                    <form @submit.prevent="searchAppointments" class="space-y-4">
                        <div>
                            <label for="cedula" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-id-card mr-2 text-blue-600"></i>
                                Número de cédula
                            </label>
                            <input
                                type="text"
                                id="cedula"
                                v-model="form.cedula"
                                required
                                placeholder="Ej: 1234567890"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                            />
                            <div v-if="form.errors.cedula" class="text-red-600 text-sm mt-1">
                                {{ form.errors.cedula }}
                            </div>
                        </div>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <i v-if="!form.processing" class="fas fa-search mr-2"></i>
                            <i v-else class="fas fa-spinner fa-spin mr-2"></i>
                            {{ form.processing ? 'Buscando...' : 'Buscar mis citas' }}
                        </button>
                    </form>
                </div>

                <!-- Lista de citas -->
                <div v-if="appointments && appointments.length > 0" class="space-y-6">
                    <div
                        v-for="appointment in appointments"
                        :key="appointment.id"
                        class="glass rounded-2xl p-6 sm:p-8 hover:shadow-2xl transition-all duration-300 animate-fade-in-up"
                    >
                        <!-- Header de la cita -->
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                            <!-- Info principal -->
                            <div class="flex-1 space-y-4">
                                <!-- Status y badges -->
                                <div class="flex flex-wrap items-center gap-3">
                                    <span :class="getStatusClass(appointment.status)" class="px-4 py-2 rounded-full text-sm font-semibold border-2 flex items-center gap-2">
                                        <i :class="['fas', getStatusIcon(appointment.status)]"></i>
                                        {{ getStatusText(appointment.status) }}
                                    </span>
                                    <span v-if="isToday(appointment.start_at)" class="px-4 py-2 rounded-full text-sm font-semibold bg-amber-100 text-amber-800 border-2 border-amber-300 animate-pulse">
                                        <i class="fas fa-star mr-1"></i>
                                        Hoy
                                    </span>
                                    <span v-if="isPast(appointment.start_at)" class="px-4 py-2 rounded-full text-sm font-semibold bg-gray-100 text-gray-600 border-2 border-gray-300">
                                        <i class="fas fa-history mr-1"></i>
                                        Pasada
                                    </span>
                                </div>

                                <!-- Doctor -->
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                                        {{ appointment.doctor.name.charAt(0) }}
                                    </div>
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900">
                                            {{ appointment.doctor.name }}
                                        </h3>
                                        <p class="text-indigo-600 font-medium">
                                            <i class="fas fa-stethoscope mr-2"></i>
                                            {{ appointment.doctor.specialty }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Fecha y hora -->
                                <div class="grid sm:grid-cols-2 gap-4">
                                    <div class="flex items-center gap-3 bg-blue-50 rounded-xl p-4">
                                        <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center text-white shadow-md">
                                            <i class="fas fa-calendar-alt text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wider">Fecha</p>
                                            <p class="font-semibold text-gray-900">{{ formatDate(appointment.start_at) }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 bg-purple-50 rounded-xl p-4">
                                        <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center text-white shadow-md">
                                            <i class="fas fa-clock text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase tracking-wider">Hora</p>
                                            <p class="font-semibold text-gray-900">{{ formatTime(appointment.start_at) }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Notas -->
                                <div v-if="appointment.notas" class="bg-purple-50 border-l-4 border-purple-600 rounded-lg p-4">
                                    <p class="text-sm font-medium text-gray-700 mb-1">
                                        <i class="fas fa-notes-medical mr-2 text-purple-600"></i>
                                        Motivo de consulta
                                    </p>
                                    <p class="text-gray-900">{{ appointment.notas }}</p>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="flex lg:flex-col gap-3">
                                <button
                                    v-if="canReschedule(appointment)"
                                    @click="openRescheduleModal(appointment)"
                                    class="flex-1 lg:flex-none px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all"
                                >
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    Reagendar
                                </button>
                                <button
                                    v-if="canCancel(appointment)"
                                    @click="openCancelModal(appointment)"
                                    class="flex-1 lg:flex-none px-6 py-3 bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all"
                                >
                                    <i class="fas fa-times-circle mr-2"></i>
                                    Cancelar
                                </button>
                                <button
                                    @click="toggleDetails(appointment.id)"
                                    class="flex-1 lg:flex-none px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-all"
                                >
                                    <i :class="['fas', showingDetails === appointment.id ? 'fa-chevron-up' : 'fa-chevron-down', 'mr-2']"></i>
                                    {{ showingDetails === appointment.id ? 'Ocultar' : 'Ver Detalle' }}
                                </button>
                            </div>
                        </div>

                        <!-- Detalles expandibles -->
                        <transition name="slide-fade">
                            <div v-if="showingDetails === appointment.id" class="mt-6 pt-6 border-t-2 border-gray-200 space-y-3">
                                <div class="grid sm:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Paciente</p>
                                        <p class="font-semibold text-gray-900">{{ appointment.nombre_paciente }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Cédula</p>
                                        <p class="font-semibold text-gray-900">{{ appointment.cedula_paciente }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Email</p>
                                        <p class="font-semibold text-gray-900">{{ appointment.email_paciente }}</p>
                                    </div>
                                    <div v-if="appointment.telefono_paciente">
                                        <p class="text-sm text-gray-500">Teléfono</p>
                                        <p class="font-semibold text-gray-900">{{ appointment.telefono_paciente }}</p>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <p class="text-sm text-gray-500">Fecha de registro</p>
                                        <p class="font-semibold text-gray-900">{{ formatDate(appointment.created_at) }} a las {{ formatTime(appointment.created_at) }}</p>
                                    </div>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>

                <!-- Estado vacío -->
                <div v-else-if="cedula" class="glass rounded-2xl p-12 text-center max-w-2xl mx-auto animate-fade-in-up">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-calendar-times text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">No se encontraron citas</h3>
                    <p class="text-gray-600">No hay citas registradas con el número de cédula <strong>{{ cedula }}</strong></p>
                </div>
            </div>
        </div>

        <!-- Modal de cancelación -->
        <transition name="modal">
            <div v-if="cancelModal.show" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Overlay -->
                    <div class="fixed inset-0 transition-opacity bg-black/50 backdrop-blur-sm" @click="closeCancelModal"></div>

                    <!-- Modal -->
                    <div class="inline-block align-bottom glass rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="p-6 sm:p-8">
                            <!-- Icono -->
                            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-exclamation-triangle text-3xl text-red-600"></i>
                            </div>

                            <!-- Título -->
                            <h3 class="text-2xl font-bold text-center text-gray-900 mb-4">
                                ¿Cancelar cita?
                            </h3>

                            <!-- Detalles de la cita -->
                            <div v-if="cancelModal.appointment" class="bg-gray-50 rounded-xl p-4 mb-6 space-y-2">
                                <p class="text-sm text-gray-600">
                                    <strong>Doctor:</strong> {{ cancelModal.appointment.doctor.name }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <strong>Fecha:</strong> {{ formatDate(cancelModal.appointment.start_at) }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <strong>Hora:</strong> {{ formatTime(cancelModal.appointment.start_at) }}
                                </p>
                            </div>

                            <p class="text-center text-gray-600 mb-6">
                                Esta acción no se puede deshacer. El doctor será notificado sobre la cancelación.
                            </p>

                            <!-- Botones -->
                            <div class="flex gap-3">
                                <button
                                    @click="closeCancelModal"
                                    :disabled="cancelModal.loading"
                                    class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-all disabled:opacity-50"
                                >
                                    No, mantener
                                </button>
                                <button
                                    @click="confirmCancel"
                                    :disabled="cancelModal.loading"
                                    class="flex-1 px-6 py-3 bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all disabled:opacity-50"
                                >
                                    <i v-if="cancelModal.loading" class="fas fa-spinner fa-spin mr-2"></i>
                                    {{ cancelModal.loading ? 'Cancelando...' : 'Sí, cancelar' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Modal de reagendamiento -->
        <transition name="modal">
            <div v-if="rescheduleModal.show" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Overlay -->
                    <div class="fixed inset-0 transition-opacity bg-black/50 backdrop-blur-sm" @click="closeRescheduleModal"></div>

                    <!-- Modal -->
                    <div class="inline-block align-bottom glass rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                        <div class="p-6 sm:p-8">
                            <!-- Título -->
                            <h3 class="text-2xl font-bold text-center text-gray-900 mb-6">
                                <i class="fas fa-calendar-alt mr-2 text-amber-600"></i>
                                Reagendar cita
                            </h3>

                            <!-- Cita actual -->
                            <div v-if="rescheduleModal.appointment" class="bg-amber-50 border-l-4 border-amber-600 rounded-lg p-4 mb-6">
                                <p class="text-sm font-medium text-gray-700 mb-2">Cita actual:</p>
                                <p class="text-gray-900">
                                    <strong>{{ formatDate(rescheduleModal.appointment.start_at) }}</strong> a las <strong>{{ formatTime(rescheduleModal.appointment.start_at) }}</strong>
                                </p>
                            </div>

                            <!-- Loading o Calendario -->
                            <div v-if="rescheduleModal.loading" class="text-center py-12">
                                <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
                                <p class="text-gray-600">Cargando disponibilidad...</p>
                            </div>

                            <div v-else>
                                <!-- Calendario -->
                                <PatientCalendar
                                    :availability="rescheduleModal.availability"
                                    :duration="rescheduleModal.duration"
                                    @slot-selected="rescheduleModal.newSlot = $event"
                                />

                                <!-- Nueva hora seleccionada -->
                                <div v-if="rescheduleModal.newSlot" class="mt-6 bg-green-50 border-l-4 border-green-600 rounded-lg p-4">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Nueva hora seleccionada:</p>
                                    <p class="text-lg font-bold text-gray-900">
                                        {{ formatDate(rescheduleModal.newSlot) }} a las {{ formatTime(rescheduleModal.newSlot) }}
                                    </p>
                                </div>

                                <!-- Botones -->
                                <div class="flex gap-3 mt-6">
                                    <button
                                        @click="closeRescheduleModal"
                                        :disabled="rescheduleModal.loading"
                                        class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-all disabled:opacity-50"
                                    >
                                        Cancelar
                                    </button>
                                    <button
                                        @click="confirmReschedule"
                                        :disabled="!rescheduleModal.newSlot || rescheduleModal.loading"
                                        class="flex-1 px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <i v-if="rescheduleModal.loading" class="fas fa-spinner fa-spin mr-2"></i>
                                        {{ rescheduleModal.loading ? 'Reagendando...' : 'Confirmar reagendamiento' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<style scoped>
.glass {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(16px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.5;
    animation: blob 7s infinite;
}

.blob-1 {
    width: 400px;
    height: 400px;
    background: linear-gradient(180deg, #3b82f6 0%, #8b5cf6 100%);
    top: -200px;
    left: -200px;
}

.blob-2 {
    width: 350px;
    height: 350px;
    background: linear-gradient(180deg, #6366f1 0%, #ec4899 100%);
    top: 40%;
    right: -150px;
    animation-delay: 2s;
}

.blob-3 {
    width: 300px;
    height: 300px;
    background: linear-gradient(180deg, #8b5cf6 0%, #ec4899 100%);
    bottom: -100px;
    left: 30%;
    animation-delay: 4s;
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

@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fade-in-up 0.6s ease-out forwards;
}

/* Transiciones */
.slide-fade-enter-active,
.slide-fade-leave-active {
    transition: all 0.3s ease;
}

.slide-fade-enter-from {
    transform: translateY(-10px);
    opacity: 0;
}

.slide-fade-leave-to {
    transform: translateY(-10px);
    opacity: 0;
}

.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .glass,
.modal-leave-active .glass {
    transition: transform 0.3s ease;
}

.modal-enter-from .glass,
.modal-leave-to .glass {
    transform: scale(0.9);
}
</style>
