<?php
    if (isset($_COOKIE['user_type'])) {
        switch ($_COOKIE['user_type']) {
            case 'usuario':
                header("Location: usuarioInterface.php");
                break;
            case 'admin':
                header("Location: adminInterface.php");
                break;
        }
    }
    else {
        setcookie("user_type", 'guest', time() + 3600, "/");
    }

    require_once __DIR__ . "/../views/filaView.php";

    $fila = new filaView;
    if (empty($fila->getAll(['status_fila' => 'ativa']))) {
        $style = 'display: none;';
    }
    else {
        $style = '';
    }
?>

<!DOCTYPE html>
<html lang="ptBR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HUB</title>
</head>
<body>
    <h1>Pipo Hub</h1>
    
    <a href="loginUsuario.php">Realizar Login como Usuario</a> <br>
    <a href="loginAdmin.php">Realizar Login como Administrador</a> <br><br>
    
    <form style="<?php echo $style;?>" action="ctrlHub.php?class=reserva&action=create" method="post" novalidate>
        <label for="email_usuario">Email:</label><br>
        <input type="email" name="email_usuario"><br><br>
        
        <label for="senha_usuario">Senha:</label><br>
        <input type="password" name="senha_usuario"><br><br>

        <label for="prato_prioritario">Prato prioritário:</label>
        <select name="prato_prioritario">
            <?php
                require_once __DIR__ . "/../views/cardapioView.php";
                require_once __DIR__ . "/../views/pratoView.php";

                date_default_timezone_set('America/Rio_Branco');

                $hoursNow = new Datetime(date('H:i'));
                $templateAlmoco = new Datetime('11:30');
                $templateDia = new Datetime('11:30');

                $dataFila = new Datetime(date('Y-m-d'));
                $dateTomorrow = new Datetime(date('Y-m-d'));
                $dateTomorrow = $dateTomorrow->modify('+1 day');
                
                if ($hoursNow > $templateAlmoco) {
                    $filaView = new filaView(['data_fila' => $dateTomorrow->format('Y-m-d')]);
                    $dataProxima = null;
                    if (!$filaView->exists()) {
                        foreach($filaView->getAll(['status_fila' => 'ativa']) as $fila) {
                            $dataFila = new Datetime($fila->data_fila);

                            $interval = $dataFila->diff(new Datetime(date('Y-m-d')));
                            $interval = (int) $interval->format('%h');

                            if ($interval < $dataProxima) {
                                $dataProxima = $interval;
                                $dataFila = $fila->data_fila;
                            }
                        }
                    }
                    else {
                        $dataFila = $filaView->get('data_fila');
                    }
                }
                
                $dataFila = $dataFila->format('Y-m-d');
                
                
                $fila = new filaView(['data_fila' => $dataFila]);
                $cardapio = new cardapioView(['id_cardapio' => $fila->get('cardapio_fila')]);
                $pratoA = new pratoView(['id_prato' => $cardapio->get('prato_A_cardapio')]);
                $pratoB = new pratoView(['id_prato' => $cardapio->get('prato_B_cardapio')]);

                echo "<option value='" . $pratoA->get('id_prato') . "'>" . $pratoA->get('nome_prato') . "</option>";
                echo "<option value='" . $pratoB->get('id_prato') . "'>" . $pratoB->get('nome_prato') . "</option>";
            ?>
        </select> <br><br>

        <input type="number" name="id_fila" style="display: none;" value="<?php echo $fila->get('id_fila') ?>">

        <input type="submit" value="Cadastrar almoço"> <br><br>
    </form>

    <?php 
        if(isset($_GET['msg'])) {
            switch ($_GET['msg']) {
                case 'senhaincorreta':
                    echo "<strong style='color: red;'>Senha incorreta.</strong>";
                    break;
                case 'cadastrorealizado':
                    echo "<strong style='color: green;'>Cadastro do almoço realizado!</strong>";
                    break;
                case 'usuarioinexistente':
                    echo "<strong style='color: red;'>O E-mail informado não existe no nosso banco de dados.</strong>";
                    break;
                case 'acessoproibido':
                    echo "<strong style='color: red;'>Você não tem permissão para acessar essa página.</strong>";
                    break;
                case 'sairdaconta':
                    echo "<strong style='color: green;'>Você saiu da conta com sucesso!</strong>";
                    break;
                case 'naodono':
                    echo "<strong style='color: red;'>Somente o dono do grupo que você participa pode realizar reservas.</strong>";
                    break;
                case 'foradehora':
                    echo "<strong style='color: red;'>A área de confirmação de almoço só pode ser acessada após  11:30 (GMT-5)</strong>";
                    break;
                case 'jatemreserva':
                    echo "<strong style='color: red;'>Você, ou seu grupo, ja possui uma reserva nessa fila.</strong>";
                    break;
                case 'pratoindisponivel':
                    echo "<strong style='color: red;'>Este prato esta indisponível. Por favor, escolha outro.</strong>";
                    break;
                case 'jafoitodos':
                    echo "<strong style='color: green;'>Todos já confirmaram seu almoço!</strong>";
                    break;
            }
            
            header( "refresh:5;url=hub.php" );
        }
    ?>
</body>
</html>