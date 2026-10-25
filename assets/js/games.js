function escHtml(s) {
  return String(s)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;");
}

function backToMenu() {
  document.getElementById("game-select").style.display = "grid";
  document.getElementById("game-area").style.display = "none";
  document.getElementById("game-content").innerHTML = "";
  if (typeof typingTimer !== "undefined" && typingTimer) {
    clearInterval(typingTimer);
    typingTimer = null;
  }
}

function loadGame(name) {
  document.getElementById("game-select").style.display = "none";
  document.getElementById("game-area").style.display = "block";
  const gc = document.getElementById("game-content");
  if (name === "drawing") {
    gc.innerHTML = buildDrawingGame();
  } else if (name === "typing") {
    gc.innerHTML = buildTypingGame();
    initTyping();
  } else if (name === "cat") {
    gc.innerHTML = `<iframe src="https://cat-bounce.com/" class="cat-frame" allowfullscreen title="Cat Bounce"></iframe>`;
  }
}
