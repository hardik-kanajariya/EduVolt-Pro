<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Permission;

echo "=== USERS AND ROLES ===\n";
$users = User::with('roles')->get();
foreach ($users as $user) {
    $roles = $user->roles->pluck('name')->join(', ');
    echo "{$user->email} - Roles: {$roles}\n";
}

echo "\n=== PERMISSIONS ===\n";
$permissions = Permission::all();
echo "Total permissions: " . $permissions->count() . "\n";

echo "\n=== PERMISSIONS BY MODULE ===\n";
$groupedPermissions = $permissions->groupBy(function ($p) {
    $parts = explode('_', $p->name);
    return $parts[0] ?? 'general';
});

foreach ($groupedPermissions as $module => $perms) {
    echo "{$module}: " . $perms->pluck('name')->join(', ') . "\n";
}

echo "\n=== CRITICAL PERMISSIONS CHECK ===\n";
$critical = ['school_create', 'school_read', 'school_update', 'school_delete', 'user_create', 'user_read', 'user_update', 'user_delete'];
foreach ($critical as $perm) {
    $exists = Permission::where('name', $perm)->exists();
    echo $exists ? "✅ {$perm}" : "❌ {$perm}";
    echo "\n";
}
