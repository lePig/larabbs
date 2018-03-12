<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

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
}
