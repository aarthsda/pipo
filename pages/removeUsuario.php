<?php
  require_once __DIR__ . "/../config/auth.php";
  $usuarioId = requireAuth('usuario');

  require_once __DIR__ . "/../views/usuarioView.php";
  $usuario = new usuarioView(['id_usuario' => $usuarioId]);
    ?>

<!DOCTYPE html>
<html lang="PT-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pipo</title>
</head>
<body>
    <?php
        require_once __DIR__ . "/../views/grupoView.php";
        require_once __DIR__ . "/../controllers/grupoCtrl.php";
        require_once __DIR__ . "/../views/usuarioView.php";
        require_once __DIR__ . "/../controllers/usuarioCtrl.php";
        require_once __DIR__ . "/../config/phpmail.php";

        date_default_timezone_set('America/Rio_Branco');

        $usuarioDono = new usuarioView(['id_usuario' => $_COOKIE['user_id']]);

        $usuario = new usuarioView(['id_usuario' => $_GET['id']]);
        $grupo = new grupoView(['id_grupo' => $usuario->get('grupo_usuario')]);

        $usuarioCtrl = new usuarioCtrl($usuario->getAtributtes());
        
        $grupoCtrl = new grupoCtrl(['dono_grupo' => $_GET['id']]);
        $grupoCtrl->gerarCodigo();

        $usuarioCtrl->setData(['grupo_usuario' => $grupoCtrl->store()]);

        $usuarioCtrl->store();
        
        $mail->setFrom('pipo.atendimento@gmail.com', 'Pipo - Atendimento');
        $mail->addAddress($usuario->get('email_usuario'), $usuario->get('nome_usuario'));
        $mail->isHTML(true);
        $mail->Subject = 'Removido do grupo - Pipo';
        $mail->Body    = '<h3>Olá, ' . $usuario->get('nome_usuario') . '</h3>
        <p>Queremos informar que <em>' . $usuarioDono->get('nome_usuario') . '</em> removeu você do grupo de almoço que vocês faziam parte.</p>
        <p>No momento, você não faz mais parte de nenhum grupo ativo, mas não se preocupe!
Você ainda pode:</p>
        <p><strong>Reservar seu almoço individualmente,</strong> ou</p>
        <p><strong>Entrar em outro grupo disponível.</strong></p>
        <p>Para isso, acesse o site do Pipo e entre na sua conta para gerenciar seu grupo de almoço.</p>
        <p>Se você acredita que essa remoção foi um engano ou precisa de ajuda, nossa equipe está à disposição para orientar você.</p>
        <p>Atenciosamente,<br><strong>Equipe Pipo</strong></p>
        <p><em>Esta é uma mensagem automática — por favor, não responda diretamente a este e-mail.</em></p>';

        $mail->send();

        header("Location: grupoInterface.php?msg=remocaosucesso");
    ?>
</body>
</html>