<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Topic;
use App\Models\User;
use App\Models\Link;

class CategoriesController extends Controller
{
    public function show_bak(Category $category, Topic $topic, User $user, Link $link)
    {
        $order = request('order');
        // 为什么从控制器调用 model 的时候 不能先调用 where 语句？
        // $topics = $topic->where('category_id', $category->id)->withOrder($order)->paginate(20);
        $topics = $topic->withOrder($order)->where('category_id', $category->id)->paginate(20);
        // dd($topics);
        // http://blog.webfsd.com/post_laravel-cha-xun-zuo-yong-yu.html (这篇文章就是先调用的where 待研究)
        // $topics = Topic::withOrder($order)
        //                 ->where('category_id', $category->id)
        //                 ->paginate(20);
        $active_users = $user->getActiveUsers();
        $links = $link->getAllCached();
        return view('topics.index', compact('topics', 'category', 'active_users', 'links'));
    }

    public function show(Category $category, Topic $topic, User $user, Link $link)
    {
        $order = request('order');
        // 为什么从控制器调用 model 的时候 不能先调用 where 语句？
        /**
         * update 2018-04-16
         * 修改了模型Topic中的scopeWithOrder方法
         * $query = $this->recent() 为 $query ->recent()
         * $query = $this->recentReplied() 为　$query ->recentReplied();
         * 这样的话无论先调用withOrder还是where都正常
         * 原因是，上面的$query被重新赋值了(https://laravel-china.org/topics/8634/why-cant-the-where-statement-be-called-first-when-the-controller-calls-model)
         */
        $topics = $topic->where('category_id', $category->id)->withOrder($order)->paginate(20);
        // $topics = $topic->withOrder($order)->where('category_id', $category->id)->paginate(20);

        $active_users = $user->getActiveUsers();
        $links = $link->getAllCached();
        return view('topics.index', compact('topics', 'category', 'active_users', 'links'));
    }
}
