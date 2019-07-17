SET FOREIGN_KEY_CHECKS = 0;


DROP TABLE IF EXISTS `rdp_rel_vendas01`;
CREATE TABLE `rdp_rel_vendas01`
(
    `id`                 BIGINT(20)     NOT NULL AUTO_INCREMENT,


    `ano`                CHAR(4)        NOT NULL,
    `mes`                CHAR(2)        NOT NULL,
    `cod_fornec`         BIGINT(20)     NOT NULL,
    `nome_fornec`        VARCHAR(200)   NOT NULL,
    `cod_prod`           BIGINT(20)     NOT NULL,
    `desc_prod`          VARCHAR(200)   NOT NULL,
    `total_preco_venda`  DECIMAL(15, 2) NOT NULL,
    `total_preco_custo`  DECIMAL(15, 2) NOT NULL,
    `rentabilidade`      DECIMAL(15, 2) NOT NULL,
    `cod_vendedor`       BIGINT(20)     NOT NULL,
    `nome_vendedor`      VARCHAR(200)   NOT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20)     NOT NULL,
    `inserted`           DATETIME       NOT NULL,
    `updated`            DATETIME       NOT NULL,
    `user_inserted_id`   BIGINT(20)     NOT NULL,
    `user_updated_id`    BIGINT(20)     NOT NULL,
    KEY `k_rdp_rel_vendas01_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_rel_vendas01_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_rel_vendas01_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_rel_vendas01_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_rel_vendas01_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_rel_vendas01_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1
  pack_keys = 0;
