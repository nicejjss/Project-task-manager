@include('layouts.app')
@include('layouts.sidebar')
<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/css/page/taskindex.css">
<main class="content">
{{--    TODO: not finish UI--}}
    <div class="container">
        @if ($errors->any())
            <div class="error" style="margin: 20px 0;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="form-row">
            <!-- Left Column (70%) -->
            <div class="column-left">
                <div class="form-group">
                    <label for="title">Tên Công Việc: Quanr lysabksbfka bkef asdfa we f</label>
                </div>
                <div class="form-group">
                    <label for="description">Mô Tả:</label>
                    <div id="editor" style="min-height: 200px;height: 450px;">
{{--                        {!! !!}--}}
                    </div>
                </div>
            </div>
            <!-- Right Column (30%) -->
            <div class="column-right">
                <div class="form-group">
                    <label for="priority">Độ Ưu Tiên: <span style="color: red">Cao</span></label>
                </div>
                <div class="form-group">
                    <label for="priority">Trạng Thái: <span style="color: red">Cần Thực Hiện</span></label>
                </div>
                <div class="form-group">
                    <label for="user">Người Thực Hiện: <img class="avatar-app" src="{{asset('avatar.png')}}"></label>
                </div>
                <div class="form-group">
                    <label for="deadline">Loại Công Việc: Code</label>
                </div>
                <div class="form-group">
                    <label for="deadline">Thời Hạn: 12/07/2024</label>
                </div>

                <div class="form-group">
                    <label for="deadline">Hoạt Động</label>
                </div>

            </div>
        </div>
    </div>

    <div class="child-taks">
        <!-- File Attachment -->
        <div class="form-group">
            <label>Công Việc Con <span>▼</span></label>
        </div>
        <!-- File List -->
        <ul id="child-task-list"></ul>
    </div>

    <div class="file-attachment">
        <!-- File Attachment -->
        <div class="form-group">
            <label>Files Đính Kèm <span>▼</span></label>
        </div>
        <!-- File List -->
        <ul id="fileList"></ul>
    </div>

    <h3>Bình Luận</h3>
    <!-- Comment Display Area -->
    <div class="comments-section" id="commentsSection">
        <!-- Comments will be dynamically generated here -->
        <div class="comment">
            <img class="avatar-app" src="https://via.placeholder.com/50" alt="User Avatar">
            <div class="comment-details">
                <div class="notified-users">
                    <span>Thông báo tới: <strong>Nguyen A, Le B</strong></span>
                </div>
                <p class="comment-content">Đây là nội dung của bình luận số 1.</p>
            </div>
        </div>
        <div class="comment">
            <img class="avatar-app" src="https://via.placeholder.com/50" alt="User Avatar">
            <div class="comment-details">
                <div class="notified-users">
                    <span>Thông báo tới: <strong>Tran C, Pham D</strong></span>
                </div>
                <p class="comment-content">Đây là nội dung của bình luận số 2.</p>
            </div>
        </div>
        <div class="comment">
            <img class="avatar-app" src="https://via.placeholder.com/50" alt="User Avatar">
            <div class="comment-details">
                <div class="notified-users">
                    <span>Thông báo tới: <strong>Nguyen A</strong></span>
                </div>
                <p class="comment-content">Đây là nội dung của bình luận số 3.</p>
            </div>
        </div>
    </div>

    <!-- Add Comment Form -->
    <div class="add-comment-form">
        <div class="comment-input">
            <h3>Thêm Bình Luận</h3>
            <form id="commentForm">
                <div class="comment-input-area">
                    <div class="column-left-comment"><div class="form-group">
                            <textarea id="commentText" class="form-control" rows="4" placeholder="Nhập bình luận..."></textarea>
                        </div></div>
                    <div class="column-right-comment"><div class="notified-area">
                            <h4>Người Nhận Thông Báo</h4>
                            <input type="text" id="userSearch" class="form-control" placeholder="Tìm người dùng để thông báo..." autocomplete="off">
                            <div id="selectedUsers" class="selected-users"></div> <!-- Selected users list always visible -->
                            <ul id="userResults" class="user-results">
                                <li class="user-item" data-user-id="1" data-user-name="Nguyen A">Nguyen A</li>
                                <li class="user-item" data-user-id="2" data-user-name="Le B">Le B</li>
                                <li class="user-item" data-user-id="3" data-user-name="Tran C">Tran C</li>
                                <li class="user-item" data-user-id="4" data-user-name="Pham D">Pham D</li>
                            </ul> <!-- Hardcoded user list -->
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
{{--<script>--}}
{{--    var projectId = {{$projectId}}; // Pass the $projectId variable to your JavaScript file--}}
{{--</script>--}}
<script src="/js/page/taskindex.js"></script>
@include('layouts.footer')
