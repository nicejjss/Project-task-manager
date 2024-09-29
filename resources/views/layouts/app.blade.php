<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Any.Task</title>
    <link rel="stylesheet" href="/css/layouts/app.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

<!-- Horizontal Navigation -->
<header class="header">
    <div class="logo-section">
        <div class="logo">
            <a style="color: #111" href="/"><img style="width: 150px;" src="{{asset('logo.png')}}"></a>
        </div>
        <a style="text-decoration: none;" href="/" class="projects-link">Dự Án</a>
    </div>
    <div class="nav-right">
        <div class="dropdown">
            <img src="{{ $avatar ?? asset('avatar.png') }}" alt="Avatar" class="avatar-app">
            <div style="background-color: white" class="dropdown-content">
                <div style="color: #b5b5b5;
    font-size: 14px;
    padding: 10px 20px;">Xin chào {{$user['name']}}</div>
                <a style="font-size: 16px" href="/user">Thông tin cá nhân</a>
                <a style="font-size: 16px" href="{{route('logout')}}">Đăng xuất</a>
            </div>
        </div>
        <div class="notification" style="font-size: 20px">
            <i style="color: #707070;" id="notification-icon" class="fa-solid fa-bell notification-icon"></i>
        </div>
    </div>
</header>

<!-- Notification Vertical Bar -->
<div class="notification-bar" id="notification-bar">
    <div id="close-contain">
        <div class="close-icon" onclick="closeNotificationBar()">
            <i class="fa-solid fa-xmark"></i>
        </div>
    </div>
    <ul id="notification-list">
        <li>Không có thông báo nào</li>
    </ul>
</div>
<style>
    .container {
        margin: 20px;
    }

    .toast {
        visibility: hidden;
        min-width: 250px;
        margin-left: -125px;
        background-color: #333;
        color: #fff;
        text-align: center;
        border-radius: 7px;
        padding: 16px;
        position: fixed;
        z-index: 9999;
        right: 30px;
        top: 30px;
        font-size: 17px;
        opacity: 0;
        transition: opacity 0.5s, transform 0.5s;
        transform: translateX(100%);
    }

    .toast.show {
        visibility: visible;
        opacity: 1;
        transform: translateX(0);
    }

    .toast.hide {
        opacity: 0;
        transform: translateX(100%);
    }

</style>

<div id="toast" class="toast"></div>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('{{$key}}', {
        cluster: 'ap1'
    });

    var channel = pusher.subscribe('channels.user_{{$user['email']}}');
    channel.bind('invitation', function(data) {
        console.log(data);
        alert(JSON.stringify(data));
    });

    function showToast(type, message = null) {
        const toast = document.getElementById('toast');

        switch (type) {
            case 1: toast.innerText = message ? message : 'Thành Công';
                toast.style.backgroundColor = '#7DD3AE';
                toast.className = 'toast show'; break;

            case 2:toast.innerText = message ? message : 'Thất Bại';
                toast.style.backgroundColor = '#FF0000';
                toast.className = 'toast show'; break;

            default:toast.innerText = 'Cố lỗi xảy ra';
                toast.style.backgroundColor = toastColor;
                toast.className = 'toast show';break
        }


        setTimeout(() => {
            toast.className = 'toast hide';
        }, 2000);
    }
</script>
<script src="/js/layouts/app.js"></script>
