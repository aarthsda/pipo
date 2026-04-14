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
    <h1>Cadastro de Usuário</h1>

    <!-- Formulário envia via POST para /admin/create (ajuste action conforme sua rota) -->
    <form action="ctrlHub.php?class=usuario&action=create" id="form" method="post" novalidate enctype="multipart/form-data">
      <!-- Código do admin: apenas números (ajuste pattern se quiser outra validação) -->
      <div>
        <label for="nome_usuario">Nome:</label><br>
        <input type="text" id="nome_usuario" name="nome_usuario" required><br><br>
      </div>

      <div>
        <label for="email_usuario">E-mail:</label><br>
        <input type="email" id="email_usuario" name="email_usuario" required><br><br>
      </div>

      <div>
        <input type="password" id="senha_usuario" name="senha_usuario" style="display: none;" required>
      </div>

      <script>
        document.querySelector('#form').addEventListener('submit', function (e) {
          document.querySelector('#senha_usuario').value = document.querySelector('#matricula_usuario').value;
        })
      </script>

      <div>
        <label for="matricula_usuario">Matrícula:</label><br>
        <input type="text" id="matricula_usuario" name="matricula_usuario"><br><br>
      </div>

      <div>
        <label for="categoria_usuario">Categoria:</label><br>
        <select id="categoria_usuario" name="categoria_usuario">
          <option value="comum">Comum</option>
          <option value="prioritario">Prioritário</option>
        </select><br><br>
      </div>

      <br />

      <div>
        <button type="submit">Criar Usuário</button>
        <button type="reset">Limpar</button>
      </div>
    </form>

    <!-- Mensagem de ajuda: o servidor deve tratar hashing da senha e validação adicional -->
    <p>
      Observação: trate a senha de forma segura no servidor (hash/salt), valide todos os dados
      no backend e proteja a rota contra CSRF antes de usar em produção.
    </p>
  </main>
  <br><br><button onclick="window.location.replace('listUsuario.php')">Voltar à lista de Usuários</button>

</body>
</html>