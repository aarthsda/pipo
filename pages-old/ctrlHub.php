<?php
    require_once __DIR__ . "/../controllers/!allCtrl.php";
    require_once __DIR__ . "/../views/!allView.php";
    require_once __DIR__ . "/../config/phpmail.php";

    $class = $_GET['class'];
    $ctrl = $class . 'Ctrl';
    $view = $class . 'View';
    $action = $_GET['action'];

    date_default_timezone_set('America/Rio_Branco');

    if ($action == 'create') {
        $ctrl = new $ctrl($_POST);

        if ($class == 'usuario') {
            $grupoCtrl = new grupoCtrl(['dono_grupo' => null]);
            $codigo = $grupoCtrl->gerarCodigo();
            $grupoCtrl->store();
            
            $grupoView = new grupoView(['codigo_grupo' => $codigo]);

            $ctrl->setData(['grupo_usuario' => $grupoView->get('id_grupo')]);
        }
        else if ($class == 'reserva') {
            if ($_COOKIE['user_type'] == 'guest') {
                $usuarioDono = new usuarioView(['email_usuario' => $_POST['email_usuario']]);
                $identifier_prop = 'email_usuario';
                $identifier_value = $_POST['email_usuario'];
                if ($usuarioDono->exists()) {
                    if (!$usuarioDono->verifyPassword($_POST['senha_usuario'])) {
                        header("Location: hub.php?msg=senhaincorreta");
                        exit();   
                    }
                }
                else {
                    header("Location: hub.php?msg=usuarioinexistente");
                    exit();
                }
            }
            else {
                $identifier_prop = 'id_usuario';
                $identifier_value = $_COOKIE['user_id'];
                $usuarioDono = new usuarioView(['id_usuario' => $_COOKIE['user_id']]);
            }
                
            $grupoView = new grupoView(['id_grupo' => $usuarioDono->get('grupo_usuario')]);

            if ($grupoView->get('dono_grupo') != $usuarioDono->get('id_usuario')) {
                if ($identifier_prop == 'email_usuario') {
                    header("Location: hub.php?msg=naodono");
                }
                else {
                    header("Location: filaInterface.php?msg=naodono");
                }
                exit();
            }
            
            $usuarios = $usuarioDono->getAll(['grupo_usuario' => $grupoView->get('id_grupo')]);

            $reservaView = new reservaView(['id_grupo_reserva' => $usuarioDono->get('grupo_usuario'), 'id_fila_reserva' => $_POST['id_fila']]);

            if ($reservaView->exists()) {
                if ($identifier_prop == 'email_usuario') {
                    header("Location: hub.php?msg=jatemreserva");
                }
                else {
                    header("Location: filaInterface.php?msg=jatemreserva");
                }
                exit();
            }

            $prato = new pratoView(['id_prato' => $_POST['prato_prioritario']]);

            $solo = true;
            foreach ($usuarioDono->getAll(['grupo_usuario' => $usuarioDono->get('grupo_usuario')]) as $user) {
                if ($user->id_usuario != $usuarioDono->get('id_usuario')) {
                    $solo = false;
                }
            }
            
            if ($prato->get('quantidade_disponivel_prato') == '0' || ($prato->get('quantidade_disponivel_prato') == '1' && !$solo)) {
                if ($identifier_prop == 'email_usuario') {
                    header("Location: hub.php?msg=pratoindisponivel");
                }
                else {
                    header("Location: filaInterface.php?msg=pratoindisponivel");
                }
                exit();
            }

            $reservaView = new reservaView;

            $ctrl = new $ctrl([
                'data_reserva' => date('Y-m-d'), 
                'tipo_reserva' => $usuarioDono->get('categoria_usuario'),
                'status_reserva' => 'ativa',
                'prato_prioritario_reserva' => $_POST['prato_prioritario'],
                'id_grupo_reserva' => $usuarioDono->get('grupo_usuario'),
                'id_fila_reserva' => $_POST['id_fila'],
                'posicao_reserva' => $reservaView->getLastPos()
            ]);

            $filaView = new filaView(['id_fila' => $_POST['id_fila']]);
            
            $tipo = 'Individual';
            foreach ($usuarios as $user) {
                if ($user->$identifier_prop != $identifier_value) {
                    $tipo = 'Grupo';
                    if ($filaView->get('reservas_em_fila') != $filaView->get('capacidade_maxima_fila')) {
                        $ctrl->setData(['tipo_reserva' => 'comum']);
                    }
                    else {
                        $ctrl->setData(['tipo_reserva' => 'reserva']);
                    }
                }
            }
            
            $reservaView = new reservaView(['id_reserva' => $ctrl->store()]);
            
            $dataFila = new Datetime($filaView->get('data_fila'));
            $filaCtrl = new filaCtrl($filaView->getAtributtes());

            if ($reservaView->get('tipo_reserva') != 'reserva') {
                $filaCtrl->setData(['reservas_em_fila' => (int) $filaView->get('reservas_em_fila') + 1]);
                $filaCtrl->store();
            }

            $pratoView = new pratoView(['id_prato' => $_POST['prato_prioritario']]);
            $pratoCtrl = new pratoCtrl($pratoView->getAtributtes());

            foreach ($usuarios as $user) {
                if ($reservaView->get('tipo_reserva') != 'reserva') {
                    $pratoCtrl->setData(['quantidade_disponivel_prato' => (int) $pratoView->get('quantidade_disponivel_prato') - 1]);
                    $pratoCtrl->store();
                }

                $mail->setFrom('pipo.atendimento@gmail.com', 'Pipo - Atendimento');
                $mail->addAddress($user->email_usuario, $user->nome_usuario);

                $mail->isHTML(true);
                $mail->Subject = 'Reserva realizada com sucesso! - Pipo';
                $mail->Body    = '<h3>Olá, ' . $user->nome_usuario . '</h3><p>Temos uma ótima notícia!</p>
                <p>Seu almoço foi reservado com sucesso no sistema Pipo.</p>
                <br>
                <strong>Detalhes da reserva:</strong>
                <br><br>
                <strong>Reservado por:</strong> ' . $usuarioDono->get('nome_usuario') . '<br>
                <strong>Data do almoço:</strong> ' . $dataFila->format('d/m/Y') . ' 11:30 (GMT-5)' . '<br>
                <strong>Prato escolhido:</strong> ' . $pratoView->get('nome_prato') . '<br>
                <strong>Tipo de reserva:</strong> ' . $tipo . ' - ' . $reservaView->get('tipo_reserva') . '<br>
                <strong>Posição na fila:</strong> nº ' . $reservaView->get('posicao_reserva') . '<br>
                <p>Você, caso tenha cadastrado no sistema, poderá confirmar sua presença utilizando a biometria — uma forma rápida e segura de validar sua refeição.</p>
                <p>Caso prefira, também é possível realizar a confirmação manual no local.</p>
                <p>Se por algum motivo você não puder comparecer, lembre-se de cancelar sua reserva pelo site do Pipo antes do horário limite.</p>
                <p>Atenciosamente,<br><strong>Equipe Pipo</strong></p>
                <p><em>Esta é uma mensagem automática — por favor, não responda diretamente a este e-mail.</em></p>';

                $mail->send();
            }

            header("Location: hub.php?msg=cadastrorealizado");

            exit();

        }
        
        if ($_FILES) {
            foreach ($_FILES as $file) {
                move_uploaded_file($file["tmp_name"], __DIR__ . '/../uploads/' . $file["name"]);
        
                $data = [
                    'nome_upload' => $file['name'],
                    'path_upload' => 'http://localhost/pipo/uploads/' . $file["name"],
                    'tipo_upload' => $file['type'],
                    'data_upload' => date('Y-m-d')
                ];
                $uploadCtrl = new uploadCtrl($data);
                $ctrl->setData([array_key_first($_FILES) => $uploadCtrl->store()]);
                
            }
        }

        $temSenha = false;
        foreach (array_keys($_POST) as $pos => $key) {
            if (str_contains($key, 'senha')) {
                $temSenha = true;
                break;
            }
        }
        if ($temSenha) {
            $ctrl->hashPassword(); 
        }

        $ctrl->store();

        header("Location: list$class.php");

        if ($class == 'usuario' || $class == 'admin') {
            if ($class == 'usuario') {
                $msg = '<p>Você foi cadastrado(a) por um administrador da <strong>Pipo</strong> e agora faz parte do nosso sistema. 
                <p>Abaixo estão suas credenciais de acesso ao sistema:</p>';
            }
            else if ($class == 'admin') {
                $msg = '<p>Um administrador do sistema <strong>Pipo</strong> criou uma conta de acesso para você com privilégios administrativos.</p>
                <p>Abaixo estão suas credenciais de acesso ao sistema:</p>';
            }
    
            $mail->setFrom('pipo.atendimento@gmail.com', 'Pipo - Atendimento');
            $mail->addAddress($_POST["email_$class"], $_POST["nome_$class"]);
    
            $mail->isHTML(true);
            $mail->Subject = 'Novo cadastro como Usuario - Pipo';
            $mail->Body    = '<h2>Bem-vindo(a) à Pipo!</h2><h3>Olá, ' . $_POST["nome_$class"] . '</h3>' . $msg . 
            '<p><strong>E-mail:</strong> ' . $_POST["email_$class"] . '</p>
            <p><strong>Senha temporária:</strong> ' . $_POST["senha_$class"] . '</p>
            <p>Por segurança, é altamente recomendado que você altere sua senha assim que realizar o primeiro acesso.</p>
    <p>Atenciosamente,<br><strong>Equipe Pipo</strong></p>';
    
            $mail->send();
        }
    }
    else if ($action == 'delete') {
        $view = new $view(["id_$class" => $_GET['id']]);
        $ctrl = new $ctrl($view->getAtributtes());

        $temFoto = false;
        $valueFoto = '';
        foreach ($view->getAtributtes() as $key => $value) {
            if (str_contains($key, 'foto')) {
                $temFoto = true;
                $valueFoto = $value;
                break;
            }
        }
        if ($temFoto) {
            $upload = new uploadView(['id_upload' => $valueFoto]);
            unlink(__DIR__ . "/../uploads/" . $upload->get('nome_upload'));
        }

        $ctrl->erase();
        header("Location: list$class.php");
    }
    else if ($action == 'alter') {
        if (!empty($_GET['id'])) {
            $id = $_GET['id'];
        }
        if (empty($_POST)) {
            header("Location: alter$class.php?id=$id");
        }
        else {
            $ctrl = new $ctrl($_POST);
            $view = new $view(["id_$class" => $_POST["id_$class"]]);

            if ($class == 'usuario') {
                $ctrl->setData(['grupo_usuario' => $view->get('grupo_usuario')]);
            }
            else if ($class == 'reserva') {
                foreach ($view->getAtributtes() as $prop => $value) {
                    if (!in_array($prop, array_keys($_POST))) {
                        $ctrl->setData([$prop => $value]);
                    }
                }

                $pratoNew = new pratoView(['id_prato' => $_POST['prato_prioritario_reserva']]);

                $usuarioView = new usuarioView;
                $solo = true;
                foreach ($usuarioView->getAll(['id_grupo' => $view->get('id_grupo_reserva')]) as $user) {
                    if ($user->id_usuario != $_COOKIE['user_id']) {
                        $solo = false;
                    }
                }
                
                if ($pratoNew->get('quantidade_disponivel_prato') == '0' || ($pratoNew->get('quantidade_disponivel_prato') == '1' && !$solo)) {
                    header("Location: filaInterface.php?msg=pratoindisponivel");
                    exit();
                }

                $pratoOld = new pratoView(['id_prato' => $view->get('prato_prioritario_reserva')]);

                $pratoOldCtrl = new pratoCtrl($pratoOld->getAtributtes());
                $pratoOldCtrl->setData(['quantidade_disponivel_prato' => (int) $pratoOld->get('quantidade_disponivel_prato') + 1]);
                $pratoOldCtrl->store();

                $pratoNewCtrl = new pratoCtrl($pratoNew->getAtributtes());
                $pratoNewCtrl->setData(['quantidade_disponivel_prato' => (int) $pratoNew->get('quantidade_disponivel_prato') - 1]);
                $pratoNewCtrl->store();

                foreach ($usuarioDono->getAll(['grupo_usuario' => $usuarioDono->get('grupo_usuario')]) as $user) {
                    $mail->setFrom('pipo.atendimento@gmail.com', 'Pipo - Atendimento');
                    $mail->addAddress($user->email_usuario, $user->nome_usuario);
                
                    $mail->isHTML(true);
                    $mail->Subject = 'Sua reserva foi alterada com sucesso! - Pipo';
                    $mail->Body    = '<h3>Olá, ' . $user->nome_usuario . '</h3>
                    <p>Informamos que a sua <strong>reserva de almoço foi alterada com sucesso</strong> no sistema <strong>Pipo</strong>.</p>

                    <p>Aqui estão os novos detalhes da sua reserva:</p>
                    <br>
                    <strong>Prato antigo:</strong> ' . $pratoOld->get('nome_prato') . '
                    <strong>Prato atual:</strong> ' . $pratoNew->get('nome_prato') . '
                    <br>
                    <p>Caso tenha feito essa alteração, tudo está certo! 😊</p>

                    <p>Lembre-se de que, ao chegar para o almoço, você poderá <strong>confirmar sua refeição com a biometria facial</strong>, tornando o processo mais rápido e prático.</p>

                    <p>Se não reconhece essa alteração, verifique com o dono do grupo ou acesse sua conta no site do Pipo e verifique suas reservas.</p>

                <p>Atenciosamente,<br><strong>Equipe Pipo</strong></p>
                <p><em>Esta é uma mensagem automática — por favor, não responda diretamente a este e-mail.</em></p>';
                
                    $mail->send();
                }
            }
            
            $imageError = false;
            if ($_FILES) {
                $upload = new uploadView(['id_upload' => $view->get("foto_$class")]);
                foreach ($_FILES as $file) {
                    if ($file['error']) {
                        $imageError = true;
                        break;
                    }
                    if ($file["name"] != $upload->get("nome_upload")) {
                        echo "peido";
                        unlink(__DIR__ . "/../uploads/" . $upload->get('nome_upload'));
                        move_uploaded_file($file["tmp_name"], __DIR__ . '/../uploads/' . $file["name"]); 
                
                        date_default_timezone_set('America/Rio_Branco');
                        $data = [
                            'nome_upload' => $file['name'],
                            'path_upload' => 'http://localhost/pipo/uploads/' . $file["name"],
                            'tipo_upload' => $file['type'],
                            'data_upload' => date('Y-m-d')
                        ];
                        $uploadCtrl = new uploadCtrl($data);
                        $ctrl->setData([array_key_first($_FILES) => $uploadCtrl->store()]);
                    }
                    else {
                        $ctrl->setData(["foto_$class" => $view->get("id_$class")]);
                    }
                }
            }

            if ($imageError) {
                $temFoto = false;
                $keyFoto = '';
                foreach (array_keys($_POST) as $pos => $key) {
                    if (str_contains($key, 'foto')) {
                        $temFoto = true;
                        $keyFoto = $key;
                        break;
                    }
                }
                if ($temFoto) {
                    $ctrl->setData(["foto_$class" => $view->get("id_$class")]);
                }
            }
            
            $temSenha = false;
            $keyFoto = '';
            foreach (array_keys($_POST) as $pos => $key) {
                if (str_contains($key, 'senha')) {
                    $temSenha = true;
                    $keySenha = $key;
                    break;
                }
            }
            if ($temSenha && $_POST[$keySenha] != $view->get("senha_$class")) {
                $ctrl->hashPassword();
            }

            $ctrl->store();
            header("Location: list$class.php");
        }

    }
    
?>