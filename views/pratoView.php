<?php
    require_once __DIR__ . "/../models/prato.php";
    require_once __DIR__ . "/../views/uploadView.php";

    class pratoView {
        private $prato;

        public function __construct($data = null) {
            if ($data) {
                $this->buildData($data);
                $this->pickNBecome();
            }
            else {
                $this->prato = new prato;
            }
        }

        public function exists() {
            if (empty($this->prato)) {
                return false;
            }
            else {
                return true;
            }
        }

        public function get($prop) {
            return $this->prato->__get($prop);
        }

        public function buildData($data) {
            $this->prato = new prato($data);
        }

        public function getAtributtes() {
            return $this->prato->getAtributtes();
        }

        public function getAll(array $filter = null) {
            return $this->prato->all($filter);
        }

        public function pickNBecome() {
            $this->prato = $this->prato->find($this->prato->getAtributtes());
        }

        public function printTable($pratos) {
            if ($pratos) {
                echo "<table border='1' cellpadding='8' cellspacing='0'><thead><tr>
      <th>id_prato</th>
            <th>nome_prato</th>
            <th>descricao_prato</th>
            <th>quantidade_disponivel_prato</th>
            <th>data_prato</th>
            <th>foto_prato</th>
      </tr></thead><tbody>";
                foreach ($pratos as $prato) {
                    echo "<tr>";
                    foreach ($prato as $key => $value) {
                        if ($key != 'foto_prato') {
                            echo "<th>$value</th>";
                        }
                        else {
                            $view = new uploadView(['id_upload' => $value]);

                            echo "<th><img src='" . $view->get('path_upload') . "' alt='" . $view->get('nome_upload') . "' style='max-width: 100px;'></th>";
                        }
                    }
                    echo "<th><button onclick=window.location.replace('ctrlHub.php?class=prato&action=delete&id=" . $prato->id_prato . "')>Deletar</button></th>";
                    echo "<th><button onclick=window.location.replace('ctrlHub.php?class=prato&action=alter&id=" . $prato->id_prato . "')>Alterar</button></th>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            }
            else {
                echo "Nenhum prato foi encontrado.<br>";
            }
            echo "<br><br><button onclick=window.location.replace('createprato.php')>Cadastrar novo prato</button>";
        }
    }
?>