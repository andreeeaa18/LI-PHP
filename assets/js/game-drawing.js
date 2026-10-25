const COLORS = [
  { id: 0, color: "#e74c3c" },
  { id: 1, color: "#e67e22" },
  { id: 2, color: "#f1c40f" },
  { id: 3, color: "#2ecc71" },
  { id: 4, color: "#3498db" },
  { id: 5, color: "#9b59b6" },
  { id: 6, color: "#1abc9c" },
  { id: 7, color: "#e8d5b7" },
  { id: 8, color: "#2c3e50" },
  { id: 9, color: "#ecf0f1" },
  { id: 10, color: "#fff" },
  { id: 11, color: "#000" },
];
const DEFAULT_COLOR = "rgb(240,240,240)";
const GRID_SIZE = 400;
let cellColors = new Array(GRID_SIZE).fill(DEFAULT_COLOR);
let selectedColor = COLORS[11].color;
let selectedColorId = 11;
let isMouseDown = false;
let savedDrawings = [];

function buildDrawingGame() {
  return `<div class="drawing-wrap">
        <div class="color-palette" id="palette"></div>
        <div class="drawing-grid" id="drawing-grid"
            onmousedown="isMouseDown=true"
            onmouseup="isMouseDown=false"
            onmouseleave="isMouseDown=false"
        ></div>
        <div class="drawing-controls">
            <button class="btn btn-green" onclick="saveDrawing()">Save</button>
            <button class="btn btn-gray" onclick="resetDrawing()">Reset</button>
        </div>
        <div class="gallery" id="draw-gallery">
            <h2>Saved Drawings (<span id="draw-count">0</span>)</h2>
            <div class="gallery-grid" id="gallery-grid"></div>
        </div>
    </div>`;
}

document.addEventListener("mouseup", () => {
  isMouseDown = false;
});

document.addEventListener("click", (e) => {
  const pBtn = e.target.closest("#palette .obj-color");
  if (pBtn) {
    selectedColorId = parseInt(pBtn.dataset.id);
    selectedColor = pBtn.dataset.color;
    document
      .querySelectorAll("#palette .obj-color")
      .forEach((b) => b.classList.remove("selected"));
    pBtn.classList.add("selected");
  }
});
document.addEventListener("mousedown", (e) => {
  const cell = e.target.closest("#drawing-grid .grid-cell");
  if (cell) paintCell(parseInt(cell.dataset.i));
});
document.addEventListener("mouseover", (e) => {
  if (!isMouseDown) return;
  const cell = e.target.closest("#drawing-grid .grid-cell");
  if (cell) paintCell(parseInt(cell.dataset.i));
});

function buildDrawingGrid() {
  const grid = document.getElementById("drawing-grid");
  if (!grid) return;
  grid.innerHTML = cellColors
    .map(
      (c, i) =>
        `<div class="grid-cell" data-i="${i}" style="background:${c}"></div>`,
    )
    .join("");
}

function renderPalette() {
  const pal = document.getElementById("palette");
  if (!pal) return;
  pal.innerHTML = COLORS.map(
    (c) =>
      `<button class="obj-color${c.id === selectedColorId ? " selected" : ""}" data-id="${c.id}" data-color="${c.color}" style="background:${c.color}" title="${c.color}"></button>`,
  ).join("");
}

function paintCell(i) {
  cellColors[i] = selectedColor;
  const cell = document.querySelector(
    `#drawing-grid .grid-cell[data-i="${i}"]`,
  );
  if (cell) cell.style.background = selectedColor;
}

function resetDrawing() {
  cellColors = new Array(GRID_SIZE).fill(DEFAULT_COLOR);
  buildDrawingGrid();
}

async function saveDrawing() {
  const copy = [...cellColors];
  savedDrawings.push(copy);
  renderGallery();
  const fd = new FormData();
  fd.append("cell_data", JSON.stringify(copy));
  await fetch("/LI/api/drawings.php?action=save", { method: "POST", body: fd });
}

function loadDrawing(idx) {
  cellColors = [...savedDrawings[idx]];
  buildDrawingGrid();
}

function deleteDrawing(idx) {
  savedDrawings.splice(idx, 1);
  renderGallery();
}

function renderGallery() {
  const grid = document.getElementById("gallery-grid");
  const count = document.getElementById("draw-count");
  if (!grid) return;
  count.textContent = savedDrawings.length;
  grid.innerHTML = savedDrawings
    .map(
      (d, idx) => `
        <div class="gallery-item">
            <div class="gallery-thumbnail" onclick="loadDrawing(${idx})">
                ${d.map((c) => `<div class="grid-cell" style="background:${c}"></div>`).join("")}
            </div>
            <div class="gallery-item-actions">
                <button class="btn btn-sm btn-blue" onclick="loadDrawing(${idx})">Load</button>
                <button class="btn btn-sm btn-red" onclick="deleteDrawing(${idx})">Del</button>
            </div>
        </div>`,
    )
    .join("");
}

const drawingObserver = new MutationObserver(() => {
  if (document.getElementById("drawing-grid")) {
    renderPalette();
    buildDrawingGrid();
    drawingObserver.disconnect();
  }
});
drawingObserver.observe(document.body, { childList: true, subtree: true });
