<?php
// creamos la clase del jugador y su funcionamiento
class Player {
    private $id;
    private $name;
    private $money;

    public function __construct($id, $name, $money = 10000) {
        $this->id = $id; // Identificador UNICO para cada jugador
        $this->name = $name; // Nombre del jugador
        $this->money = $money; // Dinero del jugador inicial (10000)
    }

    // Implementaciones de los getters y setters

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getMoney() {
        return $this->money;
    }

    public function setMoney($money) {
        $this->money = $money;
    }
    // Definimos cuanto es el valor de la apuesta y el color por el que se apuesta
        
        // Aqui determinamoss cuanto es el valor que se va a apostar
    public function bet() {
        if ($this->money <= 1000) {
            $betAmount = $this->money;
        } else {
            $betAmount = rand($this->money * 0.08, $this->money * 0.15);
        }
        $this->money -= $betAmount;

        // Aqui determinamos el color que se va a apostar

        $rand = rand(1, 100);
        if ($rand <= 2) {
            return ['color' => 'Verde', 'amount' => $betAmount];
        } elseif ($rand <= 51) {
            return ['color' => 'Rojo', 'amount' => $betAmount];
        } else {
            return ['color' => 'Negro', 'amount' => $betAmount];
        }
    }

    // Añade el valor ganado de la apuesta si y solo si gano la apuesta a cada jugador 

    public function win($amount) {
        $this->money += $amount;
    }
}

