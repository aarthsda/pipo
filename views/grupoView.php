<?php
    require_once __DIR__ . "/../models/grupo.php";

    class grupoView {
        private $grupo;

        public function __construct($data = null) {
            if ($data) {
                $this->buildData($data);
                $this->pickNBecome();
            }
            else {
                $this->grupo = new grupo;
            }
        }
        
        public function exists() {
            if (empty($this->grupo)) {
                return false;
            }
            else {
                return true;
            }
        }

        public function get($prop) {
            return $this->grupo->__get($prop);
        }

        public function buildData($data) {
            $this->grupo = new grupo($data);
        }

        public function getAtributtes() {
            return $this->grupo->getAtributtes();
        }

        public function getAll(array $filter = null) {
            return $this->grupo->all($filter);
        }

        public function pickNBecome() {
            $this->grupo = $this->grupo->find($this->grupo->getAtributtes());
        }
    }
?>