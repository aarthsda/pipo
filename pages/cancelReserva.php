<?php
  require_once __DIR__ . "/../config/auth.php";
  $usuarioId = requireAuth('usuario');

  require_once __DIR__ . "/../views/usuarioView.php";
  $usuario = new usuarioView(['id_usuario' => $usuarioId]);

  require_once __DIR__ . "/../views/reservaView.php";
  require_once __DIR__ . "/../controllers/reservaCtrl.php";
  require_once __DIR__ . "/../views/pratoView.php";
  require_once __DIR__ . "/../controllers/pratoCtrl.php";
  require_once __DIR__ . "/../views/filaView.php";
  require_once __DIR__ . "/../controllers/filaCtrl.php";
  
  $view = new reservaView(['id_reserva' => $_GET['id']]);

  date_default_timezone_set('America/Rio_Branco');
  
  $ctrl = new reservaCtrl($view->getAtributtes());
  $ctrl->setData(['status_reserva' => 'cancelada']);
  $ctrl->store();

  $comecar = false;
  foreach ($view->getAll() as $reserva) {
    if ($comecar) {
      $viewTemp = new reservaView(['id_reserva' => $reserva->id_reserva]);
      $ctrlTemp = new reservaCtrl($viewTemp->getAtributtes());
      
      $ctrl->setData(['posicao_reserva' => (int) $viewTemp->get('posicao_reserva') - 1]);
      
      $ctrlTemp->store();
    }

    if ($reserva->id_reserva == $_GET['id']) {
      $comecar = true;
    }
  }

  $prato = new pratoView(['id_prato' => $view->get('prato_prioritario_reserva')]);

  $pratoCtrl = new pratoCtrl($prato->getAtributtes());
  $pratoCtrl->setData(['quantidade_disponivel_prato' => (int) $prato->get('quantidade_disponivel_prato') + 1]);
  $pratoCtrl->store();

  $fila = new filaView(['id_fila' => $view->get('id_fila_reserva')]);

  $filaCtrl = new filaCtrl($fila->getAtributtes());
  $filaCtrl->setData(['reservas_em_fila' => (int) $fila->get('reservas_em_fila') - 1]);
  $filaCtrl->store();

  require_once __DIR__ . "/../config/phpmail.php";

  foreach ($usuario->getAll(['grupo_usuario' => $usuario->get('grupo_usuario')]) as $user) {
    $mail->setFrom('pipo.atendimento@gmail.com', 'Pipo - Atendimento');
    $mail->addAddress($user->email_usuario, $user->nome_usuario);
  
    $mail->isHTML(true);
    $mail->Subject = 'Reserva cancelada com sucesso! - Pipo';
    $mail->Body    = '<h3>Olá, ' . $user->nome_usuario . '</h3>
    <p>Informamos que a sua <strong>reserva de almoço foi cancelada com sucesso</strong> no sistema <strong>Pipo</strong>.</p>
  
  <p>Se o cancelamento foi feito por você ou o dono do seu grupo, não se preocupe — tudo está em ordem!</p>
  
  <p>Mas, se você não reconhece essa ação, recomendamos acessar sua conta no site do Pipo para verificar suas reservas e garantir que tudo esteja correto.</p>
  
  <p>Você poderá fazer uma nova reserva a qualquer momento, individualmente ou com seu grupo de almoço.</p>
  <p>Atenciosamente,<br><strong>Equipe Pipo</strong></p>
  <p><em>Esta é uma mensagem automática — por favor, não responda diretamente a este e-mail.</em></p>';
  
    $mail->send();
  }


  header("Location: filaInterface.php?msg=reservacancelada");
?>