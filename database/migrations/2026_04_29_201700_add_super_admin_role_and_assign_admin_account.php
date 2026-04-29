<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $allPermissions = Role::allPermissionSlugs();
        DB::table('roles')->updateOrInsert(
            ['name' => Role::SUPER_ADMIN_NAME],
            [
                'permissions' => json_encode($allPermissions, JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        // updateOrInsert returns bool; fetch role id after upsert.
        $roleId = DB::table('roles')
            ->where('name', Role::SUPER_ADMIN_NAME)
            ->value('id');

        if ($roleId) {
            DB::table('users')
                ->where('email', 'admin@sanghaexam.org')
                ->update([
                    'role_id' => $roleId,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        $roleId = DB::table('roles')
            ->where('name', Role::SUPER_ADMIN_NAME)
            ->value('id');

        if ($roleId) {
            DB::table('users')
                ->where('role_id', $roleId)
                ->update([
                    'role_id' => null,
                    'updated_at' => now(),
                ]);

            DB::table('roles')->where('id', $roleId)->delete();
        }
    }
};
