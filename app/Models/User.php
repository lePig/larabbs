<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use Spatie\Permission\Traits\HasRoles;
// use App\Models\Traits\ActiveUserHelper;


class User extends Authenticatable
{
    use HasRoles; //使用laravel-permission包提供的trait

    use Notifiable {
        notify as protected laravelNotify;
    }
    // 没太明白这里的写法意思。 还得补下基础
    // --2018-03-15 update---
    // 上面的use Notifiable {notify as protected laravelNotify}的意思是将Notifiable这个trait中的notify方法做了一个别名为laravelNotify
    // 因为在本类(User)中，也同样定义了一个notify方法，这个方法会覆盖trait中的notify，因为要继续在本类中使用trait中的notify所以进行了别名
    /** ---------------------------------------------------------------------------------------------------------------------------------------- */
    use Traits\ActiveUserHelper;
    use Traits\LastActivedAtHelper;


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
    public function markAsRead()
    {
        $this->notification_count = 0;

        $this->save();

        $this->unreadNotifications->markAsRead();
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

    /**
     * 由于topic授权类时间里一直写(return $user->id == $topic->user_id) 所以在user模型中重构此方法
     * 这样其他的策略类里可以直接使用$user->isAuthorOf('策略模型')来调用
     * @date   2018-03-13
     */
    public function isAuthorOf($model)
    {
        return $this->id == $model->user_id;
    }

    // Eloquent的修改器机制，来修改密码
    public function setPasswordAttribute($value)
    {
        // 这一步判断是因为重置密码哪里已经加完密了
        if (strlen($value) != 60){
            $value = bcrypt($value);
        }

        $this->attributes['password'] = $value;
    }

    // 使用修改器来修改头像地址
    public function setAvatarAttribute($value)
    {

        if (strpos($value, 'http') === false) {
            $value = config('app.url') . '/uploads/images/avatars/' . $value;
        }

        $this->attributes['avatar'] = $value;

        //laravel也提供一个starts_with函数来更好封装了strpos
        // if (! start_with($value, 'http')) {}
    }
}
