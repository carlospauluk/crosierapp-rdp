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


# CODIGO|DESCRICAO|CUSTO_MEDIO|PRECO_VENDA|FILIAL|QTDE_MINIMA|QTDE_MAXIMA|QTDE_ATUAL|DATA_ULT_SAIDA|COD_FORNEC|FORNECEDOR
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
    `deficit`            DECIMAL(15, 2) AS (qtde_minima - qtde_atual),
    `dt_ult_saida`       DATETIME       NULL,
    `cod_fornec`         BIGINT(20)     NOT NULL,
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
                                       `cod_fornec`,
                                       `nome_fornec`),

    KEY rdp_rel_estoque01_cod_prod (`cod_prod`),
    KEY rdp_rel_estoque01_desc_prod (`desc_prod`),
    KEY rdp_rel_estoque01_desc_filial (`desc_filial`),
    KEY rdp_rel_estoque01_cod_fornec (`cod_fornec`),
    KEY rdp_rel_estoque01_nome_fornec (`nome_fornec`),

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



# PV_COMPRA
# ITEM
# QTDE
# EMISSAO
# ANO
# MES
# COD_FORNEC
# NOME_FORNEC
# COD_PROD
# DESC_PROD
# TOTAL_PRECO_VENDA
# TOTAL_PRECO_CUSTO
# RENTABILIDADE ITEM
# COD_VENDEDOR
# NOME_VENDEDOR
# LOJA
# TOTAL_CUSTO_PV
# TOTAL_VENDA_PV
# RENTABILIDADE PV
# CLIENTE PV
# GRUPO

DROP TABLE IF EXISTS `rdp_rel_compras01`;
CREATE TABLE `rdp_rel_compras01`
(
    `id`                 BIGINT(20)     NOT NULL AUTO_INCREMENT,
    `pv_compra`          BIGINT(20)     NOT NULL,
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
    `dt_prev_entrega`    DATETIME       NOT NULL,


    UNIQUE KEY `uk_rdp_rel_compras01` (`pv_compra`,
                                       `num_item`,
                                       `qtde`,
                                       `dt_emissao`,
                                       `cod_fornec`,
                                       `cod_prod`,
                                       `total_preco_venda`,
                                       `total_preco_custo`,
                                       `cod_vendedor`,
                                       `loja`,
                                       `total_custo_pv`,
                                       `total_venda_pv`,
                                       `cliente_pv`,
                                       `grupo`,
                                       `dt_prev_entrega`),

    KEY rdp_rel_compras01_dt_emissao (`dt_emissao`),
    KEY rdp_rel_compras01_cod_fornec (`cod_fornec`),
    KEY rdp_rel_compras01_nome_fornec (`nome_fornec`),
    KEY rdp_rel_compras01_cod_prod (`cod_prod`),
    KEY rdp_rel_compras01_desc_prod (`desc_prod`),
    KEY rdp_rel_compras01_loja (`loja`),
    KEY rdp_rel_compras01_cliente_pv (`cliente_pv`),
    KEY rdp_rel_compras01_grupo (`grupo`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20)     NOT NULL,
    `inserted`           DATETIME       NOT NULL,
    `updated`            DATETIME       NOT NULL,
    `user_inserted_id`   BIGINT(20)     NOT NULL,
    `user_updated_id`    BIGINT(20)     NOT NULL,
    KEY `k_rdp_rel_compras01_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_rel_compras01_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_rel_compras01_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_rel_compras01_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_rel_compras01_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_rel_compras01_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_rel_cliente01`;
CREATE TABLE `rdp_rel_cliente01`
(
    `id`                 BIGINT(20)   NOT NULL AUTO_INCREMENT,
    `codigo`             BIGINT(20)   NOT NULL,
    `nome`               VARCHAR(250) NOT NULL,
    `documento`          VARCHAR(50)  NOT NULL, #CPF
    `rg`                 VARCHAR(50),

    `endereco`           VARCHAR(250),          #ENDER, NUMERO
    `bairro`             VARCHAR(50),
    `complemento`        VARCHAR(60),
    `cidade`             VARCHAR(50),
    `estado`             VARCHAR(50),
    `cep`                VARCHAR(15),
    `fone`               VARCHAR(50),           # DDD FONE

    `localizador`        VARCHAR(10),
    `cond_pagto`         VARCHAR(10),
    `desbloqueio_tmp`    VARCHAR(10)  NOT NULL,
    `ac_compras`         DECIMAL(15, 2),        #COMPRAS
    `flag_lib_preco`     CHAR(1)      NOT NULL,
    `sugere_consulta`    CHAR(1)      NOT NULL,
    `margem_especial`    DECIMAL(15, 2),        # PERC_MARGEM
    `limite_compras`     DECIMAL(15, 2),
    `cliente_bloqueado`  CHAR(1)      NOT NULL, # FLAG_BLOQUEIO

    UNIQUE KEY `uk_rdp_rel_cliente01` (`codigo`, `documento`, `nome`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20)   NOT NULL,
    `inserted`           DATETIME     NOT NULL,
    `updated`            DATETIME     NOT NULL,
    `user_inserted_id`   BIGINT(20)   NOT NULL,
    `user_updated_id`    BIGINT(20)   NOT NULL,
    KEY `k_rdp_rel_cliente01_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_rel_cliente01_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_rel_cliente01_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_rel_cliente01_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_rel_cliente01_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_rel_cliente01_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = utf8;



ALTER TABLE rdp_rel_cliente01
    ADD tipo varchar(50),
    ADD `trabalho_endereco` VARCHAR(250),
    ADD `trabalho_cidade` VARCHAR(50),
    ADD `trabalho_estado` VARCHAR(50),
    ADD `trabalho_cargo` VARCHAR(200),
    ADD `trabalho_cep` VARCHAR(15),
    ADD `trabalho_fax` VARCHAR(50), # DDD_FAX+FONE_FAZ
    ADD dt_pri datetime,
    ADD dt_ult_compra datetime,
    ADD dt_pagto datetime,
    ADD vlr_maior_compra DECIMAL(15, 2),
    ADD vlr_ult_compra DECIMAL(15, 2),
    ADD conjuge_nome VARCHAR(250),
    ADD conjuge_dt_nasc DATETIME,
    ADD conjuge_rg VARCHAR(50),
    ADD conjuge_trabalho VARCHAR(200),
    ADD conjuge_trabalho_endereco VARCHAR(250),
    ADD conjuge_trabalho_cidade VARCHAR(50),
    ADD conjuge_trabalho_cep VARCHAR(15),
    ADD conjuge_trabalho_fone VARCHAR(50), # DDD_TRA_CON + FONE_TRA_CON
    ADD conjuge_trabalho_adm datetime, # ADM_TRA_CON: Data de admissão
    ADD obs1 VARCHAR(150),
    ADD obs2 VARCHAR(150),
    ADD obs3 VARCHAR(150),
    ADD obs4 VARCHAR(150),
    ADD obs5 VARCHAR(150),
    ADD obs6 VARCHAR(150),
    ADD obs01 VARCHAR(150),
    ADD obs02 VARCHAR(150),
    ADD obs03 VARCHAR(150),
    ADD obs04 VARCHAR(150),
    ADD obs05 VARCHAR(150),
    ADD obs06 VARCHAR(150),
    ADD obs07 VARCHAR(150),
    ADD obs08 VARCHAR(150),
    ADD obs09 VARCHAR(150),
    ADD obs10 VARCHAR(150),
    ADD obs11 VARCHAR(150),
    ADD obs12 VARCHAR(150),
    ADD obs13 VARCHAR(150),
    ADD obs14 VARCHAR(150),
    ADD obs15 VARCHAR(150),
    ADD obs16 VARCHAR(150),
    ADD obs17 VARCHAR(150),
    ADD obs18 VARCHAR(150),
    ADD obs19 VARCHAR(150),
    ADD obs20 VARCHAR(150),
    ADD dias_atraso int,
    ADD `suspenso` CHAR(1) NOT NULL,
    ADD `cobranca_endereco` VARCHAR(250),
    ADD `cobranca_bairro` VARCHAR(50),
    ADD `cobranca_cidade` VARCHAR(50),
    ADD `cobranca_estado` VARCHAR(50),
    ADD `cobranca_cep` VARCHAR(15),
    ADD dt_nas_prop DATETIME,
    ADD dt_nas_funda DATETIME,
    ADD ramo VARCHAR(60),
    ADD bens1 VARCHAR(100),
    ADD bens2 VARCHAR(100),
    ADD scania int,
    ADD volvo int,
    ADD mb int,
    ADD outros int,
    ADD scania01 int,
    ADD volvo01 int,
    ADD mb01 int,
    ADD outros01 int,
    ADD flag_casa char(1),
    ADD ref_banco VARCHAR(50),
    ADD ref_banco01 VARCHAR(50),
    ADD ref_come VARCHAR(50),
    ADD ref_come01 VARCHAR(50),
    ADD vendedor int,
    ADD pai VARCHAR(250),
    ADD mae VARCHAR(250),
    ADD conhecido_pes VARCHAR(20),
    ADD conhecido_fone VARCHAR(20),
    ADD email VARCHAR(40),
    ADD integ_wle char(1),
    ADD rg2 VARCHAR(20),
    ADD cod_munic int,
    ADD flag_comissao char(1),
    ADD dias_trv_fat int,
    ADD tipo_cliente char(1),
    ADD tipo_fornec char(1),
    ADD flag_scp char(1),
    ADD flag_chequedev char(1),
    ADD cod_consul int,
    ADD frotista char(1),
    ADD classificacao char(1);


alter table rdp_rel_cliente01 add complemento varchar(50) after endereco;


alter table rdp_rel_cliente01 change conhecido_pes conhecido_pes varchar(50);


    




