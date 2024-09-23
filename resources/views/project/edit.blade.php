@include('layouts.app')
<link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote.css" rel="stylesheet">
<link rel="stylesheet" href="/css/page/projectcreate.css">
<style>
    /* Style for the loading overlay */
    #loadingOverlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5); /* Semi-transparent black overlay */
        z-index: 1000;
    }

    /* Center the loading indicator */
    #loadingIndicator {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #4caf93;
        font-size: 24px;
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }

    .note-editor.note-frame .note-editing-area .note-editable, .note-editor.note-airframe .note-editing-area .note-editable {
        max-height: 450px !important; /* Ensure max height is applied */
        overflow-y: auto;  /* Enable vertical scrolling */
        padding-left: 20px; /* Add padding to the left */
        padding-right: 20px; /* Add padding to the right */
    }
    .note-resize {
        display: none !important; /* Hide the resize bar */
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
                    <!-- Project Name Input -->
                    <label for="projectName">Tên Dự Án <span style="color: red">*</span></label>
                    <input type="text" id="projectName" name="projectName" required value="{{$name}}">

                    <!-- QuillJS WYSIWYG Editor -->
                    <label for="projectDescription">Mô Tả</label>
                    <textarea  id="editor" style="height: 450px;">
                        {!! $description !!}
                    </textarea >

                    <!-- Create Project Button -->
                    <button type="submit" id="createBtn">Sửa Dự Án</button>
                </form>
            </div>

            <!-- Email Invitation Section -->
            <div style="width: 35%;">
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote.min.js"></script>
<script src="/js/page/projectcreate.js"> </script>
@include('layouts.footer')
