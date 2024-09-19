<?php
// creamos la clase del juego
class Game {
    private $players = []; // arreglo de jugadores
    private $roulette; // instancia de la clase ruleta
    private $lastResult; // resultado de la ruleta
    private $lastBets = []; // apuestas de los jugadores
// Constructor de la clase juego 
    public function __construct() {
        $this->roulette = new Roulette();
    }
// Añade un nuevo jugador 
    public function addPlayer($name) { // Nombre del  nuevo jugador
        $id = count($this->players) + 1;
        $this->players[$id] = new Player($id, $name);
    }
// Edita a los jugadores
    public function editPlayer($id, $name, $money) {
        if (isset($this->players[$id])) { // comprobamos que exista el jugador
            $this->players[$id]->setName($name); // editamos el nombre
            $this->players[$id]->setMoney($money); // editamos el dinero
        }
    }

// Elimina a los jugadores
    public function deletePlayer($id) { // Identificador del jugador a elimiar 
        unset($this->players[$id]);
    }
// Creamos los gertter para player, lastResult y lastBets
    public function getPlayers() {
        return $this->players;
    }

    public function getPlayerById($id) {
        return $this->players[$id] ?? null;
    }

    // Simula una ronda del juego y cada apuesta y se determina los resultados

    public function playRound() {
        $this->lastBets = [];
        foreach ($this->players as $player) {
            if ($player->getMoney() > 0) {
                $bet = $player->bet();
                $this->lastBets[$player->getId()] = $bet;
            }
        }

        $this->lastResult = $this->roulette->spin();

        foreach ($this->lastBets as $playerId => &$bet) {
            if ($bet['color'] === $this->lastResult) {
                $winAmount = $bet['color'] === 'Verde' ? $bet['amount'] * 15 : $bet['amount'] * 2;
                $this->players[$playerId]->win($winAmount);
                $bet['won'] = true;
            } else {
                $bet['won'] = false;
            }
        }
    }

    public function getLastResult() {
        return $this->lastResult;
    }

    public function getLastBets() {
        return $this->lastBets;
    }
}