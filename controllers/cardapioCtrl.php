<?php
    require_once __DIR__ . "/../models/cardapio.php";

    class cardapioCtrl {
        private $cardapio;

        public function __construct($data = array()) {
            $this->cardapio = new cardapio($data);
        }

        public function setData(array $data) {
            foreach($data as $prop => $value) {
                $this->cardapio->__set($prop, $value);
            }
        }

        public function store() {
            return $this->cardapio->save();
        }

        public function erase() {
            $this->cardapio->delete();
        }
    }
?>