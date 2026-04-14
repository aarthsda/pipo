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
    <h1>Hub de reserva do usuário</h1>
    
    <?php
      require_once __DIR__ . "/../views/reservaView.php";
      require_once __DIR__ . "/../views/grupoView.php";
      require_once __DIR__ . "/../views/filaView.php";

      $grupo = new grupoView(['id_grupo' => $usuario->get('grupo_usuario')]);
      $reservaView = new reservaView();

      if ($reservaView->getAll()) {
        echo "<table border='1' cellpadding='8' cellspacing='0'><thead><tr>
        <th>data_reserva</th>
        <th>tipo_reserva</th>
        <th>prato_prioritario_reserva</th>
        <th>fila_reserva</th>
        <th>posicao_reserva</th>
        </tr></thead><tbody>";
  
        date_default_timezone_set('America/Rio_Branco');
        
        foreach ($reservaView->getAll(['id_grupo_reserva' => $grupo->get('id_grupo'), 'status_reserva' => 'ativa']) as $reserva) {
          $fila = new filaView(['id_fila' => $reserva->id_fila_reserva]);
          echo "<tr>";
          foreach ($reserva as $key => $value) {
            if ($key == 'data_reserva' || $key == 'tipo_reserva' || $key == 'prato_prioritario_reserva' || $key == 'id_fila_reserva' || $key == 'posicao_reserva') {
                echo "<th>$value</th>";
            }
          }
          if ($grupo->get('dono_grupo') == $_COOKIE['user_id'] && (new Datetime(date('Y-m-d H:i')) < new Datetime($reserva->data_reserva . ' 11:30'))) {
            echo "<th><button onclick=window.location.replace('alterReserva.php?id=" . $reserva->id_reserva . "')>Alterar Prato</button></th>";
            echo "<th><button onclick=window.location.replace('cancelReserva.php?id=" . $reserva->id_reserva . "')>Cancelar</button></th>";
            echo "</tr>";
          }
        }
        echo "</tbody></table>";
      }
      else {
          echo "Nenhuma reserva ativa foi encontrada.<br>";
      }

      if ($grupo->get('dono_grupo') == $_COOKIE['user_id']) {
        echo "<br><br><a href='reservarFila.php'>Reservar almoço</a><br>";
      }
      else {
        echo "<br><br>";
      }

      echo "<a href='outrasReservas.php'>Ver outras reservas</a><br>";

      echo "<br><br><button onclick=window.location.replace('usuarioInterface.php')>Voltar ao Hub</button><br><br>";

      if (!empty($_GET['msg'])) {
        switch ($_GET['msg']) {
          case 'naodono':
            echo "<strong style='color: red;'>Somente o dono do grupo que você participa pode realizar reservas.</strong>";
            break;
          case 'jatemreserva':
            echo "<strong style='color: red;'>Você, ou seu grupo, ja possui uma reserva nessa fila.</strong>";
            break;
          case 'reservacancelada':
            echo "<strong style='color: green;'>Reserva cancelada com suscesso!</strong>";
            break;
          case 'pratoindisponivel':
            echo "<strong style='color: red;'>Este prato não esta mais disponível. Por favor, escolha outro.</strong>";
            break;
        }
        header( "refresh:5;url=filaInterface.php" );
      }
    ?>
</body>
</html>