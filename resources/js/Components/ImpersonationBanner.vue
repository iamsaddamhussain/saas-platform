<template>
    <div v-if="$page.props.impersonation.is_impersonating" 
         class="bg-orange-500 text-white px-4 py-2 text-center text-sm font-medium relative z-50">
        <div class="flex items-center justify-center space-x-4">
            <span>
                🎭 You are impersonating this tenant as Super Admin
            </span>
            <form @submit.prevent="stopImpersonation" class="inline">
                <button type="submit" 
                        class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded text-xs font-semibold transition-colors duration-200">
                    Stop Impersonation
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'

const stopImpersonation = () => {
    router.post(route('tenant.stop-impersonation'), {}, {
        onSuccess: () => {
            // Will be redirected to super admin dashboard
        }
    })
}
</script>