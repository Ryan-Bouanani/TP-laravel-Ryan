<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $admin = Role::create(['name' => 'admin']);
        $user = Role::create(['name' => 'user']);

        Permission::create(['name' => 'create plats']);
        Permission::create(['name' => 'delete plats']);
        Permission::create(['name' => 'edit plats']);

        // Adding permissions to admin and user
        $admin->givePermissionTo("create plats", 'edit plats', 'delete plats');
        $user->givePermissionTo("create plats", "edit plats");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
