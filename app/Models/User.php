<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;

class User extends Authenticatable
{
    // use Notifiable;
    use Notifiable {
        notify as protected laravelNotify;
    }

    // 没太明白这里的写法意思。 还得补下基础
    // --2018-03-15 update---
    // 上面的use Notifiable {notify as protected laravelNotify}的意思是将Notifiable这个trait中的notify方法做了一个别名为laravelNotify
    // 因为在本类(User)中，也同样定义了一个notify方法，这个方法会覆盖trait中的notify，因为要继续在本类中使用trait中的notify所以进行了别名
    public function notify($instance)
    {
        // 如果要通知的人是当前用户，就不必通知了！
        if ($this->id == Auth::id()) {
            return ;
        }

        $this->increment('notification_count', +1);
        $this->laravelNotify($instance);
    }

    /**
     * 标记所有的消息为已读状态
     */
    public function makeAsRead()
    {
        $this->notification_count = 0;

        $this->save();

        $this->unreadNotifications->makeAsRead();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'introduction', 'avatar'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    /**
     * 该用户发表的所有话题列表[一对多模型]
     * @date   2018-03-12
     */
    public function topics()
    {
        /**
         * 第一个参数是你要has的模型类名
         * 第二个参数是你要has模型的foreign_key 也就是在别的表中用户id的字段名，可以省略
         * 第三个参数是上面foreign_key对应本表(users表)中的字段名，默认为id 可省略
         */
        return $this->hasMany(Topic::class, 'user_id', 'id');
    }

    public function replies()
    {
        return $this->hasMany(Reply::class, 'user_id', 'id');
    }
}
