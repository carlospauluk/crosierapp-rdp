START TRANSACTION;

SET FOREIGN_KEY_CHECKS = 0;


# Menu Raíz
DELETE
FROM cfg_entmenu
WHERE uuid = 'f5dd11fb-dfde-40af-91c9-6583192e8d83';
INSERT INTO cfg_entmenu(uuid, label, icon, tipo, app_uuid, pai_uuid, ordem, css_style, inserted, updated,
                        estabelecimento_id, user_inserted_id, user_updated_id, url)
VALUES ('f5dd11fb-dfde-40af-91c9-6583192e8d83', 'crosierapp-rdp (Menu Raíz)', '', 'PAI',
        'b10ef8c0-841f-4688-9ee2-30e39639be8a', null, 1, null, now(), now(), 1, 1, 1,
        null);



DELETE
FROM cfg_entmenu
WHERE uuid = '2f92f2b0-96dc-48ac-a45e-9ca5209d5b03';
INSERT INTO cfg_entmenu(uuid, label, icon, tipo, app_uuid, pai_uuid, ordem, css_style, inserted, updated,
                        estabelecimento_id, user_inserted_id, user_updated_id, url)
VALUES ('2f92f2b0-96dc-48ac-a45e-9ca5209d5b03', 'Dashboard', 'fas fa-file-pdf', 'ENT',
        'b10ef8c0-841f-4688-9ee2-30e39639be8a', 'f5dd11fb-dfde-40af-91c9-6583192e8d83', 1, null, now(), now(), 1, 1, 1,
        '/utils/push/list/?filter%5Barquivo%5D=&filter%5BtipoArquivo%5D=pdf');

DELETE
FROM cfg_entmenu
WHERE uuid = '22d4c990-981a-4fb0-a1cc-5d7c7797e413';
INSERT INTO cfg_entmenu(uuid, label, icon, tipo, app_uuid, pai_uuid, ordem, css_style, inserted, updated,
                        estabelecimento_id, user_inserted_id, user_updated_id, url)
VALUES ('22d4c990-981a-4fb0-a1cc-5d7c7797e413', 'Relatórios (PDFs)', 'fas fa-file-pdf', 'ENT',
        'b10ef8c0-841f-4688-9ee2-30e39639be8a', 'f5dd11fb-dfde-40af-91c9-6583192e8d83', 1, null, now(), now(), 1, 1, 1,
        '/utils/push/list/?filter%5Barquivo%5D=&filter%5BtipoArquivo%5D=pdf');


DELETE
FROM cfg_entmenu
WHERE uuid = '55e290c1-ea1e-4a69-832b-045f9243b603';
INSERT INTO cfg_entmenu(uuid, label, icon, tipo, app_uuid, pai_uuid, ordem, css_style, inserted, updated,
                        estabelecimento_id, user_inserted_id, user_updated_id, url)
VALUES ('55e290c1-ea1e-4a69-832b-045f9243b603', 'Imagens', 'fas fa-file-image', 'ENT',
        'b10ef8c0-841f-4688-9ee2-30e39639be8a', 'f5dd11fb-dfde-40af-91c9-6583192e8d83', 1, null, now(), now(), 1, 1, 1,
        '/utils/push/list/?filter%5Barquivo%5D=&filter%5BtipoArquivo%5D=image');


DELETE
FROM cfg_entmenu
WHERE uuid = 'a1552faa-632b-4118-9b38-05ce63e899c7';
INSERT INTO cfg_entmenu(uuid, label, icon, tipo, app_uuid, pai_uuid, ordem, css_style, inserted, updated,
                        estabelecimento_id, user_inserted_id, user_updated_id, url)
VALUES ('a1552faa-632b-4118-9b38-05ce63e899c7', 'Clientes', 'fas fa-users', 'ENT',
        'b10ef8c0-841f-4688-9ee2-30e39639be8a', 'f5dd11fb-dfde-40af-91c9-6583192e8d83', 1, null, now(), now(), 1, 1, 1,
        '/relCliente01/list/');


DELETE
FROM cfg_entmenu
WHERE uuid = 'd2da155f-427e-47cd-8466-5cbc5ba6af50';
INSERT INTO cfg_entmenu(uuid, label, icon, tipo, app_uuid, pai_uuid, ordem, css_style, inserted, updated,
                        estabelecimento_id, user_inserted_id, user_updated_id, url)
VALUES ('d2da155f-427e-47cd-8466-5cbc5ba6af50', 'PVs', 'fas fa-shopping-cart', 'ENT',
        'b10ef8c0-841f-4688-9ee2-30e39639be8a', 'f5dd11fb-dfde-40af-91c9-6583192e8d83', 1, null, now(), now(), 1, 1, 1,
        '/ven/pv/list/');


# Para o crosierapp-vendest
DELETE
FROM cfg_entmenu
WHERE uuid = '9985913b-3081-4d50-a23b-da6b421120b1';
INSERT INTO cfg_entmenu(uuid, label, icon, tipo, app_uuid, pai_uuid, ordem, css_style, inserted, updated,
                        estabelecimento_id, user_inserted_id, user_updated_id, url)
VALUES ('9985913b-3081-4d50-a23b-da6b421120b1', 'Produtos', 'fas fa-boxes', 'ENT',
        '440e429c-b711-4411-87ed-d95f7281cd43', 'f5dd11fb-dfde-40af-91c9-6583192e8d83', 1, null, now(), now(), 1, 1, 1,
        '/est/produto/list/');



COMMIT;
