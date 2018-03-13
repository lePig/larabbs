<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Handlers\SlugTranslateHandler;

class TestController extends Controller
{
    public function http()
    {
        // dd(\Route::currentRouteAction());
        // dd(\Route::currentRouteName());
        $slug = new SlugTranslateHandler();
        $text = '深入理解nginx与php交互';
        return $slug->translate($text);
    }
}
