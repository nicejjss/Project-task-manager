<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Any.Task</title>
    <link rel="stylesheet" href="/css/layouts/app.css">
</head>
<body>

<!-- Horizontal Navigation -->
<header class="header">
    <div class="logo-section">
        <div class="logo">MyLogo</div>
        <a href="#" class="projects-link">Projects</a>
    </div>
    <div class="nav-right">
        <img src="{{ asset('images/avatar.png') }}" alt="Avatar" class="avatar">
        <div class="notification">
            <i class="notification-icon" id="notification-icon">🔔</i>
        </div>
    </div>
</header>

<!-- Notification Vertical Bar -->
<div class="notification-bar" id="notification-bar">
    <ul>
        <li>No new notifications</li>
        <li>Task Reminder</li>
        <li>New Message</li>
    </ul>
</div>

<!-- Vertical Navigation -->
<nav class="sidebar" id="sidebar">
    <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#">Create Task</a></li>
        <li><a href="#">List Tasks</a></li>
        <li><a href="#">Board</a></li>
    </ul>
</nav>

<!-- Main Content -->
<main class="content">
    @yield('content')
</main>

<script src="/js/layouts/app.js" defer></script>
</body>
</html>
