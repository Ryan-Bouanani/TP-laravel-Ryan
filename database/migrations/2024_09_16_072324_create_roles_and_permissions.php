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

        Permission::create(['name' => 'create dishes']);
        Permission::create(['name' => 'delete dishes']);
        Permission::create(['name' => 'edit dishes']);

        // Adding permissions to admin and user
        $admin->givePermissionTo("create dishes", 'edit dishes', 'delete dishes');
        $user->givePermissionTo("create dishes", "edit dishes");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
