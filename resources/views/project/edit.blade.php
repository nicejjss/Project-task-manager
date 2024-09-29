@include('layouts.app')
<!-- include libraries(jQuery, bootstrap) -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<!-- include summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<link rel="stylesheet" href="/css/page/projectcreate.css">
<style>
    .note-editor.note-frame .note-editing-area .note-editable, .note-editor.note-airframe .note-editing-area .note-editable {
        max-height: 450px !important; /* Ensure max height is applied */
        overflow-y: auto;  /* Enable vertical scrolling */
        padding-left: 20px; /* Add padding to the left */
        padding-right: 20px; /* Add padding to the right */
    }
    .note-resize {
        display: none !important; /* Hide the resize bar */
    }

    #right-column{
        width: 35%;
        display: flex;
        flex-direction: column;
    }
</style>
<main class="content">
    <div class="container">
        <!-- Title -->
        <h2 style="text-align: center;">Sửa Dự Án</h2>

        <!-- Form and Email Invitation Section Side by Side -->
        <div style="display: flex; justify-content: space-between;">
            <!-- Main Form -->
            <div style="width: 60%;">
                <form id="projectForm">
                    @csrf
                    <input type="hidden" id="projectID" name="projectID" required value="{{$projectId}}">
                    <!-- Project Name Input -->
                    <label for="projectName">Tên Dự Án <span style="color: red">*</span></label>
                    <input type="text" id="projectName" name="projectName" required value="{{$name}}">

                    <label for="projectDescription">Mô Tả</label>
                    <textarea  id="editor" style="height: 450px;">
                        {!! $description !!}
                    </textarea >

                    <!-- Create Project Button -->
                    <div class="btn-function">
                        <button style="width: 100%" type="submit" id="createBtn">Sửa Dự Án</button>
                        <a id="back-link" href="/project/{{$projectId}}"><div id="backBtn">Quay Lại</div></a>
                    </div>
                </form>
            </div>

            <!-- Email Invitation Section -->
            <div id="right-column">
                <label for="inviteEmail" style="display: block">Mời Thành Viên Qua Email</label>
                <div style="display: flex; justify-content: space-between;">
                    <div style="width: 60%">
                        <input type="email" id="inviteEmail" placeholder="Email">
                        <span id="emailError" style="color: red; display: none;"></span>
                    </div>
                    <div style="width: 40%; text-align: center;">
                        <button style="margin: 0; width: 60%" type="button" id="addPersonBtn">Thêm</button>
                    </div>
                </div>

                <!-- List of Invited People -->
                <ul id="invitedList">
                    <div>Danh Sách Mời:</div>
                    <script>
                        let invitedPeople = [];
                    </script>
                    @foreach($members as $member)
                        <li>{{$member->user->email}} <span style="color: red;
                                                               cursor: pointer;
                                                               position: absolute;
                                                               right: 40px;"
                                             class="delete-icon"
                                             data-email="{{$member->user->email}}">
                                                               X</span>
                        </li>
                        <script>
                            invitedPeople.push('{{ $member->user->email }}');
                        </script>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</main>
<!-- Custom Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <p>Bạn có chắc chắn muốn xóa email này khỏi danh sách?</p>
        <button id="confirmDeleteBtn">Xác nhận</button>
        <button id="cancelDeleteBtn">Hủy bỏ</button>
    </div>
</div>

<!-- Loading Overlay and Indicator -->
<div id="loadingOverlay">
    <div class="lds-dual-ring"></div>
</div>
<script src="/js/page/projectedit.js"> </script>
@include('layouts.footer')
