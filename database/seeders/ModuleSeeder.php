<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            [
                'name' => 'Dashboard',
                'key' => 'dashboard',
                'description' => 'Main dashboard with overview and widgets',
                'icon' => 'home',
                'core' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'User Management',
                'key' => 'users',
                'description' => 'Manage users, roles, and permissions',
                'icon' => 'users',
                'core' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Settings',
                'key' => 'settings',
                'description' => 'Tenant settings and configuration',
                'icon' => 'cog-6-tooth',
                'core' => true,
                'sort_order' => 99,
            ],
            [
                'name' => 'CRM',
                'key' => 'crm',
                'description' => 'Customer Relationship Management',
                'icon' => 'user-group',
                'core' => false,
                'sort_order' => 10,
            ],
            [
                'name' => 'School Management',
                'key' => 'school',
                'description' => 'School management system with students, classes, and attendance',
                'icon' => 'academic-cap',
                'core' => false,
                'sort_order' => 11,
            ],
            [
                'name' => 'HR Management',
                'key' => 'hr',
                'description' => 'Human Resources management system',
                'icon' => 'briefcase',
                'core' => false,
                'sort_order' => 12,
            ],
            [
                'name' => 'Finance',
                'key' => 'finance',
                'description' => 'Financial management and accounting',
                'icon' => 'banknotes',
                'core' => false,
                'sort_order' => 13,
            ],
            [
                'name' => 'Real Estate',
                'key' => 'real_estate',
                'description' => 'Real estate management system',
                'icon' => 'building-office',
                'core' => false,
                'sort_order' => 14,
            ],
            [
                'name' => 'Project Management',
                'key' => 'projects',
                'description' => 'Project and task management',
                'icon' => 'clipboard-document-list',
                'core' => false,
                'sort_order' => 15,
            ],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate(
                ['key' => $module['key']],
                $module
            );
        }
    }
}