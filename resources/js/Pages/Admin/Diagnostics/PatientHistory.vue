<template>
    <AppLayout title="Historia Clínica del Paciente">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">
                        Historia Clínica del Paciente
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Consulta completa de diagnósticos y citas por cédula
                    </p>
                </div>
                <Link :href="route('admin.diagnostics.index')" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all">
                    <i class="fas fa-arrow-left mr-2"></i>Volver a Diagnósticos
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Formulario de búsqueda -->
                <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl shadow-lg p-6 mb-8">
                    <form @submit.prevent="searchPatient" class="space-y-4">
                        <div>
                            <label class="block text-white text-sm font-medium mb-2">
                                <i class="fas fa-id-card mr-2"></i>Buscar por Cédula
                            </label>
                            <div class="flex gap-3">
                                <input
                                    v-model="searchCedula"
                                    type="text"
                                    placeholder="Ingrese la cédula del paciente"
                                    class="flex-1 px-4 py-3 rounded-lg border-0 focus:ring-2 focus:ring-purple-300 text-gray-900"
                                    required
                                />
                                <button
                                    type="submit"
                                    class="px-6 py-3 bg-white text-purple-600 font-semibold rounded-lg hover:bg-gray-100 transition-all shadow-md"
                                >
                                    <i class="fas fa-search mr-2"></i>Buscar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Información del Paciente -->
                <div v-if="patient" class="bg-white rounded-xl shadow-lg p-6 mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-user-circle text-purple-600 mr-3"></i>
                        Información del Paciente
                    </h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm text-gray-600">Nombre:</span>
                            <p class="font-semibold text-gray-900">{{ patient.name }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Cédula:</span>
                            <p class="font-semibold text-gray-900">{{ patient.cedula }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600">Email:</span>
                            <p class="font-semibold text-gray-900">{{ patient.email }}</p>
                        </div>
                        <div v-if="patient.telefono">
                            <span class="text-sm text-gray-600">Teléfono:</span>
                            <p class="font-semibold text-gray-900">{{ patient.telefono }}</p>
                        </div>
                    </div>
                </div>

                <!-- Sin resultados -->
                <div v-if="cedula && !patient" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg mb-8">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mr-3"></i>
                        <p class="text-yellow-800">
                            No se encontraron registros para la cédula <strong>{{ cedula }}</strong>
                        </p>
                    </div>
                </div>

                <!-- Tabs: Diagnósticos y Citas -->
                <div v-if="patient" class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <!-- Tab Headers -->
                    <div class="flex border-b">
                        <button
                            @click="activeTab = 'diagnostics'"
                            :class="[
                                'flex-1 px-6 py-4 text-center font-semibold transition-all',
                                activeTab === 'diagnostics'
                                    ? 'bg-purple-50 text-purple-600 border-b-2 border-purple-600'
                                    : 'text-gray-600 hover:bg-gray-50'
                            ]"
                        >
                            <i class="fas fa-file-medical mr-2"></i>
                            Diagnósticos ({{ diagnostics.length }})
                        </button>
                        <button
                            @click="activeTab = 'appointments'"
                            :class="[
                                'flex-1 px-6 py-4 text-center font-semibold transition-all',
                                activeTab === 'appointments'
                                    ? 'bg-purple-50 text-purple-600 border-b-2 border-purple-600'
                                    : 'text-gray-600 hover:bg-gray-50'
                            ]"
                        >
                            <i class="fas fa-calendar-alt mr-2"></i>
                            Citas ({{ appointments.length }})
                        </button>
                    </div>

                    <!-- Tab Content: Diagnósticos -->
                    <div v-show="activeTab === 'diagnostics'" class="p-6">
                        <div v-if="diagnostics.length === 0" class="text-center py-12 text-gray-500">
                            <i class="fas fa-inbox text-6xl mb-4 text-gray-300"></i>
                            <p>No hay diagnósticos registrados</p>
                        </div>
                        
                        <div v-else class="space-y-4">
                            <div
                                v-for="diagnostic in diagnostics"
                                :key="diagnostic.id"
                                class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition-all"
                            >
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-bold text-lg text-gray-900">
                                            Diagnóstico #{{ diagnostic.id }}
                                        </h4>
                                        <p class="text-sm text-gray-600">
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ formatDate(diagnostic.fecha_consulta) }}
                                        </p>
                                    </div>
                                    <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-medium">
                                        {{ diagnostic.especialidad }}
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <span class="text-sm text-gray-600">Médico:</span>
                                    <p class="font-semibold text-gray-900">{{ diagnostic.doctor?.name || 'N/A' }}</p>
                                </div>

                                <div class="bg-gray-50 p-3 rounded-lg mb-3">
                                    <p class="text-sm text-gray-700 line-clamp-2">{{ diagnostic.diagnostico }}</p>
                                </div>

                                <div class="flex gap-2">
                                    <Link
                                        :href="route('admin.diagnostics.show', diagnostic.id)"
                                        class="flex-1 px-4 py-2 bg-purple-600 text-white text-center rounded-lg hover:bg-purple-700 transition-all"
                                    >
                                        <i class="fas fa-eye mr-2"></i>Ver Detalles
                                    </Link>
                                    <a
                                        :href="route('admin.diagnostics.pdf', diagnostic.id)"
                                        target="_blank"
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all"
                                    >
                                        <i class="fas fa-file-pdf mr-2"></i>PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Content: Citas -->
                    <div v-show="activeTab === 'appointments'" class="p-6">
                        <div v-if="appointments.length === 0" class="text-center py-12 text-gray-500">
                            <i class="fas fa-inbox text-6xl mb-4 text-gray-300"></i>
                            <p>No hay citas registradas</p>
                        </div>
                        
                        <div v-else class="space-y-4">
                            <div
                                v-for="appointment in appointments"
                                :key="appointment.id"
                                class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition-all"
                            >
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-bold text-lg text-gray-900">
                                            {{ appointment.doctor?.name || 'Doctor no disponible' }}
                                        </h4>
                                        <p class="text-sm text-gray-600">
                                            {{ appointment.doctor?.specialty || 'Especialidad no disponible' }}
                                        </p>
                                    </div>
                                    <span
                                        :class="[
                                            'px-3 py-1 rounded-full text-sm font-medium',
                                            getStatusClass(appointment.status)
                                        ]"
                                    >
                                        <i :class="['fas mr-1', getStatusIcon(appointment.status)]"></i>
                                        {{ getStatusText(appointment.status) }}
                                    </span>
                                </div>

                                <div class="grid md:grid-cols-2 gap-3 mb-3">
                                    <div>
                                        <span class="text-sm text-gray-600">Fecha:</span>
                                        <p class="font-semibold text-gray-900">{{ formatDate(appointment.start_at) }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-600">Hora:</span>
                                        <p class="font-semibold text-gray-900">{{ formatTime(appointment.start_at) }}</p>
                                    </div>
                                </div>

                                <div v-if="appointment.notes" class="bg-gray-50 p-3 rounded-lg mb-3">
                                    <span class="text-sm text-gray-600">Notas:</span>
                                    <p class="text-sm text-gray-700">{{ appointment.notes }}</p>
                                </div>

                                <div v-if="appointment.diagnostic" class="mt-3">
                                    <Link
                                        :href="`/admin/diagnostics/${appointment.diagnostic.id}`"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all"
                                    >
                                        <i class="fas fa-file-medical mr-2"></i>Ver Diagnóstico Asociado
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    diagnostics: Array,
    appointments: Array,
    patient: Object,
    cedula: String,
});

const searchCedula = ref(props.cedula || '');
const activeTab = ref('diagnostics');

const searchPatient = () => {
    router.get(route('admin.diagnostics.patient-history'), {
        cedula: searchCedula.value
    });
};

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('es-CO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatTime = (datetime) => {
    if (!datetime) return 'N/A';
    return new Date(datetime).toLocaleTimeString('es-CO', {
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getStatusClass = (status) => {
    const classes = {
        'pendiente': 'bg-yellow-100 text-yellow-800 border border-yellow-200',
        'confirmada': 'bg-blue-100 text-blue-800 border border-blue-200',
        'completada': 'bg-green-100 text-green-800 border border-green-200',
        'cancelada': 'bg-gray-100 text-gray-800 border border-gray-200',
        'rechazada': 'bg-red-100 text-red-800 border border-red-200',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getStatusIcon = (status) => {
    const icons = {
        'pendiente': 'fa-clock',
        'confirmada': 'fa-check-circle',
        'completada': 'fa-check-double',
        'cancelada': 'fa-times-circle',
        'rechazada': 'fa-ban',
    };
    return icons[status] || 'fa-question-circle';
};

const getStatusText = (status) => {
    const texts = {
        'pendiente': 'Pendiente',
        'confirmada': 'Confirmada',
        'completada': 'Completada',
        'cancelada': 'Cancelada',
        'rechazada': 'Rechazada',
    };
    return texts[status] || status;
};
</script>
