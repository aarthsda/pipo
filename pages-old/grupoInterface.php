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
    <h1>Hub de grupo do usuário</h1>
    
    <?php
        require_once __DIR__ . "/../views/grupoView.php";
        require_once __DIR__ . "/../views/usuarioView.php";

        date_default_timezone_set('America/Sao_Paulo');

        $usuario = new usuarioView(['id_usuario' => $_COOKIE['user_id']]);
        $grupo = new grupoView(['id_grupo' => $usuario->get('grupo_usuario')]);

        echo "<table border='1' cellpadding='8' cellspacing='0'><thead><tr>
            <th>nome_usuario</th>
            <th>email_usuario</th>
            </tr></thead><tbody>";

        foreach ($usuario->getAll() as $usuario) {
            if ($usuario->grupo_usuario == $grupo->get('id_grupo')) {
                echo "<tr>";
                foreach ($usuario as $key => $value) {
                    if ($key == 'nome_usuario' || $key == 'email_usuario'){
                        echo "<th>$value</th>";
                    }
                }
                if ($grupo->get('dono_grupo') == $_COOKIE['user_id'] && $usuario->id_usuario != $_COOKIE['user_id']) {
                    echo "<th><button onclick=window.location.replace('promoteUsuario.php?id=" . $usuario->id_usuario . "')>Promover a dono</button></th>";
                    echo "<th><button onclick=window.location.replace('removeUsuario.php?id=" . $usuario->id_usuario . "')>Remover</button></th>";
                    echo "</tr>";
                }
            }
        }
        
        echo "</tbody></table>";
    
        if ($grupo->get('dono_grupo') == $_COOKIE['user_id']) {
            echo "<br><br><a href='inviteUsuario.php'>Convidar usuário para grupo</a><br>";
        }
        else {
            echo "<br><br>";
        }
        
        $usuario = new usuarioView(['id_usuario' => $_COOKIE['user_id']]);
        foreach ($usuario->getAll(['grupo_usuario' => $grupo->get('id_grupo')]) as $user) {
            if ($user->id_usuario != $_COOKIE['user_id']) {
                echo "<a href='sairGrupo.php'>Sair do grupo</a><br>";
            }
        }


        echo "<br><br><button onclick=window.location.replace('usuarioInterface.php')>Voltar ao Hub</button><br><br>";

        if (!empty($_GET['msg'])) {
            switch ($_GET['msg']) {
                case 'grupocheio':
                    echo "<br><strong style='color: red;'>O grupo ja esta cheio! Não será possível convidar outro usuário.</strong>";
                    break;
                case 'promocaosucesso':
                    echo "<br><strong style='color: green;'>Cargo de dono do grupo alterado com sucesso!</strong>";
                    break;
                case 'remocaosucesso':
                    echo "<br><strong style='color: green;'>Remoção de usuário do grupo realizada com sucesso!</strong>";
                    break;
                case 'saiugrupo':
                    echo "<br><strong style='color: green;'>Você saiu do grupo!</strong>";
                    break;
            }
            header( "refresh:5;url=grupoInterface.php" );
        }

    ?>
</body>
</html>