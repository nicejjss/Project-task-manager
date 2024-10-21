@include('layouts.app')
@include('layouts.sidebar')
<link rel="stylesheet" href="/css/page/projectindex.css">
<style>
    @media (max-width: 768px) {
        #content-detail {
            flex-direction: column;
            width: 100%;
        }

        #markdown-content {
            width: 100%;
        }

        #right-side {
            width: 100%;
            flex: 0 0 100%;
            margin-top: 20px;
        }
    }

    /* Basic styling for the modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 99;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0, 0, 0);
        background-color: rgba(0, 0, 0, 0.4);
        padding-top: 60px;
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>

<main class="content">
    <div class="status-container">
        <h2 class="title">Trạng Thái: </h2>
        <div id="status" class="status {{$status['class']}}">{{$status['text']}}</div>
    </div>
    <h2 id="description">Mô tả:</h2>
    <div id="content-detail">
        <div id="markdown-content">
            {!! $project !!}
        </div>
        <div id="right-side">
            <div id="status-text">Tổng quan Công Việc</div>
            <div>
                <canvas id="myChart"></canvas>
            </div>
            <div id="taskTypeSection">
                <!-- Task Type Button -->
                <button type="button" id="taskTypeBtn" class="task-type-btn">Quản Lý Nhóm Công Việc</button>
            </div>

            <!-- TODO: adding TaskType but not finish -->
            <!-- Task Type Modal -->
            <div id="taskTypeModal" class="modal-task-type">
                <div class="modal-content-task-type">
                    <span class="close-task-type-modal">&times;</span>
                    <h2>Nhóm Công Việc</h2>
                    <!-- Task Type List -->
                    <div id="taskTypeListContainer">
                        <!-- Add the CSRF token in a meta tag for security -->
                        <meta name="csrf-token" content="{{ csrf_token() }}">
                        <ul id="taskTypeList">
                            @foreach($taskTypes as $taskType)
                                <li class="task-type-item" id="task-{{$taskType['tasktype_id']}}">
                                    <span class="task-name">{{$taskType['tasktype_name']}}</span>
                                    <div>
                                        <button onclick="editTaskType({{$taskType['tasktype_id']}})">Sửa</button>
                                        <button onclick="deleteTaskType({{$taskType['tasktype_id']}})">Xóa</button>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Task Type Form -->
                    <form id="taskTypeForm" action="" method="post">
                        @csrf()
                        <input type="text" id="taskTypeInput" name="task_type" placeholder="Nhập Loại Công Việc" required />
                        <button type="submit" class="btn-primary">Thêm</button>
                    </form>
                </div>
            </div>

            <div>
                @if($ownerId === auth()->user()->id && !$isClose)
                    <div id="invite-text">Mời Thành Viên</div>
                    <form id="inviteForm" action="/project/{{$projectId}}/add" method="post">
                        @csrf()
                        <input type="text" name="projectID" value="{{$projectId}}" hidden>
                        <input style="outline: none" required name="email" type="email"/>
                        <input type="submit" value="Mời"/>
                    </form>
                @else
                @endif
                <div id="notification-mail" class="notification-mail"></div>

                <!-- Toggle List -->
                    <button class="member-dropdown-btn" onclick="toggleMemberList()">Thành Viên ▼</button>

                    <div id="member-email-dropdown" class="member-dropdown-content" style="display: none;">
                        <input type="text" placeholder="Search members..." id="memberSearchInput" onkeyup="filterMemberEmails()" aria-label="Search members">

                        <ul id="memberList">
                            @foreach($members as $key => $member)
                                <li class="member-item">
                                    <img src="{{ $member['avatar'] ?? asset('avatar.png') }}" alt="Avatar" class="member-avatar">
                                    <span class="member-email">{{$member['email']}} ({{ $key ? 'Thành viên' : 'Quản lý' }})</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
            </div>
        </div>
    </div>
    <!-- Include marked.js from a CDN -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctx = document.getElementById('myChart').getContext('2d');
        var openCount = {{$tasks['openCount']}};
        var inProgressCount = {{$tasks['inProgressCount']}};
        var doneCount = {{$tasks['doneCount']}};
        var closedCount = {{$tasks['closedCount']}};

        var allZero = openCount === 0 && inProgressCount === 0 && doneCount === 0 && closedCount === 0;

        var datasets = allZero ? [{
            label: 'Không có công việc',
            data: [1], // This will create a single grey bar
            backgroundColor: '#d3d3d3',
            barThickness: 15,
        }] : [
            {
                label: 'Cần thực hiện',
                data: [openCount],
                backgroundColor: '#ed8077',
                barThickness: 15,
            },
            {
                label: 'Đang thực hiện',
                data: [inProgressCount],
                backgroundColor: '#4488c5',
                barThickness: 15,
            },
            {
                label: 'Hoàn thành',
                data: [doneCount],
                backgroundColor: '#5eb5a6',
                barThickness: 15,
            },
            {
                label: 'Đã Đóng',
                data: [doneCount],
                backgroundColor: '#a1af2f',
                barThickness: 15,
            }
        ];

        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Trạng Thái'],
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y', // This makes the chart horizontal
                scales: {
                    x: {
                        stacked: true,
                        ticks: {
                            min: 0, // Ensures the x-axis starts at 0
                            max: 1  // Adjust this value based on your data range
                        },
                        display: false // This will remove all the x-axis grid lines
                    },
                    y: {
                        stacked: true,
                        ticks: {
                            display: false // This removes the "Status" label on the left
                        },
                        display: false // This will remove all the y-axis grid lines
                    },
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 20,
                            padding: 10
                        }
                    },
                },
            },
        });

    </script>
    <script src="/js/page/projectindex.js"></script>

    @if($ownerId === auth()->user()->id && !$isClose)
        <div class="button-container">
            <a style="text-decoration: none" href="/project/{{$projectId}}/edit">
                <button id="editButton">Chỉnh Sửa Dự Án</button>
            </a>
            <button id="closeButton">Đóng Dự Án</button>
        </div>
    @else
    @endif
</main>


<!-- The Modal -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <p>Bạn có chắc chắn muốn đóng dự án này không?</p>
        <p>Hành động này <b>không thể</b> hoàn tác.</p>
        <div class="modal-buttons">
            <button id="confirmClose" class="btn-confirm">Có</button>
            <button id="cancelClose" class="btn-cancel">Không</button>
        </div>
    </div>
</div>

<!-- Loading Overlay and Indicator -->
<div id="loadingOverlay">
    <div class="lds-dual-ring"></div>
</div>

<!-- CSS -->
<style>
    /* Modal background */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 999; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        background-color: rgba(0, 0, 0, 0.4); /* Black background with opacity */
    }

    /* Modal content */
    .modal-content {
        background-color: #f0f0f0;
        margin: auto;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #ccc;
        width: 30%; /* Adjust the width as needed */
        position: relative;
        top: 30%;
        transform: translateY(-50%);
        text-align: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    /* Button container */
    .modal-buttons {
        margin-top: 20px;
    }

    /* Confirm button (Yes) */
    .btn-confirm {
        width: 40%;
        background-color: #bd4c44;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin-right: 10px;
    }

    /* Cancel button (No) */
    .btn-cancel {
        width: 40%;
        background-color: #4caf93;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    /* Hover effect */
    .btn-confirm:hover,
    .btn-cancel:hover {
        opacity: 0.9;
    }
</style>

<script>
    // Get the modal
    var modal = document.getElementById("myModal");

    // Get the button that opens the modal
    var btn = document.getElementById("closeButton");

    // Get the confirm and cancel buttons
    var confirmBtn = document.getElementById("confirmClose");
    var cancelBtn = document.getElementById("cancelClose");

    // When the user clicks the button, open the modal
    btn.onclick = function() {
        modal.style.display = "block";
    }

    modal.onclick = function(e) {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    };

    cancelBtn.onclick = function() {
        modal.style.display = "none";
    }

    confirmBtn.onclick = function() {
        // Make a GET request to close the project
        fetch(`/project/{{$projectId}}/close`, {
            method: 'GET',
        })
            .then(response => response.json()) // Parse the JSON response
            .then(data => {
                if (data) {
                    showToast(1);
                    location.reload();
                } else {
                    showToast(2);
                }
            })
            .catch(error => {
                // Handle the network error
                console.error('Network error:', error);
                showToast(2); // Optionally show a toast for network errors
            });
    }

    document.getElementById('inviteForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        const loadingOverlay = document.getElementById('loadingOverlay');
        loadingOverlay.style.display = 'block';

        var xhr = new XMLHttpRequest();
        var url = this.action;
        var formData = new FormData(this);

        xhr.open('POST', url, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('input[name="_token"]').value);

        xhr.onload = function() {
            if (xhr.status === 200) {
                if (xhr.responseText !== 'Gửi mail thất bại') {
                    loadingOverlay.style.display = 'none';
                    showToast(1, xhr.responseText);
                    document.getElementById('inviteForm').reset(); // Clear the form
                } else {
                    loadingOverlay.style.display = 'none';
                    showToast(2, xhr.responseText);
                }
            } else {
                loadingOverlay.style.display = 'none';
                showToast(0, xhr.responseText);
            }
        };

        xhr.onerror = function() {
            showNotification('An error occurred during the request.', 'error');
        };

        xhr.send(formData);
    });

    var taskTypeModal = document.getElementById("taskTypeModal");

    var taskTypeBtn = document.getElementById("taskTypeBtn");
    var closeModalBtn = document.querySelector(".close-task-type-modal");
    var taskTypeForm = document.getElementById("taskTypeForm");
    var taskTypeInput = document.getElementById("taskTypeInput");
    var taskTypeList = document.getElementById("taskTypeList");

    var taskTypes = [];


    taskTypeForm.addEventListener('submit', function(event) {
        // Show the loading overlay and indicator
        const loadingOverlay = document.getElementById('loadingOverlay');
        loadingOverlay.style.display = 'block';
    });

    // Open Modal
    taskTypeBtn.onclick = function () {
        taskTypeModal.style.display = "block";
    }

    // Close Modal
    closeModalBtn.onclick = function () {
        taskTypeModal.style.display = "none";
    }

    // Close Modal if clicked outside
    window.onclick = function (event) {
        if (event.target === modal) {
            taskTypeModal.style.display = "none";
        }
    }

    // Render Task Types List
    function renderTaskTypes() {
        taskTypes.forEach(function (type, index) {
            var li = document.createElement('li');
            li.innerHTML = `
                ${type}
                <button onclick="deleteTaskType(${index})">Delete</button>
            `;
            taskTypeList.appendChild(li);
        });
    }

    // Function to toggle editing of a task type
    function editTaskType(id) {
        const taskItem = document.getElementById(`task-${id}`);
        const taskNameSpan = taskItem.querySelector('.task-name');
        const editButton = taskItem.querySelector('button[onclick^="editTaskType"]');

        if (editButton.textContent === "Sửa") {
            const taskName = taskNameSpan.textContent;
            taskNameSpan.innerHTML = `<input class="taskTypeInput" type="text" id="editTaskName-${id}" value="${taskName}">`;
            editButton.textContent = "Lưu";

            // Add event listener to detect click outside input field
            document.addEventListener('click', handleClickOutside);

        } else {
            saveTaskType(id);
        }

        // Function to handle click outside the task input
        function handleClickOutside(event) {
            const taskInput = document.getElementById(`editTaskName-${id}`);
            if (taskInput && !taskInput.contains(event.target) && event.target !== taskInput) {
                saveTaskType(id); // Save the task and revert the input back to a span
                document.removeEventListener('click', handleClickOutside); // Remove the event listener
            }
        }
    }

    // Function to save task type
    function saveTaskType(id) {
        const taskItem = document.getElementById(`task-${id}`);
        const taskNameSpan = taskItem.querySelector('.task-name');
        const editButton = taskItem.querySelector('button[onclick^="editTaskType"]');
        const newTaskName = document.getElementById(`editTaskName-${id}`).value;

        // Send AJAX request to update task name
        fetch(`/tasks/${id}/edit`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ name: newTaskName })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    taskNameSpan.textContent = newTaskName;
                    editButton.textContent = "Edit";
                } else {
                    alert('Error updating task');
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Function to delete a task type
    function deleteTaskType(id) {
        // Send AJAX request to delete task
        fetch(`/tasks/${id}/delete`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`task-${id}`).remove();
                } else {
                    alert('Error deleting task');
                }
            })
            .catch(error => console.error('Error:', error));
    }

</script>
<script src="/js/page/projectindex.js"></script>
@include('layouts.footer')
