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
    <h1>Alterar Prato</h1>

    <form action="ctrlHub.php?class=prato&action=alter" method="post" novalidate enctype="multipart/form-data">

      <input type='text' name='id_prato' id='id_prato' style="display: none;"
          value="<?php echo $_GET['id']?>" required placeholder='Ex: Lasanha de Frango'>

      <?php
        require_once __DIR__ . "/../views/pratoView.php";
        require_once __DIR__ . "/../views/uploadView.php";

        date_default_timezone_set('America/Rio_Branco');
        
        $view = new pratoView(['id_prato' => $_GET['id']]);
        $attr = $view->getAtributtes();

        $upload = new uploadView(['id_upload' => $attr['foto_prato']]);

        echo "
      <label for='nome_prato'>Nome do prato:</label><br>
      <input type='text' name='nome_prato' id='nome_prato' value='" . $attr['nome_prato'] . "' required placeholder='Ex: Lasanha de Frango'><br><br>

      <label for='descricao_prato'>Descrição:</label><br>
      <textarea name='descricao_prato' id='descricao_prato' rows='6' placeholder='Descreva o prato'>" . $attr['descricao_prato'] . "</textarea><br><br>

      <label for='quantidade_disponivel_prato'>Quantidade disponível:</label><br>
      <input type='number' name='quantidade_disponivel_prato' id='quantidade_disponivel_prato' value='" . $attr['quantidade_disponivel_prato'] . "' min='1' required><br><br>

      <input type='text' name='data_prato' id='data_prato' style='display: none;' value='" . date('Y-m-d') . "'>

      <label for='foto_prato'>Foto do Prato:</label><br>
      <input type='file' name='foto_prato' id='foto_prato' accept='image/*'><br><br>

      <script>
        document.getElementById('foto_prato').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                    
                };
                reader.readAsDataURL(file);
            }
        });
      </script>
    <img src='" . $upload->get('path_upload') . "' id='preview' style='max-width: 100px;' alt=''><br><br>

       Botão
    <button type='submit'>Salvar Prato</button>";
      ?>
    </form>

    <p>
      Observação: trate a senha de forma segura no servidor (hash/salt), valide todos os dados
      no backend e proteja a rota contra CSRF antes de usar em produção.
    </p>
  </main>
  <br><br><button onclick="window.location.replace('listPrato.php')">Voltar à lista de Pratos</button>

</body>
</html>