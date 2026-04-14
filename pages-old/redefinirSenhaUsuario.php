<!DOCTYPE html>
<html lang="PT-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pipo</title>
</head>
<body>
    <h1>Alterar senha da conta de Usuário</h1>

    <p>Digite sua nova senha.</p>

    <?php 
    
    if (empty($_POST)) {
        echo "<form action='" . $_SERVER['PHP_SELF'] . "' method='post' novalidate>
            <input type='number' name='id_usuario' style='display: none;' value='" . $_GET['id'] . "'>
    
            <label for='senha_usuario'>Senha:</label><br>
            <input type='password' name='senha_usuario'><br><br>
            
            <label for='confirm_senha'>Confirmar senha:</label><br>
            <input type='password' name='confirm_senha'><br><br>
    
            <input type='submit' value='Confirmar nova senha'> <br><br>

            </form>
            <br><br><button onclick='window.location.replace('hub.php')'>Voltar ao Hub</button>";
    }
    else {
        require_once __DIR__ . "/../controllers/usuarioCtrl.php";
        require_once __DIR__ . "/../views/usuarioView.php";

        if ($_POST['senha_usuario'] == $_POST['confirm_senha']) {
            $usuarioView = new usuarioView(['id_usuario' => $_POST['id_usuario']]);
            $usuarioCtrl = new usuarioCtrl($usuarioView->getAtributtes());
    
            $usuarioCtrl->setData(['senha_usuario' => $_POST['senha_usuario']]);
            $usuarioCtrl->hashPassword();

            $usuarioCtrl->store();

            header("Location: loginUsuario.php?msg=sucesso");
            exit();
        }
        else {
            echo "<strong style='color: red;'>A senha não foi confirmada. Por favor, digite novamente.</strong>";
        }
    }
    ?>

</body>
</html>