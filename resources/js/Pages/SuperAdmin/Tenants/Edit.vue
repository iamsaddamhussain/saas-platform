<template>
    <AuthenticatedLayout>
        <Head title="Edit Tenant" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-center justify-between mb-6">
                            <h1 class="text-2xl font-semibold text-gray-900">Edit Tenant: {{ tenant.name }}</h1>
                            <Link :href="route('super-admin.tenants.show', tenant.id)" 
                                  class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Back to Details
                            </Link>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Basic Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="name" value="Tenant Name" />
                                    <TextInput
                                        id="name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.name"
                                        required
                                        autofocus
                                    />
                                    <InputError class="mt-2" :message="form.errors.name" />
                                </div>

                                <div>
                                    <InputLabel for="subdomain" value="Subdomain" />
                                    <div class="mt-1 flex rounded-md shadow-sm">
                                        <TextInput
                                            id="subdomain"
                                            type="text"
                                            class="block w-full rounded-none rounded-l-md"
                                            v-model="form.subdomain"
                                            required
                                        />
                                        <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                            .saas-platform.localhost
                                        </span>
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.subdomain" />
                                </div>
                            </div>

                            <div>
                                <InputLabel for="domain" value="Custom Domain" />
                                <TextInput
                                    id="domain"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.domain"
                                    placeholder="example.com"
                                />
                                <InputError class="mt-2" :message="form.errors.domain" />
                            </div>

                            <!-- Status -->
                            <div class="flex items-center">
                                <input
                                    id="active"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    v-model="form.active"
                                />
                                <InputLabel for="active" value="Tenant Active" class="ml-2" />
                            </div>

                            <!-- Settings -->
                            <div class="border-t pt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Settings</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <InputLabel for="primary_color" value="Primary Color" />
                                        <TextInput
                                            id="primary_color"
                                            type="color"
                                            class="mt-1 block w-full h-10"
                                            v-model="form.settings.primary_color"
                                        />
                                        <InputError class="mt-2" :message="form.errors['settings.primary_color']" />
                                    </div>

                                    <div>
                                        <InputLabel for="timezone" value="Timezone" />
                                        <select
                                            id="timezone"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            v-model="form.settings.timezone"
                                        >
                                            <option value="UTC">UTC</option>
                                            <option value="America/New_York">America/New_York</option>
                                            <option value="America/Chicago">America/Chicago</option>
                                            <option value="America/Denver">America/Denver</option>
                                            <option value="America/Los_Angeles">America/Los_Angeles</option>
                                            <option value="Europe/London">Europe/London</option>
                                            <option value="Europe/Paris">Europe/Paris</option>
                                            <option value="Asia/Tokyo">Asia/Tokyo</option>
                                            <option value="Australia/Sydney">Australia/Sydney</option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors['settings.timezone']" />
                                    </div>

                                    <div>
                                        <InputLabel for="locale" value="Locale" />
                                        <select
                                            id="locale"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            v-model="form.settings.locale"
                                        >
                                            <option value="en">English</option>
                                            <option value="es">Spanish</option>
                                            <option value="fr">French</option>
                                            <option value="de">German</option>
                                            <option value="ja">Japanese</option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors['settings.locale']" />
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end pt-6 border-t">
                                <Link :href="route('super-admin.tenants.show', tenant.id)" 
                                      class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-4">
                                    Cancel
                                </Link>
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Update Tenant
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    tenant: Object,
})

const form = useForm({
    name: props.tenant.name,
    subdomain: props.tenant.subdomain,
    domain: props.tenant.domain || '',
    active: props.tenant.active,
    settings: {
        primary_color: props.tenant.primary_color || '#3B82F6',
        timezone: props.tenant.timezone || 'UTC',
        locale: props.tenant.locale || 'en',
    }
})

const submit = () => {
    form.patch(route('super-admin.tenants.update', props.tenant.id))
}
</script>