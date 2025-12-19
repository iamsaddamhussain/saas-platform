<template>
    <AuthenticatedLayout>
        <Head title="Create Tenant" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-center justify-between mb-6">
                            <h1 class="text-2xl font-semibold text-gray-900">Create New Tenant</h1>
                            <Link :href="route('super-admin.tenants.index')" 
                                  class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Back to Tenants
                            </Link>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Tenant Information -->
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
                                        @input="generateSubdomain"
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
                                <InputLabel for="domain" value="Custom Domain (Optional)" />
                                <TextInput
                                    id="domain"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.domain"
                                    placeholder="example.com"
                                />
                                <InputError class="mt-2" :message="form.errors.domain" />
                                <p class="mt-1 text-sm text-gray-600">Leave empty to use subdomain only</p>
                            </div>

                            <!-- Owner Information -->
                            <div class="border-t pt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Tenant Owner</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <InputLabel for="owner_name" value="Owner Name" />
                                        <TextInput
                                            id="owner_name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.owner_name"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.owner_name" />
                                    </div>

                                    <div>
                                        <InputLabel for="owner_email" value="Owner Email" />
                                        <TextInput
                                            id="owner_email"
                                            type="email"
                                            class="mt-1 block w-full"
                                            v-model="form.owner_email"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.owner_email" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                    <div>
                                        <InputLabel for="owner_password" value="Owner Password" />
                                        <TextInput
                                            id="owner_password"
                                            type="password"
                                            class="mt-1 block w-full"
                                            v-model="form.owner_password"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.owner_password" />
                                    </div>

                                    <div>
                                        <InputLabel for="owner_password_confirmation" value="Confirm Password" />
                                        <TextInput
                                            id="owner_password_confirmation"
                                            type="password"
                                            class="mt-1 block w-full"
                                            v-model="form.owner_password_confirmation"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.owner_password_confirmation" />
                                    </div>
                                </div>
                            </div>

                            <!-- Trial Settings -->
                            <div class="border-t pt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Trial Settings</h3>
                                
                                <div>
                                    <InputLabel for="trial_days" value="Trial Period (Days)" />
                                    <TextInput
                                        id="trial_days"
                                        type="number"
                                        class="mt-1 block w-full"
                                        v-model="form.trial_days"
                                        min="0"
                                        max="365"
                                        placeholder="14"
                                    />
                                    <InputError class="mt-2" :message="form.errors.trial_days" />
                                    <p class="mt-1 text-sm text-gray-600">Leave empty for no trial period</p>
                                </div>
                            </div>

                            <div class="flex items-center justify-end pt-6 border-t">
                                <Link :href="route('super-admin.tenants.index')" 
                                      class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-4">
                                    Cancel
                                </Link>
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Create Tenant
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

const form = useForm({
    name: '',
    subdomain: '',
    domain: '',
    owner_name: '',
    owner_email: '',
    owner_password: '',
    owner_password_confirmation: '',
    trial_days: 14,
})

const generateSubdomain = () => {
    if (form.name && !form.subdomain) {
        form.subdomain = form.name
            .toLowerCase()
            .replace(/[^a-z0-9]/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '')
    }
}

const submit = () => {
    form.post(route('super-admin.tenants.store'), {
        onFinish: () => form.reset('owner_password', 'owner_password_confirmation'),
    })
}
</script>