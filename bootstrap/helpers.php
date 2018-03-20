<?php

function route_class() {
    return str_replace('.', '-', Route::currentRouteName());
}

function make_excerpt($value, $length = 200) {
    $excerpt = trim(preg_replace('/\r\n|\r|\n+/', ' ', strip_tags($value)));

    return str_limit($excerpt, $length);
}


function model_admin_link($title, $model)
{
    return model_link($title, $model, 'admin');
}

function model_link($title, $model, $prefix = '') {

    // dd($model);
    // 获取数据模型名称复数的蛇形命名
    $model_name = model_plural_name($model);

    // 初始化前缀
    $prefix = $prefix ? "/$prefix/" : '/';

    // 使用站点URL拼接全量URL
    $url = config('app.url') . $prefix . $model_name . '/' . $model->id;
    // dd($url);
    // 拼接A标签返回
    return '<a href="'. $url. '" target="_blank"> '.$title. '</a>';
}

// 转换为带有下划线的变量
function model_plural_name($model) {
    $full_class_name = get_class($model);


    // 获取基础类名，例如传参为App\Models\User 会返回User
    $class_name = class_basename($full_class_name);

    // 蛇形命名
    $snake_case_name = snake_case($class_name);

    // 获取子串的复数形式，例如：传参 `user` 会得到 `users`
    return str_plural($snake_case_name);
}