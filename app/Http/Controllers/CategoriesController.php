<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Topic;

class CategoriesController extends Controller
{
    public function show(Category $category, Topic $topic)
    {
        $order = request('order');
        // 为什么从控制器调用 model 的时候 不能先调用 where 语句？
        // $topics = Topic::where('category_id', $category->id)->withOrder($order)->paginate(20);
        $topics = Topic::withOrder($order)
                        ->where('category_id', $category->id)
                        ->paginate(20);
        return view('topics.index', compact('topics', 'category'));
    }
}
