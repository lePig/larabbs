<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use App\Models\Category;
use Auth;
use App\Handlers\ImageUploadHandler;
use App\Models\User;
use App\Models\Link;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    // 话题首页列表
	public function index(Request $request, User $user, Link $link)
	{
        // dd($request->order);
        $order = $request->order;
		$topics = Topic::withOrder($order)->paginate(20);

        $active_users = $user->getActiveUsers();
        $links = $link->getAllCached();
		return view('topics.index', compact('topics', 'active_users', 'links'));
	}

    public function show(Topic $topic)
    {
        // 当slug字段有值的时候我们强制跳转(301)到带有slug的url
        if (! empty($topic->slug) && $topic->slug != request()->slug) {
            return redirect($topic->link(), 301);
        }
        return view('topics.show', compact('topic'));
    }

    /**
     * 显示创建话题页面
     * @date   2018-03-12
     */
	public function create(Topic $topic)
	{
        $categories = Category::all();
		return view('topics.create_and_edit', compact('topic', 'categories'));
	}

    /**
     * 发表话题数据入库
     * @date   2018-03-12
     */
	public function store(TopicRequest $request, Topic $topic)
	{
        $topic->fill($request->all()); //fill 方法会将传参的键值数组填充到模型的属性中, 即$topic->title='xxx' $topic->body='xxx' ...
        $topic->user_id = Auth::id();
		// $topic = Topic::create($request->all());
        $topic->save();
        return redirect()->to($topic->link())->with('message', '发表成功');
		// return redirect()->route('topics.show', $topic->id)->with('message', '发表成功');
	}

    /**
     * 编辑页面(和创建页面共用一个模板)
     * @date   2018-03-13
     */
	public function edit(Topic $topic)
	{
        $this->authorize('update', $topic);
        $categories = Category::all();
		return view('topics.create_and_edit', compact('topic', 'categories'));
	}

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

        return redirect()->to($topic->link())->with('message', '更新成功');
		// return redirect()->route('topics.show', $topic->id)->with('message', '更新成功');
	}

	public function destroy(Topic $topic)
	{
		// $this->authorize('destroy1', $topic);
        $this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('message', '已成功删除了话题');
	}

    /**
     * 话题上传图片方法
     * @date   2018-03-12
     */
    public function uploadImage(ImageUploadHandler $uploader)
    {
        $data = [
            'success' => false,
            'msg' => '上传失败',
            'file_path' => '',
        ];

        //判断是否有文件上传
        if (request()->upload_file) {
            $result = $uploader->save(request()->upload_file, 'topics', \Auth::id(), 1024);

            if ($result) {
                $data['success']   = true;
                $data['msg']       = '上传成功';
                $data['file_path'] = $result['path'];
            }
        }

        return $data;
    }
}