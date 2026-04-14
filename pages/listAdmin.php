<?php
  require_once __DIR__ . "/../config/auth.php";
  $adminId = requireAuth('admin');

  require_once __DIR__ . "/../views/adminView.php";
  $admin = new adminView(['id_admin' => $adminId]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pipo</title>
</head>
<body>
    <h1>Listar admins</h1>
    <?php
        require_once __DIR__ . "/../views/adminView.php";

        $view = new adminView();
        $view->printTable($view->getAll());
    ?>
    <br><br><button onclick="window.location.replace('hub.php')">Voltar ao Hub</button>
</body>
</html>