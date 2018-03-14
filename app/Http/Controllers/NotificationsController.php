<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class NotificationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        // dd($notifications);
        // 标记已读 未读数量清零
        Auth::user()->makeAsRead();
        return view('notifications.index', compact('notifications'));
    }

}
