START TRANSACTION;

SET FOREIGN_KEY_CHECKS = 0;

DELETE
FROM cfg_app
WHERE uuid = 'b10ef8c0-841f-4688-9ee2-30e39639be8a';

INSERT INTO cfg_app(uuid, nome, obs, inserted, updated, estabelecimento_id, user_inserted_id,
                    user_updated_id)
VALUES ('b10ef8c0-841f-4688-9ee2-30e39639be8a', 'crosierapp-rdp', 'crosierapp-rdp',
        now(), now(), 1, 1, 1);



COMMIT;
