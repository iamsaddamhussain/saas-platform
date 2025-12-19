<template>
    <AuthenticatedLayout>
        <Head title="Tenant Dashboard" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Tenant Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ tenant.name }}</h1>
                                <p class="text-sm text-gray-500">{{ tenant.subdomain }}.{{ $page.props.ziggy.location.split('.').slice(-2).join('.') }}</p>
                            </div>
                            <div v-if="stats.is_on_trial" class="bg-orange-100 border border-orange-400 text-orange-700 px-3 py-1 rounded">
                                <span class="text-xs font-semibold">TRIAL</span>
                                <span class="text-xs ml-1" v-if="stats.trial_ends_at">
                                    Ends {{ formatDate(stats.trial_ends_at) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Overview -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <UsersIcon class="h-8 w-8 text-blue-500" />
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
                                    <CheckCircleIcon class="h-8 w-8 text-green-500" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Active Users</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ stats.active_users }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <Squares2X2Icon class="h-8 w-8 text-purple-500" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Enabled Modules</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ stats.enabled_modules }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <ChartBarIcon class="h-8 w-8 text-orange-500" />
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Storage Used</p>
                                    <p class="text-2xl font-semibold text-gray-900">0 MB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modules Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <div v-for="module in enabledModules" :key="module.id" 
                         class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200 cursor-pointer">
                        <div class="p-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <component :is="getModuleIcon(module.icon)" class="h-8 w-8 text-gray-600" />
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900">{{ module.name }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ module.description }}</p>
                                    <div class="mt-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Recent Users -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-gray-900">Recent Users</h3>
                                <Link :href="tenantRoute('tenant.users.index')" 
                                      class="text-sm text-blue-600 hover:text-blue-900">
                                    View All
                                </Link>
                            </div>
                        </div>
                        <div class="p-6">
                            <div v-if="recentUsers.length > 0" class="space-y-4">
                                <div v-for="user in recentUsers" :key="user.id" class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700">
                                                    {{ user.name.charAt(0).toUpperCase() }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
                                            <p class="text-xs text-gray-500">{{ user.role?.name || 'No Role' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500" v-if="user.last_login_at">
                                            Last login: {{ formatDateRelative(user.last_login_at) }}
                                        </p>
                                        <p class="text-xs text-gray-400" v-else>Never logged in</p>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-4">
                                <UsersIcon class="mx-auto h-12 w-12 text-gray-400" />
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No users yet</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by inviting your first user.</p>
                                <div class="mt-3">
                                    <Link :href="tenantRoute('tenant.users.create')" 
                                          class="inline-flex items-center px-3 py-2 border border-transparent shadow-sm text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <UserPlusIcon class="-ml-0.5 mr-2 h-4 w-4" />
                                        Invite User
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <Link :href="tenantRoute('tenant.users.create')" 
                                      class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <UserPlusIcon class="h-4 w-4 mr-2" />
                                    Invite User
                                </Link>
                                
                                <Link :href="tenantRoute('tenant.users.index')" 
                                      class="w-full inline-flex items-center justify-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <UsersIcon class="h-4 w-4 mr-2" />
                                    Manage Users
                                </Link>
                                
                                <Link :href="tenantRoute('tenant.settings.index')" 
                                      class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <CogIcon class="h-4 w-4 mr-2" />
                                    Settings
                                </Link>
                                
                                <Link :href="tenantRoute('tenant.modules.index')" 
                                      class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <Squares2X2Icon class="h-4 w-4 mr-2" />
                                    Manage Modules
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useTenantRoute } from '@/Composables/useTenantRoute.js'
import { 
    UsersIcon, 
    CheckCircleIcon,
    Squares2X2Icon, 
    ChartBarIcon,
    UserPlusIcon,
    CogIcon,
    HomeIcon,
    BriefcaseIcon,
    AcademicCapIcon,
    BanknotesIcon,
    BuildingOfficeIcon,
    ClipboardDocumentListIcon
} from '@heroicons/vue/24/outline'

defineProps({
    tenant: Object,
    user: Object,
    stats: Object,
    enabledModules: Array,
    recentUsers: Array,
})

// Use tenant route helper
const { tenantRoute } = useTenantRoute()

// Date formatting functions
const formatDate = (date) => {
    if (!date) return 'Never';
    return new Date(date).toLocaleDateString();
};

const formatDateRelative = (date) => {
    if (!date) return 'Never';
    const now = new Date();
    const then = new Date(date);
    const diffTime = Math.abs(now - then);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 1) return 'Today';
    if (diffDays === 2) return 'Yesterday';
    if (diffDays < 7) return `${diffDays} days ago`;
    if (diffDays < 30) return `${Math.ceil(diffDays / 7)} weeks ago`;
    return formatDate(date);
};

const getModuleIcon = (iconName) => {
    const icons = {
        'home': HomeIcon,
        'users': UsersIcon,
        'cog-6-tooth': CogIcon,
        'user-group': UsersIcon,
        'academic-cap': AcademicCapIcon,
        'briefcase': BriefcaseIcon,
        'banknotes': BanknotesIcon,
        'building-office': BuildingOfficeIcon,
        'clipboard-document-list': ClipboardDocumentListIcon,
    }
    return icons[iconName] || Squares2X2Icon
}
</script>