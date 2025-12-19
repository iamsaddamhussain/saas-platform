<template>
    <AuthenticatedLayout>
        <Head title="Tenant Settings" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-6">
                            <h1 class="text-2xl font-semibold text-gray-900">Tenant Settings</h1>
                            <p class="text-gray-600 mt-1">Manage your tenant's branding and configuration.</p>
                        </div>

                        <div class="space-y-8">
                            <!-- General Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h2 class="text-lg font-medium text-gray-900 mb-4">General Settings</h2>
                                
                                <form @submit.prevent="submitGeneralSettings">
                                    <div class="space-y-4">
                                        <!-- Tenant Name -->
                                        <div>
                                            <label for="name" class="block text-sm font-medium text-gray-700">
                                                Tenant Name
                                            </label>
                                            <input
                                                type="text"
                                                id="name"
                                                v-model="generalForm.name"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                                :class="{ 'border-red-500': generalForm.errors.name }"
                                                required
                                            />
                                            <div v-if="generalForm.errors.name" class="mt-1 text-sm text-red-600">
                                                {{ generalForm.errors.name }}
                                            </div>
                                        </div>

                                        <!-- Domain -->
                                        <div>
                                            <label for="domain" class="block text-sm font-medium text-gray-700">
                                                Domain
                                            </label>
                                            <div class="mt-1 flex rounded-md shadow-sm">
                                                <input
                                                    type="text"
                                                    id="domain"
                                                    v-model="generalForm.domain"
                                                    class="flex-1 min-w-0 block w-full px-3 py-2 rounded-l-md border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                                    :class="{ 'border-red-500': generalForm.errors.domain }"
                                                    required
                                                />
                                                <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                                    .{{ $page.props.app_domain }}
                                                </span>
                                            </div>
                                            <div v-if="generalForm.errors.domain" class="mt-1 text-sm text-red-600">
                                                {{ generalForm.errors.domain }}
                                            </div>
                                        </div>

                                        <!-- Timezone -->
                                        <div>
                                            <label for="timezone" class="block text-sm font-medium text-gray-700">
                                                Timezone
                                            </label>
                                            <select
                                                id="timezone"
                                                v-model="generalForm.timezone"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                            >
                                                <option v-for="tz in timezones" :key="tz.value" :value="tz.value">
                                                    {{ tz.label }}
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Date Format -->
                                        <div>
                                            <label for="date_format" class="block text-sm font-medium text-gray-700">
                                                Date Format
                                            </label>
                                            <select
                                                id="date_format"
                                                v-model="generalForm.date_format"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                            >
                                                <option value="Y-m-d">YYYY-MM-DD ({{ formatExampleDate('Y-m-d') }})</option>
                                                <option value="m/d/Y">MM/DD/YYYY ({{ formatExampleDate('m/d/Y') }})</option>
                                                <option value="d/m/Y">DD/MM/YYYY ({{ formatExampleDate('d/m/Y') }})</option>
                                                <option value="d-m-Y">DD-MM-YYYY ({{ formatExampleDate('d-m-Y') }})</option>
                                            </select>
                                        </div>

                                        <div class="flex justify-end">
                                            <button
                                                type="submit"
                                                :disabled="generalForm.processing"
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                                            >
                                                {{ generalForm.processing ? 'Updating...' : 'Update General Settings' }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Branding Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Branding</h2>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Logo Upload -->
                                    <div>
                                        <h3 class="text-md font-medium text-gray-800 mb-2">Logo</h3>
                                        
                                        <!-- Current Logo Display -->
                                        <div v-if="tenant.logo_path" class="mb-4">
                                            <img :src="`/storage/${tenant.logo_path}`" 
                                                 alt="Current Logo" 
                                                 class="h-16 w-auto border border-gray-300 rounded">
                                            <button @click="deleteLogo" 
                                                    class="mt-2 text-sm text-red-600 hover:text-red-900">
                                                Remove Logo
                                            </button>
                                        </div>
                                        
                                        <!-- Logo Upload Form -->
                                        <form @submit.prevent="uploadLogo" enctype="multipart/form-data">
                                            <div>
                                                <input
                                                    type="file"
                                                    ref="logoInput"
                                                    @change="onLogoFileChange"
                                                    accept="image/*"
                                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                                />
                                                <div v-if="logoForm.errors.logo" class="mt-1 text-sm text-red-600">
                                                    {{ logoForm.errors.logo }}
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    Upload a PNG, JPG, or SVG file. Maximum size: 2MB.
                                                </p>
                                            </div>
                                            <div class="mt-4">
                                                <button
                                                    type="submit"
                                                    :disabled="!logoForm.logo || logoForm.processing"
                                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                                                >
                                                    {{ logoForm.processing ? 'Uploading...' : 'Upload Logo' }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Color Scheme -->
                                    <div>
                                        <h3 class="text-md font-medium text-gray-800 mb-2">Color Scheme</h3>
                                        
                                        <form @submit.prevent="updateColors">
                                            <div class="space-y-3">
                                                <div>
                                                    <label for="primary_color" class="block text-sm font-medium text-gray-700">
                                                        Primary Color
                                                    </label>
                                                    <div class="mt-1 flex">
                                                        <input
                                                            type="color"
                                                            id="primary_color"
                                                            v-model="colorForm.primary_color"
                                                            class="h-10 w-16 border border-gray-300 rounded-l-md"
                                                        />
                                                        <input
                                                            type="text"
                                                            v-model="colorForm.primary_color"
                                                            class="flex-1 border border-l-0 border-gray-300 rounded-r-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                                                        />
                                                    </div>
                                                </div>
                                                
                                                <div>
                                                    <label for="secondary_color" class="block text-sm font-medium text-gray-700">
                                                        Secondary Color
                                                    </label>
                                                    <div class="mt-1 flex">
                                                        <input
                                                            type="color"
                                                            id="secondary_color"
                                                            v-model="colorForm.secondary_color"
                                                            class="h-10 w-16 border border-gray-300 rounded-l-md"
                                                        />
                                                        <input
                                                            type="text"
                                                            v-model="colorForm.secondary_color"
                                                            class="flex-1 border border-l-0 border-gray-300 rounded-r-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                                                        />
                                                    </div>
                                                </div>
                                                
                                                <div class="pt-2">
                                                    <button
                                                        type="submit"
                                                        :disabled="colorForm.processing"
                                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                                                    >
                                                        {{ colorForm.processing ? 'Updating...' : 'Update Colors' }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Advanced Settings -->
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Advanced Settings</h2>
                                
                                <div class="space-y-4">
                                    <!-- Custom CSS -->
                                    <div>
                                        <label for="custom_css" class="block text-sm font-medium text-gray-700">
                                            Custom CSS
                                        </label>
                                        <textarea
                                            id="custom_css"
                                            v-model="advancedForm.custom_css"
                                            rows="6"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm"
                                            placeholder="/* Add your custom CSS here */&#10;.custom-class {&#10;    color: #333;&#10;}"
                                        ></textarea>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Custom CSS will be applied to your tenant's interface.
                                        </p>
                                    </div>

                                    <div class="flex justify-end">
                                        <button
                                            @click="updateAdvancedSettings"
                                            :disabled="advancedForm.processing"
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                                        >
                                            {{ advancedForm.processing ? 'Updating...' : 'Update Advanced Settings' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { useTenantRoute } from '@/Composables/useTenantRoute.js'

const props = defineProps({
    tenant: Object,
    timezones: Array,
})

const { tenantRoute } = useTenantRoute()
const logoInput = ref(null)

// General Settings Form
const generalForm = useForm({
    name: props.tenant.name,
    primary_color: props.tenant.primary_color || '#3B82F6',
    timezone: props.tenant.timezone || 'UTC',
    locale: props.tenant.locale || 'en',
})

// Logo Upload Form
const logoForm = useForm({
    logo: null,
})

// Color Scheme Form
const colorForm = useForm({
    primary_color: props.tenant.primary_color || '#3B82F6',
    secondary_color: props.tenant.secondary_color || '#64748B',
})

// Advanced Settings Form
const advancedForm = useForm({
    custom_css: props.tenant.settings?.custom_css || '',
    processing: false,
})

const formatExampleDate = (format) => {
    const date = new Date()
    const formatMap = {
        'Y': date.getFullYear(),
        'm': String(date.getMonth() + 1).padStart(2, '0'),
        'd': String(date.getDate()).padStart(2, '0'),
    }
    
    return format.replace(/[Ymd]/g, match => formatMap[match])
}

const submitGeneralSettings = () => {
    generalForm.put(tenantRoute('tenant.settings.update'))
}

const onLogoFileChange = (event) => {
    logoForm.logo = event.target.files[0]
}

const uploadLogo = () => {
    logoForm.post(tenantRoute('tenant.settings.logo'), {
        onSuccess: () => {
            logoForm.reset('logo')
            logoInput.value.value = ''
        }
    })
}

const deleteLogo = () => {
    if (confirm('Are you sure you want to remove the logo?')) {
        router.delete(tenantRoute('tenant.settings.logo'))
    }
}

const updateColors = () => {
    colorForm.put(tenantRoute('tenant.settings.update'))
}

const updateAdvancedSettings = () => {
    advancedForm.processing = true
    router.put(tenantRoute('tenant.settings.update'), {
        custom_css: advancedForm.custom_css
    }, {
        onFinish: () => advancedForm.processing = false
    })
}
</script>