<?php
    require_once __DIR__ . "/../models/usuario.php";

    class usuarioCtrl {
        private $usuario;

        public function __construct($data = array()) {
            $this->usuario = new usuario($data);
        }

        public function setData(array $data) {
            foreach($data as $prop => $value) {
                $this->usuario->__set($prop, $value);
            }
        }

        public function store() {
            return $this->usuario->save();
        }

        public function erase() {
            $this->usuario->delete();
        }

        public function hashPassword($senha = null) {
            if ($senha) {
                $senha = password_hash($senha, PASSWORD_BCRYPT);
            }
            else {
                $senha = password_hash($this->usuario->__get('senha_usuario'), PASSWORD_BCRYPT);
            }
            $this->setData(['senha_usuario' => $senha]);
        }
    }
?>