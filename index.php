<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LI - Home</title>
<link rel="stylesheet" href="/LI/assets/css/base.css">
<link rel="stylesheet" href="/LI/assets/css/cards.css">
<link rel="stylesheet" href="/LI/assets/css/index.css">
</head>
<body>
<?php
require_once 'includes/auth.php';
requireLogin();
require_once 'includes/nav.php';
$user = currentUser();
?>

<div class="page-wrap">
    <div class="page-header">
        <h1>Stories</h1>
        <button type="button" class="btn" onclick="openCreateModal()">+ Add Your Story</button>
    </div>
    <div id="posts-container" class="posts-grid">
        <div class="spinner">Loading stories...</div>
    </div>
</div>

<div class="modal-overlay" id="create-modal" style="display:none">
    <div class="modal">
        <div class="modal-header">
            <h2 id="create-modal-title">New Story</h2>
            <button type="button" class="modal-close" onclick="closeCreateModal()">✕</button>
        </div>
        <div class="modal-body">
            <div id="create-error" class="auth-error" hidden></div>
            <input type="hidden" id="post-id">
            <div class="form-group">
                <label>Title</label>
                <input type="text" id="post-title" placeholder="Give your story a title..." maxlength="255">
            </div>
            <div class="form-group">
                <label>Story Content</label>
                <textarea id="post-content" rows="8" placeholder="Write your story here..."></textarea>
            </div>
            <div class="form-group">
                <label>Image URL</label>
                <input type="url" id="post-image" placeholder="https://example.com/image.jpg">
            </div>
            <button type="button" class="btn btn-green" id="create-submit-btn" onclick="submitPost()">Publish Story</button>
        </div>
    </div>
</div>


<div class="modal-overlay" id="read-modal" style="display:none" onclick="closeReadOnOverlay(event)">
    <div class="modal" id="read-modal-inner">
        <div class="modal-header">
            <h2 id="rm-title"></h2>
            <button type="button" class="modal-close" onclick="closeReadModal()">✕</button>
        </div>
        <img id="rm-img" class="modal-img" src="" alt="" style="display:none">
        <div class="modal-body">
            <p id="rm-meta" style="color:#888;font-size:0.85rem;margin-bottom:1rem"></p>
            <p id="rm-content"></p>
        </div>
        <div class="modal-actions">
            <button type="button" class="like-btn-flat" id="rm-like-btn" onclick="toggleLike()">
                <img src="/LI/assets/icons/heart-thin-icon.webp" id="rm-heart" class="icon-heart" alt="like">
                <span id="rm-like-count">0</span>
            </button>
            <span class="comment-display"><img src="/LI/assets/icons/comment-o-svgrepo-com.svg" class="icon-comment" alt="comments"> <span id="rm-comment-count">0</span></span>
            <div class="modal-actions-right">
              
                <button type="button" class="trash-btn" id="rm-delete-btn" onclick="deletePost()" style="display:none" title="Delete">
                    <img src="/LI/assets/icons/trash-bin-minimalistic-2-svgrepo-com.svg" alt="delete">
                </button>
                  <button type="button" class="btn btn-sm btn-blue" id="rm-edit-btn" onclick="openEditModal()" style="display:none">Edit</button>
                <button type="button" class="btn btn-red btn-report" onclick="reportPost()">Report</button>
            </div>
        </div>
        <div class="comments-section">
            <h3>Comments</h3>
            <div class="comment-form">
                <input type="text" id="comment-input" placeholder="Leave a comment..." maxlength="500">
                <button type="button" class="btn btn-sm btn-blue" onclick="addComment()">Send</button>
            </div>
            <div class="comment-list" id="comment-list"></div>
        </div>
    </div>
</div>

<div class="toast" id="toast"></div>

<script>
window.__ME_ID   = <?= (int)$user['id'] ?>;
window.__ME_NAME = <?= json_encode($user['name']) ?>;
window.__ME_ROLE = <?= json_encode($user['role']) ?>;
</script>
<script src="/LI/assets/js/index.js"></script>
</body>
</html>
