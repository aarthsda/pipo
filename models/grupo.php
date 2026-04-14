<?php 
    require_once __DIR__ . "/../config/database.php";
    class grupo {
        private $data;
        private $db;

        public function __construct($data = null) {
            $this->db = Database::getConexao();
            $this->data = $data;
        }

        public function __set($prop, $value) {
            $this->data[$prop] = $value;
        }

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
            $sql = 'SELECT * FROM grupo';
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
            $sql = 'SELECT * FROM grupo';

            if ($filter) {
                $sql .= ' WHERE';
                foreach ($filter as $prop => $value) {
                    $sql .= " $prop = :$prop AND";
                }
                $sql = substr($sql, 0, -3);
            } else {
                $sql .= ' WHERE id_grupo = :id';
            }

            $stmt = $this->db->prepare($sql);

            if ($filter) {
                foreach ($filter as $prop => $value) {
                    $stmt->bindValue(":$prop", $value);
                }
            } else {
                $stmt->bindParam(':id', $this->data['id_grupo']);
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
            $stmt = $this->db->prepare("DELETE FROM grupo WHERE id_grupo = :id");
            $stmt->bindParam(':id', $this->data['id_grupo']);
            $stmt->execute();
        }

        public function save() {
            if (empty($this->data['id_grupo'])) {
                $sql = "INSERT INTO grupo (";
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
                $sql = "UPDATE grupo SET ";
                foreach ($this->data as $prop => $value) {
                    if ($prop != "id_grupo") {
                        $sql .= "$prop = :$prop, ";
                    }
                }
                $sql = substr($sql, 0, -2) . " WHERE id_grupo = :id_grupo";
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