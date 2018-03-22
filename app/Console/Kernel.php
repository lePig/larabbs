<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        // 一小时执行一次【活跃用户】数据生成命令
        // appendOutputTo('/home/vagrant/cron.log') 追加生成
        // sendOutputTo('/home/vagrant/cron.log') 覆盖生成
        // emailOutputTo('lepig@qq.com') //发送邮件
        // evenInMaintenceMode() //维护模式
        $schedule->command('larabbs:calculate-active-user')->everyFiveMinutes()->appendOutputTo('/home/vagrant/cron.log');

        //每天零点同步用户最后活动时间到users表的last_actived_at字段
        $schedule->command('larabbs:sync-user-actived-at')->dailyAt('00:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
