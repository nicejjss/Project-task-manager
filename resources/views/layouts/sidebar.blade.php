<link rel="stylesheet" type="text/css" href="/css/layouts/sidebar.css">
<!-- Vertical Navigation -->
<nav class="sidebar" id="sidebar">
    <!-- Hamburger Icon -->
    <div id="hamburger-icon">
        <i class="fa-solid fa-bars" id="hamburger"></i>
        <i style="display: none" class="fa-solid fa-xmark" id="close"></i>
    </div>
    <ul>
        <li id="project_index"><a href="/project/{{$projectId}}">Tổng Quan Dự Án</a></li>
        <li id="task_create"><a href="/project/{{$projectId}}/task/create">Tạo Task</a></li>
        <li id="task_list"><a href="/project/{{$projectId}}/task/list">Danh Sách Công Việc</a></li>
        <li id="task_analytic"><a href="/project/{{$projectId}}/analytic">Thống Kê</a></li>
        <li id="meeting"><a href="/project/{{$projectId}}/meeting">Meeting</a></li>
    </ul>
</nav>

<div id="project-name-select" class="project-dropdown">
    <button style="font-weight: 600;" class="dropbtn" onclick="toggleDropdown()">
        Dự Án: {{$currentProject['project_name']}} ▼
    </button>
    <div id="dropdown-content" class="project-dropdown-content" role="menu">
        <input type="text" placeholder="Search..." id="searchInput" onkeyup="filterProjects()" aria-label="Search projects">
        <div id="projectList">
            <span id="span-title">Dự án của tôi:</span>
            <!-- Example projects, replace with your actual project names -->
            @foreach($otherProjects as $otherProject)
                <a style="text-decoration: none;color: black" href="/project/{{$otherProject['project_id']}}" role="menuitem">{{$otherProject['project_name']}}</a>
            @endforeach
        </div>
    </div>
</div>
<script type="text/javascript" src="/js/layouts/sidebar.js"></script>
