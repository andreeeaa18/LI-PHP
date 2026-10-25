const TYPING_TEXTS = [
  "The quick brown fox jumps over the lazy dog. Pack my box with five dozen liquor jugs.",
  "Sphinx of black quartz, judge my vow. The five boxing wizards jump quickly.",
  "Jackdaws love my big sphinx of quartz. Fix problem quickly with galvanized jets.",
  "Crazy Fredrick bought many very exquisite opal jewels. We promptly judged antique ivory buckles.",
  "A wizard's job is to vex chumps quickly in fog. Watch Jeopardy! Alex Trebek's fun TV quiz game.",
  "Two driven jocks help fax my big quiz. Quick zephyrs blow, vexing daft Jim.",
];
let typingTimer = null;
let timeLeft = 45;
let typingStarted = false;
let targetText = "";
let gameOver = false;

function buildTypingGame() {
  targetText = TYPING_TEXTS[Math.floor(Math.random() * TYPING_TEXTS.length)];
  return `<div class="typing-wrap">
        <div class="typing-stats">
            <span><span class="timer" id="timer-display">45</span>s</span>
            <span style="color:#888;font-size:0.9rem">Start typing to begin the timer!</span>
        </div>
        <div class="typing-text-box" id="typing-text-display">${buildTextSpans(targetText)}</div>
        <input class="typing-input" id="typing-input" placeholder="Type here..." autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
        <div id="typing-result" style="display:none"></div>
        <div style="margin-top:1rem">
            <button class="btn btn-gray" onclick="restartTyping()">New Game</button>
        </div>
    </div>`;
}

function buildTextSpans(text) {
  return text
    .split("")
    .map(
      (c, i) =>
        `<span class="char-pending" data-i="${i}">${c === " " ? "&nbsp;" : escHtml(c)}</span>`,
    )
    .join("");
}

function initTyping() {
  const input = document.getElementById("typing-input");
  if (!input) return;
  typingStarted = false;
  gameOver = false;
  input.addEventListener("input", onTypingInput);
}

function onTypingInput(e) {
  if (gameOver) return;
  const val = e.target.value;

  if (!typingStarted && val.length > 0) {
    typingStarted = true;
    startTypingTimer();
  }

  const chars = document.querySelectorAll(
    ".typing-text-box .char-pending, .typing-text-box .char-correct, .typing-text-box .char-wrong",
  );
  chars.forEach((span, i) => {
    if (i < val.length) {
      span.className = val[i] === targetText[i] ? "char-correct" : "char-wrong";
    } else {
      span.className = "char-pending";
    }
  });

  if (val === targetText) endTyping(true);
}

function startTypingTimer() {
  timeLeft = 45;
  typingTimer = setInterval(() => {
    timeLeft--;
    const display = document.getElementById("timer-display");
    if (display) {
      display.textContent = timeLeft;
      display.className = "timer" + (timeLeft <= 10 ? " warning" : "");
    }
    if (timeLeft <= 0) endTyping(false);
  }, 1000);
}

function endTyping(won) {
  gameOver = true;
  if (typingTimer) {
    clearInterval(typingTimer);
    typingTimer = null;
  }
  const input = document.getElementById("typing-input");
  if (input) input.disabled = true;
  const result = document.getElementById("typing-result");
  if (result) {
    result.style.display = "block";
    result.innerHTML = `<div class="game-result ${won ? "win" : "lose"}">
            <h2>${won ? "You Win!" : "Time's Up!"}</h2>
            <p>${won ? "Excellent typing skills!" : "Better luck next time. Keep practicing!"}</p>
        </div>`;
  }
}

function restartTyping() {
  if (typingTimer) {
    clearInterval(typingTimer);
    typingTimer = null;
  }
  const gc = document.getElementById("game-content");
  gc.innerHTML = buildTypingGame();
  initTyping();
}
