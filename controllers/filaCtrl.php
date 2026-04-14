<?php
    require_once __DIR__ . "/../models/fila.php";

    class filaCtrl {
        private $fila;

        public function __construct($data = array()) {
            $this->fila = new fila($data);
        }

        public function setData(array $data) {
            foreach($data as $prop => $value) {
                $this->fila->__set($prop, $value);
            }
        }

        public function store() {
            return $this->fila->save();
        }

        public function erase() {
            $this->fila->delete();
        }
    }
?>