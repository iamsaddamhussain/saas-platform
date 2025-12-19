<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        $tenant = app('tenant');
        $user = auth()->user();

        return Inertia::render('Tenant/Settings/Index', [
            'tenant' => $tenant,
            'user' => $user,
            'timezones' => $this->getTimezones(),
        ]);
    }

    public function update(Request $request)
    {
        $tenant = app('tenant');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'primary_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
            'timezone' => 'nullable|string|max:50',
            'locale' => 'nullable|string|max:10',
        ]);

        // Update basic tenant info
        $tenant->update([
            'name' => $validated['name'],
            'primary_color' => $validated['primary_color'] ?? '#3B82F6',
            'timezone' => $validated['timezone'] ?? 'UTC',
            'locale' => $validated['locale'] ?? 'en',
        ]);

        return back()->with('success', 'Settings updated successfully');
    }

    public function updateLogo(Request $request)
    {
        $tenant = app('tenant');

        $request->validate([
            'logo' => 'required|image|max:2048', // 2MB max
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($tenant->logo_path) {
                Storage::disk('public')->delete($tenant->logo_path);
            }

            // Store new logo
            $logoPath = $request->file('logo')->store('tenant-logos', 'public');
            
            $tenant->update(['logo_path' => $logoPath]);
        }

        return back()->with('success', 'Logo updated successfully');
    }

    public function deleteLogo()
    {
        $tenant = app('tenant');

        if ($tenant->logo_path) {
            Storage::disk('public')->delete($tenant->logo_path);
            $tenant->update(['logo_path' => null]);
        }

        return back()->with('success', 'Logo deleted successfully');
    }

    private function getTimezones()
    {
        return [
            ['value' => 'UTC', 'label' => 'UTC'],
            ['value' => 'America/New_York', 'label' => 'Eastern Time (US & Canada)'],
            ['value' => 'America/Chicago', 'label' => 'Central Time (US & Canada)'],
            ['value' => 'America/Denver', 'label' => 'Mountain Time (US & Canada)'],
            ['value' => 'America/Los_Angeles', 'label' => 'Pacific Time (US & Canada)'],
            ['value' => 'Europe/London', 'label' => 'London'],
            ['value' => 'Europe/Berlin', 'label' => 'Berlin'],
            ['value' => 'Europe/Paris', 'label' => 'Paris'],
            ['value' => 'Asia/Tokyo', 'label' => 'Tokyo'],
            ['value' => 'Asia/Shanghai', 'label' => 'Shanghai'],
            ['value' => 'Asia/Dubai', 'label' => 'Dubai'],
            ['value' => 'Australia/Sydney', 'label' => 'Sydney'],
            ['value' => 'Pacific/Auckland', 'label' => 'Auckland'],
        ];
    }
}