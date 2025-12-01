<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import AdminHeader from '@/Components/AdminHeader.vue'

const props = defineProps({
    diagnostic: Object,
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const downloadPDF = () => {
    // Detectar si es admin o doctor para construir la URL correcta
    const role = window.location.pathname.includes('/admin/') ? 'admin' : 'doctor'
    window.open(`/${role}/diagnostics/${props.diagnostic.id}/pdf`, '_blank')
}
</script>

<template>
    <AppLayout title="Visualizar Diagnóstico">
        <Head title="Ver Diagnóstico" />

        <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-12 px-4 sm:px-6 lg:px-8">
            <AdminHeader v-if="$page.props.auth.user.role === 'admin'" />
            
            <div class="max-w-5xl mx-auto">
                <!-- Header con botones -->
                <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <Link 
                            :href="$page.props.auth.user.role === 'admin' ? '/admin/diagnostics' : '/doctor/diagnostics'"
                            class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium transition-colors mb-2"
                        >
                            <i class="fas fa-arrow-left mr-2"></i>
                            Volver a Diagnósticos
                        </Link>
                        <h1 class="text-4xl font-bold text-gray-900">
                            Diagnóstico Médico
                        </h1>
                        <p class="text-gray-600 mt-2">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            {{ formatDate(diagnostic.fecha_consulta) }}
                        </p>
                    </div>

                    <button
                        @click="downloadPDF"
                        class="inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all"
                    >
                        <i class="fas fa-file-pdf text-xl"></i>
                        Descargar PDF
                    </button>
                </div>

                <!-- Contenedor principal con estilo de documento -->
                <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-200">
                    <!-- Header del documento -->
                    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 px-8 py-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-3xl font-bold mb-2">
                                    <i class="fas fa-heartbeat mr-3"></i>
                                    MediCitas
                                </h2>
                                <p class="text-blue-100">Sistema de Gestión Médica</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-blue-100">Diagnóstico #{{ diagnostic.id }}</p>
                                <p class="text-xs text-blue-200">{{ formatDate(diagnostic.created_at) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido del diagnóstico -->
                    <div class="p-8 space-y-8">
                        <!-- Información del Paciente -->
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="bg-blue-50 rounded-xl p-6 border-l-4 border-blue-600">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-user text-blue-600 mr-3"></i>
                                    Información del Paciente
                                </h3>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Nombre Completo</p>
                                        <p class="font-semibold text-gray-900">{{ diagnostic.patient_name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Cédula</p>
                                        <p class="font-semibold text-gray-900">{{ diagnostic.cedula_paciente }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Email</p>
                                        <p class="font-semibold text-gray-900 break-all">{{ diagnostic.patient_email }}</p>
                                    </div>
                                    <div v-if="diagnostic.telefono_paciente">
                                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Teléfono</p>
                                        <p class="font-semibold text-gray-900">{{ diagnostic.telefono_paciente }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Información del Médico -->
                            <div class="bg-purple-50 rounded-xl p-6 border-l-4 border-purple-600">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-user-md text-purple-600 mr-3"></i>
                                    Médico Tratante
                                </h3>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Nombre</p>
                                        <p class="font-semibold text-gray-900">{{ diagnostic.doctor?.name }}</p>
                                    </div>
                                    <div v-if="diagnostic.especialidad">
                                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Especialidad</p>
                                        <p class="font-semibold text-gray-900">{{ diagnostic.especialidad }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Fecha de Consulta</p>
                                        <p class="font-semibold text-gray-900">{{ formatDate(diagnostic.fecha_consulta) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Diagnóstico -->
                        <div class="border-t-2 border-gray-200 pt-8">
                            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-6 border-l-4 border-indigo-600">
                                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-stethoscope text-indigo-600 mr-3"></i>
                                    Diagnóstico
                                </h3>
                                <div class="prose max-w-none">
                                    <p class="text-gray-800 whitespace-pre-wrap leading-relaxed">{{ diagnostic.diagnostico }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Medicamentos -->
                        <div v-if="diagnostic.medicamentos" class="bg-green-50 rounded-xl p-6 border-l-4 border-green-600">
                            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-pills text-green-600 mr-3"></i>
                                Medicamentos Recetados
                            </h3>
                            <div class="prose max-w-none">
                                <p class="text-gray-800 whitespace-pre-wrap leading-relaxed">{{ diagnostic.medicamentos }}</p>
                            </div>
                        </div>

                        <!-- Procedimientos -->
                        <div v-if="diagnostic.procedimientos" class="bg-blue-50 rounded-xl p-6 border-l-4 border-blue-600">
                            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-procedures text-blue-600 mr-3"></i>
                                Procedimientos Realizados
                            </h3>
                            <div class="prose max-w-none">
                                <p class="text-gray-800 whitespace-pre-wrap leading-relaxed">{{ diagnostic.procedimientos }}</p>
                            </div>
                        </div>

                        <!-- Remisiones -->
                        <div v-if="diagnostic.remisiones" class="bg-amber-50 rounded-xl p-6 border-l-4 border-amber-600">
                            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-hospital text-amber-600 mr-3"></i>
                                Remisiones a Especialistas
                            </h3>
                            <div class="prose max-w-none">
                                <p class="text-gray-800 whitespace-pre-wrap leading-relaxed">{{ diagnostic.remisiones }}</p>
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div v-if="diagnostic.observaciones" class="bg-gray-50 rounded-xl p-6 border-l-4 border-gray-600">
                            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-clipboard-list text-gray-600 mr-3"></i>
                                Observaciones Adicionales
                            </h3>
                            <div class="prose max-w-none">
                                <p class="text-gray-800 whitespace-pre-wrap leading-relaxed">{{ diagnostic.observaciones }}</p>
                            </div>
                        </div>

                        <!-- Footer del documento -->
                        <div class="border-t-2 border-gray-200 pt-8 mt-8">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-6">
                                <div class="text-sm text-gray-600">
                                    <p><i class="fas fa-info-circle mr-2"></i>Este documento es válido como diagnóstico médico oficial</p>
                                    <p class="mt-1"><i class="fas fa-shield-alt mr-2"></i>Los datos están protegidos bajo normativa de privacidad</p>
                                </div>
                                <div class="text-right">
                                    <div class="border-t-2 border-gray-400 w-64 mb-2"></div>
                                    <p class="font-semibold text-gray-900">{{ diagnostic.doctor?.name }}</p>
                                    <p class="text-sm text-gray-600" v-if="diagnostic.especialidad">{{ diagnostic.especialidad }}</p>
                                    <p class="text-xs text-gray-500 mt-1">Firma del Médico Tratante</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botón de descarga adicional al final -->
                <div class="mt-6 text-center">
                    <button
                        @click="downloadPDF"
                        class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white rounded-xl font-bold text-lg shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all"
                    >
                        <i class="fas fa-download text-xl"></i>
                        Descargar como PDF
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
