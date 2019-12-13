SET FOREIGN_KEY_CHECKS = 0;



DROP TABLE IF EXISTS `rdp_web_venda_cabec`; # nome antigo
DROP TABLE IF EXISTS `rdp_ven_pv`;
CREATE TABLE `rdp_ven_pv`
(
    `id`                 BIGINT(20)   NOT NULL AUTO_INCREMENT,

    `uuid`               char(36)     NOT NULL,
    `pv_ekt`             INT(11)        DEFAULT NULL,
    `dt_emissao`         DATETIME     NOT NULL,
    `status`             VARCHAR(50)  NOT NULL,
    `filial`             VARCHAR(200) NOT NULL,

    `vendedor`           VARCHAR(200)   DEFAULT NULL COMMENT 'codigo - nome',

    `cliente_id`         BIGINT(20)   NOT NULL,
    `cliente_cod`        BIGINT(20)   NOT NULL,
    `cliente_nome`       VARCHAR(250) NOT NULL,
    `cliente_documento`  VARCHAR(50)  NOT NULL,

    `deposito`           VARCHAR(50)    DEFAULT NULL,
    `localizador`        VARCHAR(50)    DEFAULT NULL,
    `cond_pagto`         VARCHAR(50)    DEFAULT NULL,

    `venctos`            VARCHAR(3000)  DEFAULT NULL COMMENT 'JSON',

    `obs`                varchar(3000)  DEFAULT NULL,
    `subtotal`           DECIMAL(15, 2) DEFAULT NULL,
    `descontos`          DECIMAL(15, 2) DEFAULT NULL,
    `total`              DECIMAL(15, 2) DEFAULT NULL,

    UNIQUE KEY `uk_rdp_ven_pv_uuid` (`uuid`),
    UNIQUE KEY `uk_rdp_ven_pv_pv_ekt` (`pv_ekt`),

    KEY `k_rdp_pv_cliente` (`cliente_id`),
    CONSTRAINT `fk_rdp_pv_cliente` FOREIGN KEY (`cliente_id`) references `rdp_rel_cliente01` (`id`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),

    `estabelecimento_id` BIGINT(20)   NOT NULL,
    `inserted`           DATETIME     NOT NULL,
    `updated`            DATETIME     NOT NULL,
    `user_inserted_id`   BIGINT(20)   NOT NULL,
    `user_updated_id`    BIGINT(20)   NOT NULL,

    KEY `k_rdp_ven_pv_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_ven_pv_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_ven_pv_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_ven_pv_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_ven_pv_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_ven_pv_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)

) ENGINE = INNODB
  DEFAULT charset = utf8;



DROP TABLE IF EXISTS `rdp_web_venda_corpo`; # antigo
DROP TABLE IF EXISTS `rdp_ven_pv_item`;
CREATE TABLE `rdp_ven_pv_item`
(
    `id`                 BIGINT(20)     NOT NULL AUTO_INCREMENT,

    `ven_pv_id`          BIGINT(20)     DEFAULT NULL,
    `produto_cod`        VARCHAR(50)    NOT NULL,
    `produto_desc`       VARCHAR(250)   NOT NULL,
    `cod_fornec`         BIGINT(20)     NOT NULL,
    `nome_fornec`        VARCHAR(200)   NOT NULL,
    `preco_custo`        DECIMAL(15, 2) NOT NULL,
    `preco_venda`        DECIMAL(15, 2) NOT NULL,
    `preco_orc`          DECIMAL(15, 2) NOT NULL,
    `qtde`               DECIMAL(15, 2) DEFAULT NULL,
    `desconto`           DECIMAL(15, 2) NOT NULL,
    `total`              DECIMAL(15, 2) NOT NULL,
    `obs`                varchar(3000)  DEFAULT NULL,

    KEY `k_rdp_ven_pv_item_ven_pv` (`ven_pv_id`),
    CONSTRAINT `fk_rdp_ven_pv_item_ven_pv` FOREIGN KEY (`ven_pv_id`) references `rdp_ven_pv` (`id`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20)     NOT NULL,
    `inserted`           DATETIME       NOT NULL,
    `updated`            DATETIME       NOT NULL,
    `user_inserted_id`   BIGINT(20)     NOT NULL,
    `user_updated_id`    BIGINT(20)     NOT NULL,
    KEY `k_rdp_ven_pv_item_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_ven_pv_item_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_ven_pv_item_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_ven_pv_item_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_ven_pv_item_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_ven_pv_item_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = utf8;


ALTER TABLE rdp_ven_pv_item ADD produto_id bigint(20) not null;

ALTER TABLE rdp_ven_pv_item
    ADD KEY `k_rdp_ven_pv_item_produto` (`produto_id`),
    ADD CONSTRAINT `fk_rdp_ven_pv_item_produto` FOREIGN KEY (`produto_id`) references `est_produto` (`id`);

ALTER TABLE rdp_ven_pv_item
    DROP produto_cod,
    DROP produto_desc;