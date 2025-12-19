<template>
    <AuthenticatedLayout>
        <Head :title="`User Profile - ${user.name}`" />

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-center justify-between mb-6">
                            <h1 class="text-2xl font-semibold text-gray-900">User Profile</h1>
                            <div class="space-x-2">
                                <Link :href="route('tenant.users.edit', { subdomain: currentSubdomain, user: user.id })" 
                                      class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Edit User
                                </Link>
                                <Link :href="route('tenant.users.index', { subdomain: currentSubdomain })" 
                                      class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Back to Users
                                </Link>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Profile Info -->
                            <div class="md:col-span-2">
                                <div class="space-y-6">
                                    <!-- Basic Info -->
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h2 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h2>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="text-sm font-medium text-gray-500">Full Name</label>
                                                <p class="text-gray-900">{{ user.name }}</p>
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-500">Email Address</label>
                                                <p class="text-gray-900">{{ user.email }}</p>
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-500">Role</label>
                                                <span class="inline-flex px-2 py-1 text-sm font-semibold rounded-full"
                                                      :class="getRoleColorClass(user.role?.key)">
                                                    {{ user.role?.name || 'No Role Assigned' }}
                                                </span>
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-500">Status</label>
                                                <span v-if="user.active" class="inline-flex px-2 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                                <span v-else class="inline-flex px-2 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                                    Inactive
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Account Activity -->
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h2 class="text-lg font-medium text-gray-900 mb-4">Account Activity</h2>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="text-sm font-medium text-gray-500">Created</label>
                                                <p class="text-gray-900">{{ formatDate(user.created_at) }}</p>
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-500">Last Updated</label>
                                                <p class="text-gray-900">{{ formatDate(user.updated_at) }}</p>
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-500">Last Login</label>
                                                <p class="text-gray-900">{{ user.last_login_at ? formatDate(user.last_login_at) : 'Never logged in' }}</p>
                                            </div>
                                            <div>
                                                <label class="text-sm font-medium text-gray-500">Email Verified</label>
                                                <span v-if="user.email_verified_at" class="inline-flex px-2 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                                    Verified
                                                </span>
                                                <span v-else class="inline-flex px-2 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Not Verified
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Permissions -->
                                    <div v-if="user.role && user.role.permissions && user.role.permissions.length > 0" 
                                         class="bg-gray-50 p-4 rounded-lg">
                                        <h2 class="text-lg font-medium text-gray-900 mb-4">Role Permissions</h2>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                            <span v-for="permission in user.role.permissions" 
                                                  :key="permission.id"
                                                  class="inline-flex px-2 py-1 text-sm bg-blue-100 text-blue-800 rounded-md">
                                                {{ permission.name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Profile Avatar & Quick Actions -->
                            <div class="space-y-6">
                                <!-- Avatar -->
                                <div class="bg-gray-50 p-4 rounded-lg text-center">
                                    <div class="flex justify-center mb-4">
                                        <div class="h-24 w-24 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-2xl font-medium text-gray-700">
                                                {{ user.name.charAt(0).toUpperCase() }}
                                            </span>
                                        </div>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900">{{ user.name }}</h3>
                                    <p class="text-sm text-gray-500">{{ user.role?.name || 'No Role Assigned' }}</p>
                                </div>

                                <!-- Quick Actions -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h2 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h2>
                                    <div class="space-y-2">
                                        <button v-if="user.id !== $page.props.auth.user.id"
                                                @click="toggleUserStatus" 
                                                :class="user.active 
                                                    ? 'bg-orange-500 hover:bg-orange-700' 
                                                    : 'bg-green-500 hover:bg-green-700'"
                                                class="w-full text-white font-bold py-2 px-4 rounded">
                                            {{ user.active ? 'Deactivate User' : 'Activate User' }}
                                        </button>

                                        <Link v-if="!user.email_verified_at"
                                              :href="route('tenant.users.resend-verification', user.id)"
                                              method="post"
                                              as="button"
                                              class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                            Resend Verification Email
                                        </Link>

                                        <button v-if="user.id !== $page.props.auth.user.id && user.role?.key !== 'owner'"
                                                @click="deleteUser" 
                                                class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                            Delete User
                                        </button>
                                    </div>
                                </div>

                                <!-- User Stats -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h2 class="text-lg font-medium text-gray-900 mb-4">Statistics</h2>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-500">Member Since:</span>
                                            <span class="text-sm text-gray-900">{{ formatMemberSince(user.created_at) }}</span>
                                        </div>
                                        <div v-if="user.last_login_at" class="flex justify-between">
                                            <span class="text-sm text-gray-500">Last Active:</span>
                                            <span class="text-sm text-gray-900">{{ formatLastActive(user.last_login_at) }}</span>
                                        </div>
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
import { Head, Link, router } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    user: Object,
    tenant: Object,
})

// Extract subdomain from current URL
const currentSubdomain = computed(() => {
    const host = window.location.host;
    const parts = host.split('.');
    return parts.length > 2 ? parts[0] : null;
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const formatMemberSince = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatLastActive = (date) => {
    const now = new Date()
    const lastLogin = new Date(date)
    const diffTime = Math.abs(now - lastLogin)
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    
    if (diffDays === 1) return 'Today'
    if (diffDays === 2) return 'Yesterday'
    if (diffDays < 7) return `${diffDays} days ago`
    if (diffDays < 30) return `${Math.ceil(diffDays / 7)} weeks ago`
    return formatMemberSince(date)
}

const getRoleColorClass = (roleKey) => {
    const colors = {
        'owner': 'bg-purple-100 text-purple-800',
        'admin': 'bg-blue-100 text-blue-800',
        'manager': 'bg-green-100 text-green-800',
        'user': 'bg-gray-100 text-gray-800',
    }
    return colors[roleKey] || 'bg-gray-100 text-gray-800'
}

const toggleUserStatus = () => {
    const action = props.user.active ? 'deactivate' : 'activate'
    if (confirm(`Are you sure you want to ${action} ${props.user.name}?`)) {
        router.patch(route('tenant.users.toggle-status', { subdomain: currentSubdomain.value, user: props.user.id }))
    }
}

const deleteUser = () => {
    if (confirm(`Are you sure you want to delete ${props.user.name}? This action cannot be undone.`)) {
        router.delete(route('tenant.users.destroy', { subdomain: currentSubdomain.value, user: props.user.id }), {
            onSuccess: () => router.visit(route('tenant.users.index', { subdomain: currentSubdomain.value }))
        })
    }
}
</script>