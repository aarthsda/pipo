<?php
  require_once __DIR__ . "/../config/auth.php";
  $usuarioId = requireAuth('usuario');

  require_once __DIR__ . "/../views/usuarioView.php";
  $usuario = new usuarioView(['id_usuario' => $usuarioId]);
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
    <h1>Alterar Reserva</h1>

    <!-- Formulário envia via POST para /Fila/create (ajuste action conforme sua rota) -->
    <form action="ctrlHub.php?class=reserva&action=alter" method="post" novalidate>
      
      <div>
        <input
          id="id_reserva"
          name="id_reserva"
          type="text"
          style="display: none;"
          value="<?php echo $_GET['id'];?>"
        />
      </div>

      <?php
      require_once __DIR__ . "/../views/reservaView.php";
      
      $view = new reservaView(['id_reserva' => $_GET['id']]);
      $attr = $view->getAtributtes();
      
      echo "<div>
      <label for='prato_prioritario_reserva'>Prato Prioritário:</label><br>
      <select name='prato_prioritario_reserva' id='prato_prioritario_reserva'>";
      
            require_once __DIR__ . "/../views/filaView.php";
            require_once __DIR__ . "/../views/pratoView.php";
            require_once __DIR__ . "/../views/cardapioView.php";

            $fila = new filaView(['id_fila' => $view->get('id_fila_reserva')]);
            $cardapio = new cardapioView(['id_cardapio' => $fila->get('cardapio_fila')]);

            $pratoA = new pratoView(['id_prato' => $cardapio->get('prato_A_cardapio')]);
            $pratoB = new pratoView(['id_prato' => $cardapio->get('prato_B_cardapio')]);
            $pratos = [$pratoA, $pratoB];

            foreach ($pratos as $prato) {
              if ($prato->get('id_prato') == $attr['prato_prioritario_reserva']) {
                echo '<option value="' . $prato->get('id_prato') . '" selected>' . $prato->get('nome_prato') . '</option>';
              }
              else {
                echo '<option value="' . $prato->get('id_prato') . '">' . $prato->get('nome_prato') . '</option>';
              }
            }
      ?>
      </div>

      <br />

      <div>
        <button type="submit">Alterar Fila</button>
        <button type="reset">Limpar</button>
      </div>
    </form>

    <!-- Mensagem de ajuda: o servidor deve tratar hashing da senha e validação adicional -->
    <p>
      Observação: trate a senha de forma segura no servidor (hash/salt), valide todos os dados
      no backend e proteja a rota contra CSRF antes de usar em produção.
    </p>
  </main>
  <br><br><button onclick="window.location.replace('listFila.php')">Voltar à lista de Filas</button>

</body>
</html>