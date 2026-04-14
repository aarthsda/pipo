<?php
  require_once __DIR__ . "/../config/auth.php";
  $adminId = requireAuth('admin');

  require_once __DIR__ . "/../views/adminView.php";
  $admin = new adminView(['id_admin' => $adminId]);
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
    <h1>Cadastro de Cardápio</h1>

    <!-- Formulário envia via POST para /admin/create (ajuste action conforme sua rota) -->
    <form action="ctrlHub.php?class=cardapio&action=create" method="post" novalidate>
      <!-- Código do admin: apenas números (ajuste pattern se quiser outra validação) -->
      <div>
        <label for="nome_cardapio">Nome (nome_cardapio)</label><br />
        <input
          id="nome_cardapio"
          name="nome_cardapio"
          type="text"
          required
          aria-required="true"
        />
      </div>

      <br />

      <br />

      <!-- Senha do admin -->
      <div>
        <label for="prato_A_cardapio">Prato A:</label><br>
        <select name="prato_A_cardapio" id="prato_A_cardapio">
          <?php 
            require_once __DIR__ . "/../views/pratoView.php";

            $view = new pratoView;
            $pratos = $view->getAll();

            foreach ($pratos as $prato) {
              echo '<option value="' . $prato->id_prato . '">' . $prato->nome_prato . '</option>';
            }
            
          echo "</select><br><br>

        <label for='prato_B_cardapio'>Prato B:</label><br>
        <select name='prato_B_cardapio' id='prato_B_cardapio'>";

            foreach ($pratos as $prato) {
              echo '<option value="' . $prato->id_prato . '">' . $prato->nome_prato . '</option>';
            }
            ?>
        </select>
      </div>

      <br />

      <br />

      <div>
        <button type="submit">Criar Cardápio</button>
        <button type="reset">Limpar</button>
      </div>
    </form>

    <!-- Mensagem de ajuda: o servidor deve tratar hashing da senha e validação adicional -->
    <p>
      Observação: trate a senha de forma segura no servidor (hash/salt), valide todos os dados
      no backend e proteja a rota contra CSRF antes de usar em produção.
    </p>
  </main>
  <br><br><button onclick="window.location.replace('listCardapio.php')">Voltar à lista de Cardapios</button>

</body>
</html>