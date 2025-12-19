<template>
    <AuthenticatedLayout>
        <Head title="Add New User" />

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-center justify-between mb-6">
                            <h1 class="text-2xl font-semibold text-gray-900">Add New User</h1>
                            <Link :href="route('tenant.users.index', { subdomain: currentSubdomain })" 
                                  class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Back to Users
                            </Link>
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
                                </div>

                                <!-- Send Invitation -->
                                <div class="flex items-center">
                                    <input
                                        type="checkbox"
                                        id="send_invitation"
                                        v-model="form.send_invitation"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                    />
                                    <label for="send_invitation" class="ml-2 block text-sm text-gray-900">
                                        Send invitation email to this user
                                    </label>
                                </div>

                                <!-- Invitation Message -->
                                <div v-if="form.send_invitation">
                                    <label for="invitation_message" class="block text-sm font-medium text-gray-700">
                                        Custom Invitation Message (Optional)
                                    </label>
                                    <textarea
                                        id="invitation_message"
                                        v-model="form.invitation_message"
                                        rows="3"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Add a personal message to the invitation email..."
                                    ></textarea>
                                </div>

                                <!-- Password Section -->
                                <div v-if="!form.send_invitation" class="space-y-4">
                                    <h3 class="text-lg font-medium text-gray-900">Password</h3>
                                    <p class="text-sm text-gray-600">
                                        Since you're not sending an invitation, you need to set a password for this user.
                                    </p>

                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700">
                                            Password
                                        </label>
                                        <input
                                            type="password"
                                            id="password"
                                            v-model="form.password"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                            :class="{ 'border-red-500': form.errors.password }"
                                            :required="!form.send_invitation"
                                        />
                                        <div v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.password }}
                                        </div>
                                    </div>

                                    <div>
                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                            Confirm Password
                                        </label>
                                        <input
                                            type="password"
                                            id="password_confirmation"
                                            v-model="form.password_confirmation"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                            :required="!form.send_invitation"
                                        />
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="flex items-center justify-end space-x-4 pt-4">
                                    <Link :href="route('tenant.users.index', { subdomain: currentSubdomain })" 
                                          class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                        Cancel
                                    </Link>
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                                    >
                                        {{ form.processing ? 'Creating...' : (form.send_invitation ? 'Send Invitation' : 'Create User') }}
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
    tenant: Object,
    roles: Array,
})

// Extract subdomain from current URL
const currentSubdomain = computed(() => {
    const host = window.location.host;
    const parts = host.split('.');
    return parts.length > 2 ? parts[0] : null;
});

const form = useForm({
    name: '',
    email: '',
    role_id: '',
    send_invitation: true,
    invitation_message: '',
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('tenant.users.store', { subdomain: currentSubdomain.value }))
}
</script>