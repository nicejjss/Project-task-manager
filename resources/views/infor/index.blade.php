@include('layouts.app')
<link rel="stylesheet" href="/css/page/infor.css">
<div id="content">
    <h2 id="page-text">Thông tin User</h2>
    <div class="container">
        <form id="profileForm">
        <div class="user-info">
                @csrf
                <label for="name">Tên tài khoản:<span style="color: red">*</span></label>
                <input type="text" id="name" name="name" placeholder="Tên tài khoản" required value="{{$user->name}}">

                <label for="email">Email:<span style="color: red">*</span></label>
                <input type="email" id="email" name="email" placeholder="Email của bạn" required value="{{$user->email}}">
        </div>
        <div class="avatar-section">
            <img id="avatar" src="{{ $avatar ?? asset('avatar.png') }}" alt="User Avatar" class="avatar-img">
            <input type="file" id="avatarInput" name="avatar" accept="image/png, image/jpeg" hidden value="">
            <div id="changeAvatarBtn">Đổi Avatar</div>
            <div id="errorMsg" class="error-msg"></div>
        </div>
        </form>
    </div>
    <div class="buttons">
        <button id="saveBtn" class="btn">Cập Nhật</button>
        <button id="changePasswordBtn" class="btn">Đổi Mật Khẩu</button>
        <button id="cancelBtn" class="btn">Quay Lại</button>
    </div>
</div>

<!-- Modal for Success or Error Messages -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close">X</span>
        <p id="modalMessage"></p>
    </div>
</div>

<!-- Loading Overlay and Indicator -->
<div id="loadingOverlay">
    <div class="lds-dual-ring"></div>
</div>
<script src="/js/page/infor.js"></script>
@include('layouts.footer')
