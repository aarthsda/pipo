<?php
    require_once __DIR__ . "/../models/reserva.php";

    class reservaView {
        private $reserva;

        public function __construct($data = null) {
            if ($data) {
                $this->buildData($data);
                $this->pickNBecome();
            }
            else {
                $this->reserva = new reserva;
            }
        }

        public function exists() {
            if (empty($this->reserva)) {
                return false;
            }
            else {
                return true;
            }
        }

        public function get($prop) {
            return $this->reserva->__get($prop);
        }

        public function buildData($data) {
            $this->reserva = new reserva($data);
        }

        public function getAtributtes() {
            return $this->reserva->getAtributtes();
        }

        public function getAll(array $filter = null) {
            return $this->reserva->all($filter);
        }

        public function pickNBecome() {
            $this->reserva = $this->reserva->find($this->reserva->getAtributtes());
        }

        public function getLastPos() {
            $max = 1;
            if (!empty($this->getAll())) {
                foreach ($this->getAll() as $reserva) {
                    if ((int) $reserva->posicao_reserva == $max) {
                        $max = (int) $reserva->posicao_reserva + 1;
                    }
                    else {
                        $max += 1;
                    }
                }
            }
            return $max;
        }

        public function printTable($reservas) {
            if ($reservas) {
                echo "<table border='1' cellpadding='8' cellspacing='0'><thead><tr>
      <th>id_reserva</th>
      <th>codigo_reserva</th>
      <th>nome_reserva</th>
      <th>email_reserva</th>
      <th>senha_reserva</th>
      </tr></thead><tbody>";
                foreach ($reservas as $reserva) {
                    echo "<tr>";
                    foreach ($reserva as $value) {
                        echo "<th>$value</th>";
                    }
                    echo "<th><button onclick=window.location.replace('ctrlHub.php?class=reserva&action=delete&id=" . $reserva->id_reserva . "')>Deletar</button></th>";
                    echo "<th><button onclick=window.location.replace('ctrlHub.php?class=reserva&action=alter&id=" . $reserva->id_reserva . "')>Alterar</button></th>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            }
            else {
                echo "Nenhuma reserva foi encontrada.<br>";
            }
            echo "<br><br><button onclick=window.location.replace('createreserva.php')>Cadastrar novo reserva</button>";
        }
    }
?>