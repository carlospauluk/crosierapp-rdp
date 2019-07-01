START TRANSACTION;

-- app                   1a6f4dce-b967-49ac-9fd5-892e22b90212
-- program               69a3bd02-c887-4319-bae0-3f1cd20c5608
-- entMenu (CrosierCore) 26176d19-7419-4579-968d-d3011b0ae59c
-- entMenu (Raíz do App) 92f4e43c-cdd9-45fd-9077-cba05cbcfbf3
-- entMenu (Dashboard)   a707a3ee-f957-480a-b931-d3eb770336ca

SET FOREIGN_KEY_CHECKS=0;

DELETE FROM cfg_app WHERE uuid = '1a6f4dce-b967-49ac-9fd5-892e22b90212';
DELETE FROM cfg_program WHERE uuid = '69a3bd02-c887-4319-bae0-3f1cd20c5608';
DELETE FROM cfg_entmenu WHERE uuid = '26176d19-7419-4579-968d-d3011b0ae59c';
DELETE FROM cfg_entmenu WHERE uuid = '92f4e43c-cdd9-45fd-9077-cba05cbcfbf3';
DELETE FROM cfg_entmenu WHERE uuid = 'a707a3ee-f957-480a-b931-d3eb770336ca';


INSERT INTO cfg_app(uuid,nome,obs,default_entmenu_uuid,inserted,updated,estabelecimento_id,user_inserted_id,user_updated_id) 
VALUES ('1a6f4dce-b967-49ac-9fd5-892e22b90212','Hello World App','CrosierApp de exemplos','92f4e43c-cdd9-45fd-9077-cba05cbcfbf3',now(),now(),1,1,1);

INSERT INTO cfg_program(uuid, descricao, url, app_uuid, entmenu_uuid ,inserted, updated, estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('69a3bd02-c887-4319-bae0-3f1cd20c5608','Dashboard - Hello World App', '/', '1a6f4dce-b967-49ac-9fd5-892e22b90212', null, now(), now(), 1, 1, 1);



-- Entrada de menu para o MainMenu do Crosier com apontamento para o Dashboard deste CrosierApp (É EXIBIDO NO MENU DO CROSIER-CORE)
INSERT INTO cfg_entmenu(uuid,label,icon,tipo,program_uuid,pai_uuid,ordem,css_style,inserted,updated,estabelecimento_id,user_inserted_id,user_updated_id)
VALUES ('26176d19-7419-4579-968d-d3011b0ae59c','Dashboard - Hello World App','fas fa-columns','CROSIERCORE_APPENT','69a3bd02-c887-4319-bae0-3f1cd20c5608',null,0,null,now(),now(),1,1,1);

-- Entrada de menu raíz para este CrosierApp (NÃO É EXIBIDO)
INSERT INTO cfg_entmenu(uuid,label,icon,tipo,program_uuid,pai_uuid,ordem,css_style,inserted,updated,estabelecimento_id,user_inserted_id,user_updated_id)
VALUES ('92f4e43c-cdd9-45fd-9077-cba05cbcfbf3','Hello World App - MainMenu','','PAI','',null,0,null,now(),now(),1,1,1);

-- Entrada de menu para o menu raíz deste CrosierApp com apontamento para o Dashboard deste CrosierApp TAMBÉM! (É EXIBIDO COMO PRIMEIRO ITEM DO MENU DESTE CROSIERAPP)
INSERT INTO cfg_entmenu(uuid,label,icon,tipo,program_uuid,pai_uuid,ordem,css_style,inserted,updated,estabelecimento_id,user_inserted_id,user_updated_id)
VALUES ('a707a3ee-f957-480a-b931-d3eb770336ca','Dashboard','fas fa-columns','ENT','69a3bd02-c887-4319-bae0-3f1cd20c5608','92f4e43c-cdd9-45fd-9077-cba05cbcfbf3',0,null,now(),now(),1,1,1);



COMMIT;
