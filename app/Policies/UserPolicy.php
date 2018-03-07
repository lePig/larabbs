<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    // 如果$currentUser中的id和$user中的id一样就表示操作的是同一用户
    // $currentUser不需要传递，框架会自动获取。$user是在客户端(控制器)调用authorize()方法时的第二个参数
    public function update(User $currentUser, User $user)
    {
        return $currentUser->id === $user->id;
    }
}
