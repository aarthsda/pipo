DELIMITER //

create TRIGGER tgr_usuario_after_insert AFTER INSERT
on usuario
for each ROW
begin
	UPDATE grupo SET dono_grupo = new.id_usuario WHERE id_grupo = new.grupo_usuario;
end//

DELIMITER ;