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
        background-color: rgb(0,0,0);
        background-color: rgba(0,0,0,0.4);
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
                    @if($ownerId === auth()->user()->id)
                        <div id="invite-text">Mời Thành Viên</div>
                        <form id="inviteForm" action="/project/{{$projectId}}/add" method="post">
                            @csrf()
                            <input type="text" name="projectID" value="{{$projectId}}" hidden="">
                            <input required name="email" type="email"/>
                            <input type="submit" value="Mời"/>
                        </form>
                    @else
                    @endif
                    <div id="notification-mail" class="notification-mail"></div>

                    <!-- Toggle Button -->
                    <button id="toggleButton">Thành Viên <i class="fa-solid fa-angle-down"></i></button>

                    <!-- Toggle List -->
                    <div id="emailList" style="display: none;">
                        <ul style="margin-top: 10px;">
                            @foreach($members as $key => $member)

                                <li>
                                    <img src="{{ $member['avatar'] ?? asset('avatar.png') }}" alt="Avatar" class="avatar">
                                    @if($key)
                                        <span>{{$member['email']}} (Member)</span>
                                    @else
                                        <span>{{$member['email']}} (Owner)</span>
                                    @endif
                                </li>
                            @endforeach
                            <!-- Add more list items as needed -->
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
            var acceptedCount = {{$tasks['acceptedCount']}};
            var doneCount = {{$tasks['doneCount']}};

            var allZero = openCount === 0 && inProgressCount === 0 && acceptedCount === 0 && doneCount === 0;

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
                    label: 'Chờ duyệt',
                    data: [acceptedCount],
                    backgroundColor: '#5eb5a6',
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

        @if($ownerId === auth()->user()->id)
            <div class="button-container">
                <a href="/project/{{$projectId}}/edit"><button id="editButton">Chỉnh Sửa Dự Án</button></a>
                <button id="closeButton">Đóng Dự Án</button>
            </div>
        @else
        @endif
    </main>


<!-- The Modal -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>Bạn có chắc chắn muốn đóng dự án này không?</p>
        <p>Hành động này không thể hoàn tác.</p>
        <button id="confirmClose">Yes</button>
        <button id="cancelClose">No</button>
    </div>
</div>

<script>
    // Get the modal
    var modal = document.getElementById("myModal");

    // Get the button that opens the modal
    var btn = document.getElementById("closeButton");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // Get the confirm and cancel buttons
    var confirmBtn = document.getElementById("confirmClose");
    var cancelBtn = document.getElementById("cancelClose");

    // When the user clicks the button, open the modal
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks on the confirm button, close the project
    confirmBtn.onclick = function() {
        // Call the action to close the project
        window.location.href = "/project/{{$projectId}}/close";
    }

    // When the user clicks on the cancel button, close the modal
    cancelBtn.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
@include('layouts.footer')
