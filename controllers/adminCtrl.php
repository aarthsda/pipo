<?php
    require_once __DIR__ . "/../models/admin.php";

    class adminCtrl {
        private $admin;

        public function __construct($data = array()) {
            $this->admin = new admin($data);
        }

        public function setData(array $data) {
            foreach($data as $prop => $value) {
                $this->admin->__set($prop, $value);
            }
        }

        public function store() {
            return $this->admin->save();
        }

        public function erase() {
            $this->admin->delete();
        }

        public function hashPassword($senha = null) {
            if ($senha) {
                $senha = password_hash($senha, PASSWORD_BCRYPT);
            }
            else {
                $senha = password_hash($this->admin->__get('senha_admin'), PASSWORD_BCRYPT);
            }
            $this->setData(['senha_admin' => $senha]);
        }
    }
?>