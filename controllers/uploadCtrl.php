<?php
    require_once __DIR__ . "/../models/upload.php";

    class uploadCtrl {
        private $upload;

        public function __construct($data = array()) {
            $this->upload = new upload($data);
        }

        public function setData(array $data) {
            foreach($data as $prop => $value) {
                $this->upload->__set($prop, $value);
            }
        }

        public function store() {
            return $this->upload->save();
        }

        public function erase() {
            $this->upload->delete();
        }
    }
?>