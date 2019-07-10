START TRANSACTION;

SET FOREIGN_KEY_CHECKS=0;

DELETE FROM cfg_program WHERE uuid = '21cf2617-a859-4cc8-aaf7-c3a612e429e5';
DELETE FROM cfg_entmenu WHERE uuid = '22d4c990-981a-4fb0-a1cc-5d7c7797e413';

INSERT INTO cfg_program(uuid, descricao, url, app_uuid, entmenu_uuid ,inserted, updated, estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('21cf2617-a859-4cc8-aaf7-c3a612e429e5','UTILS PUSH LIST PDF', '/utils/push/list/?filter%5Barquivo%5D=&filter%5BtipoArquivo%5D=pdf', 'b10ef8c0-841f-4688-9ee2-30e39639be8a', null, now(), now(), 1, 1, 1);

INSERT INTO cfg_entmenu(uuid, label, icon, tipo, program_uuid, pai_uuid, ordem, css_style, inserted, updated, estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('22d4c990-981a-4fb0-a1cc-5d7c7797e413', 'Push (PDFs)', 'fas fa-file-pdf', 'ENT', '21cf2617-a859-4cc8-aaf7-c3a612e429e5', 'f5dd11fb-dfde-40af-91c9-6583192e8d83', 1 , null, now(), now(), 1, 1, 1);




DELETE FROM cfg_program WHERE uuid = '5a70c308-6944-4f5f-a44d-77bad38bdd04';
DELETE FROM cfg_entmenu WHERE uuid = '55e290c1-ea1e-4a69-832b-045f9243b603';

INSERT INTO cfg_program(uuid, descricao, url, app_uuid, entmenu_uuid ,inserted, updated, estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('5a70c308-6944-4f5f-a44d-77bad38bdd04','UTILS PUSH LIST IMAGENS', '/utils/push/list/?filter%5Barquivo%5D=&filter%5BtipoArquivo%5D=image', 'b10ef8c0-841f-4688-9ee2-30e39639be8a', null, now(), now(), 1, 1, 1);

INSERT INTO cfg_entmenu(uuid, label, icon, tipo, program_uuid, pai_uuid, ordem, css_style, inserted, updated, estabelecimento_id, user_inserted_id, user_updated_id)
VALUES ('55e290c1-ea1e-4a69-832b-045f9243b603', 'Push (Imagens)', 'fas fa-file-image', 'ENT', '5a70c308-6944-4f5f-a44d-77bad38bdd04', 'f5dd11fb-dfde-40af-91c9-6583192e8d83', 1 , null, now(), now(), 1, 1, 1);




COMMIT;
