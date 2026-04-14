<?php
    require_once __DIR__ . "/../models/usuario.php";

    class usuarioView {
        private $usuario;

        public function __construct($data = null) {
            if ($data) {
                $this->buildData($data);
                $this->pickNBecome();
            }
            else {
                $this->usuario = new usuario;
            }
        }

        public function exists() {
            if (empty($this->usuario)) {
                return false;
            }
            else {
                return true;
            }
        }

        public function get($prop) {
            return $this->usuario->__get($prop);
        }

        public function buildData($data) {
            $this->usuario = new usuario($data);
        }

        public function getAtributtes() {
            if (!empty($this->usuario)) {
                return $this->usuario->getAtributtes();
            }
            
            return false;
        }

        public function getAll(array $filter = null) {
            return $this->usuario->all($filter);
        }

        public function pickNBecome() {
            $this->usuario = $this->usuario->find($this->usuario->getAtributtes());
        }

        public function printTable($usuarios) {
            if ($usuarios) {
                echo "<table border='1' cellpadding='8' cellspacing='0'><thead><tr>
      <th>id_usuario</th>
      <th>nome_usuario</th>
      <th>email_usuario</th>
      <th>matricula_usuario</th>
      <th>categoria_usuario</th>
      <th>grupo_usuario</th>
      </tr></thead><tbody>";
                foreach ($usuarios as $usuario) {
                    echo "<tr>";
                    foreach ($usuario as $prop => $value) {
                        if ($prop != 'senha_usuario') {
                            echo "<th>$value</th>";
                        }
                    }
                    echo "<th><button onclick=window.location.replace('ctrlHub.php?class=usuario&action=delete&id=" . $usuario->id_usuario . "')>Deletar</button></th>";
                    echo "<th><button onclick=window.location.replace('ctrlHub.php?class=usuario&action=alter&id=" . $usuario->id_usuario . "')>Alterar</button></th>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            }
            else {
                echo "Nenhum usuario foi encontrado.<br>";
            }
            echo "<br><br><button onclick=window.location.replace('createusuario.php')>Cadastrar novo usuario</button>";
        }

        public function verifyPassword($input) {
            if (password_verify($input, $this->usuario->__get('senha_usuario'))) {
                return true;
            }
            else {
                return false;
            }
        }
    }
?>