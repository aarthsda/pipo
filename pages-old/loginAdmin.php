<!DOCTYPE html>
<html lang="PT-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pipo</title>
</head>
<body>
    <h1>Realizar Login como Administrador</h1>

    <form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post" novalidate>
        <label for="email_admin">Email:</label><br>
        <input type="email" name="email_admin"><br><br>
        
        <label for="senha_admin">Senha:</label><br>
        <input type="password" name="senha_admin"><br>
        <a href="forgetSenhaAdmin.php">Esqueci minha senha</a><br><br>

        <input type="submit" value="Realizar Login"> <br><br>

    </form>
    <br><br><button onclick="window.location.replace('hub.php')">Voltar ao Hub</button>

    <?php 
        require_once __DIR__ . "/../views/adminView.php";
        require_once __DIR__ . "/../config/phpmail.php";

        if(!empty($_GET['msg']) && $_GET['msg']) {
            echo "<strong style='color: green;'>Senha alterada com sucesso!</strong>";
        }

        if (!empty($_POST)) {
            $admin = new adminView(['email_admin' => $_POST['email_admin']]);
            
            if (!empty($admin->getAtributtes())) {
                if ($admin->verifyPassword($_POST['senha_admin'])) {
                    $time = time() + (10 * 365 * 24 * 60 * 60);
                    
                    setcookie("user_type", 'admin', $time, "/");
                    setcookie("user_id", $admin->get('id_admin'), $time, "/");
                    setcookie("user_password", $_POST['senha_admin'], $time, "/");

                    header("Location: adminInterface.php");

                    $mail->setFrom('pipo.atendimento@gmail.com', 'Pipo - Atendimento');
                    $mail->addAddress($admin->get('email_admin'), $admin->get('nome_admin'));

                    date_default_timezone_set('America/Rio_Branco');

                    function detectOS($ua) {
                        $os = 'Desconhecido';
                        $oses = [
                            'Windows NT 10.0' => 'Windows 10',
                            'Windows NT 6.3'  => 'Windows 8.1',
                            'Windows NT 6.2'  => 'Windows 8',
                            'Windows NT 6.1'  => 'Windows 7',
                            'Windows NT 6.0'  => 'Windows Vista',
                            'Windows NT 5.1'  => 'Windows XP',
                            'Macintosh'       => 'macOS',
                            'Android'         => 'Android',
                            'iPhone'          => 'iOS',
                            'iPad'            => 'iPadOS',
                            'Linux'           => 'Linux'
                        ];
                        foreach ($oses as $pattern => $name) {
                            if (stripos($ua, $pattern) !== false) {
                                return $name;
                            }
                        }
                        return $os;
                    }

                    function detectBrowser($ua) {
                        $browsers = [
                            'Edge'      => 'Edge',
                            'Edg'       => 'Edge (Chromium)', // some UA use Edg
                            'OPR'       => 'Opera',
                            'Chrome'    => 'Google Chrome',
                            'CriOS'     => 'Google Chrome (iOS)',
                            'Firefox'   => 'Mozilla Firefox',
                            'Safari'    => 'Safari',
                            'MSIE'      => 'Internet Explorer',
                            'Trident'   => 'Internet Explorer' // newer IE UA
                        ];
                        foreach ($browsers as $pattern => $name) {
                            if (stripos($ua, $pattern) !== false) {
                                return $name;
                            }
                        }
                        return 'Desconhecido';
                    }

                    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
                    $os = detectOS($ua);
                    $browser = detectBrowser($ua);

                    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconhecido';
                    if ($ip === '::1') {
                        $ip = '127.0.0.1';
                    }
            
                    // $mail->isHTML(true);
                    // $mail->Subject = 'Alerta de login realizado - Pipo';
                    // $mail->Body    = '<h3>Olá, ' . $admin->get('nome_admin') . '</h3><p>Detectamos um novo acesso à sua conta no sistema <strong>Pipo</strong>.</p>
                    // <p><strong>Data e hora:</strong> ' . date("d/m/Y H:i") . ' (GMT-5)</p>
                    // <p><strong>Endereço IP:</strong> ' . $ip . '</p>
                    // <p><strong>Dispositivo:</strong> ' . $os . ' - ' . $browser . '</p>
                    // <p>Se foi você quem realizou o login, não é necessário fazer nada.</p>
                    // <p>Se <strong>não reconhece este acesso</strong>, recomendamos que altere sua senha imediatamente para proteger sua conta:</p>
                    // <a href="http://localhost/pipo/pages/redefinirSenhaAdmin.php?id=' . $admin->get('id_admin') . '">Alterar minha senha</a>
                    // <p>Em caso de dúvidas ou problemas, entre em contato com o suporte da Pipo.</p>
                    // <p>Atenciosamente,<br><strong>Equipe Pipo</strong></p>
                    // <p><em>Esta é uma mensagem automática — por favor, não responda diretamente a este e-mail.</em></p>';
            
                    // $mail->send();

                }
                else {
                    echo "<strong style='color: red;'>Senha incorreta.</strong>";
                }
            }
            else {
                echo "<strong style='color: red;'>A(s) informação(ões) não existe(m) no nosso banco de dados.</strong>";
            }

        }
    ?>
</body>
</html>