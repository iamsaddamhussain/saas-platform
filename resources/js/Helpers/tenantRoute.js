// Tenant route helper that automatically includes subdomain
export function tenantRoute(name, parameters = {}) {
    // Extract subdomain from current URL
    const host = window.location.host;
    const parts = host.split('.');
    const subdomain = parts.length > 2 ? parts[0] : null;
    
    if (subdomain && name.startsWith('tenant.')) {
        return route(name, { subdomain, ...parameters });
    }
    
    return route(name, parameters);
}