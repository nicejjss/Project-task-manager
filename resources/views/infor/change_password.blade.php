@include('layouts.app')
<style>
    .container {
        display: flex;
        justify-content: center;
        align-items: center;
        position: absolute;
        top: 50%;
        left: 48%;
        transform: translate(-50%, -50%);
    }

    .auth-container {
        background-color: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        width: 450px;
        max-width: 450px;
    }

    h2 {
        color: #333;
        margin-bottom: 20px;
        font-weight: 600;
        font-size: 24px;
    }

    .error {
        color: red;
        background-color: #ffe5e5;
        padding: 10px;
        border-radius: 5px;
    }

    form input {
        border: 2px solid #ccc;
        border-radius: 5px;
        padding: 10px;
        width: 100%;
        font-size: 16px;
        transition: border-color 0.3s ease;
        margin-bottom: 10px;
        outline: none;
    }

    button {
        background-color: #4caf93;
        border: none;
        padding: 10px;
        border-radius: 5px;
        font-size: 16px;
        color: white;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #42a386;
    }

    .alert-success {
        color: #155724;
        background-color: #d4edda;
        padding: 10px;
        border-radius: 5px;
        margin-top: 20px;
    }

    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        padding: 10px;
        border-radius: 5px;
        margin-top: 20px;
    }

    .error ul{
        list-style: none;
    }

    .btns {
        display: flex;
        gap: 10px;
    }

    .d-grid {
        width: 50%;
    }

    .d-grid button {
        width: 100%;
    }

    #back-to-infor {
        background-color: #ffc107;
        border: none;
        padding: 10px;
        border-radius: 5px;
        font-size: 16px;
        color: white;
        cursor: pointer;
        transition: background-color 0.3s ease;
        text-decoration: none;
        text-align: center;
        display: block;
    }

    #back-to-infor:hover {
        background-color: #efb507;
    }
</style>
<div class="container">
    <div class="auth-container">
        <!-- Forgot Password Form -->
        <div id="forgot-password-form" class="auth-form">
            <h2 class="text-center">Đổi Mật Khẩu</h2>
            @if ($errors->any())
                <div class="error" style="margin: 20px 0;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="/user/change_password" method="POST">
                @csrf
                <div class="mb-3">
                    <input type="password" class="form-control" id="signup-password" name="password" required placeholder="Nhập Mật Khẩu">
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" id="confirm-password" name="confirm_pass" required placeholder="Nhập Lại Mật Khẩu">
                </div>
                <div class="btns">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning">Tiếp Tục</button>
                    </div>
                    <div class="d-grid">
                        <a href="/user" id="back-to-infor" class="btn btn-secondary">
                            Quay lại
                        </a>
                    </div>
                </div>
            </form>
        </div>
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
@include('layouts.footer')
