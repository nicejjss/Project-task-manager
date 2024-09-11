<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/authentication/styles.css">
</head>
<body>

<div class="container">
    <div class="auth-container">
        <!-- Logo Section -->
        <div class="logo">
            <img src="./logo.png" alt="Logo">
        </div>

        <!-- Sign Up Form -->
        <div id="signup-form" class="auth-form">
            <h2 class="text-center">Đăng Ký</h2>
            <form action="/signup" method="POST">
                @csrf
                <div class="mb-3">
                    <input type="text" class="form-control" id="signup-email" name="email" required placeholder="Nhập Email">
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" id="signup-password" name="password" required placeholder="Nhập Mật Khẩu">
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" id="confirm-password" name="confirm-password" required placholder="Nhập Lại Mật Khẩu">
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Đăng Ký</button>
                </div>
                <div class="form-toggle">
                    Already have an account? <a href="{{route('login')}}" id="login-link">Đăng Nhập</a>
                </div>
            </form>
            <div class="separator">HOẶC</div>
            <div class="text-center mt-3">
                <a href="{{route('google.login')}}">
                    <button class="btn btn-outline-dark">
                        <img src="https://img.icons8.com/color/16/000000/google-logo.png"/> Đăng Ký Bằng Google
                    </button>
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="./js/authentication/script.js"></script>
</body>
</html>
