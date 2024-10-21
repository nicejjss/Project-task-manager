@include('layouts.app')
@include('layouts.sidebar')
<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/css/page/taskindex.css">
<main class="content">
    {{--    TODO: not finish UI--}}
    <div class="container">
        @if($taskParent)
            <div style="margin-bottom: 10px">
                <div style="display: inline">Công việc cha:</div>
                <a href="/project/{{$projectId}}/task/{{$taskParent['task_id']}}">{{$taskParent['title']}}</a>
            </div>
        @endif
        <div class="form-row">
            <!-- Left Column (70%) -->
            <div class="column-left">
                <div class="comment-own">
                    <img class="avatar-app" src="{{ $creator['avatar'] ?? asset('avatar.png') }}" alt="{{$creator['name']}}">
                    <div class="comment-own-infor">
                        <h4 class="comment-own-infor-name">{{$creator['name']}}</h4>
                        <p class="comment-own-infor-date">{{$createTime}}</p>
                    </div>
                </div>
                <div id="task-name" class="form-group">
                    <label for="title"><p style="display: inline">Tên Công Việc:</p> {{$title}}</label>
                </div>
                <hr>
                <div class="form-group">
                    <label id="description">Mô Tả:</label>
                    <div id="editor" style="min-height: 200px;height: 450px;">
                        {!! $description !!}
                    </div>
                </div>
            </div>
            <!-- Right Column (30%) -->
            <div class="column-right">
                <div class="form-group">
                    <label for="priority">Độ Ưu Tiên: <span
                            class="priority-{{$priority}}">{{$priorityMessage}}</span></label>
                </div>
                <div class="form-group">
                    <label for="priority">Trạng Thái: <span
                            class="priority-{{$status}}">{{$statusMessage}}</span></label>
                </div>
                <div class="form-group">
                    <label id="assignee" for="user">Người Thực Hiện:
                        <img class="avatar-app" src="{{ $assigneeAvatar ?? asset('avatar.png') }}"
                             alt="{{$assigneeName}}">
                        <p class="assignee-name">{{$assigneeName}}</p></label>
                </div>
                <div class="form-group">
                    <label for="deadline">Loại Công Việc: {{$type}}</label>
                </div>
                <div class="form-group">
                    <label for="deadline">Thời Hạn: <label
                            class="{{ $isDeadline ? 'deadline-overdue' : 'deadline-upcoming' }}">{{$deadline}}</label></label>
                </div>
                <hr>
                <div class="form-group">
                    {{--                    TODO: add history--}}
                    <p class="history-prefix">Hoạt Động:</p>
                    <div class="log-list" id="log-list">
                            @foreach($histories as $history)
                                <div class="log-item">
                                    <div class="comment-own">
                                        <img class="avatar-app" src="{{ $history['avatar'] ?? asset('avatar.png') }}" alt="{{$history['name']}}">
                                        <div class="comment-own-infor">
                                            <p class="comment-own-infor-name">{{$history['name']}}</p>
                                            <p class="comment-own-infor-date">{{$history['createTime']}}</p>
                                        </div>
                                    </div>
                                    <p class="history-description">
                                        {{$history['description']}}
                                    </p>
                                </div>
                            @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="child-task container">
        <!-- Hidden Checkbox -->
        <input type="checkbox" id="toggle-child-tasks" style="display: none;"/>

        <!-- Label as Toggle -->
        <label for="toggle-child-tasks" class="task-label">
            Công Việc Con <span>▼</span>
        </label>

        <!-- File List -->
        <ul id="child-task-list">
            <li class="child-task-item">
                <a href="/project/{{$projectId}}/task/{{$taskId}}/child/create" onclick="displayLoading()">+ Thêm công việc con</a>
            </li>
            @foreach($childTasks as $childTask)
                <li class="child-task-item">
                    <a href="/project/{{$projectId}}/task/{{$childTask['task_id']}}" onclick="displayLoading()">{{$childTask['title']}}</a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="file-attachment container">
        <!-- File Attachment -->
        <input type="checkbox" id="toggle-files" style="display: none;"/>

        <!-- Label to Toggle the File List -->
        <label for="toggle-files" class="file-label">
            Files Đính Kèm <span>▼</span>
        </label>

        <!-- File List -->
        <ul id="file-list">
            <li class="file-item">
                <!-- Clickable text to trigger file input -->
                <span id="upload-text" style="cursor: pointer; color: blue; text-decoration: underline;">Thêm file</span>

                <!-- Hidden file input -->
                <input type="file" id="file-input" name="file" style="display: none;"/>

                <!-- Submit button -->
                <button id="submit-file">Submit</button>

                <!-- Placeholder for displaying the returned file -->
                <div id="file-display"></div>
            </li>
            @foreach($attachments as $attachment)
                <li class="file-item">
                    <a href="/project/{{$projectId}}/task/{{$taskId}}/attachment/{{$attachment['file_id']}}/download">
                        {{$attachment['fileName']}}
                    </a>
                    <a href="/project/{{$projectId}}/task/{{$taskId}}/attachment/{{$attachment['file_id']}}/delete"
                       class="delete-cross" title="Xóa" data-delete-url="/project/{{$projectId}}/task/{{$taskId}}/attachment/{{$attachment['file_id']}}/delete">×</a>
                </li>
            @endforeach
        </ul>
    </div>

    <h3>Bình Luận</h3>
    <!-- Comment Display Area -->
    <div class="comments-section" id="commentsSection">
        <!-- Comments will be dynamically generated here -->
        @foreach($comments as $comment)
            <div class="comment container">
                <div class="comment-own">
                    <img class="avatar-app" src="{{ $comment['commentAvatar'] ?? asset('avatar.png') }}" alt="{{$comment['commentName']}}">
                    <div class="comment-own-infor">
                        <h4 class="comment-own-infor-name">{{$comment['commentName']}}</h4>
                        <p class="comment-own-infor-date">{{$comment['created_at']}}</p>
                    </div>
                </div>
                <div class="comment-details">
                    <div class="notified-users">
                        <p class="notify-prefix">Thông báo tới:</p>
                        @foreach($comment['notification'] as $userNotify )
                            <div class="notify-container">
                                <img class="avatar-app avatar-app-notify" src="{{ $userNotify['avatar'] ?? asset('avatar.png') }}" alt="{{$userNotify['name']}}">
                                <div class="notify-name">{{$userNotify['name']}}</div>
                            </div>
                        @endforeach
                    </div>
                    <p class="comment-content">{{$comment['comment_text']}}</p>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Add Comment Form -->
    <div class="add-comment-form container">
        <div class="comment-input">
            <h3>Thêm Bình Luận</h3>
            <form id="commentForm">
                <input name="projectId" id="projectId" value="{{$projectId}}" hidden>
                <input name="taskId" id="taskId" value="{{$taskId}}" hidden>
                <div class="comment-input-area">
                    <div class="column-left-comment">
                        <div class="form-group">
                            <textarea id="commentText" class="form-control" rows="4"
                                      placeholder="Nhập bình luận..."></textarea>
                        </div>
                    </div>
                    <div class="column-right-comment">
                        <div class="notified-area">
                            <h4 class="notify-label">Người Nhận Thông Báo</h4>
                            <input type="text" id="userSearch" class="form-control"
                                   placeholder="Nhập tên cần tìm..." autocomplete="off">
                            <div id="selectedUsers" class="selected-users"></div>
                            <!-- Selected users list always visible -->
                            <ul id="userResults" class="user-results">
                                @foreach($members as $member)
                                    <li class="user-item" data-user-id="{{$member['id']}}" data-user-name="{{$member['name']}}">{{$member['name']}}</li>
                                @endforeach
                            </ul>
                            <input type="hidden" name="notifiedUsers" id="notifiedUsers" value="">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Gửi Bình Luận</button>
            </form>
        </div>
    </div>

</main>
<!-- Loading Overlay and Indicator -->
<div id="loadingOverlay">
    <div class="lds-dual-ring"></div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script>
    let members = @json($members->toArray());
</script>
<script src="/js/page/taskindex.js"></script>
@include('layouts.footer')
