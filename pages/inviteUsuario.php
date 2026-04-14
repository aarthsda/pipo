<?php
  require_once __DIR__ . "/../config/auth.php";
  $usuarioId = requireAuth('usuario');

  require_once __DIR__ . "/../views/usuarioView.php";
  $usuario = new usuarioView(['id_usuario' => $usuarioId]);

    require_once __DIR__ . "/../views/grupoView.php";

    $grupoFrom = new grupoView(['dono_grupo' => $_COOKIE['user_id']]);
            
    $cheio = false;
    foreach ($usuario->getAll() as $user) {
        if ($user->grupo_usuario == $grupoFrom->get('id_grupo') && $user->id_usuario != $_COOKIE['user_id']) {
            $cheio = true;
            break;
        }
    }

    if ($cheio) {
        header("Location: grupoInterface.php?msg=grupocheio");
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
    <h1>Convidar outro usuário para grupo</h1>
    
    <?php
        require_once __DIR__ . "/../views/usuarioView.php";
        require_once __DIR__ . "/../config/phpmail.php";

        date_default_timezone_set('America/Rio_Branco');

        $view = new usuarioView;
        if (!empty($_GET) && (empty($_GET['invited']))) {
            $userTo = new usuarioView(['id_usuario' => $_GET['id']]);
            $userFrom = new usuarioView(['id_usuario' => $_COOKIE['user_id']]);
            
            header("Location: inviteUsuario.php?invited=" . $_GET['id']);
            
            $mail->setFrom('pipo.atendimento@gmail.com', 'Pipo - Atendimento');
            $mail->addAddress($userTo->get('email_usuario'), $userTo->get('nome_usuario'));

            $mail->isHTML(true);
            $mail->Subject = 'Convite para grupo - Pipo';
            $mail->Body    = '<h3>Olá, ' . $userTo->get('nome_usuario') . '</h3><p>Você recebeu um convite para formar um grupo de almoço com <em>' . $userFrom->get('nome_usuario') . '</em>! Essa é uma ótima oportunidade para socializar, trocar ideias e aproveitar o intervalo com boa companhia.</p>
            <br>
            <strong>Detalhes do convite:</strong>
            <br><br>
            <strong>Enviado por:</strong> ' . $userFrom->get('email_usuario') . '<br>
            <strong>Grupo:</strong> 2 pessoas<br>
            <strong>Status:</strong> Aguardando sua confirmação<br>
            <strong>Expira em:</strong> ' . date('d')+1 . date('/m/Y H:i') . '<br>
            <p>Para aceitar o convite, acesse o link abaixo:</p>
            <p><a href="http://localhost/pipo/pages/acceptInvite.php?idFrom=' . $userFrom->get('id_usuario') . '&idTo=' . $userTo->get('id_usuario') . '&date=' . date('d')+1 . date('-m-Y!H-i') . '">Confirmar participação no grupo</a></p>
            <p>Para recusar o convite, apenas ignore esse e-mail.</p>
            <p>Se você não responder dentro do prazo, o convite será automaticamente cancelado.</p>
            <p>Atenciosamente,<br><strong>Equipe Pipo</strong></p>
            <p><em>Esta é uma mensagem automática — por favor, não responda diretamente a este e-mail.</em></p>';
    
            $mail->send();

            exit();
            }
        else {
            if (!empty($_GET['invited'])) {
                $view->buildData(['id_usuario' => $_GET['invited']]);
                $view->pickNBecome();

                echo "<strong style='color: green;'>Convite de grupo enviado para <em>" . $view->get('nome_usuario') . "</em></strong><br><br>";
            }

            echo "<table border='1' cellpadding='8' cellspacing='0'><thead><tr>
            <th>nome_usuario</th>
            <th>email_usuario</th>
            <th>Convite</th>
            </tr></thead><tbody>";
            
            $usuarios = $view->getAll();

            foreach ($usuarios as $usuario) {
                if ($usuario->id_usuario != $_COOKIE['user_id']) {
                    echo "<tr>";
                    foreach ($usuario as $key => $value) {
                        if ($key == 'nome_usuario' || $key == 'email_usuario'){
                            echo "<th>$value</th>";
                        }
                    }
                    echo "<th><button onclick=window.location.replace('inviteUsuario.php?id=" . $usuario->id_usuario . "')>Convidar</button></th>";
                    echo "</tr>";
                }
            }

            echo "</tbody></table>";
        }

    ?>
    <br><br><button onclick="window.location.replace('grupoInterface.php')">Voltar ao Hub</button>

    <?php
      
    ?>
</body>
</html>