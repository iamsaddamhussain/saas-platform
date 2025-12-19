import { computed } from 'vue'

export function useTenantRoute() {
    // Extract subdomain from current URL
    const currentSubdomain = computed(() => {
        const host = window.location.host;
        const parts = host.split('.');
        return parts.length > 2 ? parts[0] : null;
    });

    // Helper function to generate tenant routes with automatic subdomain
    const tenantRoute = (name, parameters = {}) => {
        if (currentSubdomain.value && name.startsWith('tenant.')) {
            return route(name, { subdomain: currentSubdomain.value, ...parameters });
        }
        return route(name, parameters);
    };

    return {
        currentSubdomain,
        tenantRoute
    };
}