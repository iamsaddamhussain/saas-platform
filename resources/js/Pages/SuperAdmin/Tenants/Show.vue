<template>
    <AuthenticatedLayout>
        <Head title="Tenant Details" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h1 class="text-2xl font-semibold text-gray-900">{{ tenant.name }}</h1>
                                <p class="text-gray-600">{{ tenant.subdomain }}.saas-platform.localhost</p>
                            </div>
                            <div class="flex space-x-2">
                                <Link :href="route('super-admin.tenants.edit', tenant.id)" 
                                      class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                    Edit
                                </Link>
                                <Link :href="route('super-admin.tenants.index')" 
                                      class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Back to Tenants
                                </Link>
                            </div>
                        </div>

                        <!-- Status and Stats -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-gray-50 p-4 rounded">
                                <h3 class="text-sm font-medium text-gray-500">Status</h3>
                                <p class="mt-1">
                                    <span v-if="tenant.active" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                    <span v-else class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Inactive
                                    </span>
                                </p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded">
                                <h3 class="text-sm font-medium text-gray-500">Total Users</h3>
                                <p class="mt-1 text-lg font-semibold">{{ stats.users_count }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded">
                                <h3 class="text-sm font-medium text-gray-500">Active Users</h3>
                                <p class="mt-1 text-lg font-semibold">{{ stats.active_users_count }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded">
                                <h3 class="text-sm font-medium text-gray-500">Enabled Modules</h3>
                                <p class="mt-1 text-lg font-semibold">{{ stats.modules_count }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tenant Info -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Tenant Information</h3>
                        </div>
                        <div class="p-6">
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ tenant.name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Subdomain</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ tenant.subdomain }}</dd>
                                </div>
                                <div v-if="tenant.domain">
                                    <dt class="text-sm font-medium text-gray-500">Custom Domain</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ tenant.domain }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(tenant.created_at) }}</dd>
                                </div>
                                <div v-if="stats.trial_ends_at">
                                    <dt class="text-sm font-medium text-gray-500">Trial Status</dt>
                                    <dd class="mt-1 text-sm">
                                        <span v-if="stats.is_on_trial" class="text-orange-600">
                                            Trial ends {{ formatDate(stats.trial_ends_at) }}
                                        </span>
                                        <span v-else class="text-red-600">
                                            Trial ended {{ formatDate(stats.trial_ends_at) }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <form @submit.prevent="impersonate" class="w-full">
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Impersonate Tenant
                                </button>
                            </form>
                            <button @click="toggleStatus" 
                                    :class="tenant.active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ tenant.active ? 'Deactivate' : 'Activate' }} Tenant
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Users -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Users</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="user in tenant.users" :key="user.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ user.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.role?.name || 'No Role' }}</td>
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
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Enabled Modules -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Enabled Modules</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div v-for="module in tenant.enabled_modules" :key="module.id" 
                                 class="border border-gray-200 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900">{{ module.name }}</h4>
                                <p class="text-sm text-gray-500 mt-1">{{ module.description }}</p>
                            </div>
                        </div>
                        <div v-if="tenant.enabled_modules.length === 0" class="text-center py-4 text-gray-500">
                            No modules enabled
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

defineProps({
    tenant: Object,
    stats: Object,
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
}

const impersonate = () => {
    if (confirm(`Are you sure you want to impersonate this tenant?`)) {
        router.post(route('super-admin.tenants.impersonate', tenant.id))
    }
}

const toggleStatus = () => {
    const action = tenant.active ? 'deactivate' : 'activate'
    if (confirm(`Are you sure you want to ${action} this tenant?`)) {
        router.patch(route('super-admin.tenants.update', tenant.id), {
            active: !tenant.active
        })
    }
}
</script>