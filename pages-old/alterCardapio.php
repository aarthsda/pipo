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
    <h1>Alterar Cardápio</h1>

    <!-- Formulário envia via POST para /admin/create (ajuste action conforme sua rota) -->
    <form action="ctrlHub.php?class=cardapio&action=alter" method="post" novalidate>
      
    <div>
        <input
          id="id_cardapio"
          name="id_cardapio"
          type="text"
          required
          aria-required="true"
          style="display: none;"
          value="<?php echo $_GET['id']?>"
        />
      </div>

      <?php
        require_once __DIR__ . "/../views/cardapioView.php";
        
        $view = new cardapioView(['id_cardapio' => $_GET['id']]);
        $attr = $view->getAtributtes();

      echo "<div>
        <label for='nome_cardapio'>Nome (nome_cardapio)</label><br />
        <input
          id='nome_cardapio'
          name='nome_cardapio'
          type='text'
          required
          aria-required='true'
          value='". $attr['nome_cardapio'] . "'
        />
      </div>

      <br />

      <br /><div>
        <label for='prato_A_cardapio'>Prato A:</label><br>
        <select name='prato_A_cardapio' id='prato_A_cardapio'>";
          
            require_once __DIR__ . "/../views/pratoView.php";

            $view = new pratoView;
            $pratos = $view->getAll();

            foreach ($pratos as $prato) {
              if ($prato->id_prato == $attr['prato_A_cardapio']) {
                echo '<option value="' . $prato->id_prato . '" selected>' . $prato->nome_prato . '</option>';
              }
              else {
                echo '<option value="' . $prato->id_prato . '">' . $prato->nome_prato . '</option>';
              }
            }
            
          echo "</select><br><br>

        <label for='prato_B_cardapio'>Prato B:</label><br>
        <select name='prato_B_cardapio' id='prato_B_cardapio'>";

            foreach ($pratos as $prato) {
              if ($prato->id_prato == $attr['prato_B_cardapio']) {
                echo '<option value="' . $prato->id_prato . '" selected>' . $prato->nome_prato . '</option>';
              }
              else {
                echo '<option value="' . $prato->id_prato . '">' . $prato->nome_prato . '</option>';
              }
            }
            ?>
        </select>
      </div>

      <br />

      <br />

      <div>
        <button type="submit">Alterar Cardápio</button>
        <button type="reset">Limpar</button>
      </div>
    </form>

    <!-- Mensagem de ajuda: o servidor deve tratar hashing da senha e validação adicional -->
    <p>
      Observação: trate a senha de forma segura no servidor (hash/salt), valide todos os dados
      no backend e proteja a rota contra CSRF antes de usar em produção.
    </p>";
  </main>
  <br><br><button onclick="window.location.replace('listCardapio.php')">Voltar à lista de Cardapios</button>

</body>
</html>