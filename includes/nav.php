<?php
require_once __DIR__ . '/auth.php';
$user = currentUser();
$current = basename($_SERVER['PHP_SELF']);
?>
<nav>
    <a class="logo" href="/LI/index.php">Should I know this?</a>
    <div class="nav-links">
        <?php if (isLoggedIn()): ?>
            <a href="/LI/index.php" <?= $current==='index.php'?'class="active"':'' ?>>Home</a>
            <a href="/LI/profile.php" <?= $current==='profile.php'?'class="active"':'' ?>>My Posts</a>
            <a href="/LI/games.php" <?= $current==='games.php'?'class="active"':'' ?>>Games</a>
            <button onclick="logout()">Logout</button>
            <span style="color:#aaa;font-size:0.85rem;">Hi, <?= htmlspecialchars($user['name']) ?>!</span>
            
        <?php else: ?>
            <a href="/LI/login.php">Login</a>
        <?php endif; ?>
    </div>
</nav>
<script>
async function logout() {
    await fetch('/LI/api/auth.php?action=logout');
    window.location.href = '/LI/login.php';
}
</script>
