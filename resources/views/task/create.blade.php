@include('layouts.app')
@include('layouts.sidebar')
<link rel="stylesheet" href="/css/page/taskcreate.css">
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<main class="content">
    <div class="container">
        <h1 id="create-text">Create Task</h1>
        <form id="taskForm" enctype="multipart/form-data">
            @csrf
            <div class="form-row">
                <!-- Left Column (70%) -->
                <div class="column-left">
                    <div class="form-group">
                        <label for="title">Title:</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <div id="editor" style="min-height: 200px; height: auto;"></div> <!-- Quill Editor -->
                    </div>
                </div>
                <!-- Right Column (30%) -->
                <div class="column-right">
                    <div class="form-group">
                        <label for="priority">Priority:</label>
                        <select id="priority" name="priority" class="priority-select">
                            <option value=""></option>
                            @foreach($taskPriority as $key => $value)
                                <option value="{{$key}}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="user">Assign To:</label>
                        <select id="user" name="user">
                            <option value=""></option>
                            @foreach($members as $member)
                                <option value="{{$member['id']}}">{{$member['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="deadline">Deadline:</label>
                        <input type="date" id="deadline" name="deadline">
                    </div>
                </div>
            </div>

            <!-- File Attachment -->
            <div class="form-group">
                <label for="attachment">Attach Files:</label>
                <input type="file" id="attachment" name="attachment" multiple onchange="previewFiles()">
                <ul id="fileList"></ul>
            </div>

            <button id="submit-btn" type="submit">Create Task</button>
        </form>
        <div id="output"></div>
    </div>
</main>
<div id="loadingOverlay">
    <div id="loadingIndicator">Loading, please wait...</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/showdown/1.9.1/showdown.min.js"></script>
<script src="/js/page/taskcreate.js"> </script>
@include('layouts.footer')
