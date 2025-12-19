<template>
    <AuthenticatedLayout>
        <Head title="Manage Tenants" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-center justify-between mb-6">
                            <h1 class="text-2xl font-semibold text-gray-900">Manage Tenants</h1>
                            <Link :href="route('super-admin.tenants.create')" 
                                  class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create New Tenant
                            </Link>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tenant
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Subdomain
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Users
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Created
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="tenant in tenants.data" :key="tenant.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ tenant.name }}</div>
                                                <div class="text-sm text-gray-500" v-if="tenant.domain">{{ tenant.domain }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-900">{{ tenant.subdomain }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ tenant.users_count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="tenant.active" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Active
                                            </span>
                                            <span v-else class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                Inactive
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(tenant.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <Link :href="route('super-admin.tenants.show', tenant.id)" 
                                                  class="text-indigo-600 hover:text-indigo-900">
                                                View
                                            </Link>
                                            <Link :href="route('super-admin.tenants.edit', tenant.id)" 
                                                  class="text-yellow-600 hover:text-yellow-900">
                                                Edit
                                            </Link>
                                            <form @submit.prevent="impersonate(tenant)" class="inline">
                                                <button type="submit" class="text-blue-600 hover:text-blue-900">
                                                    Impersonate
                                                </button>
                                            </form>
                                            <button @click="deleteTenant(tenant)" 
                                                    class="text-red-600 hover:text-red-900">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="tenants.links" class="mt-4">
                            <nav class="flex items-center justify-between">
                                <div class="flex-1 flex justify-between sm:hidden">
                                    <Link v-if="tenants.prev_page_url" :href="tenants.prev_page_url" 
                                          class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Previous
                                    </Link>
                                    <Link v-if="tenants.next_page_url" :href="tenants.next_page_url" 
                                          class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Next
                                    </Link>
                                </div>
                                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm text-gray-700">
                                            Showing {{ tenants.from }} to {{ tenants.to }} of {{ tenants.total }} results
                                        </p>
                                    </div>
                                    <div>
                                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                            <template v-for="link in tenants.links" :key="link.label">
                                                <Link v-if="link.url" :href="link.url" 
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

defineProps({
    tenants: Object,
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
}

const impersonate = (tenant) => {
    if (confirm(`Are you sure you want to impersonate ${tenant.name}?`)) {
        router.post(route('super-admin.tenants.impersonate', tenant.id))
    }
}

const deleteTenant = (tenant) => {
    if (confirm(`Are you sure you want to delete ${tenant.name}? This action cannot be undone.`)) {
        router.delete(route('super-admin.tenants.destroy', tenant.id))
    }
}
</script>