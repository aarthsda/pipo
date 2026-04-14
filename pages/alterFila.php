<?php
  if (empty($_COOKIE) || $_COOKIE['user_type'] != 'admin') {
    header("Location: hub.php?msg=acessoproibido");
    exit();
  }

  require_once __DIR__ . "/../views/adminView.php";

  $admin = new adminView(['id_admin' => $_COOKIE['user_id']]);

  if (!$admin->verifyPassword($_COOKIE['user_password'])) {
    setcookie("user_type", 'guest', time() + 3600, "/");
    setcookie("user_id", '', time() + 3600, "/");
    setcookie("user_password", '', time() + 3600, "/");

    header("Location: hub.php");
    exit();
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
    <h1>Alterar Fila</h1>

    <!-- Formulário envia via POST para /Fila/create (ajuste action conforme sua rota) -->
    <form action="ctrlHub.php?class=fila&action=alter" method="post" novalidate>
      
      <div>
        <input
          id="id_fila"
          name="id_fila"
          type="text"
          style="display: none;"
          value="<?php echo $_GET['id'];?>"
        />
      </div>

      <?php
      require_once __DIR__ . "/../views/filaView.php";
        
        $view = new filaView(['id_fila' => $_GET['id']]);
        $attr = $view->getAtributtes();

      echo "<div>
        <label for='data_fila'>Data (data_fila)</label><br />
        <input
          id='data_fila'
          name='data_fila'
          type='date'
          value='" . $attr['data_fila'] . "'
          required
        />
      </div>

      <br />

      <div>
        <label for='capacidade_maxima_fila'>Capacidade Máxima (capacidade_maxima_fila)</label><br />
        <input
          id='capacidade_maxima_fila'
          name='capacidade_maxima_fila'
          type='number'
          value='" . $attr['capacidade_maxima_fila'] . "'
          required
        />
      </div>

      <br />

      <div>
        <input
          id='status_fila'
          name='status_fila'
          type='text'
          value='" . $attr['status_fila'] . "'
          required
        />
      </div>

      <br />
      
      <div>
        <input
          id='reservas_em_fila'
          name='reservas_em_fila'
          type='number'
          value='" . $attr['reservas_em_fila'] . "'
          style='display: none;'
          required
        />
      </div>

      <br />

      <!-- Nome do Fila -->
      <div>
        <label for='cardapio_fila'>Cardápio (cardapio_fila)</label><br />
        <select name='cardapio_fila' id='cardapio_fila'>";
          
              require_once __DIR__ . "/../views/cardapioView.php";

              $view = new cardapioView;
              $cardapios = $view->getAll();

              foreach ($cardapios as $cardapio) {
                if ($cardapio->id_cardapio == $attr['cardapio_fila']) {
                  echo '<option value="' . $cardapio->id_cardapio . '" selected>' . $cardapio->nome_cardapio . '</option>';
                }
                else {
                  echo '<option value="' . $cardapio->id_cardapio . '">' . $cardapio->nome_cardapio . '</option>';
                }
              }
          ?>
        </select>
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