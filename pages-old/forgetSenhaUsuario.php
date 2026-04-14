<!DOCTYPE html>
<html lang="PT-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pipo</title>
</head>
<body>
    <h1>Recuperar acesso à conta de Usuário</h1>

    <p>Digite o e-mail cadastrado para que possamos enviar um link de redefinição de senha e ajudar você a recuperar o acesso à sua conta.</p>

    <form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post" novalidate>
        <label for="email_usuario">Email:</label><br>
        <input type="email" name="email_usuario"><br><br>

        <input type="submit" value="Enviar código"> <br><br>
    </form>
    <br><br><button onclick='window.location.replace("hub.php")'>Voltar ao Hub</button>

    <?php 
        if (!empty($_POST)) {
            require_once __DIR__ . "/../config/phpmail.php";
            require_once __DIR__ . "/../views/usuarioView.php";
    
            $usuario = new usuarioView(['email_usuario' => $_POST['email_usuario']]);

            if (!empty($usuario->getAtributtes())) {
                $mail->setFrom('pipo.atendimento@gmail.com', 'Pipo - Atendimento');
                $mail->addAddress($usuario->get('email_usuario'), $usuario->get('nome_usuario'));
        
                $mail->isHTML(true);
                $mail->Subject = 'Recuperar acesso a sua conta - Pipo';
                $mail->Body    = '<h2>Recuperação de senha - Usuário</h2><h3>Olá, ' . $usuario->get('nome_usuario') . '</h3><p>Recebemos uma solicitação para redefinir a senha da sua conta na <strong>Pipo</strong>.</p>
        <p>Para continuar o processo e criar uma nova senha, acesse o link abaixo:</p>
        <a href="http://localhost/pipo/pages/redefinirSenhaUsuario.php?id=' . $usuario->get('id_usuario') . 
        '">Clique Aqui</a><p>Se você não solicitou essa recuperação, pode ignorar este e-mail com segurança. Sua conta permanecerá protegida.</p>
        <p>Atenciosamente,<br><strong>Equipe Pipo</strong></p>
        <p><em>Esta é uma mensagem automática — por favor, não responda diretamente a este e-mail.</em></p>';
        
                $mail->send();

                echo "<strong>Para prosseguir, acesse o link que foi enviado pelo e-mail.</strong>";
            }
            else {
                echo "<strong style='color: red;'>Este e-mail não existe no nosso banco de dados.</strong>";
            }
    
        }
    ?>
</body>
</html>