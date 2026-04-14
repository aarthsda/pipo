<?php
    require_once __DIR__ . "/../models/admin.php";

    class adminView {
        private $admin;

        public function __construct($data = null) {
            if ($data) {
                $this->buildData($data);
                $this->pickNBecome();
            }
            else {
                $this->admin = new admin;
            }
        }

        public function get($prop) {
            return $this->admin->__get($prop);
        }

        public function buildData($data) {
            $this->admin = new admin($data);
        }

        public function getAtributtes() {
            if (!empty($this->admin)) {
                return $this->admin->getAtributtes();
            }
            
            return false;
        }

        public function getAll(array $filter = null) {
            return $this->admin->all($filter);
        }

        public function pickNBecome() {
            $this->admin = $this->admin->find($this->admin->getAtributtes());
        }

        public function printTable($admins) {
            if ($admins) {
                echo "<table border='1' cellpadding='8' cellspacing='0'><thead><tr>
      <th>id_admin</th>
      <th>codigo_admin</th>
      <th>nome_admin</th>
      <th>email_admin</th>
      </tr></thead><tbody>";
                foreach ($admins as $admin) {
                    echo "<tr>";
                    foreach ($admin as $prop => $value) {
                        if ($prop != 'senha_admin') {
                            echo "<th>$value</th>";
                        }       
                    }
                    if ($admin->id_admin != $_COOKIE['user_id']) {
                        echo "<th><button onclick=window.location.replace('ctrlHub.php?class=admin&action=delete&id=" . $admin->id_admin . "')>Deletar</button></th>";
                    }
                    echo "<th><button onclick=window.location.replace('ctrlHub.php?class=admin&action=alter&id=" . $admin->id_admin . "')>Alterar</button></th>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            }
            else {
                echo "Nenhum admin foi encontrado.<br>";
            }
            echo "<br><br><button onclick=window.location.replace('createAdmin.php')>Cadastrar novo Admin</button>";
        }

        public function verifyPassword($input) {
            if (password_verify($input, $this->admin->__get('senha_admin'))) {
                return true;
            }
            else {
                return false;
            }
        }
    }
?>