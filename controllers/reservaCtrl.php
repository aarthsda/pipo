<?php
    require_once __DIR__ . "/../models/reserva.php";

    class reservaCtrl {
        private $reserva;

        public function __construct($data = array()) {
            $this->reserva = new reserva($data);
        }

        public function setData(array $data) {
            foreach($data as $prop => $value) {
                $this->reserva->__set($prop, $value);
            }
        }

        public function store() {
            return $this->reserva->save();
        }

        public function erase() {
            $this->reserva->delete();
        }
    }
?>