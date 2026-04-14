DELIMITER //

create TRIGGER tgr_usuario_after_update AFTER UPDATE
on usuario
for each ROW
begin
    IF NEW.grupo_usuario <> OLD.grupo_usuario THEN
        DELETE FROM grupo WHERE dono_grupo = OLD.id_usuario AND id_grupo = old.grupo_usuario;
    END IF;
end//

DELIMITER ;