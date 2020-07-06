<html>
<head>
    <title>新闻管理</title>
    <style>
        tr:first-child{
            background-color: #f3f3f3;
        }
        tr > td{
            border:1px solid #0094ff;
        }
        .my-form{
            margin-left: 30px;
        }
        .create-news-box{
            margin: 10px auto;
            width: 385px;
        }
        .item-box{
            display: flex;
            margin-top: 20px;
        }
        .create-news-box .item{
            width: 300px;
            margin-left: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    @yield('content')
</div>
</body>
</html>

