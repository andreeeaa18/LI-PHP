<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LI – Games</title>
<link rel="stylesheet" href="/LI/assets/css/base.css">
<link rel="stylesheet" href="/LI/assets/css/games.css">
</head>
<body>
<?php
require_once 'includes/auth.php';
requireLogin();
require_once 'includes/nav.php';
?>

<div class="page-wrap">
    <div class="page-header">
        <h1><img src="/LI/assets/icons/game-svgrepo-com.svg" style="width:32px;height:32px;vertical-align:middle;margin-right:8px"> Games</h1>
    </div>
    <div class="games-grid" id="game-select">
        <div class="game-card" onclick="loadGame('drawing')">
            <img src="/LI/assets/icons/paint-palette-artist-svgrepo-com.svg" class="game-icon-img" alt="Drawing">
            <h3>Drawing Game</h3>
            <p>Paint on a pixel grid. Save your masterpieces to the gallery!</p>
        </div>
        <div class="game-card" onclick="loadGame('typing')">
            <img src="/LI/assets/icons/keyboard-svgrepo-com.svg" class="game-icon-img" alt="Keyboard">
            <h3>Keyboard Challenge</h3>
            <p>Type a text as fast as you can. You have 45 seconds!</p>
        </div>
        <div class="game-card" onclick="loadGame('cat')">
            <img src="/LI/assets/icons/cat-svgrepo-com.svg" class="game-icon-img" alt="Cat">
            <h3>Cat Bounce</h3>
            <p>An interactive cat-bouncing simulation. Pure chaos.</p>
        </div>
    </div>

    <div id="game-area" style="display:none">
        <button class="btn btn-gray" onclick="backToMenu()" style="margin-bottom:1.2rem">Back to Games</button>
        <div id="game-content"></div>
    </div>
</div>

<script src="/LI/assets/js/games.js"></script>
<script src="/LI/assets/js/game-drawing.js"></script>
<script src="/LI/assets/js/game-typing.js"></script>
</body>
</html>
