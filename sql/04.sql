SET FOREIGN_KEY_CHECKS = 0;


DROP TABLE IF EXISTS `rdp_rel_vendas01`;
CREATE TABLE `rdp_rel_vendas01`
(
    `id`                 BIGINT(20)     NOT NULL AUTO_INCREMENT,

    `prevenda`           BIGINT(20)     NOT NULL,
    `num_item`           INT(11)        NOT NULL,
    `qtde`               INT(11)        NOT NULL,
    `dt_emissao`         DATETIME       NOT NULL,
    `ano`                CHAR(4)        NOT NULL,
    `mes`                CHAR(2)        NOT NULL,
    `cod_fornec`         BIGINT(20)     NOT NULL,
    `nome_fornec`        VARCHAR(200)   NOT NULL,
    `cod_prod`           VARCHAR(50)    NOT NULL,
    `desc_prod`          VARCHAR(200)   NOT NULL,
    `total_preco_venda`  DECIMAL(15, 2) NOT NULL,
    `total_preco_custo`  DECIMAL(15, 2) NOT NULL,
    `rentabilidade`      DECIMAL(15, 2) NOT NULL,
    `cod_vendedor`       BIGINT(20)     NOT NULL,
    `nome_vendedor`      VARCHAR(200)   NOT NULL,
    `loja`               VARCHAR(200)   NOT NULL,
    `total_custo_pv`     DECIMAL(15, 2) NOT NULL,
    `total_venda_pv`     DECIMAL(15, 2) NOT NULL,
    `rentabilidade_pv`   DECIMAL(15, 2) NOT NULL,
    `cliente_pv`         VARCHAR(200)   NOT NULL,
    `grupo`              VARCHAR(200)   NOT NULL,

    UNIQUE KEY `uk_rdp_rel_vendas01` (`prevenda`,
                                      `num_item`,
                                      `qtde`,
                                      `dt_emissao`,
                                      `ano`,
                                      `mes`,
                                      `cod_fornec`,
                                      `cod_prod`,
                                      `total_preco_venda`,
                                      `total_preco_custo`,
                                      `cod_vendedor`,
                                      `loja`,
                                      `total_venda_pv`,
                                      `cliente_pv`,
                                      `grupo`),

    KEY rdp_rel_vendas01_dt_emissao (`dt_emissao`),
    KEY rdp_rel_vendas01_cod_fornec (`cod_fornec`),
    KEY rdp_rel_vendas01_nome_fornec (`nome_fornec`),
    KEY rdp_rel_vendas01_cod_prod (`cod_prod`),
    KEY rdp_rel_vendas01_desc_prod (`desc_prod`),
    KEY rdp_rel_vendas01_loja (`loja`),
    KEY rdp_rel_vendas01_cliente_pv (`cliente_pv`),
    KEY rdp_rel_vendas01_grupo (`grupo`),
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
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_rel_ctspagrec01`;
CREATE TABLE `rdp_rel_ctspagrec01`
(
    `id`                 BIGINT(20)     NOT NULL AUTO_INCREMENT,

    `lancto`             BIGINT(20)     NOT NULL,
    `docto`              VARCHAR(50)    NOT NULL,
    `dt_movto`           DATE           NOT NULL,
    `dt_vencto`          DATE           NOT NULL,
    `dt_pagto`           DATE           NULL,
    `cod_cliente`        BIGINT(20)     NOT NULL,
    `nome_cli_for`       VARCHAR(200)   NOT NULL,
    `localizador`        BIGINT(20)     NULL,
    `localizador_desc`   VARCHAR(200)   NULL,
    `filial`             BIGINT(20)     NOT NULL,
    `desc_filial`        VARCHAR(200)   NOT NULL,
    `valor_titulo`       DECIMAL(15, 2) NOT NULL,
    `valor_baixa`        DECIMAL(15, 2) NULL,
    `situacao`           VARCHAR(1)     NOT NULL,
    `tipo_pag_rec`       VARCHAR(1)     NOT NULL,
    `numero_nf`          BIGINT(20)     NULL,
    `dt_emissao_nf`      DATE           NULL,

    UNIQUE KEY `UK_rdp_rel_ctspagrec01` (`lancto`, `docto`, `dt_movto`, `dt_vencto`, `cod_cliente`, `nome_cli_for`,
                                         `filial`, `desc_filial`, `valor_titulo`, `situacao`, `tipo_pag_rec`),

    KEY K_rdp_rel_ctspagrec01_dt_vencto (`dt_vencto`),
    KEY K_rdp_rel_ctspagrec01_localizador (`localizador`),
    KEY K_rdp_rel_ctspagrec01_filial (`filial`),
    KEY K_rdp_rel_ctspagrec01_tipo_pag_rec (`tipo_pag_rec`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20)     NOT NULL,
    `inserted`           DATETIME       NOT NULL,
    `updated`            DATETIME       NOT NULL,
    `user_inserted_id`   BIGINT(20)     NOT NULL,
    `user_updated_id`    BIGINT(20)     NOT NULL,
    KEY `k_rdp_rel_ctspagrec01_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_rel_ctspagrec01_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_rel_ctspagrec01_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_rel_ctspagrec01_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_rel_ctspagrec01_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_rel_ctspagrec01_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;

# ALTER TABLE rdp_rel_ctspagrec01 ADD UNIQUE KEY `UK_rdp_rel_ctspagrec01` (`lancto`,`docto`,`dt_movto`,`dt_vencto`,`cod_cliente`,`nome_cli_for`,`filial`,`valor_titulo`,`situacao`,`tipo_pag_rec`);


DROP TABLE IF EXISTS `rdp_rel_compfor01`;
CREATE TABLE `rdp_rel_compfor01`
(
    `id`                 BIGINT(20)     NOT NULL AUTO_INCREMENT,

    `lancto`             BIGINT(20)     NOT NULL,
    `docto`              VARCHAR(50)    NOT NULL,
    `dt_movto`           DATE           NOT NULL,
    `cod_prod`           VARCHAR(50)    NOT NULL,
    `desc_prod`          VARCHAR(200)   NOT NULL,
    `qtde`               DECIMAL(15, 2) NOT NULL,
    `preco_custo`        DECIMAL(15, 2) NULL,
    `total`              DECIMAL(15, 2) NULL,
    `cod_fornec`         BIGINT(20)     NOT NULL,
    `nome_fornec`        VARCHAR(200)   NOT NULL,
    `obs`                VARCHAR(2000)  NULL,

    UNIQUE KEY `UK_rdp_rel_compfor01` (`lancto`, `docto`, `dt_movto`, `cod_prod`, `desc_prod`, `qtde`,
                                       `cod_fornec`, `nome_fornec`),

    KEY K_rdp_rel_compfor01_dt_movto (`dt_movto`),
    KEY K_rdp_rel_compfor01_cod_fornec (`cod_fornec`),
    KEY K_rdp_rel_compfor01_cod_prod (`cod_prod`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20)     NOT NULL,
    `inserted`           DATETIME       NOT NULL,
    `updated`            DATETIME       NOT NULL,
    `user_inserted_id`   BIGINT(20)     NOT NULL,
    `user_updated_id`    BIGINT(20)     NOT NULL,
    KEY `k_rdp_rel_compfor01_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_rel_compfor01_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_rel_compfor01_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_rel_compfor01_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_rel_compfor01_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_rel_compfor01_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;


# CODIGO|DESCRICAO|CUSTO_MEDIO|PRECO_VENDA|FILIAL|QTDE_MINIMA|QTDE_MAXIMA|QTDE_ATUAL|DATA_ULT_SAIDA|FORNECEDOR
DROP TABLE IF EXISTS `rdp_rel_estoque01`;
CREATE TABLE `rdp_rel_estoque01`
(
    `id`                 BIGINT(20)     NOT NULL AUTO_INCREMENT,

    `cod_prod`           VARCHAR(50)    NOT NULL,
    `desc_prod`          VARCHAR(200)   NOT NULL,
    `custo_medio`        DECIMAL(15, 2) NOT NULL,
    `preco_venda`        DECIMAL(15, 2) NOT NULL,
    `desc_filial`        VARCHAR(200)   NOT NULL,
    `qtde_minima`        DECIMAL(15, 2) NOT NULL,
    `qtde_maxima`        DECIMAL(15, 2) NOT NULL,
    `qtde_atual`         DECIMAL(15, 2) NOT NULL,
    `dt_ult_saida`       DATETIME       NULL,
    `nome_fornec`        VARCHAR(200)   NOT NULL,


    UNIQUE KEY `uk_rdp_rel_estoque01` (`cod_prod`,
                                       `desc_prod`,
                                       `custo_medio`,
                                       `preco_venda`,
                                       `desc_filial`,
                                       `qtde_minima`,
                                       `qtde_maxima`,
                                       `qtde_atual`,
                                       `dt_ult_saida`,
                                       `nome_fornec`),

    KEY rdp_rel_estoque01_cod_prod (`cod_prod`),
    KEY rdp_rel_estoque01_desc_prod (`desc_prod`),
    KEY rdp_rel_estoque01_desc_filial (`desc_filial`),
    KEY rdp_rel_estoque01_fornecedor (`nome_fornec`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20)     NOT NULL,
    `inserted`           DATETIME       NOT NULL,
    `updated`            DATETIME       NOT NULL,
    `user_inserted_id`   BIGINT(20)     NOT NULL,
    `user_updated_id`    BIGINT(20)     NOT NULL,
    KEY `k_rdp_rel_estoque01_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_rel_estoque01_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_rel_estoque01_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_rel_estoque01_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_rel_estoque01_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_rel_estoque01_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;