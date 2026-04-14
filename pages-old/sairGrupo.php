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

        $usuario = new usuarioView(['id_usuario' => $_COOKIE['user_id']]);
        $grupo = new grupoView(['id_grupo' => $usuario->get('grupo_usuario')]);

        
        foreach ($usuario->getAll(['grupo_usuario' => $grupo->get('id_grupo')]) as $user) {
          if ($user->id_usuario != $_COOKIE['user_id']) {
            $usuarioTo = $user;
          }
        }

        $grupoCtrl = new grupoCtrl($grupo->getAtributtes());
        $grupoCtrl->setData(['dono_grupo' => $user->id_usuario]);
        $grupoCtrl->store();

        $mail->setFrom('pipo.atendimento@gmail.com', 'Pipo - Atendimento');
        $mail->addAddress($usuarioTo->email_usuario, $usuarioTo->nome_usuario);
        $mail->isHTML(true);
        $mail->Subject = 'O outro participante saiu do seu grupo - Pipo';
        $mail->Body    = '<h3>Olá, ' . $usuarioTo->nome_usuario . '</h3>
        <p>Queremos informar que <em>' . $usuario->get('nome_usuario') . '</em> decidiu deixar o grupo de almoço que vocês haviam formado.</p>
        <p>No momento, você está novamente sem um parceiro de grupo, mas não se preocupe!
        Você ainda pode:</p>
        <p><strong>Reservar seu almoço individualmente,</strong> ou</p>
        <p><strong>Entrar em outro grupo disponível.</strong></p>
        <p>Para isso, acesse o site do Pipo e entre na sua conta para gerenciar seu grupo de almoço.</p>
        <p>Em caso de dúvidas ou problemas, entre em contato com o suporte da Pipo.</p>
        <p>Atenciosamente,<br><strong>Equipe Pipo</strong></p>
        <p><em>Esta é uma mensagem automática — por favor, não responda diretamente a este e-mail.</em></p>';

        $mail->send();
        
        $grupoCtrl = new grupoCtrl(['dono_grupo' => $_COOKIE['user_id']]);
        $grupoCtrl->gerarCodigo();

        $usuarioCtrl = new usuarioCtrl($usuario->getAtributtes());
        $usuarioCtrl->setData(['grupo_usuario' => $grupoCtrl->store()]);

        $usuarioCtrl->store();

        header("Location: grupoInterface.php?msg=saiugrupo");
    ?>
</body>
</html>