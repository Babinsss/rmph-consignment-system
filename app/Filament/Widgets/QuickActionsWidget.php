<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class QuickActionsWidget extends Widget
{
    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.quick-actions-widget';

    public function getUserData(): array
    {
        $user = Auth::user();

        $roleLabel = match ($user?->role) {
            'admin' => 'Hospital Administrator',
            'pharmacy_staff' => 'Supervising Pharmacist',
            'medical_supply_staff' => 'Central CSR Materials Officer',
            'department_requester' => 'Ward Requisition Staff',
            default => 'Staff Member',
        };

        $roleBadgeColor = match ($user?->role) {
            'admin' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300',
            'pharmacy_staff' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
            'medical_supply_staff' => 'bg-teal-100 text-teal-800 dark:bg-teal-900/40 dark:text-teal-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300',
        };

        return [
            'name' => $user?->name ?? 'Staff User',
            'roleLabel' => $roleLabel,
            'roleBadgeColor' => $roleBadgeColor,
            'department' => ucfirst($user?->department ?? 'General'),
        ];
    }
}
