<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SeedRolesAndPermissionsData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $roles = [
            ['name' => '游客']
        ];

        // 清除缓存？
        app()['cache']->forget('spatie.permission.cache');

        // 先创建权限(管理用户内容权限、管理用户权限、管理站点权限)
        Permission::create(['name' => 'manage_contents']);
        Permission::create(['name' => 'manage_users']);
        Permission::create(['name' => 'edit_settings']);

        // 先创建一个站长角色，并赋予权限
        $founder = Role::create(['name' => 'Founder']);
        $founder->givePermissionTo('manage_users');
        $founder->givePermissionTo('manage_contents');
        $founder->givePermissionTo('edit_settings');

        // 在创建一个管理员角色
        $maintainer  = Role::create(['name' => 'Maintainer']);
        $maintainer ->givePermissionTo('manage_contents');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        app()['cache']->forget('spatie.permission.cache');

        // 清空所有数据表数据
        $tableNames = config('permission.table_names');
        Model::unguard(); // 这个不知道是干啥用的?

        DB::table($tableNames['roles'])->delete();
        DB::table($tableNames['permissions'])->delete();
        DB::table($tableNames['model_has_permissions'])->delete();
        DB::table($tableNames['model_has_roles'])->delete();
        DB::table($tableNames['role_has_permissions'])->delete();

        Model::reguard();
    }
}
