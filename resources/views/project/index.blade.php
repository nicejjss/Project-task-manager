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
            <div id="status-text">Trạng Thái Công Việc</div>
            <div>
                <canvas id="myChart"></canvas>
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
                    <button class="member-dropdown-btn" onclick="toggleMemberList()">Members ▼</button>

                    <div id="member-email-dropdown" class="member-dropdown-content" style="display: none;">
                        <input type="text" placeholder="Search members..." id="memberSearchInput" onkeyup="filterMemberEmails()" aria-label="Search members">

                        <ul id="memberList">
                            @foreach($members as $key => $member)
                                <li class="member-item">
                                    <img src="{{ $member['avatar'] ?? asset('avatar.png') }}" alt="Avatar" class="member-avatar">
                                    <span class="member-email">{{$member['email']}} ({{ $key ? 'Member' : 'Owner' }})</span>
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

        var allZero = openCount === 0 && inProgressCount === 0 && doneCount === 0;

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
        <p>Hành động này không thể hoàn tác.</p>
        <div class="modal-buttons">
            <button id="confirmClose" class="btn-confirm">Yes</button>
            <button id="cancelClose" class="btn-cancel">No</button>
        </div>
    </div>
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

        var xhr = new XMLHttpRequest();
        var url = this.action;
        var formData = new FormData(this);

        xhr.open('POST', url, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('input[name="_token"]').value);

        xhr.onload = function() {
            if (xhr.status === 200) {
                if (xhr.responseText !== 'Gửi mail thất bại') {
                    showToast(1, xhr.responseText);
                    document.getElementById('inviteForm').reset(); // Clear the form
                } else {
                    showToast(2, xhr.responseText);
                }
            } else {
                showToast(0, xhr.responseText);
            }
        };

        xhr.onerror = function() {
            showNotification('An error occurred during the request.', 'error');
        };

        xhr.send(formData);
    });

</script>
<script src="/js/page/projectindex.js"></script>
@include('layouts.footer')
