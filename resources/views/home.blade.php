@include('layouts.app')
<link rel="stylesheet" href="/css/page/home.css">
<main style="min-height: fit-content;" class="content">
    <div class="container">
        <h2>Tìm Dự Án</h2>
        @if ($errors->any())
            <div class="error" style="margin: 20px 0;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form id="search" method="GET">
            <!-- Search by project name -->
            <div class="search flex">
                <div class="form-group">
                    <label for="projectName">Tên Dự Án:</label>
                    <input type="text" id="projectName" name="project_name" placeholder="Nhập tên dự án">
                </div>

                <!-- Search by status -->
                <div class="form-group">
                    <label for="status">Trạng Thái:</label>
                    <select id="status" name="project_status">
                        <option value="-1">Tất Cả</option>
                        <option value="0">Khởi Tạo</option>
                        <option value="1">Đang Phát Triển</option>
                        <option value="2">Đã Đóng</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="role">Vai Trò Trong Dự Án:</label>
                    <select id="role" name="role">
                        <option value="0">Tất Cả</option>
                        <option value="1">Quản Lý</option>
                        <option value="2">Thành Viên</option>
                    </select>
                </div>

                <!-- Search by user in project -->
                <div class="form-group">
                    <label for="user">Thành Viên Trong Dự Án:</label>
                    <input type="email" id="user" name="user_mail" placeholder="Nhập email thành viên">
                </div>
            </div>
            <div class="sort flex">
                <!-- Sorting options -->
                <div class="form-group">
                    <label for="sort">Sắp Xếp Theo:</label>
                    <select id="sort" name="sort">
                        <option value="1">Tên dự án: A-Z</option>
                        <option value="2">Tên dự án: Z-A</option>
                        <option value="3">Ngày bắt đầu: tăng dần</option>
                        <option value="4">Ngày bắt đầu: giảm dần</option>
                        <option value="5">Người tham gia tham gia: tăng dần</option>
                        <option value="6">Người tham gia: giảm dần</option>
                        <option value="7">Số lượng CV: tăng dần</option>
                        <option value="8">Số lượng CV: giảm dần</option>
                    </select>
                </div>

                <!-- Submit button -->
                <div class="actions search-btn">
                    <button type="submit">Search</button>
                </div>
            </div>
        </form>

        <h2>Kết Quả</h2>
        <div id="create-project-link">
            <a href="/project/create">Tạo Dự Án</a>
        </div>

        <!-- Project Results Section -->
        <div class="projects-container">
            @if(count($projects))
                @foreach($projects as $project)
                    <div class="project-wrapper">
                        <div class="project-card">
                            <h3>{{$project->project_name}}</h3>
                            <p>Trạng Thái: <span class="status {{$project->statusColor}}"> {{$project->statusText}}</span></p>
                            <p>Ngày Bắt Đầu: {{$project->created_at->toDateString()}}</p>
                            <p>Vai Trò Trong Dự Án: <span>{{$project->role}}</span></p>
                            <p>Thành Viên: {{$project->membersCount}}</p>
                            <a href="/project/{{$project->project_id}}"><button class="view-btn">Xem Chi Tiết</button></a>
                        </div>

                        <!-- Hover box to show more information -->
                        <div class="hover-box">
                            <p class="color-task">Công việc đã tạo: <span>{{$project->tasksCount}}</span></p>
                            <p class="color-open">Công việc cần làm: <span>{{$project->taskOpenCount}}</span></p>
                            <p class="color-process">Công việc đang thực hiện: <span>{{$project->taskProcessingCount}}</span></p>
                            <p class="color-done">Công việc đã hoàn thành: <span>{{$project->taskDoneCount}}</span></p>
                        </div>
                    </div>
                @endforeach
                @else
                <h3 style="color: gray">Không Tìm thấy Dự Án</h3>
            @endif
        </div>
    </div>
</main>
<script>
    // Get the query string from the URL
    const queryString = window.location.search;

    // Create a URLSearchParams object
    const urlParams = new URLSearchParams(queryString);

    // Get the values of specific parameters
    const projectName = urlParams.get('project_name');
    const projectStatus = urlParams.get('project_status');
    const role = urlParams.get('role');
    const userMail = urlParams.get('user_mail');
    const sort = urlParams.get('sort');

    // Log the values to the console
    console.log('Project Name:', projectName);
    console.log('Project Status:', projectStatus);
    console.log('Role:', role);
    console.log('User Mail:', userMail);
    console.log('Sort:', sort);

    // Set the value of the input fields based on the query parameters
    document.getElementById('projectName').value = projectName || '';
    document.getElementById('status').value = projectStatus || '-1';
    document.getElementById('role').value = role || '0';
    document.getElementById('user').value = userMail || '';
    document.getElementById('sort').value = sort || '1';
</script>
@include('layouts.footer')
