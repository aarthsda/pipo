<?php

$obj = new stdClass;
$obj->usuariosGrupo = $usuario->getAll(['grupo_usuario' => $grupoFrom->get('id_grupo')]);
$obj->usuario = $usuario;
$obj->usuarioDono = $usuarioDono;

var_dump($obj);
file_put_contents('data.json', json_encode($obj));

?>