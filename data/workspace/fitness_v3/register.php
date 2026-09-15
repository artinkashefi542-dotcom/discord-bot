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
    <title>ثبت‌نام | AK Fitness Pro</title>
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
            overflow-x: hidden;
            overflow-y: auto;
            position: relative;
            padding: 20px 0;
        }

        /* Animated gradient background */
        .bg-gradient {
            position: fixed;
            inset: 0;
            background: 
                radial-gradient(ellipse 80% 60% at 80% 30%, rgba(0, 212, 255, 0.08) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 20% 70%, rgba(80, 40, 200, 0.06) 0%, transparent 60%),
                radial-gradient(ellipse 90% 50% at 50% 0%, rgba(0, 212, 255, 0.04) 0%, transparent 50%);
            animation: bgShift 12s ease-in-out infinite alternate;
            z-index: 0;
        }

        @keyframes bgShift {
            0% { opacity: 0.6; transform: scale(1) translate(0, 0); }
            50% { opacity: 1; transform: scale(1.05) translate(2%, 1%); }
            100% { opacity: 0.7; transform: scale(1) translate(-1%, -1%); }
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
            left: -80px;
            animation: float1 8s ease-in-out infinite;
        }

        .orb-2 {
            width: 250px;
            height: 250px;
            background: rgba(80, 40, 200, 0.06);
            bottom: -80px;
            right: -60px;
            animation: float2 10s ease-in-out infinite;
        }

        .orb-3 {
            width: 180px;
            height: 180px;
            background: rgba(0, 212, 255, 0.05);
            top: 50%;
            right: 30%;
            animation: float3 14s ease-in-out infinite;
        }

        @keyframes float1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, 40px) scale(1.1); }
            66% { transform: translate(-20px, -20px) scale(0.95); }
        }

        @keyframes float2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-40px, -30px) scale(1.1); }
        }

        @keyframes float3 {
            0%, 100% { transform: scale(1); opacity: 0.3; }
            50% { transform: scale(1.3); opacity: 0.6; }
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

        .particle:nth-child(1) { left: 8%; animation-duration: 14s; animation-delay: 0s; }
        .particle:nth-child(2) { left: 18%; animation-duration: 16s; animation-delay: 2s; width: 2px; height: 2px; }
        .particle:nth-child(3) { left: 32%; animation-duration: 11s; animation-delay: 4s; }
        .particle:nth-child(4) { left: 48%; animation-duration: 19s; animation-delay: 1s; width: 4px; height: 4px; }
        .particle:nth-child(5) { left: 62%; animation-duration: 13s; animation-delay: 3s; width: 2px; height: 2px; }
        .particle:nth-child(6) { left: 73%; animation-duration: 10s; animation-delay: 5s; }
        .particle:nth-child(7) { left: 82%; animation-duration: 17s; animation-delay: 0.5s; width: 2px; height: 2px; }
        .particle:nth-child(8) { left: 40%; animation-duration: 12s; animation-delay: 6s; }
        .particle:nth-child(9) { left: 5%; animation-duration: 18s; animation-delay: 3.5s; width: 2px; height: 2px; }
        .particle:nth-child(10) { left: 90%; animation-duration: 9s; animation-delay: 1.5s; }
        .particle:nth-child(11) { left: 25%; animation-duration: 21s; animation-delay: 7s; width: 2px; height: 2px; }
        .particle:nth-child(12) { left: 55%; animation-duration: 10s; animation-delay: 2.5s; }
        .particle:nth-child(13) { left: 68%; animation-duration: 15s; animation-delay: 4.5s; }
        .particle:nth-child(14) { left: 95%; animation-duration: 12s; animation-delay: 0.8s; width: 2px; height: 2px; }

        @keyframes particleFloat {
            0% { transform: translateY(100vh) scale(0); opacity: 0; }
            10% { opacity: 0.5; }
            90% { opacity: 0.5; }
            100% { transform: translateY(-100vh) scale(1); opacity: 0; }
        }

        /* Card */
        .register-card {
            position: relative;
            z-index: 10;
            width: 92%;
            max-width: 460px;
            background: var(--bg-card);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 44px 32px 36px;
            animation: cardAppear 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(30px) scale(0.96);
        }

        @keyframes cardAppear {
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .register-card::before {
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
            margin-bottom: 32px;
        }

        .logo-icon {
            font-size: 44px;
            display: block;
            margin-bottom: 10px;
            filter: drop-shadow(0 0 20px var(--neon-glow));
            animation: iconPulse 3s ease-in-out infinite;
        }

        @keyframes iconPulse {
            0%, 100% { transform: scale(1); filter: drop-shadow(0 0 20px var(--neon-glow)); }
            50% { transform: scale(1.05); filter: drop-shadow(0 0 30px var(--neon-glow)); }
        }

        .logo-text {
            font-size: 24px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--neon), #7b61ff, var(--neon));
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: shimmer 4s linear infinite;
        }

        .logo-subtitle {
            font-size: 13px;
            color: var(--text-dim);
            margin-top: 6px;
            font-weight: 300;
            letter-spacing: 1px;
        }

        @keyframes shimmer {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }

        /* Section labels */
        .section-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--neon);
            margin: 24px 0 14px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border);
        }

        .section-label:first-of-type {
            margin-top: 0;
        }

        .section-label .section-icon {
            font-size: 16px;
        }

        /* Form row - 2 columns */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .form-row .form-group.full-width {
            grid-column: 1 / -1;
        }

        /* Form group */
        .form-group {
            margin-bottom: 16px;
            position: relative;
            transition: transform 0.2s ease;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 500;
            color: var(--text-dim);
            margin-bottom: 6px;
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
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            opacity: 0.4;
            transition: opacity 0.3s;
            pointer-events: none;
        }

        .form-group:focus-within .icon {
            opacity: 0.9;
        }

        .input-wrapper input[type="text"],
        .input-wrapper input[type="password"],
        .input-wrapper input[type="number"] {
            width: 100%;
            padding: 12px 40px 12px 12px;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text);
            font-family: 'Vazirmatn', sans-serif;
            font-size: 14px;
            font-weight: 400;
            outline: none;
            transition: all 0.35s cubic-bezier(0.25, 0.8, 0.25, 1);
            direction: rtl;
        }

        .input-wrapper input::placeholder {
            color: rgba(122, 129, 153, 0.5);
        }

        .input-wrapper input:focus {
            border-color: var(--neon);
            background: rgba(0, 212, 255, 0.04);
            box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.08), 0 0 20px rgba(0, 212, 255, 0.06);
        }

        /* Remove number spinners */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="number"] {
            -moz-appearance: textfield;
        }

        /* Toggle password */
        .toggle-pass {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-dim);
            font-size: 16px;
            cursor: pointer;
            padding: 4px;
            transition: color 0.3s;
            z-index: 2;
        }

        .toggle-pass:hover {
            color: var(--neon);
        }

        /* Gender radio */
        .gender-group {
            display: flex;
            gap: 12px;
        }

        .gender-option {
            flex: 1;
            position: relative;
        }

        .gender-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .gender-option label.gender-label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 12px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-dim);
            transition: all 0.3s;
            margin-bottom: 0;
        }

        .gender-option label.gender-label .g-icon {
            font-size: 18px;
        }

        .gender-option input[type="radio"]:checked + label.gender-label {
            border-color: var(--neon);
            background: rgba(0, 212, 255, 0.08);
            color: var(--neon);
            box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.06);
        }

        .gender-option label.gender-label:hover {
            border-color: rgba(0, 212, 255, 0.3);
            background: rgba(0, 212, 255, 0.04);
        }

        /* Button */
        .btn-register {
            width: 100%;
            padding: 15px;
            margin-top: 28px;
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

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 180, 216, 0.35);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .btn-register::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent, rgba(255,255,255,0.15), transparent);
            transform: translateX(100%);
            transition: transform 0.6s;
        }

        .btn-register:hover::after {
            transform: translateX(-100%);
        }

        .btn-register:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-register .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            margin: 0 auto;
        }

        .btn-register.loading .btn-text { display: none; }
        .btn-register.loading .spinner { display: block; }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Links */
        .form-footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 18px;
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
            .register-card {
                padding: 36px 20px 28px;
                border-radius: 20px;
            }
            .logo-icon { font-size: 38px; }
            .logo-text { font-size: 20px; }
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
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
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="register-card">
        <div class="logo">
            <span class="logo-icon">💪</span>
            <div class="logo-text">AK Fitness Pro</div>
            <div class="logo-subtitle">ساخت حساب جدید</div>
        </div>

        <div id="message" class="message">
            <span id="msg-icon"></span>
            <span id="msg-text"></span>
        </div>

        <form id="registerForm" autocomplete="off">
            <!-- Personal Info -->
            <div class="section-label">
                <span class="section-icon">👤</span>
                <span>اطلاعات شخصی</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">نام</label>
                    <div class="input-wrapper">
                        <span class="icon">✏️</span>
                        <input type="text" id="first_name" name="first_name" placeholder="نام" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="last_name">نام خانوادگی</label>
                    <div class="input-wrapper">
                        <span class="icon">✏️</span>
                        <input type="text" id="last_name" name="last_name" placeholder="نام خانوادگی" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="username">نام کاربری</label>
                <div class="input-wrapper">
                    <span class="icon">👤</span>
                    <input type="text" id="username" name="username" placeholder="یک نام کاربری انتخاب کنید" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">رمز عبور</label>
                <div class="input-wrapper">
                    <span class="icon">🔒</span>
                    <input type="password" id="password" name="password" placeholder="رمز عبور خود را وارد کنید" required>
                    <button type="button" class="toggle-pass" onclick="togglePassword('password', this)" tabindex="-1">👁️</button>
                </div>
            </div>

            <!-- Body Info -->
            <div class="section-label">
                <span class="section-icon">🏋️</span>
                <span>اطلاعات بدنی</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="weight">وزن (کیلوگرم)</label>
                    <div class="input-wrapper">
                        <span class="icon">⚖️</span>
                        <input type="number" id="weight" name="weight" placeholder="مثلاً 75" min="20" max="300" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="height">قد (سانتی‌متر)</label>
                    <div class="input-wrapper">
                        <span class="icon">📏</span>
                        <input type="number" id="height" name="height" placeholder="مثلاً 180" min="100" max="250" required>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="age">سن</label>
                    <div class="input-wrapper">
                        <span class="icon">🎂</span>
                        <input type="number" id="age" name="age" placeholder="مثلاً 25" min="10" max="100" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>جنسیت</label>
                    <div class="gender-group">
                        <div class="gender-option">
                            <input type="radio" id="male" name="gender" value="male" checked>
                            <label for="male" class="gender-label">
                                <span class="g-icon">🧑</span>
                                <span>مرد</span>
                            </label>
                        </div>
                        <div class="gender-option">
                            <input type="radio" id="female" name="gender" value="female">
                            <label for="female" class="gender-label">
                                <span class="g-icon">👩</span>
                                <span>زن</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-register" id="registerBtn">
                <span class="btn-text">ثبت‌نام</span>
                <div class="spinner"></div>
            </button>
        </form>

        <div class="form-footer">
            <a href="index.php">حساب دارید؟ ورود</a>
        </div>
    </div>

    <script>
        function togglePassword(inputId, btn) {
            const inp = document.getElementById(inputId);
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

        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            hideMessage();

            const btn = document.getElementById('registerBtn');
            const first_name = document.getElementById('first_name').value.trim();
            const last_name = document.getElementById('last_name').value.trim();
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            const weight = document.getElementById('weight').value;
            const height = document.getElementById('height').value;
            const age = document.getElementById('age').value;
            const gender = document.querySelector('input[name="gender"]:checked').value;

            // Validation
            if (!first_name || !last_name || !username || !password) {
                showMessage('error', 'لطفاً تمام فیلدهای اطلاعات شخصی را پر کنید');
                return;
            }
            if (!weight || !height || !age) {
                showMessage('error', 'لطفاً تمام فیلدهای اطلاعات بدنی را پر کنید');
                return;
            }
            if (password.length < 6) {
                showMessage('error', 'رمز عبور باید حداقل ۶ کاراکتر باشد');
                return;
            }

            btn.classList.add('loading');
            btn.disabled = true;

            try {
                const formData = new FormData();
                formData.append('first_name', first_name);
                formData.append('last_name', last_name);
                formData.append('username', username);
                formData.append('password', password);
                formData.append('weight', weight);
                formData.append('height', height);
                formData.append('age', age);
                formData.append('gender', gender);

                const response = await fetch('api/auth.php?action=register', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showMessage('success', 'ثبت‌نام موفق! در حال انتقال...');
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 1500);
                } else {
                    showMessage('error', result.message || 'خطا در ثبت‌نام. لطفاً دوباره تلاش کنید');
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
        document.querySelectorAll('.form-group').forEach(group => {
            group.addEventListener('focusin', function() {
                this.style.transform = 'scale(1.01)';
            });
            group.addEventListener('focusout', function() {
                this.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>