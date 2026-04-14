<?php 
    require_once __DIR__ . "/../config/database.php";
    class admin {
        private $data;
        private $db;
        
        //Inicio da estrutura "__construct"
        public function __construct($data = null) {
            $this->db = Database::getConexao();
            $this->data = $data;
        }
        //Fim da estrutura

        //A função "__construct" deve ser chamada no momento da criação do objeto, com a finalidade de definir seus atributos, bem como sua conexão com o banco de dados 

        //Inicio da estrutura "__set"
        public function __set($prop, $value) {
            $this->data[$prop] = $value;
        }
        //Fim da estrutura

        //A função "__set" deve ser chamada quando há intenção de alterar/definir atributos 

        public function __get($prop = null) {
            if ($prop) {
                return $this->data[$prop];
            }
            else {
                return $this->data;
            }
        }

        public function getAtributtes() {
            return $this->data;
        }

        public function all(array $filter = null) {
            $sql = 'SELECT * FROM admin';
            if ($filter) {
                $sql .= ' WHERE';
                foreach ($filter as $prop => $value) {
                    $sql .= " $prop = :$prop AND";
                }
                $sql = substr($sql, 0, -3);
            }
            $stmt = $this->db->prepare($sql);
            if ($filter) {
                foreach ($filter as $prop => $value) {
                    $stmt->bindValue(":$prop", $value);
                }
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_CLASS);
        }

        public function find($filter = null) {
            $sql = 'SELECT * FROM admin';

            if ($filter) {
                $sql .= ' WHERE';
                foreach ($filter as $prop => $value) {
                    $sql .= " $prop = :$prop AND";
                }
                $sql = substr($sql, 0, -3);
            } else {
                $sql .= ' WHERE id_admin = :id';
            }

            $stmt = $this->db->prepare($sql);

            if ($filter) {
                foreach ($filter as $prop => $value) {
                    $stmt->bindValue(":$prop", $value);
                }
            } else {
                $stmt->bindParam(':id', $this->data['id_admin']);
            }

            $stmt->execute();

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $this->data = $data;
                return $this;
            }

            return null;
        }


        public function delete() {
            $stmt = $this->db->prepare("DELETE FROM admin WHERE id_admin = :id");
            $stmt->bindParam(':id', $this->data['id_admin']);
            $stmt->execute();
        }

        public function save() {
            if (empty($this->data['id_admin'])) {
                $sql = "INSERT INTO admin (";
                $char = '';
                for ($i = 0; $i < 2; $i++) {
                    foreach ($this->data as $prop => $value) {
                        $sql .= "$char$prop, ";
                    }
                    $sql = substr($sql, 0, -2) . ") VALUES (";
                    $char = ':';
                }
                $sql = substr($sql, 0, -9);
                $action = 'INSERT';
            }
            else {
                $sql = "UPDATE admin SET ";
                foreach ($this->data as $prop => $value) {
                    if ($prop != "id_admin") {
                        $sql .= "$prop = :$prop, ";
                    }
                }
                $sql = substr($sql, 0, -2) . " WHERE id_admin = :id_admin";
                $action = 'UPDATE';
            }

            $stmt = $this->db->prepare($sql);

            foreach ($this->data as $prop => $value) {
                $stmt->bindValue(":$prop", $value);
            }

            $stmt->execute();
            if ($action == 'INSERT') {
                return (int) $this->db->lastInsertId();
            }
        }
    }
?>