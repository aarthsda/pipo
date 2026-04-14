<?php
    require_once __DIR__ . "/../models/fila.php";
    require_once __DIR__ . "/reservaView.php";

    class filaView {
        private $fila;

        public function __construct($data = null) {
            if ($data) {
                $this->buildData($data);
                $this->pickNBecome();
            }
            else {
                $this->fila = new fila;
            }
        }

        public function exists() {
            if (empty($this->fila)) {
                return false;
            }
            else {
                return true;
            }
        }

        public function get($prop) {
            return $this->fila->__get($prop);
        }

        public function buildData($data = null) {
            $this->fila = new fila($data);
        }

        public function getAtributtes() {
            return $this->fila->getAtributtes();
        }

        public function getAll(array $filter = null) {
            return $this->fila->all($filter);
        }

        public function pickNBecome() {
            $this->fila = $this->fila->find($this->fila->getAtributtes());
        }

        public function printTable($filas) {
            if ($filas) {
                echo "<table border='1' cellpadding='8' cellspacing='0'><thead><tr>
      <th>id_fila</th>
      <th>data_fila</th>
      <th>capacidade_maxima_fila</th>
      <th>status_fila</th>
      <th>reservas_em_fila</th>
      <th>cardapio_fila</th>
      </tr></thead><tbody>";
                foreach ($filas as $fila) {
                    echo "<tr>";
                    foreach ($fila as $value) {
                        echo "<th>$value</th>";
                    }
                    echo "<th><button onclick=window.location.replace('ctrlHub.php?class=fila&action=delete&id=" . $fila->id_fila . "')>Deletar</button></th>";
                    echo "<th><button onclick=window.location.replace('ctrlHub.php?class=fila&action=alter&id=" . $fila->id_fila . "')>Alterar</button></th>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            }
            else {
                echo "Nenhuma fila foi encontrada.<br>";
            }
            echo "<br><br><button onclick=window.location.replace('createfila.php')>Cadastrar novo fila</button>";
        }

        public function getReservas() {
            $reservas = new reservaView;
            return $reservas->getAll(['id_fila_reserva' => $this->fila->get('id_fila')]);
        }
    }
?>