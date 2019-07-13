DROP TABLE IF EXISTS `rdp_utl_relatoriospush`;

CREATE TABLE `rdp_utl_relatoriospush`
(

    `dt_envio`             datetime                             NOT NULL,
    `descricao`            varchar(400) COLLATE utf8_swedish_ci NULL,
    `user_destinatario_id` bigint(20)                           NOT NULL,
    `dt_aberto_em`         datetime                             NULL,
    `arquivo`              varchar(300) COLLATE utf8_swedish_ci NOT NULL,
    `tipo_arquivo`         varchar(100) COLLATE utf8_swedish_ci NULL,

    `id`                   bigint(20) AUTO_INCREMENT            NOT NULL,
    `estabelecimento_id`   bigint(20)                           NOT NULL,
    `inserted`             datetime                             NOT NULL,
    `updated`              datetime                             NOT NULL,
    `user_inserted_id`     bigint(20)                           NOT NULL,
    `user_updated_id`      bigint(20)                           NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `UK_rdp_utl_relatoriospush` (`arquivo`),
    KEY `K_rdp_utl_relatoriospush_estabelecimento` (`estabelecimento_id`),
    KEY `K_rdp_utl_relatoriospush_user_inserted` (`user_inserted_id`),
    KEY `K_rdp_utl_relatoriospush_user_updated` (`user_updated_id`),
    CONSTRAINT `FK_rdp_utl_relatoriospush_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`),
    CONSTRAINT `FK_rdp_utl_relatoriospush_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
    CONSTRAINT `FK_rdp_utl_relatoriospush_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_swedish_ci;


