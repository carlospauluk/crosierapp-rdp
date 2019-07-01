START TRANSACTION;

-- app                   b10ef8c0-841f-4688-9ee2-30e39639be8a
-- program               b5bab428-2265-43df-a407-daecfed34e05
-- entMenu (CrosierCore) 2dd6aac9-14ba-49d9-9676-d116a062b747
-- entMenu (Raíz do App) f5dd11fb-dfde-40af-91c9-6583192e8d83
-- entMenu (Dashboard)   6542ace8-132a-46e6-91fb-900313d20275

SET FOREIGN_KEY_CHECKS=0;

DELETE FROM cfg_app WHERE uuid = 'b10ef8c0-841f-4688-9ee2-30e39639be8a';
DELETE FROM cfg_program WHERE uuid = 'b5bab428-2265-43df-a407-daecfed34e05';
DELETE FROM cfg_entmenu WHERE uuid = '2dd6aac9-14ba-49d9-9676-d116a062b747';
DELETE FROM cfg_entmenu WHERE uuid = 'f5dd11fb-dfde-40af-91c9-6583192e8d83';
DELETE FROM cfg_entmenu WHERE uuid = '6542ace8-132a-46e6-91fb-900313d20275';


INSERT INTO cfg_app(uuid,nome,obs,default_entmenu_uuid,inserted,updated,estabelecimento_id,user_inserted_id,user_updated_id) 
VALUES ('b10ef8c0-841f-4688-9ee2-30e39639be8a','crosierapp-rdp','crosierapp-rdp','f5dd11fb-dfde-40af-91c9-6583192e8d83',now(),now(),1,1,1);

INSERT INTO cfg_program(uuid, descricao, url, app_uuid, entmenu_uuid ,inserted, updated, estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('b5bab428-2265-43df-a407-daecfed34e05','Dashboard', '/', 'b10ef8c0-841f-4688-9ee2-30e39639be8a', null, now(), now(), 1, 1, 1);



-- Entrada de menu para o MainMenu do Crosier com apontamento para o Dashboard deste CrosierApp (É EXIBIDO NO MENU DO CROSIER-CORE)
INSERT INTO cfg_entmenu(uuid,label,icon,tipo,program_uuid,pai_uuid,ordem,css_style,inserted,updated,estabelecimento_id,user_inserted_id,user_updated_id)
VALUES ('2dd6aac9-14ba-49d9-9676-d116a062b747','Rodoponta','fas fa-columns','CROSIERCORE_APPENT','b5bab428-2265-43df-a407-daecfed34e05',null,0,null,now(),now(),1,1,1);

-- Entrada de menu raíz para este CrosierApp (NÃO É EXIBIDO)
INSERT INTO cfg_entmenu(uuid,label,icon,tipo,program_uuid,pai_uuid,ordem,css_style,inserted,updated,estabelecimento_id,user_inserted_id,user_updated_id)
VALUES ('f5dd11fb-dfde-40af-91c9-6583192e8d83','crosierapp-rdp (MainMenu)','','PAI','',null,0,null,now(),now(),1,1,1);

-- Entrada de menu para o menu raíz deste CrosierApp com apontamento para o Dashboard deste CrosierApp TAMBÉM! (É EXIBIDO COMO PRIMEIRO ITEM DO MENU DESTE CROSIERAPP)
INSERT INTO cfg_entmenu(uuid,label,icon,tipo,program_uuid,pai_uuid,ordem,css_style,inserted,updated,estabelecimento_id,user_inserted_id,user_updated_id)
VALUES ('6542ace8-132a-46e6-91fb-900313d20275','Dashboard','fas fa-columns','ENT','b5bab428-2265-43df-a407-daecfed34e05','f5dd11fb-dfde-40af-91c9-6583192e8d83',0,null,now(),now(),1,1,1);



COMMIT;
