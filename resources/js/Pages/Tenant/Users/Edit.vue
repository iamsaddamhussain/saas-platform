<template>
    <AuthenticatedLayout>
        <Head :title="`Edit User - ${user.name}`" />

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-center justify-between mb-6">
                            <h1 class="text-2xl font-semibold text-gray-900">Edit User: {{ user.name }}</h1>
                            <div class="space-x-2">
                                <Link :href="route('tenant.users.show', { subdomain: currentSubdomain, user: user.id })" 
                                      class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    View Profile
                                </Link>
                                <Link :href="route('tenant.users.index', { subdomain: currentSubdomain })" 
                                      class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Back to Users
                                </Link>
                            </div>
                        </div>

                        <form @submit.prevent="submit">
                            <div class="space-y-6">
                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">
                                        Full Name
                                    </label>
                                    <input
                                        type="text"
                                        id="name"
                                        v-model="form.name"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        :class="{ 'border-red-500': form.errors.name }"
                                        required
                                    />
                                    <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.name }}
                                    </div>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">
                                        Email Address
                                    </label>
                                    <input
                                        type="email"
                                        id="email"
                                        v-model="form.email"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        :class="{ 'border-red-500': form.errors.email }"
                                        required
                                    />
                                    <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.email }}
                                    </div>
                                </div>

                                <!-- Role -->
                                <div>
                                    <label for="role_id" class="block text-sm font-medium text-gray-700">
                                        Role
                                    </label>
                                    <select
                                        id="role_id"
                                        v-model="form.role_id"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        :class="{ 'border-red-500': form.errors.role_id }"
                                        :disabled="user.role?.key === 'owner' && user.id === $page.props.auth.user.id"
                                        required
                                    >
                                        <option value="">Select a role...</option>
                                        <option v-for="role in roles" :key="role.id" :value="role.id">
                                            {{ role.name }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.role_id" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.role_id }}
                                    </div>
                                    <div v-if="user.role?.key === 'owner' && user.id === $page.props.auth.user.id" 
                                         class="mt-1 text-sm text-gray-500">
                                        You cannot change your own role as the owner.
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="flex items-center">
                                    <input
                                        type="checkbox"
                                        id="active"
                                        v-model="form.active"
                                        :disabled="user.id === $page.props.auth.user.id"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                    />
                                    <label for="active" class="ml-2 block text-sm text-gray-900">
                                        Active User
                                    </label>
                                    <div v-if="user.id === $page.props.auth.user.id" 
                                         class="ml-2 text-sm text-gray-500">
                                        (You cannot deactivate yourself)
                                    </div>
                                </div>

                                <!-- Password Change -->
                                <div class="space-y-4 pt-6 border-t border-gray-200">
                                    <h3 class="text-lg font-medium text-gray-900">Change Password</h3>
                                    <p class="text-sm text-gray-600">
                                        Leave password fields empty to keep the current password.
                                    </p>

                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700">
                                            New Password
                                        </label>
                                        <input
                                            type="password"
                                            id="password"
                                            v-model="form.password"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                            :class="{ 'border-red-500': form.errors.password }"
                                        />
                                        <div v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.password }}
                                        </div>
                                    </div>

                                    <div>
                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                            Confirm New Password
                                        </label>
                                        <input
                                            type="password"
                                            id="password_confirmation"
                                            v-model="form.password_confirmation"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        />
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="flex items-center justify-end space-x-4 pt-4">
                                    <Link :href="route('tenant.users.index')" 
                                          class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                        Cancel
                                    </Link>
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                                    >
                                        {{ form.processing ? 'Updating...' : 'Update User' }}
                                    </button>
                                </div>
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
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    user: Object,
    roles: Array,
    tenant: Object,
})

// Extract subdomain from current URL
const currentSubdomain = computed(() => {
    const host = window.location.host;
    const parts = host.split('.');
    return parts.length > 2 ? parts[0] : null;
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    role_id: props.user.role?.id || '',
    active: props.user.active,
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.patch(route('tenant.users.update', { subdomain: currentSubdomain.value, user: props.user.id }))
}
</script>