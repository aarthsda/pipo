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
    <h1>Cadastro de Admin</h1>

    <!-- Formulário envia via POST para /admin/create (ajuste action conforme sua rota) -->
    <form action="ctrlHub.php?class=admin&action=create" id="form" method="post" novalidate>
      <!-- Código do admin: apenas números (ajuste pattern se quiser outra validação) -->
      <div>
        <label for="codigo_admin">Código (codigo_admin)</label><br />
        <input
          id="codigo_admin"
          name="codigo_admin"
          type="text"
          inputmode="numeric"
          pattern="\d+"
          placeholder="Somente números"
          required
          aria-required="true"
        />
        <small>Somente dígitos (ex.: 12345)</small>
      </div>

      <br />

      <!-- Nome do admin -->
      <div>
        <label for="nome_admin">Nome (nome_admin)</label><br />
        <input
          id="nome_admin"
          name="nome_admin"
          type="text"
          maxlength="100"
          placeholder="Nome completo"
          required
          aria-required="true"
        />
      </div>

      <br />

      <!-- Senha do admin -->
      <div>
        <input type="password" id="senha_admin" name="senha_admin" style="display: none;" required>
      </div>

      <script>
        document.querySelector('#form').addEventListener('submit', function (e) {
          document.querySelector('#senha_admin').value = document.querySelector('#codigo_admin').value;
        })
      </script>

      <br />

      <!-- Email do admin -->
      <div>
        <label for="email_admin">E-mail (email_admin)</label><br />
        <input
          id="email_admin"
          name="email_admin"
          type="email"
          maxlength="150"
          placeholder="seu@exemplo.com"
          required
          aria-required="true"
        />
      </div>

      <br />

      <div>
        <button type="submit">Criar Admin</button>
        <button type="reset">Limpar</button>
      </div>
    </form>

    <!-- Mensagem de ajuda: o servidor deve tratar hashing da senha e validação adicional -->
    <p>
      Observação: trate a senha de forma segura no servidor (hash/salt), valide todos os dados
      no backend e proteja a rota contra CSRF antes de usar em produção.
    </p>
  </main>
  <br><br><button onclick="window.location.replace('listAdmin.php')">Voltar à lista de admins</button>

</body>
</html>