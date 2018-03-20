<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Handlers\SlugTranslateHandler;
use App\Models\User;

class TestController extends Controller
{
    public function http()
    {
        // dd(\Route::currentRouteAction());
        // dd(\Route::currentRouteName());
        // $slug = new SlugTranslateHandler();
        // $text = '深入理解nginx与php交互';
        // return $slug->translate($text);

        // echo model_link('abc', app(\App\Models\Topic::class), 'admin');

        $user = new User;

        $user->calculateActiveUsers();
        // $user->calculate
    }
}
