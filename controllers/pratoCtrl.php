<?php
    require_once __DIR__ . "/../models/prato.php";

    class pratoCtrl {
        private $prato;

        public function __construct($data = array()) {
            $this->prato = new prato($data);
        }

        public function setData(array $data) {
            foreach($data as $prop => $value) {
                $this->prato->__set($prop, $value);
            }
        }

        public function store() {
            return $this->prato->save();
        }

        public function erase() {
            $this->prato->delete();
        }
    }
?>