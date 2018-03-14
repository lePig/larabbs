<?php

use Illuminate\Database\Seeder;
use App\Models\Reply;
use App\Models\User;
use App\Models\Topic;

/**
 * 假数据生成逻辑
 */
class ReplysTableSeeder extends Seeder
{
    public function run()
    {
        // 所有用户ID数组
        $user_ids = User::all()->pluck('id')->toArray();

        // 所有话题ID数组
        $topic_ids = Topic::all()->pluck('id')->toArray();

        // 获取Faker实例
        $faker = app(Faker\Generator::class);

        $replys = factory(Reply::class)->times(50)->make()->each(function ($reply, $index) use($user_ids, $topic_ids, $faker) {
            //从user_ids中随机取出一个赋值
            $reply->user_id = $faker->randomElement($user_ids);

            //同上 从topic_ids随机取一个
            $reply->topic_id = $faker->randomElement($topic_ids);

            // $reply->content = $faker->sentence();
        });

        Reply::insert($replys->toArray());
    }

}

