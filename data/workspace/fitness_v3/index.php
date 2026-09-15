<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود | AK Fitness Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --neon: #00d4ff;
            --neon-glow: rgba(0, 212, 255, 0.4);
            --neon-dim: rgba(0, 212, 255, 0.15);
            --bg-deep: #0a0e1a;
            --bg-card: rgba(15, 20, 40, 0.65);
            --bg-input: rgba(255, 255, 255, 0.05);
            --border: rgba(255, 255, 255, 0.08);
            --text: #e4e8f1;
            --text-dim: #7a8199;
            --error: #ff4d6a;
            --success: #00e676;
        }

        body {
            font-family: 'Vazirmatn', sans-serif;
            background: var(--bg-deep);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Animated gradient background */
        .bg-gradient {
            position: fixed;
            inset: 0;
            background: 
                radial-gradient(ellipse 80% 60% at 20% 30%, rgba(0, 212, 255, 0.08) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 80% 70%, rgba(80, 40, 200, 0.06) 0%, transparent 60%),
                radial-gradient(ellipse 90% 50% at 50% 100%, rgba(0, 212, 255, 0.04) 0%, transparent 50%);
            animation: bgShift 12s ease-in-out infinite alternate;
            z-index: 0;
        }

        @keyframes bgShift {
            0% { opacity: 0.6; transform: scale(1) translate(0, 0); }
            50% { opacity: 1; transform: scale(1.05) translate(-2%, 1%); }
            100% { opacity: 0.7; transform: scale(1) translate(1%, -1%); }
        }

        /* Floating orbs */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(60px);
            z-index: 0;
            pointer-events: none;
        }

        .orb-1 {
            width: 300px;
            height: 300px;
            background: rgba(0, 212, 255, 0.07);
            top: -100px;
            right: -80px;
            animation: float1 8s ease-in-out infinite;
        }

        .orb-2 {
            width: 250px;
            height: 250px;
            background: rgba(80, 40, 200, 0.06);
            bottom: -80px;
            left: -60px;
            animation: float2 10s ease-in-out infinite;
        }

        .orb-3 {
            width: 180px;
            height: 180px;
            background: rgba(0, 212, 255, 0.05);
            top: 50%;
            left: 50%;
            animation: float3 14s ease-in-out infinite;
        }

        @keyframes float1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(-30px, 40px) scale(1.1); }
            66% { transform: translate(20px, -20px) scale(0.95); }
        }

        @keyframes float2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(40px, -30px) scale(1.1); }
        }

        @keyframes float3 {
            0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.3; }
            50% { transform: translate(-50%, -50%) scale(1.3); opacity: 0.6; }
        }

        /* Particles */
        .particles {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: var(--neon);
            border-radius: 50%;
            opacity: 0;
            animation: particleFloat linear infinite;
        }

        .particle:nth-child(1) { left: 10%; animation-duration: 12s; animation-delay: 0s; }
        .particle:nth-child(2) { left: 20%; animation-duration: 15s; animation-delay: 2s; width: 2px; height: 2px; }
        .particle:nth-child(3) { left: 35%; animation-duration: 10s; animation-delay: 4s; }
        .particle:nth-child(4) { left: 50%; animation-duration: 18s; animation-delay: 1s; width: 4px; height: 4px; }
        .particle:nth-child(5) { left: 65%; animation-duration: 14s; animation-delay: 3s; width: 2px; height: 2px; }
        .particle:nth-child(6) { left: 75%; animation-duration: 11s; animation-delay: 5s; }
        .particle:nth-child(7) { left: 85%; animation-duration: 16s; animation-delay: 0.5s; width: 2px; height: 2px; }
        .particle:nth-child(8) { left: 45%; animation-duration: 13s; animation-delay: 6s; }
        .particle:nth-child(9) { left: 5%; animation-duration: 17s; animation-delay: 3.5s; width: 2px; height: 2px; }
        .particle:nth-child(10) { left: 92%; animation-duration: 9s; animation-delay: 1.5s; }
        .particle:nth-child(11) { left: 28%; animation-duration: 20s; animation-delay: 7s; width: 2px; height: 2px; }
        .particle:nth-child(12) { left: 58%; animation-duration: 11s; animation-delay: 2.5s; }

        @keyframes particleFloat {
            0% { transform: translateY(100vh) scale(0); opacity: 0; }
            10% { opacity: 0.6; }
            90% { opacity: 0.6; }
            100% { transform: translateY(-100vh) scale(1); opacity: 0; }
        }

        /* Card */
        .login-card {
            position: relative;
            z-index: 10;
            width: 92%;
            max-width: 400px;
            background: var(--bg-card);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 48px 32px 40px;
            animation: cardAppear 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(30px) scale(0.96);
        }

        @keyframes cardAppear {
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .login-card::before {
            content: '';
            position: absolute;
            inset: -1px;
            border-radius: 24px;
            padding: 1px;
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.15), transparent 50%, rgba(0, 212, 255, 0.08));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        /* Logo */
        .logo {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-icon {
            font-size: 48px;
            display: block;
            margin-bottom: 12px;
            filter: drop-shadow(0 0 20px var(--neon-glow));
            animation: iconPulse 3s ease-in-out infinite;
        }

        @keyframes iconPulse {
            0%, 100% { transform: scale(1); filter: drop-shadow(0 0 20px var(--neon-glow)); }
            50% { transform: scale(1.05); filter: drop-shadow(0 0 30px var(--neon-glow)); }
        }

        .logo-text {
            font-size: 26px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--neon), #7b61ff, var(--neon));
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: shimmer 4s linear infinite;
            text-shadow: none;
        }

        .logo-text-glow {
            text-shadow: 0 0 40px var(--neon-glow);
        }

        @keyframes shimmer {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }

        .logo-subtitle {
            font-size: 13px;
            color: var(--text-dim);
            margin-top: 6px;
            font-weight: 300;
            letter-spacing: 1px;
        }

        /* Form */
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-dim);
            margin-bottom: 8px;
            transition: color 0.3s;
        }

        .form-group:focus-within label {
            color: var(--neon);
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper .icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            opacity: 0.4;
            transition: opacity 0.3s;
            pointer-events: none;
        }

        .form-group:focus-within .icon {
            opacity: 0.9;
        }

        .input-wrapper input {
            width: 100%;
            padding: 14px 44px 14px 14px;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 14px;
            color: var(--text);
            font-family: 'Vazirmatn', sans-serif;
            font-size: 15px;
            font-weight: 400;
            outline: none;
            transition: all 0.35s cubic-bezier(0.25, 0.8, 0.25, 1);
            direction: rtl;
        }

        .input-wrapper input::placeholder {
            color: rgba(122, 129, 153, 0.6);
        }

        .input-wrapper input:focus {
            border-color: var(--neon);
            background: rgba(0, 212, 255, 0.04);
            box-shadow: 0 0 0 4px rgba(0, 212, 255, 0.08), 0 0 20px rgba(0, 212, 255, 0.06);
        }

        /* Toggle password */
        .toggle-pass {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-dim);
            font-size: 18px;
            cursor: pointer;
            padding: 4px;
            transition: color 0.3s;
            z-index: 2;
        }

        .toggle-pass:hover {
            color: var(--neon);
        }

        /* Button */
        .btn-login {
            width: 100%;
            padding: 15px;
            margin-top: 8px;
            background: linear-gradient(135deg, #00b4d8, #0077b6);
            border: none;
            border-radius: 14px;
            color: #fff;
            font-family: 'Vazirmatn', sans-serif;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 4px 20px rgba(0, 180, 216, 0.25);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 180, 216, 0.35);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent, rgba(255,255,255,0.15), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s;
        }

        .btn-login:hover::after {
            transform: translateX(100%);
        }

        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-login .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            margin: 0 auto;
        }

        .btn-login.loading .btn-text { display: none; }
        .btn-login.loading .spinner { display: block; }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Links */
        .form-footer {
            text-align: center;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }

        .form-footer a {
            color: var(--neon);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            position: relative;
        }

        .form-footer a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            right: 0;
            width: 0;
            height: 2px;
            background: var(--neon);
            transition: width 0.3s;
            border-radius: 1px;
        }

        .form-footer a:hover::after {
            width: 100%;
        }

        .form-footer a:hover {
            text-shadow: 0 0 15px var(--neon-glow);
        }

        .form-footer span {
            color: var(--text-dim);
            font-size: 14px;
        }

        /* Messages */
        .message {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 16px;
            display: none;
            align-items: center;
            gap: 8px;
            animation: messageSlide 0.4s ease-out;
        }

        .message.show {
            display: flex;
        }

        .message.error {
            background: rgba(255, 77, 106, 0.1);
            border: 1px solid rgba(255, 77, 106, 0.2);
            color: var(--error);
        }

        .message.success {
            background: rgba(0, 230, 118, 0.1);
            border: 1px solid rgba(0, 230, 118, 0.2);
            color: var(--success);
        }

        @keyframes messageSlide {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card {
                padding: 40px 24px 32px;
                border-radius: 20px;
            }
            .logo-icon { font-size: 40px; }
            .logo-text { font-size: 22px; }
        }

        @media (min-height: 800px) {
            body { padding: 40px 0; }
        }
    </style>
</head>
<body>
    <div class="bg-gradient"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="login-card">
        <div class="logo">
            <span class="logo-icon">💪</span>
            <div class="logo-text logo-text-glow">AK Fitness Pro</div>
            <div class="logo-subtitle">فیتنس حرفه‌ای</div>
        </div>

        <div id="message" class="message">
            <span id="msg-icon"></span>
            <span id="msg-text"></span>
        </div>

        <form id="loginForm" autocomplete="off">
            <div class="form-group">
                <label for="username">نام کاربری</label>
                <div class="input-wrapper">
                    <span class="icon">👤</span>
                    <input type="text" id="username" name="username" placeholder="نام کاربری خود را وارد کنید" required autocomplete="username">
                </div>
            </div>

            <div class="form-group">
                <label for="password">رمز عبور</label>
                <div class="input-wrapper">
                    <span class="icon">🔒</span>
                    <input type="password" id="password" name="password" placeholder="رمز عبور خود را وارد کنید" required autocomplete="current-password">
                    <button type="button" class="toggle-pass" onclick="togglePassword()" tabindex="-1">👁️</button>
                </div>
            </div>

            <button type="submit" class="btn-login" id="loginBtn">
                <span class="btn-text">ورود</span>
                <div class="spinner"></div>
            </button>
        </form>

        <div class="form-footer">
            <a href="register.php">حساب ندارید؟ ثبت نام</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const inp = document.getElementById('password');
            const btn = document.querySelector('.toggle-pass');
            if (inp.type === 'password') {
                inp.type = 'text';
                btn.textContent = '🙈';
            } else {
                inp.type = 'password';
                btn.textContent = '👁️';
            }
        }

        function showMessage(type, text) {
            const msg = document.getElementById('message');
            const icon = document.getElementById('msg-icon');
            const msgText = document.getElementById('msg-text');
            msg.className = 'message ' + type;
            icon.textContent = type === 'error' ? '⚠️' : '✅';
            msgText.textContent = text;
            msg.classList.add('show');
        }

        function hideMessage() {
            document.getElementById('message').classList.remove('show');
        }

        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            hideMessage();

            const btn = document.getElementById('loginBtn');
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;

            if (!username || !password) {
                showMessage('error', 'لطفاً تمام فیلدها را پر کنید');
                return;
            }

            btn.classList.add('loading');
            btn.disabled = true;

            try {
                const formData = new FormData();
                formData.append('username', username);
                formData.append('password', password);

                const response = await fetch('api/auth.php?action=login', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showMessage('success', 'ورود موفق! در حال انتقال...');
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 1200);
                } else {
                    showMessage('error', result.message || 'نام کاربری یا رمز عبور اشتباه است');
                    btn.classList.remove('loading');
                    btn.disabled = false;
                }
            } catch (err) {
                showMessage('error', 'خطا در ارتباط با سرور. لطفاً دوباره تلاش کنید');
                btn.classList.remove('loading');
                btn.disabled = false;
            }
        });

        // Focus animation for inputs
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                this.closest('.form-group').style.transform = 'scale(1.01)';
            });
            input.addEventListener('blur', function() {
                this.closest('.form-group').style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>