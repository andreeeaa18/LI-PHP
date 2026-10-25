<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LI - Login</title>
<link rel="stylesheet" href="/LI/assets/css/base.css">
<link rel="stylesheet" href="/LI/assets/css/login.css">
</head>
<body>
<div class="auth-wrap">
    <div class="auth-box">
        <h1>Blog</h1>
        <div class="auth-tabs">
            <div class="auth-tab active" id="tab-login" onclick="switchTab('login')">Login</div>
            <div class="auth-tab" id="tab-signup" onclick="switchTab('signup')">Sign Up</div>
        </div>
        <div id="auth-error" class="auth-error" hidden></div>


        <form id="login-form" onsubmit="doLogin(event)">
            <div class="form-group">
                <label>Name</label>
                <input type="text" id="login-name" placeholder="Your name" required autocomplete="username">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" id="login-pass" placeholder="Password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn" style="width:100%">Login</button>
        </form>

    
        <form id="signup-form" onsubmit="doSignup(event)" hidden>
            <div class="form-group">
                <label>Name</label>
                <input type="text" id="signup-name" placeholder="Your name" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" id="signup-email" placeholder="email@example.com" required autocomplete="email">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" id="signup-pass" placeholder="******" required autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-blue" style="width:100%">Create Account</button>
        </form>
    </div>
</div>

<script>
function switchTab(tab) {
    document.getElementById('tab-login').classList.toggle('active', tab === 'login');
    document.getElementById('tab-signup').classList.toggle('active', tab === 'signup');
    document.getElementById('login-form').hidden = tab !== 'login';
    document.getElementById('signup-form').hidden = tab !== 'signup';
    document.getElementById('auth-error').hidden = true;
}

function showError(msg) {
    const el = document.getElementById('auth-error');
    el.textContent = msg;
    el.hidden = false;
}

async function doLogin(e) {
    e.preventDefault();
    const name = document.getElementById('login-name').value.trim();
    const pass = document.getElementById('login-pass').value;
    const fd = new FormData();
    fd.append('name', name);
    fd.append('password', pass);
    const res = await fetch('/LI/api/auth.php?action=login', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success) {
        window.location.href = '/LI/index.php';
    } else {
        showError(data.error || 'Login failed');
    }
}

async function doSignup(e) {
    e.preventDefault();
    const name  = document.getElementById('signup-name').value.trim();
    const email = document.getElementById('signup-email').value.trim();
    const pass  = document.getElementById('signup-pass').value;
    const fd = new FormData();
    fd.append('name', name);
    fd.append('email', email);
    fd.append('password', pass);
    const res = await fetch('/LI/api/auth.php?action=signup', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success) {
        showError('');
        switchTab('login');
        document.getElementById('login-name').value = name;
    } else {
        showError(data.error || 'Signup failed');
    }
}
</script>
</body>
</html>
