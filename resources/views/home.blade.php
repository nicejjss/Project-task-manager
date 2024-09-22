@include('layouts.app')
<link rel="stylesheet" href="/css/page/home.css">
<main class="content">
    <div class="project-page">
        <div class="container">
            <!-- First Row: Projects Title and Create Project Link -->
            <div class="row">
                <h1>Dự án Tham Gia</h1>
                <a href="{{route('project.create')}}" class="create-project-link">Tạo Dự Án</a>
            </div>

            <!-- Second Row: List of Projects -->
            <div class="projects">
                <ul id="projects-list">
                    @if (count($projects))
                        @foreach($projects as $project)
                            <li>
                                <a href="/project/{{$project['id']}}">
                                    <div class="details">
                                        {{$project['name']}} ({{$project['is_owner'] === 1 ? 'Owner' : 'Member'}})
                                    </div>
                                    <div class="info">
                                        Công Việc: {{$project['tasks']}} <br>
                                        Thành Viên: {{$project['members']}}
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    @else
                        <h3 style="color: #909b95;">Chưa tham gia dự án nào</h3>
                        <div style="color: #909b95;">Tạo mới dự án</div>
                    @endif
                    <!-- Projects will be dynamically injected here -->
                </ul>
            </div>

            <!-- Second Section: Tasks Assigned to Me -->
            <div class="tasks">
                <h1 style="margin-bottom: 20px">Công việc của bạn</h1>
                <ul id="tasks-list">
                    @if (count($tasks))
                        @foreach($tasks as $task)
                            <li>
                                <a href="/task/{{$task['id']}}">
                                    <div class="details">
                                        <div class="task-project">{{$task['projectName']}}</div>
                                        <div>{{$task['title']}}</div>
                                    </div>
                                    <div class="info">
                                        Thời Gian: {{$task['dueDate']}} <br>
                                        Trạng thái: {{$task['status']}}
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    @else
                        <h3 style="color: #909b95;">Chưa có công việc nào</h3>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <script src="/js/page/home.js"> </script>
</main>
@include('layouts.footer')
