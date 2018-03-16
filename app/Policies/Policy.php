<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class Policy
{
    use HandlesAuthorization;

    public function __construct()
    {
        //
    }

    public function before($user, $ability)
	{
        // 判断如果此用户有内容管理权限，就返回true
        if ($user->can('manage_contents')) {
            return true;
        }
	}
}
