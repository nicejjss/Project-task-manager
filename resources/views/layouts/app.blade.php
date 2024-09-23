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
            <a style="color: #111" href="/"><img style="width: 130px;" src="{{asset('logo.png')}}"></a>
        </div>
        <a href="/" class="projects-link">Trang chủ</a>
    </div>
    <div class="nav-right">
        <div class="dropdown">
            <img src="{{ $user['avatar'] ?? asset('avatar.png') }}" alt="Avatar" class="avatar-app">
            <div class="dropdown-content">
                <div style="color: #b5b5b5;
    font-size: 14px;
    padding: 10px 20px;">Xin chào {{$user['name']}}</div>
                <a href="#">Personal Info</a>
                <a href="{{route('logout')}}">Logout</a>
            </div>
        </div>
        <div class="notification" style="font-size: 20px">
            <i style="color: #707070;" id="notification-icon" class="fa-solid fa-bell notification-icon"></i>
        </div>
    </div>
</header>

<!-- Notification Vertical Bar -->
<div class="notification" style="font-size: 20px">
    <i id="notification-icon" class="fa-solid fa-bell notification-icon"></i>
    <span id="notification-badge" class="badge" style="display: none;"></span>
</div>

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
</script>
