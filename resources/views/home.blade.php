@include('layouts.app')
<link rel="stylesheet" href="/css/page/home.css">
<main class="content">
    <div class="container">
        <h2>Dự Án Của Tôi</h2>
        <form id="search" method="GET">
            <!-- Search by project name -->
            <div class="form-group">
                <label for="projectName">Tên Dự Án:</label>
                <input type="text" id="projectName" name="project_name" placeholder="Nhập tên dự án">
            </div>

            <!-- Search by status -->
            <div class="form-group">
                <label for="status">Trạng Thái:</label>
                <select id="status" name="project_status">
                    <option value="0">Tất Cả</option>
                    <option value="1">Mở</option>
                    <option value="2">Đang Phát Triển</option>
                    <option value="3">Đã Đóng</option>
                </select>
            </div>

            <!-- Search by user in project -->
            <div class="form-group">
                <label for="user">Tên Thành Viên:</label>
                <input type="text" id="user" name="user_name" placeholder="Nhập tên thành viên">
            </div>

            <!-- Sorting options -->
            <div class="form-group">
                <label for="sort">Sắp Xếp Theo:</label>
                <select id="sort" name="sort">
                    <option value="1">Tên dự án: A-Z</option>
                    <option value="2">Tên dự án: Z-A</option>
                    <option value="3">Ngày bắt đầu: tăng dần</option>
                    <option value="4">Ngày bắt đầu: giảm dần dần</option>
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
        </form>

        <!-- Project Results Section -->
        <div class="project-list" id="projectList">
           @foreach($projects as $project)
                <div class="project-card">
                    <h3>{{$project->project_name}}</h3>
                    <p>Status: <span class="status"> {{$project->statusText}}</span></p>
                    <p>Start Date: {{$project->created_at}}</p>
                    <button class="view-btn">View Details</button>
                </div>
           @endforeach
        </div>

        <!-- Hover Info Box (for displaying task details) -->
        <div class="hover-info" id="hoverInfo"></div>
    </div>
</main>
<script>
    // Get references to project list and hover info box
    const projectList = document.getElementById('projectList');
    const hoverInfo = document.getElementById('hoverInfo');

    // Function to update the hover info content
    function updateHoverInfo(project) {
        hoverInfo.innerHTML = `
                <p>Tasks Created: ${project.tasks.created}</p>
                <p>Tasks Open: ${project.tasks.open}</p>
                <p>Tasks Finished: ${project.tasks.finished}</p>
                <p>Tasks Closed: ${project.tasks.closed}</p>
            `;
    }

    projectCard = document.getElementsByName('project-card')[0];
    // Add mouseover event to show task details beside the cursor
    projectCard.addEventListener('mousemove', (e) => {
        updateHoverInfo(project);  // Update the task info for this project

        // Calculate the position of the hover box
        let hoverLeft = e.clientX + 20;
        let hoverTop = e.clientY + 20;

        // Get the dimensions of the hover box
        const hoverWidth = hoverInfo.offsetWidth;
        const hoverHeight = hoverInfo.offsetHeight;

        // Adjust the position if the hover box goes off-screen
        if (hoverLeft + hoverWidth > window.innerWidth) {
            hoverLeft = e.clientX - hoverWidth - 20;
        }
        if (hoverTop + hoverHeight > window.innerHeight) {
            hoverTop = e.clientY - hoverHeight - 20;
        }

        // Set the position of the hover box
        hoverInfo.style.left = hoverLeft + 'px';
        hoverInfo.style.top = hoverTop + 'px';
        hoverInfo.style.display = 'block';  // Show the hover info box
    });


    // Add mouseleave event to hide the hover info box
    projectCard.addEventListener('mouseleave', () => {
        hoverInfo.style.display = 'none';  // Hide the hover info box
    });

    projectList.appendChild(projectCard);
</script>
@include('layouts.footer')
