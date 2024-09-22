@include('layouts.app')
@include('layouts.sidebar')
<link rel="stylesheet" href="/css/page/projectindex.css">
    <main class="content">
        <h2 id="description">Mô tả:</h2>
        <div id="content-detail">
            <div id="markdown-content">
                <!-- Raw Markdown will be inserted here -->
            </div>
            <div id="right-side">
                <div id="status-text">Trạng Thái Công Việc</div>
                <div>
                    <canvas id="myChart"></canvas>
                </div>
                <div id="invite-text">Mời Thành Viên</div>
                <form id="inviteForm" action="/project/{{$projectId}}/add" method="post">
                    @csrf()
                    <input type="text" name="projectID" value="{{$projectId}}" hidden="">
                    <input required name="email" type="email"/>
                    <input type="submit" value="Mời"/>
                </form>

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
        <!-- Include marked.js from a CDN -->
        <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

        <script>
            // Your raw Markdown content passed from Laravel (output safely)
            let rawMarkdownContent = @json($project);

            // Convert the raw Markdown to HTML using marked.js
            let htmlContent = marked.parse(rawMarkdownContent);

            // Insert the converted HTML into the page
            document.getElementById('markdown-content').innerHTML = htmlContent;
        </script>

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

        <div class="button-container">
            <a href="/project/{{$projectId}}/edit"><button id="editButton">Chỉnh Sửa Dự Án</button></a>
            <button id="closeButton">Đóng Dự Án</button>
        </div>
    </main>

@include('layouts.footer')
