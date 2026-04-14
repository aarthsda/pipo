<?php
    if (empty($_COOKIE) || $_COOKIE['user_type'] != 'usuario' || $_COOKIE['user_id'] != $_GET['idTo']) {
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

    require_once __DIR__ . "/../views/grupoView.php";
    require_once __DIR__ . "/../controllers/grupoCtrl.php";
    require_once __DIR__ . "/../controllers/usuarioCtrl.php";
    require_once __DIR__ . "/../config/phpmail.php";

    date_default_timezone_set('America/Rio_Branco');

    $time = str_replace('-', ':', substr($_GET['date'], strpos($_GET['date'], '!') + 1));
    $dateExpire = new Datetime(substr($_GET['date'], 0, strpos($_GET['date'], '!')) . " $time");
    $dateToday = new Datetime(date('Y-m-d H:i'));

    $interval = $dateExpire->diff($dateToday);
    $interval = (int) $interval->format('%d');
    
    $grupoFrom = new grupoView(['dono_grupo' => $_GET['idFrom']]);

    $cheio = false;
    foreach ($usuario->getAll() as $user) {
        if ($user->grupo_usuario == $grupoFrom->get('id_grupo') && $user->id_usuario != $_GET['idFrom']) {
            $cheio = true;
            break;
        }
    }

    if ($cheio) {
        header("Location: usuarioInterface.php?msg=grupocheio");
        exit();
    }

    if ($interval >= 1) {
        header("Location: usuarioInterface.php?msg=conviteexpirado");
        exit();
    }

    
    $usuarioCtrl = new usuarioCtrl($usuario->getAtributtes());
    
    $usuarioCtrl->setData(['grupo_usuario' => $grupoFrom->get('id_grupo')]);

    $usuarioCtrl->store();

    $usuarioDono = new usuarioView(['id_usuario' => $grupoFrom->get('dono_grupo')]);

    $obj = new stdClass;
    $obj->usuariosGrupo = $usuario->getAll(['grupo_usuario' => $grupoFrom->get('id_grupo')]);
    $obj->usuario = $usuario;
    $obj->usuarioDono = $usuarioDono;

    var_dump($obj);
    file_put_contents('emails/data.json', json_encode($obj));


//     foreach ($usuario->getAll(['grupo_usuario' => $grupoFrom->get('id_grupo')]) as $user) {
//         if ($user->id_usuario != $_COOKIE['user_id']) {
//             $mail->setFrom('pipo.atendimento@gmail.com', 'Pipo - Atendimento');
//             $mail->addAddress($user->email_usuario, $user->nome_usuario);
    
//             $mail->isHTML(true);
//             $mail->Subject = 'Um novo participante entrou no seu grupo! - Pipo';
//             $mail->Body    = '<h3>Olá, ' . $user->nome_usuario . '</h3><p>Temos uma novidade!</p>
//     <p>O usuário <em>' . $usuario->get('nome_usuario') . '</em> acabou de entrar no seu grupo de almoço no sistema Pipo.</p>
//     <p>Agora vocês estão juntos no mesmo grupo e poderão realizar as próximas reservas de forma compartilhada.
// Lembre-se de que apenas o dono do grupo é responsável por realizar a reserva e escolher o prato prioritário.</p>
// <p>Se quiser, você pode acessar o site do Pipo para conferir os detalhes do seu grupo e verificar a situação atual da reserva.</p>
// <p>Atenciosamente,<br><strong>Equipe Pipo</strong></p>
// <p><em>Esta é uma mensagem automática — por favor, não responda diretamente a este e-mail.</em></p>';
    
//             $mail->send();
//         }
//         else {
//             $mail->setFrom('pipo.atendimento@gmail.com', 'Pipo - Atendimento');
//             $mail->addAddress($user->email_usuario, $user->nome_usuario);
    
//             $mail->isHTML(true);
//             $mail->Subject = 'Você entrou em um novo grupo! - Pipo';
//             $mail->Body    = '<h3>Olá, ' . $user->nome_usuario . '</h3><p>Boas notícias!</p>
//     <p>Você entrou em um novo grupo de almoço no sistema Pipo.</p>
//     <p>O grupo é composto por:</p>
//     <p><strong>Dono do grupo:</strong> ' . $usuarioDono->get('nome_usuario') . '</p>
//     <p><strong>Você:</strong> ' . $usuario->get('nome_usuario') . '</p>
//     <p>Agora vocês poderão realizar as reservas de forma conjunta, e o dono do grupo será responsável por confirmar as reservas e escolher o prato prioritário.</p>
//     <p>Se quiser conferir mais detalhes sobre seu novo grupo, acesse o site do Pipo e entre na sua conta.</p>
//     <p>Atenciosamente,<br><strong>Equipe Pipo</strong></p>
//     <p><em>Esta é uma mensagem automática — por favor, não responda diretamente a este e-mail.</em></p>';
    
//             $mail->send();

//         }
//     }

    header("Location: usuarioInterface.php?msg=conviteaceitado");
?>