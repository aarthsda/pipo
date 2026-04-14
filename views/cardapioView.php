<?php
    require_once __DIR__ . "/../models/cardapio.php";

    class cardapioView {
        private $cardapio;

        public function __construct($data = null) {
            if ($data) {
                $this->buildData($data);
                $this->pickNBecome();
            }
            else {
                $this->cardapio = new cardapio;
            }
        }

        public function get($prop) {
            return $this->cardapio->__get($prop);
        }

        public function buildData($data) {
            $this->cardapio = new cardapio($data);
        }

        public function getAtributtes() {
            return $this->cardapio->getAtributtes();
        }

        public function getAll(array $filter = null) {
            return $this->cardapio->all($filter);
        }

        public function pickNBecome() {
            $this->cardapio = $this->cardapio->find($this->cardapio->getAtributtes());
        }

        public function printTable($cardapios) {
            if ($cardapios) {
                echo "<table border='1' cellpadding='8' cellspacing='0'><thead><tr>
      <th>id_cardapio</th>
      <th>nome_cardapio</th>
      <th>prato_A_cardapio</th>
      <th>prato_B_cardapio</th>
      </tr></thead><tbody>";
                foreach ($cardapios as $cardapio) {
                    echo "<tr>";
                    foreach ($cardapio as $value) {
                        echo "<th>$value</th>";
                    }
                    echo "<th><button onclick=window.location.replace('ctrlHub.php?class=cardapio&action=delete&id=" . $cardapio->id_cardapio . "')>Deletar</button></th>";
                    echo "<th><button onclick=window.location.replace('ctrlHub.php?class=cardapio&action=alter&id=" . $cardapio->id_cardapio . "')>Alterar</button></th>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            }
            else {
                echo "Nenhum cardapio foi encontrado.<br>";
            }
            echo "<br><br><button onclick=window.location.replace('createcardapio.php')>Cadastrar novo cardapio</button>";
        }
    }
?>