<?php
  if (empty($_COOKIE) || $_COOKIE['user_type'] != 'usuario') {
    header("Location: hub.php?msg=acessoproibido");
    exit();
  }

  require_once __DIR__ . "/../views/usuarioView.php";
  
  $usuario = new usuarioView(['id_usuario' => $_COOKIE['user_id']]);
  
  if (!$usuario->verifyPassword($_COOKIE['user_password'])) {
    setcookie("user_type", 'guest', time() + 3600, "/");
    setcookie("user_id", '', time() + 3600, "/");
    setcookie("user_password", '', time() + 3600, "/");
    
    header("Location: hub.php");
    exit();
  }

  require_once __DIR__ . "/../views/filaView.php";

  date_default_timezone_set('America/Rio_Branco');

  $filaView = new filaView(['data_fila' => date('Y-m-d')]);

  $dataProxima = null;
  $idProximo = null;
  if (!$filaView->exists()) {
    $filaView->buildData();
    foreach($filaView->getAll(['status_fila' => 'ativa']) as $fila) {
      $dataFila = new Datetime($fila->data_fila);

      $today = new Datetime(date('Y-m-d'));
      $interval = $today->diff($dataFila);
      $interval = (int) $interval->format('%h');

      var_dump($dataFila);
      // var_dump();

      if ($interval < $dataProxima) {
        $dataProxima = $interval;
        $idProximo = $fila->id_fila;
      }
    }

    $id = $idProximo;
  }
  else {
    $id = $filaView->get('id_fila');
  }
?>

<!DOCTYPE html>
<html lang="ptBR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pipo</title>
</head>
<body>
    <main>
    <h1>Reservar almoço</h1>

    <!-- Formulário envia via POST para /Fila/create (ajuste action conforme sua rota) -->
    <form action="ctrlHub.php?class=reserva&action=create" method="post" novalidate>
      <?php
      require_once __DIR__ . "/../views/filaView.php";
      
      $view = new filaView(['id_fila' => $id]);
      $attr = $view->getAtributtes();
      
      echo "<div>
      <label for='id_fila'>Fila de:</label><br>
      <select name='id_fila' id='id_fila'>";
      
            require_once __DIR__ . "/../views/cardapioView.php";
            require_once __DIR__ . "/../views/pratoView.php";
            require_once __DIR__ . "/../views/filaView.php";

            $fila = new filaView(['id_fila' => $id]);

            foreach ($fila->getAll(['status_fila' => 'ativa']) as $f) {
              $data = new Datetime($f->data_fila);
              if ($f->id_fila == $id) {
                echo '<option value="' . $f->id_fila . '" selected>' . $data->format('d/m/Y') . ' 11:30</option>';
              }
              else {
                echo '<option value="' . $f->id_fila . '">' . $data->format('d/m/Y') . ' 11:30</option>';
              }
            }
      
      echo "</select></div><br><div>
      <label for='prato_prioritario'>Prato prioritário:</label>
      <select name='prato_prioritario'>";

      $cardapio = new cardapioView(['id_cardapio' => $fila->get('cardapio_fila')]);
      $pratoA = new pratoView(['id_prato' => $cardapio->get('prato_A_cardapio')]);
      $pratoB = new pratoView(['id_prato' => $cardapio->get('prato_B_cardapio')]);

      echo "<option value='" . $pratoA->get('id_prato') . "'>" . $pratoA->get('nome_prato') . "</option>";
      echo "<option value='" . $pratoB->get('id_prato') . "'>" . $pratoB->get('nome_prato') . "</option>";
      ?>
      </select></div>

      <br />

      <div>
        <button type="submit">Reservar almoço</button>
      </div>
    </form>
  </main>
  <br><br><button onclick="window.location.replace('listFila.php')">Voltar à lista de Filas</button>

</body>
</html>