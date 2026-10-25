<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LI – My Posts</title>
<link rel="stylesheet" href="/LI/assets/css/base.css">
<link rel="stylesheet" href="/LI/assets/css/cards.css">
<link rel="stylesheet" href="/LI/assets/css/profile.css">
</head>
<body>
<?php
require_once 'includes/auth.php';
requireLogin();
require_once 'includes/nav.php';
$user = currentUser();
?>

<div class="user-banner">
    <div style="max-width:100%;margin:1 auto;padding:1.5rem 1rem;background-color: var(--nav-bg);">
        <h2 style="color: white;margin-left: 7px"><img src="/LI/assets/icons/profile-svgrepo-com (2).svg" style="width:28px;height:28px;vertical-align:middle;margin-right:2px;"> <?= htmlspecialchars($user['name']) ?></h2>
        <p style="color: white; margin-top: 5px;margin-left: 9px; margin-bottom: 5px">Your published stories</p>
    </div>
</div>

<div class="page-wrap">
    <div class="page-header" style="margin-bottom:1rem">
        <h1>My Stories</h1>
        <button class="btn" onclick="window.location.href='/LI/index.php'">+ Add New Story</button>
    </div>
    <div id="my-posts" class="posts-grid">
        <div class="spinner">Loading...</div>
    </div>
</div>

<div class="toast" id="toast"></div>

<script>
const ME_ID = <?= (int)$user['id'] ?>;
const ME_ROLE = <?= json_encode($user['role']) ?>;

async function loadMyPosts() {
    const res = await fetch('/LI/api/posts.php?action=user_posts');
    const posts = await res.json();
    const container = document.getElementById('my-posts');
    if (!posts.length) {
        container.innerHTML = '<div class="empty-state"><span class="emoji"></span>No stories yet.<br><br><a href="/LI/index.php" class="btn">Write your first story</a></div>';
        return;
    }
    container.innerHTML = posts.map(p => {
        const img = p.image_url
            ? `<img src="${escHtml(p.image_url)}" alt="" onerror="this.style.display='none'">`
            : `<div class="card-placeholder"></div>`;
        return `
        <div class="post-card">
            ${img}
            <div class="card-body">
                <h3>${escHtml(p.title)}</h3>
                <div class="card-meta">${formatDate(p.created_at)}</div>
                <div class="card-excerpt">${escHtml(p.content.substring(0, 80))}${p.content.length>80?'…':''}</div>
            </div>
            <div class="card-footer">
                <span><img src="/LI/assets/icons/heart-thin-icon.webp" class="icon-heart" alt="likes"> ${p.like_count}</span>
                <span><img src="/LI/assets/icons/comment-o-svgrepo-com.svg" class="icon-comment" alt="comments"> ${p.comment_count}</span>
                <button class="trash-btn" onclick="deletePost(${p.id}, this)" title="Delete">
                    <img src="/LI/assets/icons/trash-bin-minimalistic-2-svgrepo-com.svg" alt="delete">
                </button>
            </div>
        </div>`;
    }).join('');
}

async function deletePost(id, btn) {
    if (!confirm('Delete this story permanently?')) return;
    btn.disabled = true;
    const res = await fetch(`/LI/api/posts.php?action=delete&id=${id}`);
    const data = await res.json();
    if (data.success) {
        showToast('Story deleted!');
        loadMyPosts();
    } else {
        showToast(data.error || 'Error deleting');
        btn.disabled = false;
    }
}

function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function formatDate(d) {
    return new Date(d).toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' });
}
function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}

loadMyPosts();
</script>
</body>
</html>
