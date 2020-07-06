<?php


namespace App\Http\Controllers;


use App\Model\NewsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Validator;


class NewsController extends Controller
{
    /**
     * 数据列表
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('news.index', ['news' => NewsModel::all()]);
    }

    /**
     * 保存
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        $news = new NewsModel();
        $input = Input::except('_token');
        $rules = [
            'title'=> 'required',
            'intro'=> 'required',
        ];
        $massage = [
            'title.required' =>'标题不能为空',
            'intro.required' =>'内容不能为空',
        ];

        $validator = \Validator::make($input,$rules,$massage);
        $res = $validator->passes();
        if($res === false){
            return back() -> withErrors($validator);
        }
        $news->title = $input["title"];
        $news->intro = $input["intro"];
        $data = $news->save();
        if ($data) {
            echo "<script>alert('添加成功');location.href='/news'</script>";
        }
    }

    /**
     * 添加
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        return view('news.create');
    }

    /**
     * 查看
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        $newsItem = NewsModel::where(["id"=>$id])->first();
        return view("news.update",["newsItem"=>$newsItem]);
    }

    /**
     * 修改
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(Request $request, int $id)
    {
        $input = Input::except('_token');
        $news = NewsModel::where(["id"=>$id])->first();
        if (empty($news)){
            echo "<script>alert('修改失败!新闻不存在');location.href='/news'</script>";
        }
        $rules = [
            'title'=> 'required',
            'intro'=> 'required',
        ];
        $massage = [
            'title.required' =>'标题不能为空',
            'intro.required' =>'内容不能为空',
        ];

        $validator = \Validator::make($input,$rules,$massage);
        $res = $validator->passes();
        if($res === false){
            return back() -> withErrors($validator);
        }
        $news->title = $input["title"];
        $news->intro = $input["intro"];
        $data = $news->save();
        if ($data){
            echo "<script>alert('修改成功');location.href='/news'</script>";
        }
    }

    /**
     * 删除
     *
     * @param Request $request
     * @param int $id
     */
    public function del(Request $request, int $id)
    {
        $news = NewsModel::where(["id"=>$id])->first();
        if (empty($news)){
            echo "<script>alert('删除失败,新闻不存在');location.href='/news'</script>";
        }
        $res = $news->delete();
        if ($res) {
            echo "<script>alert('删除成功');location.href='/news'</script>";
        }
    }
}
