@include('layouts.app')
<link rel="stylesheet" href="/css/page/projectcreate.css">
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
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
</style>
<main class="content">
    <div class="container">
        <h2 style="text-align: center"> Thêm Dự Án</h2>
        <form id="projectForm">
            @csrf
            <!-- Project Name Input -->
            <label for="projectName">Tên Dự Án <span style="color: red">*</span></label>
            <input type="text" id="projectName" name="projectName" required>

            <!-- QuillJS WYSIWYG Editor -->
            <label for="projectDescription">Mô Tả</label>
            <div id="editor" style="min-height: 200px;"></div>

            <!-- Email Invitation Input -->
            <label for="inviteEmail">Mời Thành Viên Qua Email</label>
            <div style="display: flex;justify-content: space-between;">
                <div style="width: 60%">
                    <input type="email" id="inviteEmail" placeholder="Email">
                    <span id="emailError" style="color: red; display: none;"></span>
                </div>
                <div style="width: 40%;text-align: center;"><button  style="margin: 0;width: 60%" type="button" id="addPersonBtn">Thêm</button></div>
            </div>

            <!-- List of Invited People -->
            <ul id="invitedList">
                <div>Danh Sách Mời:</div>
            </ul>

            <!-- Create Project Button -->
            <button type="submit" id="createBtn">Tạo Dự Án</button>
        </form>
    </div>
</main>
<!-- Loading Overlay and Indicator -->
<div id="loadingOverlay">
    <div id="loadingIndicator">Loading, please wait...</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/showdown/1.9.1/showdown.min.js"></script>
<script src="/js/page/projectcreate.js"> </script>
@include('layouts.footer')
