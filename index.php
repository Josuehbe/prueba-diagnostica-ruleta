<?php
// Llamamos a todas la clases para el funcinamiento del juego
require_once 'Player.php';
require_once 'Roulette.php';
require_once 'Game.php';

// Inicia la sesiòn para mantener el jueo en su estado 
session_start();

// Inicia el juego si no existe en la sesiòn 
if (!isset($_SESSION['game'])) {
    $_SESSION['game'] = new Game();
}

$game = $_SESSION['game'];

// Manejamos todo el crud y el inicio de cada partida 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_player':
                $game->addPlayer($_POST['name']);
                break;
            case 'edit_player':
                $game->editPlayer($_POST['id'], $_POST['name'], $_POST['money']);
                break;
            case 'delete_player':
                $game->deletePlayer($_POST['id']);
                break;
            case 'play_round':
                $game->playRound();
                break;
        }
    }
}


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Simulación de Ruleta</title>
    <style>
        /* Damos un poco de estilo al juego */
        body {
            font-family: Arial, sans-serif;
        }
        .player-list, .result-list {
            list-style-type: none;
            padding: 0;
        }
        .player-list li, .result-list li {
            margin: 5px 0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .player-list li:hover, .result-list li:hover {
            background-color: #f0f0f0;
        }
        .result-list li.win {
            background-color: #d4edda;
        }
        .result-list li.lose {
            background-color: #f8d7da;
        }
        .roulette {
            width: 200px;
            height: 200px;
            border: 10px solid #333;
            border-radius: 50%;
            position: relative;
            margin: 20px auto;
            animation: spin 4s ease-out;
        }
        .roulette::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 10px;
            height: 10px;
            background: #333;
            border-radius: 50%;
            transform: translate(-50%, -50%);
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(1440deg); }
        }
    </style>
</head>
<body>
    <h1>Simulación de Ruleta</h1>
    
    <!-- Aqui agregamos a cada jugar. No hay un tope maximo de jugadores, se pouede incluir la cantidad que se desee -->
    <h2>Agregar Jugador</h2>
    <form method="POST">
        <input type="hidden" name="action" value="add_player">
        <input type="text" name="name" required>
        <button type="submit">Agregar</button>
    </form>

    <!-- Aqui se puede apreciar todos los jugares incluidos y ver sus saldos con los que cuentan para seguir jugando 
     y tambien podemos editar o eliminarlos si es necesario -->
    <h2>Jugadores</h2>
    <ul class="player-list">
    <?php foreach ($game->getPlayers() as $player): ?>
        <li>
            <?= htmlspecialchars($player->getName()) ?> - $<?= $player->getMoney() ?>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="action" value="edit_player">
                <input type="hidden" name="id" value="<?= $player->getId() ?>">
                <input type="text" name="name" value="<?= htmlspecialchars($player->getName()) ?>" required>
                <input type="number" name="money" value="<?= $player->getMoney() ?>" required>
                <button type="submit">Editar</button>
            </form>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="action" value="delete_player">
                <input type="hidden" name="id" value="<?= $player->getId() ?>">
                <button type="submit">Eliminar</button>
            </form>
        </li>
    <?php endforeach; ?>
    </ul>

    <!-- Aqui esta el boton para iniciar cada ronda del juego  -->
    <form method="POST" id="play-round-form">
        <input type="hidden" name="action" value="play_round">
        <button type="submit">Jugar Ronda</button>
    </form>

    <!-- Animamos una ¨ruleta¨ para darle un poco mas de dinamismo al juego -->
    <div id="roulette" class="roulette" style="display: none;"></div>

    <!-- Aqui vemos los resultados de cada ronda jugada por el usuario, no muestra por cual color aposto cada usuario
      y si gano o perdio y tambien nos muestra el valor que aposto  -->
    <?php if ($game->getLastResult()): ?>
    <h2>Resultado de la última ronda</h2>
    <p>La ruleta salió: <?= $game->getLastResult() ?></p>
    <ul class="result-list">
    <?php foreach ($game->getLastBets() as $playerId => $bet): ?>
        <li class="<?= $bet['won'] ? 'win' : 'lose' ?>">
            <?= htmlspecialchars($game->getPlayerById($playerId)->getName()) ?> 
            apostó $<?= $bet['amount'] ?> al <?= $bet['color'] ?> 
            y <?= $bet['won'] ? 'ganó' : 'perdió' ?>.
        </li>
    <?php endforeach; ?>
    </ul>
    <?php endif; ?>

    <script>
        // Usamos JavaScrip para mostrar la ruleta con una animaciòn y como se maneja todo el juego 
    document.getElementById('play-round-form').addEventListener('submit', function(event) {
        event.preventDefault();
        const roulette = document.getElementById('roulette');
        const spinDuration = 4000; // Vemos la duraración de la animación de giro en milisegundos

        // Esta es la funciòn para mostrar la animaciòn de la ruleta
        function showRoulette() {
            roulette.style.display = 'block';
            roulette.style.animation = `spin ${spinDuration / 1000}s ease-out`;
        }

        // Aqui implemente la animaciòn del cambio de color de la ruleta entre verde, rojo y negro
        function changeRouletteColor() {
            const colors = ['#00ff00', '#ff0000', '#000000']; // Estos son los numeros de los colores Verde, Rojo, Negro
            let colorIndex = 0;
            return setInterval(() => {
                roulette.style.backgroundColor = colors[colorIndex];
                colorIndex = (colorIndex + 1) % colors.length;
            }, 200);
        }

        // Aqui seleccionamos el color ganador por cada ronda 
        function setWinningColor(winningColor) {
            const colorMap = {
                'Verde': '#00ff00',
                'Rojo': '#ff0000',
                'Negro': '#000000'
            };
            roulette.style.backgroundColor = colorMap[winningColor] || '#ffffff';
        }

        // Iniciamos la animacio de la ruleta 
        showRoulette();
        const colorChangeInterval = changeRouletteColor();

        // Se simula la ruleta de un casino
        setTimeout(() => {
            clearInterval(colorChangeInterval);
            const winningColor = '<?= $game->getLastResult() ?>';
            setWinningColor(winningColor);
            
            // Actualizar los resultados 
            this.submit();
        }, spinDuration);
    });
</script>
</body>
</html>