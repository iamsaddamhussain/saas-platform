<template>
    <AuthenticatedLayout>
        <Head title="Manage Users" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-center justify-between mb-6">
                            <h1 class="text-2xl font-semibold text-gray-900">Manage Users</h1>
                            <Link :href="tenantRoute('tenant.users.create')" 
                                  class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Add New User
                            </Link>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            User
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Role
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Last Login
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="user in users.data" :key="user.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-700">
                                                            {{ user.name.charAt(0).toUpperCase() }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                                    <div v-if="user.id === $page.props.auth.user.id" class="text-xs text-blue-600">(You)</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ user.email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                                  :class="getRoleColorClass(user.role?.key)">
                                                {{ user.role?.name || 'No Role' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="user.active" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Active
                                            </span>
                                            <span v-else class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                Inactive
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ user.last_login_at ? formatDate(user.last_login_at) : 'Never' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <Link :href="tenantRoute('tenant.users.show', { user: user.id })" 
                                                  class="text-indigo-600 hover:text-indigo-900">
                                                View
                                            </Link>
                                            <Link :href="tenantRoute('tenant.users.edit', { user: user.id })" 
                                                  class="text-yellow-600 hover:text-yellow-900">
                                                Edit
                                            </Link>
                                            <button v-if="user.id !== $page.props.auth.user.id"
                                                    @click="toggleUserStatus(user)" 
                                                    :class="user.active ? 'text-orange-600 hover:text-orange-900' : 'text-green-600 hover:text-green-900'">
                                                {{ user.active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                            <button v-if="user.id !== $page.props.auth.user.id && user.role?.key !== 'owner'"
                                                    @click="deleteUser(user)" 
                                                    class="text-red-600 hover:text-red-900">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="users.links" class="mt-4">
                            <nav class="flex items-center justify-between">
                                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm text-gray-700">
                                            Showing {{ users.from }} to {{ users.to }} of {{ users.total }} results
                                        </p>
                                    </div>
                                    <div>
                                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                            <template v-for="link in users.links" :key="link.label">
                                                <Link v-if="link.url" :href="addSubdomainToUrl(link.url)" 
                                                      class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                                                      :class="link.active 
                                                        ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' 
                                                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'"
                                                      v-html="link.label">
                                                </Link>
                                                <span v-else 
                                                      class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500"
                                                      v-html="link.label">
                                                </span>
                                            </template>
                                        </nav>
                                    </div>
                                </div>
                            </nav>
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
import { useTenantRoute } from '@/Composables/useTenantRoute.js'

defineProps({
    users: Object,
    tenant: Object,
})

const { tenantRoute } = useTenantRoute()

// Helper function to add subdomain to pagination URLs
const addSubdomainToUrl = (url) => {
    if (!url) return url;
    
    const currentSubdomain = window.location.host.split('.')[0];
    
    // If the URL is relative, it should work as-is since we're already on the tenant domain
    if (url.startsWith('/')) {
        return url;
    }
    
    // If it's an absolute URL, ensure it has the correct subdomain
    try {
        const urlObj = new URL(url);
        if (!urlObj.host.startsWith(currentSubdomain + '.')) {
            urlObj.host = currentSubdomain + '.' + urlObj.host.split('.').slice(1).join('.');
        }
        return urlObj.toString();
    } catch (e) {
        return url;
    }
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
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

const toggleUserStatus = (user) => {
    const action = user.active ? 'deactivate' : 'activate'
    if (confirm(`Are you sure you want to ${action} ${user.name}?`)) {
        router.patch(tenantRoute('tenant.users.toggle-status', { user: user.id }))
    }
}

const deleteUser = (user) => {
    if (confirm(`Are you sure you want to delete ${user.name}? This action cannot be undone.`)) {
        router.delete(tenantRoute('tenant.users.destroy', { user: user.id }))
    }
}
</script>