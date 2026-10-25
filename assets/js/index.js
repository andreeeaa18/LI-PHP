const ME = {
  id: window.__ME_ID,
  name: window.__ME_NAME,
  role: window.__ME_ROLE,
};
let currentPost = null;
let allPosts = [];

async function loadPosts() {
  const res = await fetch("/LI/api/posts.php?action=list");
  allPosts = await res.json();
  renderPosts(allPosts);
}

function renderPosts(posts) {
  const container = document.getElementById("posts-container");
  if (!posts.length) {
    container.innerHTML =
      '<div class="empty-state"><span class="emoji"></span>No stories yet. Be the first!</div>';
    return;
  }
  container.innerHTML = posts
    .map((p) => {
      const imageContent = p.image_url
        ? `<img src="${p.image_url}" alt="">`
        : `<div class="card-placeholder">📖</div>`;
      return `
        <div class="post-card" onclick="openPost(${p.id})">
            ${imageContent}
            <div class="card-body">
                <h3>${escHtml(p.title)}</h3>
                <div class="card-meta">By ${escHtml(p.author)} · ${formatDate(p.created_at)}</div>
                <div class="card-excerpt">${escHtml(p.content.substring(0, 80))}${p.content.length > 80 ? "…" : ""}</div>
            </div>
            <div class="card-footer">
                <span>${
                  p.user_liked
                    ? '<img src="/LI/assets/icons/red-heart-icon.png" class="icon-heart liked" alt="liked">'
                    : '<img src="/LI/assets/icons/heart-thin-icon.webp" class="icon-heart" alt="like">'
                } ${p.like_count}</span>
                <span><img src="/LI/assets/icons/comment-o-svgrepo-com.svg" class="icon-comment" alt="comments"> ${p.comment_count}</span>
            </div>
        </div>`;
    })
    .join("");
}

async function openPost(id) {
  const res = await fetch(`/LI/api/posts.php?action=get&id=${id}`);
  currentPost = await res.json();

  document.getElementById("rm-title").textContent = currentPost.title;
  document.getElementById("rm-content").textContent = currentPost.content;
  document.getElementById("rm-meta").textContent =
    `By ${currentPost.author} · ${formatDate(currentPost.created_at)}`;
  document.getElementById("rm-like-count").textContent = currentPost.like_count;
  document.getElementById("rm-comment-count").textContent =
    currentPost.comment_count;

  const heartImg = document.getElementById("rm-heart");
  heartImg.src = currentPost.user_liked
    ? "/LI/assets/icons/red-heart-icon.png"
    : "/LI/assets/icons/heart-thin-icon.webp";
  heartImg.classList.toggle("liked", currentPost.user_liked);

  const imgEl = document.getElementById("rm-img");
  if (currentPost.image_url) {
    imgEl.src = currentPost.image_url;
    imgEl.style.display = "block";
  } else {
    imgEl.style.display = "none";
  }

  const canDelete = ME.role === "admin" || ME.id === currentPost.author_id;
  document.getElementById("rm-delete-btn").style.display = canDelete
    ? "inline-flex"
    : "none";
  document.getElementById("rm-edit-btn").style.display = canDelete
    ? "inline-block"
    : "none";

  document.getElementById("comment-input").value = "";
  await loadComments(id);

  document.getElementById("read-modal").style.display = "flex";
}

function closeReadModal() {
  document.getElementById("read-modal").style.display = "none";
  currentPost = null;
  loadPosts();
}

function closeReadOnOverlay(e) {
  if (e.target === document.getElementById("read-modal")) closeReadModal();
}

async function toggleLike() {
  if (!currentPost) return;
  const fd = new FormData();
  fd.append("post_id", currentPost.id);
  const res = await fetch("/LI/api/likes.php?action=toggle", {
    method: "POST",
    body: fd,
  });
  const data = await res.json();
  currentPost.user_liked = data.liked;
  currentPost.like_count = data.count;
  const heartImg = document.getElementById("rm-heart");
  heartImg.src = data.liked
    ? "/LI/assets/icons/red-heart-icon.png"
    : "/LI/assets/icons/heart-thin-icon.webp";
  heartImg.classList.toggle("liked", data.liked);
  document.getElementById("rm-like-count").textContent = data.count;
}

async function loadComments(postId) {
  const res = await fetch(`/LI/api/comments.php?action=list&post_id=${postId}`);
  const comments = await res.json();
  renderComments(comments);
  document.getElementById("rm-comment-count").textContent = comments.length;
}

function renderComments(comments) {
  const root = comments.filter((c) => !c.parent_id);
  const children = {};
  comments
    .filter((c) => c.parent_id)
    .forEach((c) => {
      if (!children[c.parent_id]) children[c.parent_id] = [];
      children[c.parent_id].push(c);
    });

  const list = document.getElementById("comment-list");
  list.innerHTML = "";

  function buildComment(c, isReply) {
    const div = document.createElement("div");
    div.className = "comment-item" + (isReply ? " reply" : "");
    div.dataset.id = c.id;
    div.innerHTML = `
            <div class="comment-author">${escHtml(c.author)}</div>
            <div class="comment-text">${escHtml(c.content)}</div>
            <div class="comment-meta">${formatDate(c.created_at)}</div>
            ${
              !isReply
                ? `<div class="reply-toggle">
                <button class="btn-reply" onclick="toggleReplyForm(${c.id})">Reply...</button>
                <div class="reply-form" id="rf-${c.id}" style="display:none;margin-top:6px">
                    <input type="text" id="ri-${c.id}" placeholder="Reply..." maxlength="500">
                    <button class="btn btn-sm btn-blue" onclick="addReply(${c.id})">Send</button>
                </div>
            </div>`
                : ""
            }
        `;
    list.appendChild(div);
    if (children[c.id]) children[c.id].forEach((ch) => buildComment(ch, true));
  }

  root.forEach((c) => buildComment(c, false));
}

function toggleReplyForm(commentId) {
  const form = document.getElementById(`rf-${commentId}`);
  form.style.display = form.style.display === "none" ? "flex" : "none";
  if (form.style.display === "flex")
    document.getElementById(`ri-${commentId}`).focus();
}

async function addComment() {
  const input = document.getElementById("comment-input");
  const content = input.value.trim();
  if (!content || !currentPost) return;
  const fd = new FormData();
  fd.append("post_id", currentPost.id);
  fd.append("content", content);
  const res = await fetch("/LI/api/comments.php?action=add", {
    method: "POST",
    body: fd,
  });
  const data = await res.json();
  if (data.success) {
    input.value = "";
    await loadComments(currentPost.id);
  }
}

async function addReply(parentId) {
  const input = document.getElementById(`ri-${parentId}`);
  const content = input.value.trim();
  if (!content || !currentPost) return;
  const fd = new FormData();
  fd.append("post_id", currentPost.id);
  fd.append("parent_id", parentId);
  fd.append("content", content);
  const res = await fetch("/LI/api/comments.php?action=add", {
    method: "POST",
    body: fd,
  });
  const data = await res.json();
  if (data.success) {
    input.value = "";
    document.getElementById(`rf-${parentId}`).style.display = "none";
    await loadComments(currentPost.id);
  }
}

async function reportPost() {
  if (!currentPost) return;
  if (!confirm("Report this post as inappropriate?")) return;
  const fd = new FormData();
  fd.append("post_id", currentPost.id);
  await fetch("/LI/api/reports.php?action=report", {
    method: "POST",
    body: fd,
  });
  showToast("Post reported. Thank you!");
}

async function deletePost() {
  if (!currentPost) return;
  if (!confirm("Delete this post permanently?")) return;
  const res = await fetch(
    `/LI/api/posts.php?action=delete&id=${currentPost.id}`,
  );
  const data = await res.json();
  if (data.success) {
    closeReadModal();
    showToast("Post deleted!");
  } else {
    showToast(data.error || "Error");
  }
}

function openCreateModal() {
  document.getElementById("create-modal-title").textContent = "New Story";
  document.getElementById("create-submit-btn").textContent = "Publish Story";
  document.getElementById("post-id").value = "";
  document.getElementById("post-title").value = "";
  document.getElementById("post-content").value = "";
  document.getElementById("post-image").value = "";
  document.getElementById("create-error").hidden = true;
  document.getElementById("create-modal").style.display = "flex";
}

function openEditModal() {
  if (!currentPost) return;
  document.getElementById("create-modal-title").textContent = "Edit Story";
  document.getElementById("create-submit-btn").textContent = "Update Story";
  document.getElementById("post-id").value = currentPost.id;
  document.getElementById("post-title").value = currentPost.title;
  document.getElementById("post-content").value = currentPost.content;
  document.getElementById("post-image").value = currentPost.image_url || "";
  document.getElementById("create-error").hidden = true;
  document.getElementById("read-modal").style.display = "none";
  document.getElementById("create-modal").style.display = "flex";
}

function closeCreateModal() {
  document.getElementById("create-modal").style.display = "none";
  document.getElementById("post-id").value = "";
  document.getElementById("post-title").value = "";
  document.getElementById("post-content").value = "";
  document.getElementById("post-image").value = "";
}

async function submitPost() {
  const id = document.getElementById("post-id").value;
  const title = document.getElementById("post-title").value.trim();
  const content = document.getElementById("post-content").value.trim();
  const image = document.getElementById("post-image").value.trim();
  const errEl = document.getElementById("create-error");

  if (!title || !content) {
    errEl.textContent = "Title and content are required.";
    errEl.hidden = false;
    return;
  }
  errEl.hidden = true;

  const fd = new FormData();
  fd.append("title", title);
  fd.append("content", content);
  fd.append("image_url", image);

  const isEdit = id !== "";
  if (isEdit) {
    fd.append("id", id);
  }

  const action = isEdit ? "update" : "create";

  const res = await fetch(`/LI/api/posts.php?action=${action}`, {
    method: "POST",
    body: fd,
  });
  const data = await res.json();

  if (data.success) {
    closeCreateModal();
    showToast(isEdit ? "Story updated!" : "Story published! 🎉");
    loadPosts();
    currentPost = null;
  } else {
    errEl.textContent = data.error || "Error";
    errEl.hidden = false;
  }
}

function escHtml(s) {
  return String(s)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}
function formatDate(d) {
  return new Date(d).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
}
function showToast(msg) {
  const t = document.getElementById("toast");
  t.textContent = msg;
  t.classList.add("show");
  setTimeout(() => t.classList.remove("show"), 3000);
}

loadPosts();
