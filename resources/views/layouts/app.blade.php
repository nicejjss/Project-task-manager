<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        <a style="text-decoration: none;" href="/" class="projects-link">DS Dự Án</a>
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
            <span id="notification-dot" class="notification-dot"></span>
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
        @if(count($notifications))
            @foreach($notifications as $notification)
                @if($notification['type'] == 0)
                    <li>
                        <!-- Button for invitation, dynamically holds project data -->
                        <button class="show-popup"
                                data-project-id="{{ $notification['project_id'] }}"
                                data-project-name="{{ $notification['project_name'] }}">
                            Bạn Có lời mời vào dự án {{ $notification['project_name'] }}
                        </button>
                    </li>
                @else
                    <li>
                        <a href="{{ $notification['url'] }}">
                            {{ $notification['message'] }}
                        </a>
                    </li>
                @endif
            @endforeach
        @else
            <li>Không có thông báo nào</li>
        @endif
    </ul>

    <div id="notification-popup" style="display:none;">
        <p id="popup-message"></p>
        <button id="popup-yes">Yes</button>
        <button id="popup-no">No</button>
    </div>
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

    var channel = pusher.subscribe('channels.user_{{$user['id']}}');
    channel.bind('invitation', function(data) {
        document.getElementById('notification-dot').style.display = 'block';

        alert(data.msg);
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

    const popup = document.getElementById('notification-popup');
    const popupMessage = document.getElementById('popup-message');
    const popupYes = document.getElementById('popup-yes');
    const popupNo = document.getElementById('popup-no');
    let currentProjectId = null; // Track the project id of the current popup

    // Function to show the popup with specific project information
    document.querySelectorAll('.show-popup').forEach(button => {
        button.addEventListener('click', function() {
            currentProjectId = this.getAttribute('data-project-id');
            const projectName = this.getAttribute('data-project-name');

            // Set the project name in the popup message
            popupMessage.textContent = `Bạn Có lời mời vào dự án ${projectName}`;

            // Show the popup
            popup.style.display = 'block';
        });
    });

    // Handle the "Yes" button click - redirect to the specific project URL
    popupYes.addEventListener('click', function() {
        if (currentProjectId) {
            // Redirect to the project accept URL (you can customize the URL format)
            window.location.href = `/project/${currentProjectId}/accept-invite`;
        }
    });

    // Handle the "No" button click - close the popup
    popupNo.addEventListener('click', function() {
        // Close the popup
        popup.style.display = 'none';
    });
</script>
<script src="/js/layouts/app.js"></script>
