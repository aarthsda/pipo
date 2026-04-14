<?php
    require_once __DIR__ . "/../models/grupo.php";

    class grupoCtrl {
        private $grupo;

        public function __construct($data = array()) {
            $this->grupo = new grupo($data);
        }

        public function setData(array $data) {
            foreach($data as $prop => $value) {
                $this->grupo->__set($prop, $value);
            }
        }

        public function store() {
            return $this->grupo->save();
        }

        public function erase() {
            $this->grupo->delete();
        }

        public function hashPassword($senha = null) {
            if ($senha) {
                $senha = password_hash($senha, PASSWORD_BCRYPT);
            }
            else {
                $senha = password_hash($this->grupo->__get('senha_grupo'), PASSWORD_BCRYPT);
            }
            $this->setData(['senha_grupo' => $senha]);
        }

        public function gerarCodigo() {
            $grupos = $this->grupo->all();

            $codigoUnico = false;

            while (!$codigoUnico) {
                $codigo = rand(1000, 9999);
                $codigoUnico = true; // assumimos que é único

                foreach ($grupos as $grupo) {
                    if ($grupo->codigo_grupo == $codigo) {
                        $codigoUnico = false; // achou duplicado → gera outro
                        break;
                    }
                }
            }

            $this->grupo->__set('codigo_grupo', $codigo);
            return $codigo;
        }
    }
?>