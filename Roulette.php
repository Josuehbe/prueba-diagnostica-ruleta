<?php
// creamos la clase ruleta
class Roulette {
    // aqui simulamos el giro de la ruleta y su color al terminar el giro
    public function spin() {
        // Incluimos las posbilidades qque hay de caer en cada color 
        $rand = rand(1, 100);
        if ($rand <= 2) { // 2% de probabilidad de caer en verde
            return 'Verde';
        } elseif ($rand <= 51) {// 49% de probabilidad de caer en rojo
            return 'Rojo';
        } else {
            return 'Negro';// 49% de probabilidad de caer en negro
        }
    }
}