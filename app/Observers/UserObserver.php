<?php

namespace App\Observers;

use App\Models\User;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class UserObserver
{
    public function creating(User $user)
    {
        //
    }

    public function updating(User $user)
    {
        //
    }

    /**
     * 模型监控器 对没有头像的用户赋予默认头像
     * (avatar: https://fsdhubcdn.phphub.org/uploads/images/201710/30/1/TrJS40Ey5k.png)
     * @date   2018-03-22
     * @return [type]     [description]
     */
    public function saving(User $user)
    {
        if (empty($user->avatar)) {
            $user->avatar = 'https://fsdhubcdn.phphub.org/uploads/images/201710/30/1/TrJS40Ey5k.png';
        }
    }
}