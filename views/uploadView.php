<?php
    require_once __DIR__ . "/../models/upload.php";

    class uploadView {
        private $upload;

        public function __construct($data = null) {
            if ($data) {
                $this->buildData($data);
                $this->pickNBecome();
            }
            else {
                $this->upload = new upload;
            }
        }

        public function exists() {
            if (empty($this->upload)) {
                return false;
            }
            else {
                return true;
            }
        }

        public function get($prop) {
            return $this->upload->__get($prop);
        }

        public function buildData($data) {
            $this->upload = new upload($data);
        }

        public function getAtributtes() {
            return $this->upload->getAtributtes();
        }

        public function getAll(array $filter = null) {
            return $this->upload->all($filter);
        }

        public function pickNBecome() {
            $this->upload = $this->upload->find($this->upload->getAtributtes());
        }
    }
?>