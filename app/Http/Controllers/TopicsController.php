<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use App\Models\Category;
use App\Handlers\ImageUploadHandler;
use Auth;
use Mail;
use App\Models\User;
use App\Models\Link;
use App\Models\VisitorRegistry;
// use Visitor;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show', 'zhuzichuan', 'sendEmailConfirmationTo']]);
    }

    public function index(Request $request, Topic $topic, User $user, Link $link)
   {
       $topics = $topic->withOrder($request->order)->paginate(20);
       $active_users = $user->getActiveUsers();
       $links = $link->getAllCached();

       return view('topics.index', compact('topics', 'active_users', 'links'));
   }

   public function zhuzichuan(){
       $user = User::find(1);
       $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
       return view('topics.zhuzichuan');
   }

   protected function sendEmailConfirmationTo($user)
    {
        $view = 'topics.zhuzichuan';
        $to = $user->email;
        $subject = "感谢注册 Sample 应用！请确认你的邮箱。";

        Mail::send($view, ['user' => $user], function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }


    public function show(Request $request, Topic $topic)
    {

        // Visitor::log($topic->id);
        if($this->hasTopic($topic->id) )
        {
            //ip already exist in db.
            $visitor = VisitorRegistry::where('topic_id','=',$topic->id)->first();
            $visitor->update(['clicks'=>$visitor->clicks + 1]);

        }else{


            $result = VisitorRegistry::create([
                'clicks'     => 1,
                'topic_id' => $topic->id,
            ]);

        }
        if ( ! empty($topic->slug) && $topic->slug != $request->slug) {
            return redirect($topic->link(), 301);
        }

        return view('topics.show', compact('topic'));
    }

    public function hasTopic($id)
    {
        return count(VisitorRegistry::where('topic_id','=',$id)->get()) > 0;
    }

	public function create(Topic $topic)
	{
        $categories = Category::all();
		return view('topics.create_and_edit', compact('topic', 'categories'));
	}

	public function store(TopicRequest $request, Topic $topic)
	{

		$topic->fill($request->all());
        $topic->user_id = Auth::id();
        $topic->save();
		return redirect()->to($topic->link())->with('success', '成功创建话题！');
	}

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

		return redirect()->to($topics->link())->with('success', '更新成功！');
	}

	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('success', '删除成功！');
	}

    public function uploadImage(Request $request, ImageUploadHandler $uploader){
        $data = [
            'success' => false,
            'msg' => '上传失败',
            'file_path' => ''
        ];

        if ($file = $request->upload_file) {
            $result = $uploader->save($request->upload_file, 'topics', \Auth::id(), 1024);
            if ($result) {
                $data['file_path'] = $result['path'];
                $data['msg'] = "上传成功";
                $data['success'] = true;
            }
        }
        return $data;
    }
}
