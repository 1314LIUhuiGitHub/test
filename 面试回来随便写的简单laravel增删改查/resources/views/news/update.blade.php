@extends('layout.default')
@section('content')
    <div class="create-news-box">
        <h2>修改新闻</h2>
        @if ($errors->any())
            <div class="alert alert-danger" style="margin-left: 30px">
                @foreach ($errors->all() as $error)
                    <div style="color: red">{{ $error }}</div>
                @endforeach
            </div>
        @endif
        <form action="{{route('news.update',$newsItem->id)}}"  class="my-form" method="post">
            @csrf
            {{method_field('PATCH')}}
            <div class="item-box">
                <div>标题</div>
                <input type="text" class="item" name="title" value="{{$newsItem->title}}">
            </div>
            <div class="item-box">
                <div>内容</div>
                <textarea name="intro" class="item" style="height: 150px">{{$newsItem->intro}}</textarea>
            </div>
            <div class="item-box form-sub" style="justify-content: flex-end">
                <input type="submit" value="提交">
            </div>
        </form>
    </div>
@endsection
