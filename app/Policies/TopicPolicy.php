<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Topic;

class TopicPolicy extends Policy
{
    public function update(User $user, Topic $topic)
    {
        // return $topic->user_id == $user->id;
        // 编辑话题的时候 如果当前登录用户id和话题作者id相同就放行
        // 第一个参数框架会自动获取，第二个参数是在TopicsController中调用authorize方法传递的第二个参数,即topic模型实例
        return $topic->user_id == $user->id;
        // return true;
    }

    public function destroy(User $user, Topic $topic)
    {
        return $user->id == $topic->user_id;
        // return true;
    }

    /**
     * 由于上面2个方法一直写(return $user->id == $topic->user_id) 所以重构此方法
     * @date   2018-03-13
     */
    public function isAuthorOf(User $user, Topic $model)
    {
        return $user->id == $model->user_id;
    }
}
