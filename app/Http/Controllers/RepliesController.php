<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReplyRequest;
use Auth;

class RepliesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }


	public function store(ReplyRequest $request, Reply $reply)
	{
		// $reply = Reply::create($request->all());
		// return redirect()->route('replies.show', $reply->id)->with('message', 'Created successfully.');
        $reply->topic_id = $request->topic_id;
        $reply->content  = $request->content;
        $reply->user_id  = Auth::id();
        $reply->save();

        return redirect()->to($reply->topic->link())->with('message', '发布评论成功');
	}

	public function destroy(Reply $reply)
	{
		$this->authorize('destroy', $reply);
		$reply->delete();

		// return redirect()->route('replies.index')->with('message', 'Deleted successfully.');
        return redirect()->to($reply->topic->link())->with('message', '删除评论成功');
	}
}