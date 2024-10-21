@include('layouts.app')
@include('layouts.sidebar')
<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote.css" rel="stylesheet">
<link rel="stylesheet" href="/css/page/taskcreate.css">
<main class="content">
    <div class="container">
        <h1 id="create-text">Công Việc Mới</h1>

        @if($parentTask)
            <div style="margin-bottom: 10px">
                <div style="display: inline">Công việc cha: </div>
                <a href="/project/{{$projectId}}/task/{{$parentTask['task_id']}}" onclick="displayLoading()">{{$parentTask['title']}}</a>
            </div>
        @endif
        @if ($errors->any())
            <div class="error" style="margin: 20px 0;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form id="taskForm" enctype="multipart/form-data">
            @csrf
            <div class="form-row">
                @if($parentTask)
                    <input name="parent" id="parent" value="{{$parentTask['task_id']}}" hidden="">
                @endif
                <!-- Left Column (70%) -->
                <div class="column-left">
                    <div class="form-group">
                        <label for="title">Tên Công Việc:<span style="color: red">*</span></label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Mô Tả:</label>
                        <div id="editor" style="min-height: 200px;height: 450px;"></div>
                    </div>
                </div>
                <!-- Right Column (30%) -->
                <div class="column-right">
                    <div class="form-group">
                        <label for="priority">Độ Ưu Tiên:<span style="color: red">*</span></label>
                        <select id="priority" name="priority" class="priority-select">
                            <option value=""></option>
                            @foreach($taskPriority as $key => $value)
                                <option value="{{$key}}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="user">Người Thực Hiện:<span style="color: red">*</span></label>
                        <select id="user" name="assignee">
                            <option value=""></option>
                            @foreach($members as $member)
                                <option value="{{$member['id']}}">{{$member['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="deadline">Loại Công Việc:</label>
                        <select id="tasktype" name="tasktype">
                            <option value="0">Không Xác Định</option>
                            @if(count($taskTypes))
                                @foreach($taskTypes as $taskType)
                                    <option value="{{$taskType['tasktype_id']}}">{{$taskType['tasktype_name']}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="deadline">Thời Hạn:</label>
                        <input type="date" id="deadline" name="deadline">
                    </div>
                </div>
            </div>

            <!-- File Attachment -->
            <div class="form-group">
                <label for="fileInput">Files Đính Kèm:</label>
                <input type="file" id="fileInput" style="display: none;" multiple>
                <button type="button" id="addFilesBtn">Thêm Tệp</button>
            </div>

            <!-- File List -->
            <ul id="fileList"></ul>

            <button id="submit-btn" type="submit">Create Task</button>
        </form>
    </div>
</main>
<!-- Loading Overlay and Indicator -->
<div id="loadingOverlay">
    <div class="lds-dual-ring"></div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote.min.js"></script>
<script>
    var projectId = {{$projectId}}; // Pass the $projectId variable to your JavaScript file
    var hasParent = {{data_get($parentTask, 'task_id', 0)}}
</script>
<script src="/js/page/taskcreate.js"> </script>
@include('layouts.footer')
