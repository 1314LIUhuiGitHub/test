@extends('layout.default')
@section('content')
    <p><a href="{{route('news.create')}}">新增新闻</a></p>
    <table style="text-align: center;width: 500px; border-collapse: collapse;">
        <tr>
            <td>编号(id)</td>
            <td>标题</td>
            <td>时间</td>
            <td>操作</td>
        </tr>
        @foreach ($news as $item)
            <tr>
                <td>{{$item["id"]}}</td>
                <td>{{$item["title"]}}</td>
                <td>{{$item["created_at"]}}</td>
                <td>
                    <a href="{{route('news.update',$item->id)}}">修改</a>
                    <a href="/news/del/{{$item->id}}">删除</a>
                </td>
            </tr>
        @endforeach
    </table>
@endsection
