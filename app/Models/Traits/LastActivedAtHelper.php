<?php
namespace App\Models\Traits;

// use Redis;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use DB;

trait LastActivedAtHelper {
    protected $hash_prefix = 'larabbs_last_active_at_';
    protected $field_prefix = 'user_';


    /**
     * 将登录时间写入到Redis的hash表中
     * @date   2018-03-21
     */
    public function recordLastActivedAt()
    {
        // 获取今天的日期
        $date = Carbon::now()->toDateString();
        // \Log::info($date);

        //hash表名
        $hash = $this->hash_prefix . $date;

        // dd(Redis::hgetall($hash));

        //hash表里字段名
        $field = $this->field_prefix . $this->id; //这个$this表示被哪个模型引用就表示那个模型的实例

        // 当前时间 如：2017-10-21 08:35:15
        $now = Carbon::now()->toDateTimeString();

        //写入数据，字段已存在会被更新
        Redis::hSet($hash, $field, $now);

    }

    /**
     * 同步Redis中数据到数据库中
     * @date   2018-03-21
     */
    public function syncUserActivedAt()
    {
        // 获取昨天的日期
        $yesterday = Carbon::yesterday()->toDateString();
        // $yesterday = Carbon::now()->toDateString();

        $hash = $this->hash_prefix . $yesterday;

        $data = Redis::hGetAll($hash);

        foreach ($data as $user_id => $actived_at) {
            $uid = str_replace($this->field_prefix, '', $user_id); //这里的user_id的值是类似user_1、user_2、user_3
            \Log::info($uid);
            //DB::table('users')->where('id', $uid)->update(['last_actived_at' => $actived_at]);
            //另一种入库写法
            if ($user = $this->find($uid)) {
                $user->last_actived_at = $actived_at;
                $user->save();
            }
        }

        Redis::del($hash);
    }

    //访问器
    public function getLastActivedAtAttribute($value)
    {
        $data = Carbon::now()->toDateString();

        $hash = $this->hash_prefix . $data;

        $field = $this->field_prefix . $this->id;

        // 三元运算符，优先选择 Redis 的数据，否则使用数据库中
        $datetime = Redis::hGet($hash, $field) ?: $value;

        // 如果存在的话，返回时间对应的 Carbon 实体
        if ($datetime) {
            return new Carbon($datetime); //因为模板中使用了diffForHumans()所以要返回一个Carbon实体
        } else {
            return $this->created_at;
        }
    }
}