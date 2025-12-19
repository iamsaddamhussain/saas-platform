<template>
    <AuthenticatedLayout>
        <Head title="Super Admin Dashboard" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stats Overview -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <BuildingOfficeIcon class="h-8 w-8 text-blue-500" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Total Tenants</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ stats.total_tenants }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <CheckCircleIcon class="h-8 w-8 text-green-500" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Active Tenants</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ stats.active_tenants }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <UsersIcon class="h-8 w-8 text-purple-500" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Total Users</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ stats.total_users }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <ClockIcon class="h-8 w-8 text-orange-500" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Trial Ending Soon</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ stats.trial_ending_soon }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Tenants and Module Usage -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Recent Tenants -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Tenants</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div v-for="tenant in recentTenants.data" :key="tenant.id" class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ tenant.name }}</p>
                                        <p class="text-sm text-gray-500">{{ tenant.subdomain }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-500">{{ tenant.users_count }} users</p>
                                        <Link :href="route('super-admin.tenants.show', tenant.id)" 
                                              class="text-sm text-blue-600 hover:text-blue-800">
                                            View
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Module Usage -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Module Usage</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div v-for="module in moduleUsage" :key="module.key" class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ module.name }}</p>
                                        <p class="text-sm text-gray-500">{{ module.enabled_count }} tenants</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" 
                                                 :style="{ width: module.usage_percentage + '%' }"></div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">{{ module.usage_percentage }}%</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex space-x-4">
                            <Link :href="route('super-admin.tenants.create')" 
                                  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <PlusIcon class="h-4 w-4 mr-2" />
                                Create Tenant
                            </Link>
                            <Link :href="route('super-admin.tenants.index')" 
                                  class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <BuildingOfficeIcon class="h-4 w-4 mr-2" />
                                Manage Tenants
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { BuildingOfficeIcon, CheckCircleIcon, UsersIcon, ClockIcon, PlusIcon } from '@heroicons/vue/24/outline'

defineProps({
    stats: Object,
    recentTenants: Object,
    recentUsers: Object,
    moduleUsage: Array,
})
</script>