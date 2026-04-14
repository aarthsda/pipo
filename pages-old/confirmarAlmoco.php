<?php
    setcookie("user_type", 'guest', time() + 3600, "/");
    setcookie("user_id", '', time() + 3600, "/");
    setcookie("user_password", '', time() + 3600, "/");

    // setcookie("time", '', time() - 3600, "/");
    // setcookie("first", '', time() - 3600, "/");

    // var_dump($_COOKIE);
    
    require_once __DIR__ . "/../controllers/!allCtrl.php";
    require_once __DIR__ . "/../views/!allView.php";
    require_once __DIR__ . "/../config/phpmail.php";

    date_default_timezone_set('America/Rio_Branco');
    // echo "<br><br>";
    // var_dump(!isset($_COOKIE['time']));
    if (!isset($_COOKIE['time'])) {
        setcookie("time", date('Y-m-d H:i'), time() + 3600, "/");
        
        $reserva = new reservaView;
        $reservas = $reserva->getAll(['tipo_reserva' => 'reserva', 'status_reserva' => 'ativa']);
        $prioritarios = $reserva->getAll(['tipo_reserva' => 'prioritario', 'status_reserva' => 'ativa']);
        $comuns = $reserva->getAll(['tipo_reserva' => 'comum', 'status_reserva' => 'ativa']);

        if (!empty($prioritarios) || !empty($comuns)) {
            if (!empty($prioritarios)) {
                $first = $prioritarios[0]->id_reserva;
            }
            else {
                $first = $comuns[0]->id_reserva;
            }
        }
        else if (!empty($reservas)) {
            $first = $reservas[0]->id_reserva;
        }
        else {
            setcookie("time", '', time() - 3600, "/");
            setcookie("first", '', time() - 3600, "/");
            header('Location: hub.php?msg=jafoitodos');
            exit();
        }

        setcookie("first", $first, time() + 3600, "/");

        $reserva = new reservaView(['id_reserva' => $first]);
        $grupo = new grupoView(['id_grupo' => $reserva->get('id_grupo_reserva')]);
        $usuario = new usuarioView;
        
        foreach ($usuario->getAll(['grupo_usuario' => $grupo->get('id_grupo')]) as $user) {
            $mail->setFrom('pipo.atendimento@gmail.com', 'Pipo - Atendimento');
            $mail->addAddress($user->email_usuario, $user->nome_usuario);
        
            $mail->isHTML(true);
            $mail->Subject = 'Aviso! O tempo para confirmar sua reserva esta acabando - Pipo';
            $mail->Body    = '<h3>Olá, ' . $user->nome_usuario . '</h3>
            <p>Você, ou o dono do seu grupo, tem apenas 2 minutos para chegar ao refeitório e efetuar a reserva do almoço.</p>

<p>Caso o tempo se esgote, você será automaticamente realocado para o final da fila, e a prioridade atual será perdida.</p>

<p>Recomendamos que todos fiquem atentos ao horário e prontos para se dirigir ao refeitório imediatamente.</p>

<p>Lembre-se:<em> a pontualidade garante uma experiência mais rápida e organizada para todos!</em></p>
        <p>Atenciosamente,<br><strong>Equipe Pipo</strong></p>
        <p><em>Esta é uma mensagem automática — por favor, não responda diretamente a este e-mail.</em></p>';
        
        $mail->send();

        header("Refresh:0");
    }
}
// echo "<br><br>";
// var_dump($_COOKIE);

    // if (new Datetime(date('H:i')) < new Datetime('11:30') || new Datetime(date('H:i')) >= new Datetime('14:20')) {
    //     header("Location: hub.php?msg=foradehora");
    //     exit();
    // }

?>

<!DOCTYPE html>
<html lang="PT-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pipo</title>
</head>
<body>
    <h1>Área de confirmação de almoço</h1>

    <a href="http://localhost/pipo/_Reconhecimento/frontend/reconhecimento.php">Confirmar almoço com Biometria</a><br><br>

    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" novalidate>
        <label for="email_usuario">Email:</label><br>
        <input type="email" name="email_usuario"><br><br>
        
        <label for="senha_usuario">Senha:</label><br>
        <input type="password" name="senha_usuario"><br><br>

        <input type="submit" value="Confirmar almoço">
    </form>

    <?php
        $late = new DateTime($_COOKIE['time'] ?? date('Y-m-d'));
        $now = new DateTime(date('Y-m-d H:i'));

        // Calcula a diferença entre elas
        $diferenca = $late->diff($now);

        // Converte a diferença total em minutos
        $minutos = ($diferenca->days * 24 * 60) + ($diferenca->h * 60) + $diferenca->i;

        // Verifica se a diferença é de mais de 2 minutos
        if ($minutos > 2 || ($minutos == 2 && $diferenca->s > 0)) {
            $reserva = new reservaView(['id_reserva' => $_COOKIE['first']]);
            $reservaCtrl = new reservaCtrl($reserva->getAtributtes());

            $reservaCtrl->setData(['tipo_reserva' => 'reserva']);
            $reservaCtrl->store();

            setcookie("time", '', time() - 3600, "/");
            header("Refresh:0");
        }

        if (!empty($_POST)) {
            if (empty($_POST['matricula_usuario'])) {
                $usuarioDono = new usuarioView(['email_usuario' => $_POST['email_usuario']]);
        
                if ($usuarioDono->exists()) {
                    if (!$usuarioDono->verifyPassword($_POST['senha_usuario'])) {
                        header("Location: confirmarAlmoco.php?msg=senhaincorreta");
                        exit();   
                    }
                }
                else {
                    header("Location: confirmarAlmoco.php?msg=usuarioinexistente");
                    exit();
                }
            }
            else {
                $usuarioDono = new usuarioView(['matricula_usuario' => $_POST['matricula_usuario']]);
            }
            
            $reserva = new reservaView(['id_reserva' => $_COOKIE['first']]);
            if ($usuarioDono->get('grupo_usuario') != $reserva->get('id_grupo_reserva')) {
                header("Location: confirmarAlmoco.php?msg=furou");
                exit();
            }
    
            $grupoView = new grupoView(['id_grupo' => $usuarioDono->get('grupo_usuario')]);
    
            if ($grupoView->get('dono_grupo') != $usuarioDono->get('id_usuario')) {
                header("Location: confirmarAlmoco.php?msg=naodono");
                exit();
            }
    
            $reserva = new reservaView(['id_grupo_reserva' => $grupoView->get('id_grupo')]);

            if (!$reserva->exists()) {
                header("Location: confirmarAlmoco.php?msg=naoreserva");
                exit();
            }

            $fila = new filaView(['id_fila' => $reserva->get('id_fila_reserva')]);

            if ($fila->get('data_fila') != date('Y-m-d')) {
                header("Location: confirmarAlmoco.php?msg=naoreserva");
                exit();
            }

            $reservaCtrl = new reservaCtrl($reserva->getAtributtes());
            $reservaCtrl->setData(['status_reserva' => 'concluida']);
            $reservaCtrl->store();
            setcookie("time", '', time() - 3600, "/");

            foreach ($usuarioDono->getAll(['grupo_usuario' => $usuarioDono->get('grupo_usuario')]) as $user) {
                $mail->setFrom('pipo.atendimento@gmail.com', 'Pipo - Atendimento');
                $mail->addAddress($user->email_usuario, $user->nome_usuario);
            
                $mail->isHTML(true);
                $mail->Subject = 'Sua reserva foi confirmada com sucesso! - Pipo';
                $mail->Body    = '<h3>Olá, ' . $user->nome_usuario . '</h3>
                <p>Temos uma ótima notícia! Sua <strong>reserva de almoço foi confirmada com sucesso</strong> no sistema <strong>Pipo</strong>.</p>

                <p>Aqui estão os detalhes da confirmação:</p>

                <ul>
                <li><strong>Data do almoço:</strong> ' . date('d/m/Y') . '</li>
                <li><strong>Confirmado por:</strong> ' . $usuarioDono->get('nome_usuario') . '</li>
                <li><strong>Hora da confirmação:</strong> ' . date('H:i') . ' (GMT-5)</li>
                </ul>

                <p>Agora é só aproveitar o almoço com o seu prato favorito!</p>

                <p>Mas, se você não reconhece essa ação, recomendamos que entre em contato com o suporte da plataforma Pipo para verificar suas reservas e garantir que tudo esteja correto.</p>

                <p>Atenciosamente,<br><strong>Equipe Pipo</strong></p>
                <p><em>Esta é uma mensagem automática — por favor, não responda diretamente a este e-mail.</em></p>';
            
                $mail->send();
            }
    
            header("Location: confirmarAlmoco.php?msg=confirmada");
    
        }
        
        echo "<br><br><button onclick=window.location.replace('hub.php')>Voltar ao Hub</button><br><br>";

        if (!empty($_GET)) {
            switch ($_GET['msg']) {
                case 'senhaincorreta':
                    echo "<strong style='color: red;'>Senha incorreta.</strong>";
                    break;
                case 'usuarioinexistente':
                    echo "<strong style='color: red;'>O E-mail informado não existe no nosso banco de dados.</strong>";
                    break;
                case 'naodono':
                    echo "<strong style='color: red;'>Somente o dono do grupo que você participa pode realizar reservas.</strong>";
                    break;
                case 'confirmada':
                    echo "<strong style='color: green;'>Reserva confirmada com suscesso! Tenha um bom almoço!</strong>";
                    break;
                case 'naoreserva':
                    echo "<strong style='color: red;'>Você não possui uma reserva pro dia de hoje.</strong>";
                    break;
                case 'furou':
                    echo "<strong style='color: red;'>Ainda não chegou a sua vez de confirmar sua reserva.</strong>";
                    break;
            }
            header( "Refresh:5;url=confirmarAlmoco.php" );
        }

        header("Refresh:140");
    ?>
</body>
</html>