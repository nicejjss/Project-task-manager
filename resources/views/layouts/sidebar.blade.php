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

<div id="project-name">
    Dự Án: Project
</div>
