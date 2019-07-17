SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `rdp_cts_d001_controle`;
CREATE TABLE `rdp_cts_d001_controle`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `campo1`             INT(11)        DEFAULT NULL,
    `campo2`             INT(11)        DEFAULT NULL,
    `campo3`             INT(11)        DEFAULT NULL,
    `campo4`             INT(11)        DEFAULT NULL,
    `campo5`             INT(11)        DEFAULT NULL,
    `data_1`             DATETIME       DEFAULT NULL,
    `data_2`             DATETIME       DEFAULT NULL,
    `band`               INT(11)        DEFAULT NULL,
    `brad`               INT(11)        DEFAULT NULL,
    `registra`           VARCHAR(1)     DEFAULT NULL,
    `cupom`              INT(11)        DEFAULT NULL,
    `multa`              DECIMAL(10, 2) DEFAULT NULL,
    `juros`              DECIMAL(4, 2)  DEFAULT NULL,
    `num_fatura`         INT(11)        DEFAULT NULL,
    `cont_ident`         INT(11)        DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_cts_d001_controle_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_cts_d001_controle_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_cts_d001_controle_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_cts_d001_controle_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_cts_d001_controle_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_cts_d001_controle_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  AUTO_INCREMENT = 4
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_cts_d002_cliente`;
CREATE TABLE `rdp_cts_d002_cliente`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `codigo`             INT(11)        DEFAULT NULL,
    `nome`               VARCHAR(70)    DEFAULT NULL,
    `data_pri`           DATETIME       DEFAULT NULL,
    `cpf`                VARCHAR(20)    DEFAULT NULL,
    `rg`                 VARCHAR(20)    DEFAULT NULL,
    `ender`              VARCHAR(40)    DEFAULT NULL,
    `cidade`             VARCHAR(30)    DEFAULT NULL,
    `uf`                 VARCHAR(2)     DEFAULT NULL,
    `cep`                VARCHAR(9)     DEFAULT NULL,
    `ddd`                VARCHAR(4)     DEFAULT NULL,
    `fone`               VARCHAR(12)    DEFAULT NULL,
    `bairro`             VARCHAR(30)    DEFAULT NULL,
    `tipo`               VARCHAR(1)     DEFAULT NULL,
    `ender_tra`          VARCHAR(40)    DEFAULT NULL,
    `cidade_tra`         VARCHAR(30)    DEFAULT NULL,
    `uf_tra`             VARCHAR(2)     DEFAULT NULL,
    `cep_tra`            VARCHAR(9)     DEFAULT NULL,
    `ddd_fax`            VARCHAR(4)     DEFAULT NULL,
    `fone_fax`           VARCHAR(12)    DEFAULT NULL,
    `data_ult`           DATETIME       DEFAULT NULL,
    `cargo_tra`          VARCHAR(15)    DEFAULT NULL,
    `maior_compra`       DECIMAL(10, 2) DEFAULT NULL,
    `conjuge`            VARCHAR(30)    DEFAULT NULL,
    `nasc_con`           DATETIME       DEFAULT NULL,
    `trabalho_con`       VARCHAR(30)    DEFAULT NULL,
    `rg_con`             VARCHAR(20)    DEFAULT NULL,
    `ender_tra_con`      VARCHAR(30)    DEFAULT NULL,
    `cid_tra_con`        VARCHAR(30)    DEFAULT NULL,
    `dias_atraso`        INT(11)        DEFAULT NULL,
    `cep_tra_con`        VARCHAR(9)     DEFAULT NULL,
    `ddd_tra_con`        VARCHAR(4)     DEFAULT NULL,
    `fone_tra_con`       VARCHAR(12)    DEFAULT NULL,
    `adm_tra_con`        DATETIME       DEFAULT NULL,
    `cargo_tra_con`      VARCHAR(15)    DEFAULT NULL,
    `vlr_ultcompra`      DECIMAL(10, 2) DEFAULT NULL,
    `obs3`               VARCHAR(60)    DEFAULT NULL,
    `pessoas_auto2`      VARCHAR(60)    DEFAULT NULL,
    `data_pagto`         DATETIME       DEFAULT NULL,
    `obs1`               VARCHAR(60)    DEFAULT NULL,
    `obs2`               VARCHAR(60)    DEFAULT NULL,
    `cond_pagto_id`      INT(11)        DEFAULT NULL,
    `suspenso`           VARCHAR(1)     DEFAULT NULL,
    `lim_compras`        DECIMAL(6, 2)  DEFAULT NULL,
    `compras`            DECIMAL(6, 2)  DEFAULT NULL,
    `localizador_id`     INT(11)        DEFAULT NULL,
    `endercob`           VARCHAR(40)    DEFAULT NULL,
    `cidadecob`          VARCHAR(30)    DEFAULT NULL,
    `ufcob`              VARCHAR(2)     DEFAULT NULL,
    `cepcob`             VARCHAR(9)     DEFAULT NULL,
    `bairrocob`          VARCHAR(30)    DEFAULT NULL,
    `obs4`               VARCHAR(60)    DEFAULT NULL,
    `obs5`               VARCHAR(60)    DEFAULT NULL,
    `obs6`               VARCHAR(60)    DEFAULT NULL,
    `nas_prop`           DATETIME       DEFAULT NULL,
    `nas_funda`          DATETIME       DEFAULT NULL,
    `ramo`               VARCHAR(30)    DEFAULT NULL,
    `bens01`             VARCHAR(50)    DEFAULT NULL,
    `bens02`             VARCHAR(50)    DEFAULT NULL,
    `scania`             INT(11)        DEFAULT NULL,
    `volvo`              INT(11)        DEFAULT NULL,
    `mb`                 INT(11)        DEFAULT NULL,
    `outros`             INT(11)        DEFAULT NULL,
    `scania01`           INT(11)        DEFAULT NULL,
    `volvo01`            INT(11)        DEFAULT NULL,
    `mb01`               INT(11)        DEFAULT NULL,
    `outros01`           INT(11)        DEFAULT NULL,
    `flag_casa`          VARCHAR(1)     DEFAULT NULL,
    `ref_banco`          VARCHAR(50)    DEFAULT NULL,
    `ref_banco01`        VARCHAR(50)    DEFAULT NULL,
    `ref_come`           VARCHAR(50)    DEFAULT NULL,
    `ref_come01`         VARCHAR(50)    DEFAULT NULL,
    `vendedor`           INT(11)        DEFAULT NULL,
    `pai`                VARCHAR(30)    DEFAULT NULL,
    `mae`                VARCHAR(30)    DEFAULT NULL,
    `pes_conhe`          VARCHAR(50)    DEFAULT NULL,
    `fon_conhe`          VARCHAR(20)    DEFAULT NULL,
    `email`              VARCHAR(40)    DEFAULT NULL,
    `integ_wle`          VARCHAR(1)     DEFAULT NULL,
    `obs01`              VARCHAR(75)    DEFAULT NULL,
    `obs02`              VARCHAR(75)    DEFAULT NULL,
    `obs03`              VARCHAR(75)    DEFAULT NULL,
    `obs04`              VARCHAR(75)    DEFAULT NULL,
    `obs05`              VARCHAR(75)    DEFAULT NULL,
    `obs06`              VARCHAR(75)    DEFAULT NULL,
    `obs07`              VARCHAR(75)    DEFAULT NULL,
    `obs08`              VARCHAR(75)    DEFAULT NULL,
    `obs09`              VARCHAR(75)    DEFAULT NULL,
    `obs10`              VARCHAR(75)    DEFAULT NULL,
    `obs11`              VARCHAR(75)    DEFAULT NULL,
    `obs12`              VARCHAR(75)    DEFAULT NULL,
    `obs13`              VARCHAR(75)    DEFAULT NULL,
    `obs14`              VARCHAR(75)    DEFAULT NULL,
    `obs15`              VARCHAR(75)    DEFAULT NULL,
    `obs16`              VARCHAR(75)    DEFAULT NULL,
    `obs17`              VARCHAR(75)    DEFAULT NULL,
    `obs18`              VARCHAR(75)    DEFAULT NULL,
    `obs19`              VARCHAR(75)    DEFAULT NULL,
    `obs20`              VARCHAR(75)    DEFAULT NULL,
    `rg2`                VARCHAR(20)    DEFAULT NULL,
    `numero`             VARCHAR(20)    DEFAULT NULL,
    `complemento`        VARCHAR(60)    DEFAULT NULL,
    `cod_munic`          INT(11)        DEFAULT NULL,
    `flag_lib_preco`     VARCHAR(1)     DEFAULT NULL,
    `flag_bloqueio`      VARCHAR(1)     DEFAULT NULL,
    `flag_comissao`      VARCHAR(1)     DEFAULT NULL,
    `dias_trv_fat`       INT(11)        DEFAULT NULL,
    `perc_margem`        DECIMAL(4, 2)  DEFAULT NULL,
    `tipo_cliente`       VARCHAR(1)     DEFAULT NULL,
    `tipo_fornec`        VARCHAR(1)     DEFAULT NULL,
    `flag_scp`           VARCHAR(1)     DEFAULT NULL,
    `flag_chequedev`     VARCHAR(1)     DEFAULT NULL,
    `cod_consul`         INT(11)        DEFAULT NULL,
    `frotista`           VARCHAR(1)     DEFAULT NULL,

    UNIQUE KEY `codigo` (`codigo`),
    KEY `localizador_id` (`localizador_id`),
    KEY `cts_d002_cliente_idx1` (`nome`),
    KEY `cts_d002_cliente_idx2` (`cpf`),
    KEY `cts_d002_cliente_fk1` (`cond_pagto_id`),
    CONSTRAINT `cts_d002_cliente_fk2` FOREIGN KEY (`localizador_id`) references `cts_d005_localizador` (`codigo`),
    CONSTRAINT `cts_d002_cliente_fk3` FOREIGN KEY (`cond_pagto_id`) references `fat_d092_condpag` (`codigo`),


    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_cts_d002_cliente_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_cts_d002_cliente_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_cts_d002_cliente_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_cts_d002_cliente_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_cts_d002_cliente_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_cts_d002_cliente_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  AUTO_INCREMENT = 10
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_cts_d005_localizador`;
CREATE TABLE `rdp_cts_d005_localizador`
(
    `id`                 BIGINT(20)  NOT NULL AUTO_INCREMENT,
    `codigo`             INT(11)     NOT NULL,
    `localizador`        VARCHAR(40) NOT NULL,
    `vencto`             DATETIME DEFAULT NULL,
    `seq_brad`           INT(11)  DEFAULT NULL,
    `seq_itau`           INT(11)  DEFAULT NULL,

    UNIQUE KEY `codigo` (`codigo`),
    KEY `cts_d005_localizador_idx1` (`localizador`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20)  NOT NULL,
    `inserted`           DATETIME    NOT NULL,
    `updated`            DATETIME    NOT NULL,
    `user_inserted_id`   BIGINT(20)  NOT NULL,
    `user_updated_id`    BIGINT(20)  NOT NULL,
    KEY `k_rdp_cts_d005_localizador_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_cts_d005_localizador_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_cts_d005_localizador_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_cts_d005_localizador_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_cts_d005_localizador_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_cts_d005_localizador_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  AUTO_INCREMENT = 4
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_cts_d009_filial`;
CREATE TABLE `rdp_cts_d009_filial`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `codigo`             INT(11)     DEFAULT NULL,
    `desc_filial`        VARCHAR(60) DEFAULT NULL,
    `cgc`                VARCHAR(15) DEFAULT NULL,
    `insc`               VARCHAR(15) DEFAULT NULL,
    `ender`              VARCHAR(40) DEFAULT NULL,
    `cidade`             VARCHAR(20) DEFAULT NULL,
    `uf`                 VARCHAR(2)  DEFAULT NULL,
    `nota`               INT(11)     DEFAULT NULL,
    `tique`              INT(11)     DEFAULT NULL,
    `ped_compra`         INT(11)     DEFAULT NULL,
    `cod_mun`            VARCHAR(10) DEFAULT NULL,
    `num_end`            INT(11)     DEFAULT NULL,
    `cnae`               VARCHAR(10) DEFAULT NULL,
    `imunic`             VARCHAR(10) DEFAULT NULL,
    `fimpressao`         VARCHAR(10) DEFAULT NULL,
    `contingencia`       VARCHAR(2)  DEFAULT NULL,
    `ambiente`           VARCHAR(1)  DEFAULT NULL,
    `bairro`             VARCHAR(40) DEFAULT NULL,
    `cep`                VARCHAR(9)  DEFAULT NULL,
    `fone`               VARCHAR(20) DEFAULT NULL,
    `dir_nfe`            VARCHAR(80) DEFAULT NULL,
    `dir_remessa`        VARCHAR(80) DEFAULT NULL,
    `dir_retorno`        VARCHAR(80) DEFAULT NULL,
    `nr_itau`            INT(11)     DEFAULT NULL,
    `nfe`                INT(11)     DEFAULT NULL,
    `nr_bb`              INT(11)     DEFAULT NULL,
    `nr_bradesco`        INT(11)     DEFAULT NULL,
    `seq_brad`           INT(11)     DEFAULT NULL,
    `seq_bb`             INT(11)     DEFAULT NULL,
    UNIQUE KEY `codigo` (`codigo`),
    KEY `cts_d009_filial_idx1` (`desc_filial`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_cts_d009_filial_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_cts_d009_filial_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_cts_d009_filial_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_cts_d009_filial_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_cts_d009_filial_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_cts_d009_filial_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  AUTO_INCREMENT = 6
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_cts_d010_cts_pagrec`;
CREATE TABLE `rdp_cts_d010_cts_pagrec`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `lancto`             INT(11)        DEFAULT NULL,
    `num_fatura`         INT(11)        DEFAULT NULL,
    `movto`              DATETIME       DEFAULT NULL,
    `vencto`             DATETIME       DEFAULT NULL,
    `pagto`              DATETIME       DEFAULT NULL,
    `data_outros`        DATETIME       DEFAULT NULL,
    `cliente_id`         INT(11)    NOT NULL,
    `localizador_id`     INT(11)    NOT NULL,
    `filial_id`          INT(11)    NOT NULL,
    `valor_titulo`       DECIMAL(10, 4) DEFAULT NULL,
    `valor_pago`         DECIMAL(10, 4) DEFAULT NULL,
    `situacao`           VARCHAR(1)     DEFAULT NULL,
    `tipo`               VARCHAR(1)     DEFAULT NULL,
    `emitido`            VARCHAR(1)     DEFAULT NULL,
    `bloqueto`           INT(11)        DEFAULT NULL,
    `docto`              VARCHAR(12)    DEFAULT NULL,
    `descricao`          char(1)        DEFAULT NULL,
    `descontos`          DECIMAL(10, 4) DEFAULT NULL,
    `acrescimos`         DECIMAL(10, 4) DEFAULT NULL,
    `vendedor_id`        INT(11)        DEFAULT NULL,
    `marca`              VARCHAR(1)     DEFAULT NULL,
    `numero_nf`          INT(11)        DEFAULT NULL,
    `emissao_nf`         DATETIME       DEFAULT NULL,
    `integ_wle`          VARCHAR(1)     DEFAULT NULL,
    `cod_inst01`         VARCHAR(2)     DEFAULT NULL,
    `cod_inst02`         VARCHAR(2)     DEFAULT NULL,
    `dias_pro`           INT(11)        DEFAULT NULL,
    `taxa`               DECIMAL(4, 4)  DEFAULT NULL,
    `multa`              DECIMAL(4, 4)  DEFAULT NULL,
    `nosso_nr`           VARCHAR(17)    DEFAULT NULL,
    `envio`              VARCHAR(1)     DEFAULT NULL,
    `num_fat`            INT(11)        DEFAULT NULL,
    `cod_banco`          VARCHAR(5)     DEFAULT NULL,
    `des_banco`          VARCHAR(10)    DEFAULT NULL,
    `agencia`            VARCHAR(10)    DEFAULT NULL,
    `conta`              VARCHAR(15)    DEFAULT NULL,
    `num_cheque`         VARCHAR(15)    DEFAULT NULL,
    `nominal`            VARCHAR(20)    DEFAULT NULL,
    `deposito`           INT(11)        DEFAULT NULL,
    `data_caixa`         DATETIME       DEFAULT NULL,

    UNIQUE KEY `lancto` (`lancto`),
    KEY `cts_d010_cts_pagrec_idx1` (`cliente_id`),
    KEY `cts_d010_cts_pagrec_fk2` (`localizador_id`),
    KEY `cts_d010_cts_pagrec_fk3` (`filial_id`),
    KEY `cts_d010_cts_pagrec_fk4` (`vendedor_id`),
    CONSTRAINT `cts_d010_cts_pagrec_fk4` FOREIGN KEY (`vendedor_id`) references `fat_d095_vendedor` (`codigo`),
    CONSTRAINT `cts_d010_cts_pagrec_fk1` FOREIGN KEY (`cliente_id`) references `cts_d002_cliente` (`codigo`),
    CONSTRAINT `cts_d010_cts_pagrec_fk2` FOREIGN KEY (`localizador_id`) references `cts_d005_localizador` (`codigo`),
    CONSTRAINT `cts_d010_cts_pagrec_fk3` FOREIGN KEY (`filial_id`) references `cts_d009_filial` (`codigo`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_cts_d010_cts_pagrec_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_cts_d010_cts_pagrec_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_cts_d010_cts_pagrec_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_cts_d010_cts_pagrec_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_cts_d010_cts_pagrec_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_cts_d010_cts_pagrec_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  AUTO_INCREMENT = 7
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_cts_d011_baixas`;
CREATE TABLE `rdp_cts_d011_baixas`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `lancto`             INT(11)       DEFAULT NULL,
    `data`               DATETIME      DEFAULT NULL,
    `valor`              DECIMAL(8, 4) DEFAULT NULL,
    `juros`              DECIMAL(8, 4) DEFAULT NULL,
    `descontos`          DECIMAL(8, 4) DEFAULT NULL,
    `observacao`         VARCHAR(75)   DEFAULT NULL,

    KEY `cts_d011_baixas_idx1` (`lancto`),
    CONSTRAINT `cts_d011_baixas_fk1` FOREIGN KEY (`id`) references `cts_d010_cts_pagrec` (`id`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_cts_d011_baixas_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_cts_d011_baixas_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_cts_d011_baixas_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_cts_d011_baixas_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_cts_d011_baixas_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_cts_d011_baixas_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_cts_obs2_cliente`;
CREATE TABLE `rdp_cts_obs2_cliente`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `codigo_id`          BIGINT(20) NOT NULL,
    `obs1`               VARCHAR(80) DEFAULT NULL,
    `obs2`               VARCHAR(80) DEFAULT NULL,
    `obs3`               VARCHAR(80) DEFAULT NULL,
    `obs4`               VARCHAR(80) DEFAULT NULL,
    `obs5`               VARCHAR(80) DEFAULT NULL,
    `obs6`               VARCHAR(80) DEFAULT NULL,
    `obs7`               VARCHAR(80) DEFAULT NULL,
    `obs8`               VARCHAR(80) DEFAULT NULL,
    `obs9`               VARCHAR(80) DEFAULT NULL,
    `obs10`              VARCHAR(80) DEFAULT NULL,
    `obs11`              VARCHAR(80) DEFAULT NULL,
    `obs12`              VARCHAR(80) DEFAULT NULL,
    `obs13`              VARCHAR(80) DEFAULT NULL,
    `obs14`              VARCHAR(80) DEFAULT NULL,
    `obs15`              VARCHAR(80) DEFAULT NULL,
    `obs16`              VARCHAR(80) DEFAULT NULL,
    `obs17`              VARCHAR(80) DEFAULT NULL,
    `obs18`              VARCHAR(80) DEFAULT NULL,
    `c1`                 VARCHAR(80) DEFAULT NULL,
    `c2`                 VARCHAR(80) DEFAULT NULL,
    `c3`                 VARCHAR(80) DEFAULT NULL,
    `c4`                 VARCHAR(80) DEFAULT NULL,
    `c5`                 VARCHAR(80) DEFAULT NULL,
    `c6`                 VARCHAR(80) DEFAULT NULL,
    `c7`                 VARCHAR(80) DEFAULT NULL,
    `c8`                 VARCHAR(80) DEFAULT NULL,
    `c9`                 VARCHAR(80) DEFAULT NULL,
    `c10`                VARCHAR(80) DEFAULT NULL,
    `c11`                VARCHAR(80) DEFAULT NULL,
    `c12`                VARCHAR(80) DEFAULT NULL,
    `c13`                VARCHAR(80) DEFAULT NULL,
    `c14`                VARCHAR(80) DEFAULT NULL,
    `c15`                VARCHAR(80) DEFAULT NULL,
    `c16`                VARCHAR(80) DEFAULT NULL,
    `c17`                VARCHAR(80) DEFAULT NULL,
    `c18`                VARCHAR(80) DEFAULT NULL,
    `i1`                 VARCHAR(80) DEFAULT NULL,
    `i2`                 VARCHAR(80) DEFAULT NULL,
    `i3`                 VARCHAR(80) DEFAULT NULL,
    `i4`                 VARCHAR(80) DEFAULT NULL,
    `i5`                 VARCHAR(80) DEFAULT NULL,
    `i6`                 VARCHAR(80) DEFAULT NULL,
    `i7`                 VARCHAR(80) DEFAULT NULL,
    `i8`                 VARCHAR(80) DEFAULT NULL,
    `i9`                 VARCHAR(80) DEFAULT NULL,
    `i10`                VARCHAR(80) DEFAULT NULL,
    `i11`                VARCHAR(80) DEFAULT NULL,
    `i12`                VARCHAR(80) DEFAULT NULL,
    `i13`                VARCHAR(80) DEFAULT NULL,
    `i14`                VARCHAR(80) DEFAULT NULL,
    `i15`                VARCHAR(80) DEFAULT NULL,
    `i16`                VARCHAR(80) DEFAULT NULL,
    `i17`                VARCHAR(80) DEFAULT NULL,
    `i18`                VARCHAR(80) DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_cts_obs2_cliente_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_cts_obs2_cliente_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_cts_obs2_cliente_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_cts_obs2_cliente_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_cts_obs2_cliente_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_cts_obs2_cliente_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_cts_obs3_cliente`;
CREATE TABLE `rdp_cts_obs3_cliente`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `codigo_id`          BIGINT(20) NOT NULL,
    `obs1`               VARCHAR(80) DEFAULT NULL,
    `obs2`               VARCHAR(80) DEFAULT NULL,
    `obs3`               VARCHAR(80) DEFAULT NULL,
    `obs4`               VARCHAR(80) DEFAULT NULL,
    `obs5`               VARCHAR(80) DEFAULT NULL,
    `obs6`               VARCHAR(80) DEFAULT NULL,
    `obs7`               VARCHAR(80) DEFAULT NULL,
    `obs8`               VARCHAR(80) DEFAULT NULL,
    `obs9`               VARCHAR(80) DEFAULT NULL,
    `obs10`              VARCHAR(80) DEFAULT NULL,
    `obs11`              VARCHAR(80) DEFAULT NULL,
    `obs12`              VARCHAR(80) DEFAULT NULL,
    `obs13`              VARCHAR(80) DEFAULT NULL,
    `obs14`              VARCHAR(80) DEFAULT NULL,
    `obs15`              VARCHAR(80) DEFAULT NULL,
    `obs16`              VARCHAR(80) DEFAULT NULL,
    `obs17`              VARCHAR(80) DEFAULT NULL,
    `obs18`              VARCHAR(80) DEFAULT NULL,
    `c1`                 VARCHAR(80) DEFAULT NULL,
    `c2`                 VARCHAR(80) DEFAULT NULL,
    `c3`                 VARCHAR(80) DEFAULT NULL,
    `c4`                 VARCHAR(80) DEFAULT NULL,
    `c5`                 VARCHAR(80) DEFAULT NULL,
    `c6`                 VARCHAR(80) DEFAULT NULL,
    `c7`                 VARCHAR(80) DEFAULT NULL,
    `c8`                 VARCHAR(80) DEFAULT NULL,
    `c9`                 VARCHAR(80) DEFAULT NULL,
    `c10`                VARCHAR(80) DEFAULT NULL,
    `c11`                VARCHAR(80) DEFAULT NULL,
    `c12`                VARCHAR(80) DEFAULT NULL,
    `c13`                VARCHAR(80) DEFAULT NULL,
    `c14`                VARCHAR(80) DEFAULT NULL,
    `c15`                VARCHAR(80) DEFAULT NULL,
    `c16`                VARCHAR(80) DEFAULT NULL,
    `c17`                VARCHAR(80) DEFAULT NULL,
    `c18`                VARCHAR(80) DEFAULT NULL,
    `i1`                 VARCHAR(80) DEFAULT NULL,
    `i2`                 VARCHAR(80) DEFAULT NULL,
    `i3`                 VARCHAR(80) DEFAULT NULL,
    `i4`                 VARCHAR(80) DEFAULT NULL,
    `i5`                 VARCHAR(80) DEFAULT NULL,
    `i6`                 VARCHAR(80) DEFAULT NULL,
    `i7`                 VARCHAR(80) DEFAULT NULL,
    `i8`                 VARCHAR(80) DEFAULT NULL,
    `i9`                 VARCHAR(80) DEFAULT NULL,
    `i10`                VARCHAR(80) DEFAULT NULL,
    `i11`                VARCHAR(80) DEFAULT NULL,
    `i12`                VARCHAR(80) DEFAULT NULL,
    `i13`                VARCHAR(80) DEFAULT NULL,
    `i14`                VARCHAR(80) DEFAULT NULL,
    `i15`                VARCHAR(80) DEFAULT NULL,
    `i16`                VARCHAR(80) DEFAULT NULL,
    `i17`                VARCHAR(80) DEFAULT NULL,
    `i18`                VARCHAR(80) DEFAULT NULL,

    UNIQUE KEY `codigo` (`codigo_id`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_cts_obs3_cliente_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_cts_obs3_cliente_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_cts_obs3_cliente_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_cts_obs3_cliente_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_cts_obs3_cliente_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_cts_obs3_cliente_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_cxa_d001_controle`;
CREATE TABLE `rdp_cxa_d001_controle`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `campo1`             INT(11)  DEFAULT NULL,
    `campo2`             INT(11)  DEFAULT NULL,
    `data_cxa`           DATETIME DEFAULT NULL,
    `data_rec`           DATETIME DEFAULT NULL,
    `caixa`              INT(11)  DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_cxa_d001_controle_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_cxa_d001_controle_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_cxa_d001_controle_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_cxa_d001_controle_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_cxa_d001_controle_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_cxa_d001_controle_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_cxa_d002_recebimentos`;
CREATE TABLE `rdp_cxa_d002_recebimentos`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `lancto`             INT(11)        DEFAULT NULL,
    `tipo`               VARCHAR(1)     DEFAULT NULL,
    `matricula`          VARCHAR(25)    DEFAULT NULL,
    `vencto`             DATETIME       DEFAULT NULL,
    `recto`              DATETIME       DEFAULT NULL,
    `caixa`              DATETIME       DEFAULT NULL,
    `valor`              DECIMAL(10, 2) DEFAULT NULL,
    `vendedor`           INT(11)        DEFAULT NULL,
    `obs`                VARCHAR(30)    DEFAULT NULL,
    `espec`              VARCHAR(1)     DEFAULT NULL,
    `num_cheque`         VARCHAR(12)    DEFAULT NULL,
    `marca`              VARCHAR(1)     DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_cxa_d002_recebimentos_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_cxa_d002_recebimentos_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_cxa_d002_recebimentos_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_cxa_d002_recebimentos_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_cxa_d002_recebimentos_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_cxa_d002_recebimentos_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_cxa_d003_caixa`;
CREATE TABLE `rdp_cxa_d003_caixa`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `lancto`             INT(11)    NOT NULL,
    `tipo`               VARCHAR(1)     DEFAULT NULL,
    `data`               DATETIME       DEFAULT NULL,
    `hist`               VARCHAR(75)    DEFAULT NULL,
    `valor`              DECIMAL(10, 2) DEFAULT NULL,
    `num_cxa`            INT(11)        DEFAULT NULL,
    UNIQUE KEY `lancto` (`lancto`),


    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_cxa_d003_caixa_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_cxa_d003_caixa_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_cxa_d003_caixa_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_cxa_d003_caixa_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_cxa_d003_caixa_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_cxa_d003_caixa_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_dep_d113_controle`;
CREATE TABLE `rdp_dep_d113_controle`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `lancto`             INT(11) DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_dep_d113_controle_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_dep_d113_controle_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_dep_d113_controle_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_dep_d113_controle_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_dep_d113_controle_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_dep_d113_controle_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_dep_d114_deposito`;
CREATE TABLE `rdp_dep_d114_deposito`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `codigo`             INT(11)     DEFAULT NULL,
    `deposito`           VARCHAR(50) DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_dep_d114_deposito_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_dep_d114_deposito_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_dep_d114_deposito_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_dep_d114_deposito_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_dep_d114_deposito_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_dep_d114_deposito_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_dep_d115_movimento`;
CREATE TABLE `rdp_dep_d115_movimento`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `lancto`             INT(11)        DEFAULT NULL,
    `data`               DATETIME       DEFAULT NULL,
    `documento`          VARCHAR(15)    DEFAULT NULL,
    `deposito`           INT(11)        DEFAULT NULL,
    `produto`            VARCHAR(15)    DEFAULT NULL,
    `tipo`               VARCHAR(1)     DEFAULT NULL,
    `qtde`               DECIMAL(10, 2) DEFAULT NULL,
    `preco`              DECIMAL(8, 2)  DEFAULT NULL,
    `hist`               VARCHAR(50)    DEFAULT NULL,
    `hor`                INT(11)        DEFAULT NULL,
    `min`                INT(11)        DEFAULT NULL,
    `lancto_pv`          INT(11)        DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_dep_d115_movimento_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_dep_d115_movimento_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_dep_d115_movimento_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_dep_d115_movimento_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_dep_d115_movimento_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_dep_d115_movimento_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_dep_d116_localizacao`;
CREATE TABLE `rdp_dep_d116_localizacao`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `deposito`           INT(11)     DEFAULT NULL,
    `produto`            VARCHAR(13) DEFAULT NULL,
    `localizacao`        VARCHAR(15) DEFAULT NULL,
    `qmin`               INT(11)     DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_dep_d116_localizacao_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_dep_d116_localizacao_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_dep_d116_localizacao_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_dep_d116_localizacao_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_dep_d116_localizacao_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_dep_d116_localizacao_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_dep_d117_compra`;
CREATE TABLE `rdp_dep_d117_compra`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `lancto`             INT(11)        DEFAULT NULL,
    `docto`              VARCHAR(10)    DEFAULT NULL,
    `tipo`               VARCHAR(1)     DEFAULT NULL,
    `data_movto`         DATETIME       DEFAULT NULL,
    `produto`            VARCHAR(13)    DEFAULT NULL,
    `digito`             VARCHAR(1)     DEFAULT NULL,
    `qtde`               DECIMAL(10, 4) DEFAULT NULL,
    `preco_unit`         DECIMAL(10, 2) DEFAULT NULL,
    `preco_vend`         DECIMAL(10, 2) DEFAULT NULL,
    `total_nota`         DECIMAL(10, 2) DEFAULT NULL,
    `icms`               DECIMAL(6, 2)  DEFAULT NULL,
    `ipi`                DECIMAL(6, 2)  DEFAULT NULL,
    `acessorio`          DECIMAL(6, 2)  DEFAULT NULL,
    `outros`             DECIMAL(6, 2)  DEFAULT NULL,
    `margem`             DECIMAL(4, 4)  DEFAULT NULL,
    `preco_custo`        DECIMAL(8, 2)  DEFAULT NULL,
    `preco_medio`        DECIMAL(8, 2)  DEFAULT NULL,
    `vencto`             DATETIME       DEFAULT NULL,
    `flag`               VARCHAR(1)     DEFAULT NULL,
    `custo_desc`         DECIMAL(4, 2)  DEFAULT NULL,
    `hora`               INT(11)        DEFAULT NULL,
    `minuto`             INT(11)        DEFAULT NULL,
    `p_desc`             DECIMAL(6, 2)  DEFAULT NULL,
    `cod_fornec`         INT(11)        DEFAULT NULL,
    `responsavel`        VARCHAR(15)    DEFAULT NULL,
    `codigo_data`        INT(11)        DEFAULT NULL,
    `custo_merc`         DECIMAL(8, 2)  DEFAULT NULL,
    `preco_cad`          DECIMAL(8, 2)  DEFAULT NULL,
    `localizacao`        VARCHAR(20)    DEFAULT NULL,
    `foto_catalogo`      VARCHAR(20)    DEFAULT NULL,
    `vlr_desc`           DECIMAL(6, 2)  DEFAULT NULL,
    `vlr_ipi`            DECIMAL(6, 2)  DEFAULT NULL,
    `vlr_icms`           DECIMAL(6, 2)  DEFAULT NULL,
    `vlr_outras`         DECIMAL(6, 2)  DEFAULT NULL,
    `vlr_frete`          DECIMAL(6, 2)  DEFAULT NULL,
    `base_icms`          DECIMAL(8, 2)  DEFAULT NULL,
    `base_subst`         DECIMAL(8, 2)  DEFAULT NULL,
    `tot_mercadorias`    DECIMAL(8, 2)  DEFAULT NULL,
    `tot_notafiscal`     DECIMAL(8, 2)  DEFAULT NULL,
    `tot_ipi`            DECIMAL(6, 2)  DEFAULT NULL,
    `tot_subst`          DECIMAL(6, 2)  DEFAULT NULL,
    `tot_frete`          DECIMAL(6, 2)  DEFAULT NULL,
    `tot_descontos`      DECIMAL(6, 2)  DEFAULT NULL,
    `datanf`             DATETIME       DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_dep_d117_compra_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_dep_d117_compra_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_dep_d117_compra_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_dep_d117_compra_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_dep_d117_compra_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_dep_d117_compra_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_dep_d118_insumo`;
CREATE TABLE `rdp_dep_d118_insumo`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `produto`            VARCHAR(13)   DEFAULT NULL,
    `cod_insumo`         VARCHAR(13)   DEFAULT NULL,
    `qtde`               DECIMAL(4, 4) DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_dep_d118_insumo_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_dep_d118_insumo_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_dep_d118_insumo_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_dep_d118_insumo_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_dep_d118_insumo_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_dep_d118_insumo_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_dep_d119_reposicao`;
CREATE TABLE `rdp_dep_d119_reposicao`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `codigo`             VARCHAR(13)    DEFAULT NULL,
    `descricao`          VARCHAR(50)    DEFAULT NULL,
    `unidade`            VARCHAR(10)    DEFAULT NULL,
    `saldo`              DECIMAL(10, 4) DEFAULT NULL,
    `preco`              DECIMAL(10, 2) DEFAULT NULL,
    `totalp`             DECIMAL(10, 2) DEFAULT NULL,
    `data`               DATETIME       DEFAULT NULL,
    `localizacao`        VARCHAR(40)    DEFAULT NULL,
    `arquivo`            VARCHAR(30)    DEFAULT NULL,
    `preco_custo`        DECIMAL(10, 2) DEFAULT NULL,
    `smv`                DECIMAL(10, 2) DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_dep_d119_reposicao_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_dep_d119_reposicao_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_dep_d119_reposicao_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_dep_d119_reposicao_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_dep_d119_reposicao_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_dep_d119_reposicao_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_dweb_d037_os_dpz`;
CREATE TABLE `rdp_dweb_d037_os_dpz`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `filial`             INT(11)     DEFAULT NULL,
    `numero_pv`          INT(11)     DEFAULT NULL,
    `item`               INT(11)     DEFAULT NULL,
    `numero_nfe`         INT(11)     DEFAULT NULL,
    `numero_os`          INT(11)     DEFAULT NULL,
    `status`             VARCHAR(10) DEFAULT NULL,
    `descricao`          VARCHAR(50) DEFAULT NULL,
    `mensagem`           VARCHAR(50) DEFAULT NULL,
    `fila`               INT(11)     DEFAULT NULL,
    `tabela`             VARCHAR(1)  DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_dweb_d037_os_dpz_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_dweb_d037_os_dpz_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_dweb_d037_os_dpz_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_dweb_d037_os_dpz_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_dweb_d037_os_dpz_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_dweb_d037_os_dpz_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1
  row_format = compact;



DROP TABLE IF EXISTS `rdp_dweb_d038_corpo_dpz`;
CREATE TABLE `rdp_dweb_d038_corpo_dpz`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `filial`             INT(11)     DEFAULT NULL,
    `numero`             INT(11)     DEFAULT NULL,
    `os`                 INT(11)     DEFAULT NULL,
    `placa`              VARCHAR(8)  DEFAULT NULL,
    `retorno`            VARCHAR(15) DEFAULT NULL,
    `descret`            VARCHAR(40) DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_dweb_d038_corpo_dpz_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_dweb_d038_corpo_dpz_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_dweb_d038_corpo_dpz_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_dweb_d038_corpo_dpz_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_dweb_d038_corpo_dpz_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_dweb_d038_corpo_dpz_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_est_curv_abc`;
CREATE TABLE `rdp_est_curv_abc`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `codigo`             VARCHAR(13) DEFAULT NULL,
    `descricao`          VARCHAR(50) DEFAULT NULL,
    `curva`              VARCHAR(1)  DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_est_curv_abc_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_est_curv_abc_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_est_curv_abc_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_est_curv_abc_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_curv_abc_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_curv_abc_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_est_d067_ncm`;
CREATE TABLE `rdp_est_d067_ncm`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `cest`               VARCHAR(7)  DEFAULT NULL,
    `ncm2`               VARCHAR(2)  DEFAULT NULL,
    `ncm4`               VARCHAR(4)  DEFAULT NULL,
    `ncm8`               VARCHAR(8)  DEFAULT NULL,
    `descricao`          VARCHAR(80) DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_est_d067_ncm_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_est_d067_ncm_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_est_d067_ncm_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_est_d067_ncm_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_d067_ncm_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_d067_ncm_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;


DROP TABLE IF EXISTS `rdp_est_d070_controle`;
CREATE TABLE `rdp_est_d070_controle`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `campo1`             INT(11) DEFAULT NULL,
    `campo2`             INT(11) DEFAULT NULL,
    `campo3`             INT(11) DEFAULT NULL,
    `campo4`             INT(11) DEFAULT NULL,
    `campo5`             INT(11) DEFAULT NULL,
    `lancto_bal`         INT(11) DEFAULT NULL,
    `nro_itens`          INT(11) DEFAULT NULL,
    `nr_nota`            INT(11) DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_est_d070_controle_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_est_d070_controle_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_est_d070_controle_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_est_d070_controle_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_d070_controle_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_d070_controle_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_est_d071_produto`;
CREATE TABLE `rdp_est_d071_produto`
(
    `id`                 BIGINT(20) NOT NULL,
    `codigo`             VARCHAR(13)    DEFAULT NULL,
    `digito`             VARCHAR(1)     DEFAULT NULL,
    `descricao`          VARCHAR(35)    DEFAULT NULL,
    `formula`            VARCHAR(35)    DEFAULT NULL,
    `unidade`            VARCHAR(10)    DEFAULT NULL,
    `preco_custo`        DECIMAL(10, 4) DEFAULT NULL,
    `preco_venda`        DECIMAL(10, 4) DEFAULT NULL,
    `ult_entrada`        DATETIME       DEFAULT NULL,
    `ult_saida`          DATETIME       DEFAULT NULL,
    `deptoi`             INT(11)        DEFAULT NULL,
    `deptoii`            INT(11)        DEFAULT NULL,
    `laboratorio`        INT(11)        DEFAULT NULL,
    `qtde_inicial`       DECIMAL(10, 4) DEFAULT NULL,
    `qtde_atual`         DECIMAL(10, 4) DEFAULT NULL,
    `fornecedor`         INT(11)        DEFAULT NULL,
    `qtde_saidas`        DECIMAL(10, 4) DEFAULT NULL,
    `qtde_entradas`      DECIMAL(10, 4) DEFAULT NULL,
    `qtde_maxima`        DECIMAL(10, 4) DEFAULT NULL,
    `qtde_minima`        DECIMAL(10, 4) DEFAULT NULL,
    `cod_alter`          VARCHAR(10)    DEFAULT NULL,
    `preco_custod`       DECIMAL(6, 4)  DEFAULT NULL,
    `preco_vendad`       DECIMAL(6, 4)  DEFAULT NULL,
    `data_alter`         DATETIME       DEFAULT NULL,
    `preco_medio`        DECIMAL(10, 4) DEFAULT NULL,
    `ipi`                DECIMAL(4, 4)  DEFAULT NULL,
    `icms`               DECIMAL(4, 4)  DEFAULT NULL,
    `preco_custo_s`      DECIMAL(6, 4)  DEFAULT NULL,
    `preco_medio_s`      DECIMAL(6, 4)  DEFAULT NULL,
    `vencto1`            DATETIME       DEFAULT NULL,
    `vencto2`            DATETIME       DEFAULT NULL,
    `vencto3`            DATETIME       DEFAULT NULL,
    `vencto4`            DATETIME       DEFAULT NULL,
    `vencto5`            DATETIME       DEFAULT NULL,
    `qt1`                INT(11)        DEFAULT NULL,
    `qt2`                INT(11)        DEFAULT NULL,
    `qt3`                INT(11)        DEFAULT NULL,
    `qt4`                INT(11)        DEFAULT NULL,
    `qt5`                INT(11)        DEFAULT NULL,
    `cod_barras`         VARCHAR(13)    DEFAULT NULL,
    `custo_fiscal`       DECIMAL(8, 4)  DEFAULT NULL,
    `obs1`               VARCHAR(70)    DEFAULT NULL,
    `obs2`               VARCHAR(70)    DEFAULT NULL,
    `fileira`            VARCHAR(10)    DEFAULT NULL,
    `prateleira`         VARCHAR(10)    DEFAULT NULL,
    `bandeja`            VARCHAR(10)    DEFAULT NULL,
    `etiqueta`           VARCHAR(10)    DEFAULT NULL,
    `data_fal`           DATETIME       DEFAULT NULL,
    `qtde_fal`           INT(11)        DEFAULT NULL,
    `qtde_enc`           DECIMAL(10, 4) DEFAULT NULL,
    `ncm`                VARCHAR(8)     DEFAULT NULL,
    `trava_desconto`     VARCHAR(1)     DEFAULT NULL,
    `cfop_dentro`        VARCHAR(6)     DEFAULT NULL,
    `cfop_fora`          VARCHAR(6)     DEFAULT NULL,
    `sit_trib`           VARCHAR(5)     DEFAULT NULL,
    `tipo_trib`          VARCHAR(1)     DEFAULT NULL,
    `foto_catalogo`      VARCHAR(20)    DEFAULT NULL,
    `tab_comissao`       INT(11)        DEFAULT NULL,
    `flag_ident`         VARCHAR(1)     DEFAULT NULL,
    `flag_posic`         VARCHAR(1)     DEFAULT NULL,
    `dias_gara`          INT(11)        DEFAULT NULL,
    `ntz_receita`        VARCHAR(3)     DEFAULT NULL,
    `ent_cst_pis`        VARCHAR(2)     DEFAULT NULL,
    `ent_ali_pis`        DECIMAL(4, 4)  DEFAULT NULL,
    `ent_ali_cof`        DECIMAL(4, 4)  DEFAULT NULL,
    `sai_cst_pis`        VARCHAR(2)     DEFAULT NULL,
    `sai_ali_pis`        DECIMAL(4, 4)  DEFAULT NULL,
    `sai_ali_cof`        DECIMAL(4, 4)  DEFAULT NULL,
    `trib_piscof`        VARCHAR(1)     DEFAULT NULL,
    `ent_cst_cof`        VARCHAR(2)     DEFAULT NULL,
    `sai_cst_cof`        VARCHAR(2)     DEFAULT NULL,
    `tipo_produto`       VARCHAR(1)     DEFAULT NULL,
    `preco_minimo`       DECIMAL(8, 4)  DEFAULT NULL,
    UNIQUE KEY `codigo` (`codigo`),


    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_est_d071_produto_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_est_d071_produto_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_est_d071_produto_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_est_d071_produto_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_d071_produto_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_d071_produto_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)


) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_est_d072_movimento`;
CREATE TABLE `rdp_est_d072_movimento`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `lancto`             INT(11)        DEFAULT NULL,
    `docto`              VARCHAR(10)    DEFAULT NULL,
    `tipo`               VARCHAR(1)     DEFAULT NULL,
    `data_movto`         DATETIME       DEFAULT NULL,
    `produto`            VARCHAR(13)    DEFAULT NULL,
    `digito`             VARCHAR(1)     DEFAULT NULL,
    `qtde`               DECIMAL(10, 4) DEFAULT NULL,
    `preco_unit`         DECIMAL(10, 2) DEFAULT NULL,
    `preco_vend`         DECIMAL(10, 2) DEFAULT NULL,
    `total_nota`         DECIMAL(10, 2) DEFAULT NULL,
    `icms`               DECIMAL(6, 2)  DEFAULT NULL,
    `ipi`                DECIMAL(6, 2)  DEFAULT NULL,
    `acessorio`          DECIMAL(6, 2)  DEFAULT NULL,
    `outros`             DECIMAL(6, 2)  DEFAULT NULL,
    `margem`             DECIMAL(4, 4)  DEFAULT NULL,
    `preco_custo`        DECIMAL(8, 2)  DEFAULT NULL,
    `preco_medio`        DECIMAL(8, 2)  DEFAULT NULL,
    `vencto`             DATETIME       DEFAULT NULL,
    `flag`               VARCHAR(1)     DEFAULT NULL,
    `custo_desc`         DECIMAL(4, 2)  DEFAULT NULL,
    `hora`               INT(11)        DEFAULT NULL,
    `minuto`             INT(11)        DEFAULT NULL,
    `p_desc`             DECIMAL(6, 2)  DEFAULT NULL,
    `cod_fornec`         INT(11)        DEFAULT NULL,
    `responsavel`        VARCHAR(15)    DEFAULT NULL,
    `codigo_data`        INT(11)        DEFAULT NULL,
    `custo_merc`         DECIMAL(8, 2)  DEFAULT NULL,
    `preco_cad`          DECIMAL(8, 2)  DEFAULT NULL,
    `localizacao`        VARCHAR(20)    DEFAULT NULL,
    `foto_catalogo`      VARCHAR(20)    DEFAULT NULL,
    `vlr_desc`           DECIMAL(6, 2)  DEFAULT NULL,
    `vlr_ipi`            DECIMAL(6, 2)  DEFAULT NULL,
    `vlr_icms`           DECIMAL(6, 2)  DEFAULT NULL,
    `vlr_outras`         DECIMAL(6, 2)  DEFAULT NULL,
    `vlr_frete`          DECIMAL(6, 2)  DEFAULT NULL,
    `base_icms`          DECIMAL(8, 2)  DEFAULT NULL,
    `base_subst`         DECIMAL(8, 2)  DEFAULT NULL,
    `tot_mercadorias`    DECIMAL(8, 2)  DEFAULT NULL,
    `tot_notafiscal`     DECIMAL(8, 2)  DEFAULT NULL,
    `tot_ipi`            DECIMAL(6, 2)  DEFAULT NULL,
    `tot_subst`          DECIMAL(6, 2)  DEFAULT NULL,
    `tot_frete`          DECIMAL(6, 2)  DEFAULT NULL,
    `tot_descontos`      DECIMAL(6, 2)  DEFAULT NULL,
    `datanf`             DATETIME       DEFAULT NULL,
    `qtde_util`          DECIMAL(6, 4)  DEFAULT NULL,
    `perc_st`            DECIMAL(2, 2)  DEFAULT NULL,
    `lancto_pv`          INT(11)        DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_est_d072_movimento_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_est_d072_movimento_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_est_d072_movimento_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_est_d072_movimento_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_d072_movimento_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_d072_movimento_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_est_d073_dep1`;
CREATE TABLE `rdp_est_d073_dep1`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `codigo`             INT(11)       DEFAULT NULL,
    `depto`              VARCHAR(35)   DEFAULT NULL,
    `ncm`                VARCHAR(8)    DEFAULT NULL,
    `desconto1`          DECIMAL(2, 2) DEFAULT NULL,
    `desconto2`          DECIMAL(2, 2) DEFAULT NULL,
    `desconto3`          DECIMAL(2, 2) DEFAULT NULL,
    `qtde1`              INT(11)       DEFAULT NULL,
    `qtde2`              INT(11)       DEFAULT NULL,
    `qtde3`              INT(11)       DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_est_d073_dep1_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_est_d073_dep1_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_est_d073_dep1_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_est_d073_dep1_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_d073_dep1_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_d073_dep1_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_est_d074_dep2`;
CREATE TABLE `rdp_est_d074_dep2`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `codigo`             INT(11)     DEFAULT NULL,
    `depto`              VARCHAR(35) DEFAULT NULL,
    `ncm`                VARCHAR(8)  DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_est_d074_dep2_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_est_d074_dep2_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_est_d074_dep2_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_est_d074_dep2_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_d074_dep2_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_d074_dep2_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_est_d075_marca`;
CREATE TABLE `rdp_est_d075_marca`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `codigo`             INT(11)     DEFAULT NULL,
    `laboratorio`        VARCHAR(35) DEFAULT NULL,
    `ddd`                VARCHAR(4)  DEFAULT NULL,
    `fone`               VARCHAR(12) DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_est_d075_marca_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_est_d075_marca_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_est_d075_marca_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_est_d075_marca_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_d075_marca_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_d075_marca_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)

) ENGINE = INNODB
  DEFAULT charset = latin1;


DROP TABLE IF EXISTS `rdp_est_d076_comissao`;
CREATE TABLE `rdp_est_d076_comissao`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `depto1`             INT(11)       DEFAULT NULL,
    `depto2`             INT(11)       DEFAULT NULL,
    `comvista`           DECIMAL(4, 4) DEFAULT NULL,
    `comprazo`           DECIMAL(4, 4) DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_est_d076_comissao_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_est_d076_comissao_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_est_d076_comissao_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_est_d076_comissao_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_d076_comissao_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_d076_comissao_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_est_d078_edi`;
CREATE TABLE `rdp_est_d078_edi`
(
    `id`                 BIGINT(20) NOT NULL,
    `cod_edi`            VARCHAR(13) DEFAULT NULL,
    `cod_pro`            VARCHAR(13) DEFAULT NULL,
    `dig_pro`            VARCHAR(1)  DEFAULT NULL,
    `grp_pro`            INT(11)     DEFAULT NULL,
    `nom_pro`            VARCHAR(40) DEFAULT NULL,
    UNIQUE KEY `cod_edi` (`cod_edi`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_est_d078_edi_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_est_d078_edi_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_est_d078_edi_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_est_d078_edi_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_d078_edi_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_d078_edi_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_est_d079_balanco`;
CREATE TABLE `rdp_est_d079_balanco`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `lancto`             INT(11)        DEFAULT NULL,
    `data`               DATETIME       DEFAULT NULL,
    `codigo_data`        INT(11)        DEFAULT NULL,
    `produto`            VARCHAR(13)    DEFAULT NULL,
    `digito`             VARCHAR(1)     DEFAULT NULL,
    `quantidade`         DECIMAL(10, 4) DEFAULT NULL,
    `usuario`            VARCHAR(10)    DEFAULT NULL,
    `flag`               VARCHAR(1)     DEFAULT NULL,
    `motivo`             VARCHAR(75)    DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_est_d079_balanco_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_est_d079_balanco_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_est_d079_balanco_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_est_d079_balanco_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_d079_balanco_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_d079_balanco_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;


DROP TABLE IF EXISTS `rdp_est_d080_nfentrada`;
CREATE TABLE `rdp_est_d080_nfentrada`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `filial`             INT(11)        DEFAULT NULL,
    `cod_fornecedor`     INT(11)        DEFAULT NULL,
    `nro_nota`           DECIMAL(8, 2)  DEFAULT NULL,
    `serie`              VARCHAR(8)     DEFAULT NULL,
    `emissao`            DATETIME       DEFAULT NULL,
    `entrada`            DATETIME       DEFAULT NULL,
    `cod_fiscal`         VARCHAR(3)     DEFAULT NULL,
    `digito`             VARCHAR(1)     DEFAULT NULL,
    `cod_tipo_venda`     INT(11)        DEFAULT NULL,
    `moeda`              INT(11)        DEFAULT NULL,
    `local`              INT(11)        DEFAULT NULL,
    `ped_interno`        INT(11)        DEFAULT NULL,
    `nota_origem`        INT(11)        DEFAULT NULL,
    `nr_total_itens`     DECIMAL(6, 2)  DEFAULT NULL,
    `total_mercad`       DECIMAL(14, 2) DEFAULT NULL,
    `total_servicos`     DECIMAL(14, 2) DEFAULT NULL,
    `total_nota`         DECIMAL(14, 2) DEFAULT NULL,
    `desconto_valor`     DECIMAL(14, 2) DEFAULT NULL,
    `valor_frete`        DECIMAL(14, 2) DEFAULT NULL,
    `valor_seguro`       DECIMAL(14, 2) DEFAULT NULL,
    `valor_desace`       DECIMAL(14, 2) DEFAULT NULL,
    `outras_despesas`    DECIMAL(14, 2) DEFAULT NULL,
    `desc_serv`          DECIMAL(14, 2) DEFAULT NULL,
    `aliq_icms`          DECIMAL(4, 4)  DEFAULT NULL,
    `base_icms`          DECIMAL(14, 2) DEFAULT NULL,
    `base_ipi`           DECIMAL(14, 2) DEFAULT NULL,
    `valor_icms`         DECIMAL(14, 2) DEFAULT NULL,
    `valor_ipi`          DECIMAL(14, 2) DEFAULT NULL,
    `prazo_medio`        DECIMAL(6, 4)  DEFAULT NULL,
    `valor_tr`           DECIMAL(14, 4) DEFAULT NULL,
    `data_dolar`         DATETIME       DEFAULT NULL,
    `valor_dolar`        DECIMAL(14, 4) DEFAULT NULL,
    `flag01`             VARCHAR(1)     DEFAULT NULL,
    `flag02`             VARCHAR(1)     DEFAULT NULL,
    `flag03`             VARCHAR(1)     DEFAULT NULL,
    `flag04`             VARCHAR(1)     DEFAULT NULL,
    `flag05`             VARCHAR(1)     DEFAULT NULL,
    `flag06`             VARCHAR(1)     DEFAULT NULL,
    `flag07`             VARCHAR(1)     DEFAULT NULL,
    `flag08`             VARCHAR(1)     DEFAULT NULL,
    `flag09`             VARCHAR(1)     DEFAULT NULL,
    `flag10`             VARCHAR(1)     DEFAULT NULL,
    `cod_moeda`          INT(11)        DEFAULT NULL,
    `perc_juros`         DECIMAL(4, 4)  DEFAULT NULL,
    `nro_doc_01`         VARCHAR(15)    DEFAULT NULL,
    `nro_doc_02`         VARCHAR(15)    DEFAULT NULL,
    `nro_doc_03`         VARCHAR(15)    DEFAULT NULL,
    `nro_doc_04`         VARCHAR(15)    DEFAULT NULL,
    `nro_doc_05`         VARCHAR(15)    DEFAULT NULL,
    `nro_doc_06`         VARCHAR(15)    DEFAULT NULL,
    `nro_doc_07`         VARCHAR(15)    DEFAULT NULL,
    `nro_doc_08`         VARCHAR(15)    DEFAULT NULL,
    `nro_doc_09`         VARCHAR(15)    DEFAULT NULL,
    `nro_doc_10`         VARCHAR(15)    DEFAULT NULL,
    `nro_doc_11`         VARCHAR(15)    DEFAULT NULL,
    `nro_doc_12`         VARCHAR(15)    DEFAULT NULL,
    `vencto_01`          DATETIME       DEFAULT NULL,
    `vencto_02`          DATETIME       DEFAULT NULL,
    `vencto_03`          DATETIME       DEFAULT NULL,
    `vencto_04`          DATETIME       DEFAULT NULL,
    `vencto_05`          DATETIME       DEFAULT NULL,
    `vencto_06`          DATETIME       DEFAULT NULL,
    `vencto_07`          DATETIME       DEFAULT NULL,
    `vencto_08`          DATETIME       DEFAULT NULL,
    `vencto_09`          DATETIME       DEFAULT NULL,
    `vencto_10`          DATETIME       DEFAULT NULL,
    `vencto_11`          DATETIME       DEFAULT NULL,
    `vencto_12`          DATETIME       DEFAULT NULL,
    `prest_01`           DECIMAL(14, 2) DEFAULT NULL,
    `prest_02`           DECIMAL(14, 2) DEFAULT NULL,
    `prest_03`           DECIMAL(14, 2) DEFAULT NULL,
    `prest_04`           DECIMAL(14, 2) DEFAULT NULL,
    `prest_05`           DECIMAL(14, 2) DEFAULT NULL,
    `prest_06`           DECIMAL(14, 2) DEFAULT NULL,
    `prest_07`           DECIMAL(14, 2) DEFAULT NULL,
    `prest_08`           DECIMAL(14, 2) DEFAULT NULL,
    `prest_09`           DECIMAL(14, 2) DEFAULT NULL,
    `prest_10`           DECIMAL(14, 2) DEFAULT NULL,
    `prest_11`           DECIMAL(14, 2) DEFAULT NULL,
    `prest_12`           DECIMAL(14, 2) DEFAULT NULL,
    `data_base`          DATETIME       DEFAULT NULL,
    `flag_cp`            VARCHAR(1)     DEFAULT NULL,
    `field_85`           DECIMAL(4, 4)  DEFAULT NULL,
    `desc_servicos`      DECIMAL(8, 2)  DEFAULT NULL,
    `base_subst`         DECIMAL(8, 2)  DEFAULT NULL,
    `valor_subst`        DECIMAL(8, 2)  DEFAULT NULL,
    `base_issqn`         DECIMAL(8, 2)  DEFAULT NULL,
    `valor_issqn`        DECIMAL(8, 2)  DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_est_d080_nfentrada_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_est_d080_nfentrada_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_est_d080_nfentrada_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_est_d080_nfentrada_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_d080_nfentrada_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_d080_nfentrada_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_est_d081_corpo_nfentrada`;
CREATE TABLE `rdp_est_d081_corpo_nfentrada`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `nr_nota`            DECIMAL(6, 2)  DEFAULT NULL,
    `cod_fornecedor`     INT(11)        DEFAULT NULL,
    `item`               INT(11)        DEFAULT NULL,
    `cod_produto`        VARCHAR(13)    DEFAULT NULL,
    `digito`             VARCHAR(1)     DEFAULT NULL,
    `deptoi`             INT(11)        DEFAULT NULL,
    `deptoii`            INT(11)        DEFAULT NULL,
    `laboratorio`        INT(11)        DEFAULT NULL,
    `qtde`               DECIMAL(14, 4) DEFAULT NULL,
    `preco_unit`         DECIMAL(14, 2) DEFAULT NULL,
    `preco_total`        DECIMAL(14, 2) DEFAULT NULL,
    `perc_icms`          DECIMAL(4, 4)  DEFAULT NULL,
    `perc_ipi`           DECIMAL(4, 4)  DEFAULT NULL,
    `perc_desc`          DECIMAL(4, 4)  DEFAULT NULL,
    `perc_issqn`         DECIMAL(4, 4)  DEFAULT NULL,
    `outros`             DECIMAL(14, 4) DEFAULT NULL,
    `ct`                 VARCHAR(3)     DEFAULT NULL,
    `entrada`            DATETIME       DEFAULT NULL,
    `flag01`             VARCHAR(1)     DEFAULT NULL,
    `flag02`             VARCHAR(1)     DEFAULT NULL,
    `flag03`             VARCHAR(1)     DEFAULT NULL,
    `flag04`             VARCHAR(1)     DEFAULT NULL,
    `flag05`             VARCHAR(1)     DEFAULT NULL,
    `flag06`             VARCHAR(1)     DEFAULT NULL,
    `flag07`             VARCHAR(1)     DEFAULT NULL,
    `flag08`             VARCHAR(1)     DEFAULT NULL,
    `flag09`             VARCHAR(1)     DEFAULT NULL,
    `flag10`             VARCHAR(1)     DEFAULT NULL,
    `field_30`           DECIMAL(12, 2) DEFAULT NULL,
    `perc_encargo`       DECIMAL(4, 4)  DEFAULT NULL,
    `perc_frete`         DECIMAL(4, 4)  DEFAULT NULL,
    `perc_margem`        DECIMAL(4, 4)  DEFAULT NULL,
    `prec_venda`         DECIMAL(8, 2)  DEFAULT NULL,
    `custo_anter`        DECIMAL(8, 2)  DEFAULT NULL,
    `custo_mercad`       DECIMAL(8, 2)  DEFAULT NULL,
    `custo_medatu`       DECIMAL(8, 2)  DEFAULT NULL,
    `prec_suger`         DECIMAL(8, 2)  DEFAULT NULL,
    `flag_atsuger`       VARCHAR(1)     DEFAULT NULL,
    `descricao`          VARCHAR(40)    DEFAULT NULL,
    `vlr_desconto`       DECIMAL(8, 2)  DEFAULT NULL,
    `vlr_icms`           DECIMAL(8, 2)  DEFAULT NULL,
    `vlr_ipi`            DECIMAL(8, 2)  DEFAULT NULL,
    `vlr_frete`          DECIMAL(8, 2)  DEFAULT NULL,
    `vlr_encargo`        DECIMAL(8, 2)  DEFAULT NULL,
    `vlr_issqn`          DECIMAL(8, 2)  DEFAULT NULL,
    `base_icms`          DECIMAL(8, 2)  DEFAULT NULL,
    `base_ipi`           DECIMAL(8, 2)  DEFAULT NULL,
    `base_issqn`         DECIMAL(8, 2)  DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_est_d081_corpo_nfentrada_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_est_d081_corpo_nfentrada_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_est_d081_corpo_nfentrada_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_est_d081_corpo_nfentrada_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_d081_corpo_nfentrada_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_est_d081_corpo_nfentrada_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;


DROP TABLE IF EXISTS `rdp_fat_d039_clivscfop`;
CREATE TABLE `rdp_fat_d039_clivscfop`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `codigo`             INT(11)       DEFAULT NULL,
    `tipo_nf`            VARCHAR(1)    DEFAULT NULL,
    `cfop`               VARCHAR(6)    DEFAULT NULL,
    `cst`                VARCHAR(3)    DEFAULT NULL,
    `ipi`                DECIMAL(4, 4) DEFAULT NULL,
    `icms_dentro`        DECIMAL(4, 4) DEFAULT NULL,
    `icms_fora`          DECIMAL(4, 4) DEFAULT NULL,
    `base_calculo`       VARCHAR(1)    DEFAULT NULL,
    `mva`                DECIMAL(4, 4) DEFAULT NULL,
    `partilha_dentro`    DECIMAL(4, 4) DEFAULT NULL,
    `obs`                VARCHAR(75)   DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_fat_d039_clivscfop_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_fat_d039_clivscfop_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_fat_d039_clivscfop_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_fat_d039_clivscfop_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d039_clivscfop_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d039_clivscfop_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_fat_d090_controle`;
CREATE TABLE `rdp_fat_d090_controle`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `campo1`             INT(11)     DEFAULT NULL,
    `campo2`             INT(11)     DEFAULT NULL,
    `campo3`             INT(11)     DEFAULT NULL,
    `campo4`             INT(11)     DEFAULT NULL,
    `campo5`             INT(11)     DEFAULT NULL,
    `obs1`               VARCHAR(50) DEFAULT NULL,
    `obs2`               VARCHAR(50) DEFAULT NULL,
    `oficinas`           INT(11)     DEFAULT NULL,
    `dias`               INT(11)     DEFAULT NULL,
    `email`              VARCHAR(40) DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_fat_d090_controle_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_fat_d090_controle_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_fat_d090_controle_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_fat_d090_controle_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d090_controle_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d090_controle_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_fat_d091_natureza`;
CREATE TABLE `rdp_fat_d091_natureza`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `codigo`             VARCHAR(3)    DEFAULT NULL,
    `digito`             VARCHAR(1)    DEFAULT NULL,
    `descricao`          VARCHAR(30)   DEFAULT NULL,
    `int01`              VARCHAR(1)    DEFAULT NULL,
    `int02`              VARCHAR(1)    DEFAULT NULL,
    `int03`              VARCHAR(1)    DEFAULT NULL,
    `int04`              VARCHAR(1)    DEFAULT NULL,
    `int05`              VARCHAR(1)    DEFAULT NULL,
    `sistema`            VARCHAR(1)    DEFAULT NULL,
    `ipi`                DECIMAL(4, 4) DEFAULT NULL,
    `icms`               DECIMAL(4, 4) DEFAULT NULL,
    `outros`             DECIMAL(4, 4) DEFAULT NULL,
    `substipi`           VARCHAR(1)    DEFAULT NULL,
    `substicm`           VARCHAR(1)    DEFAULT NULL,
    `substoutros`        VARCHAR(1)    DEFAULT NULL,
    `novo_codigo`        VARCHAR(7)    DEFAULT NULL,
    `tipo`               VARCHAR(1)    DEFAULT NULL,
    `instrucao1`         VARCHAR(75)   DEFAULT NULL,
    `instrucao2`         VARCHAR(75)   DEFAULT NULL,
    `instrucao3`         VARCHAR(75)   DEFAULT NULL,
    `novo_cst`           VARCHAR(3)    DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_fat_d091_natureza_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_fat_d091_natureza_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_fat_d091_natureza_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_fat_d091_natureza_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d091_natureza_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d091_natureza_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_fat_d092_condpag`;
CREATE TABLE `rdp_fat_d092_condpag`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `codigo`             INT(11)    NOT NULL,
    `descricao`          VARCHAR(30)    DEFAULT NULL,
    `data_base`          DATETIME       DEFAULT NULL,
    `num_pagtos`         INT(11)        DEFAULT NULL,
    `inter1`             INT(11)        DEFAULT NULL,
    `inter2`             INT(11)        DEFAULT NULL,
    `inter3`             INT(11)        DEFAULT NULL,
    `inter4`             INT(11)        DEFAULT NULL,
    `inter5`             INT(11)        DEFAULT NULL,
    `inter6`             INT(11)        DEFAULT NULL,
    `desconto`           DECIMAL(10, 2) DEFAULT NULL,
    `acrescimo`          DECIMAL(10, 2) DEFAULT NULL,
    `dia_base`           INT(11)        DEFAULT NULL,
    `flag01`             VARCHAR(1)     DEFAULT NULL,
    `flag02`             VARCHAR(1)     DEFAULT NULL,
    `dia_fixo`           INT(11)        DEFAULT NULL,
    `dia_fixo2`          VARCHAR(2)     DEFAULT NULL,
    `perc_comissao`      DECIMAL(10, 2) DEFAULT NULL,
    `integ_wle`          VARCHAR(1)     DEFAULT NULL,
    `perc_margem`        DECIMAL(4, 4)  DEFAULT NULL,
    UNIQUE KEY `codigo` (`codigo`),
    KEY `fat_d092_condpag_idx1` (`descricao`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_fat_d092_condpag_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_fat_d092_condpag_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_fat_d092_condpag_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_fat_d092_condpag_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d092_condpag_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d092_condpag_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  AUTO_INCREMENT = 3
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_fat_d093_nf`;
CREATE TABLE `rdp_fat_d093_nf`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `filial`             INT(11)        DEFAULT NULL,
    `numero_nf`          INT(11)        DEFAULT NULL,
    `emissao`            DATETIME       DEFAULT NULL,
    `natureza`           VARCHAR(3)     DEFAULT NULL,
    `digito`             VARCHAR(1)     DEFAULT NULL,
    `nome`               VARCHAR(70)    DEFAULT NULL,
    `ender`              VARCHAR(40)    DEFAULT NULL,
    `cidade`             VARCHAR(25)    DEFAULT NULL,
    `uf`                 VARCHAR(2)     DEFAULT NULL,
    `cep`                VARCHAR(9)     DEFAULT NULL,
    `cgc`                VARCHAR(20)    DEFAULT NULL,
    `insc`               VARCHAR(20)    DEFAULT NULL,
    `condicao_pagto`     INT(11)        DEFAULT NULL,
    `transporte`         VARCHAR(40)    DEFAULT NULL,
    `encargos`           DECIMAL(4, 4)  DEFAULT NULL,
    `descontos`          DECIMAL(4, 4)  DEFAULT NULL,
    `localizador`        INT(11)        DEFAULT NULL,
    `frete`              DECIMAL(8, 2)  DEFAULT NULL,
    `seguro`             DECIMAL(8, 2)  DEFAULT NULL,
    `sub_total`          DECIMAL(10, 2) DEFAULT NULL,
    `vlr_encargos`       DECIMAL(8, 2)  DEFAULT NULL,
    `total_nota`         DECIMAL(10, 2) DEFAULT NULL,
    `marca`              VARCHAR(20)    DEFAULT NULL,
    `numero`             VARCHAR(20)    DEFAULT NULL,
    `qtde`               DECIMAL(8, 2)  DEFAULT NULL,
    `especie`            VARCHAR(20)    DEFAULT NULL,
    `peso_bruto`         DECIMAL(8, 2)  DEFAULT NULL,
    `peso_liquido`       DECIMAL(8, 2)  DEFAULT NULL,
    `transportadora`     VARCHAR(40)    DEFAULT NULL,
    `ender_trans`        VARCHAR(40)    DEFAULT NULL,
    `cidade_trans`       VARCHAR(25)    DEFAULT NULL,
    `uf_trans`           VARCHAR(2)     DEFAULT NULL,
    `placa`              VARCHAR(8)     DEFAULT NULL,
    `vencto01`           DATETIME       DEFAULT NULL,
    `vencto02`           DATETIME       DEFAULT NULL,
    `vencto03`           DATETIME       DEFAULT NULL,
    `vencto04`           DATETIME       DEFAULT NULL,
    `vencto05`           DATETIME       DEFAULT NULL,
    `valor01`            DECIMAL(8, 2)  DEFAULT NULL,
    `valor02`            DECIMAL(8, 2)  DEFAULT NULL,
    `valor03`            DECIMAL(8, 2)  DEFAULT NULL,
    `valor04`            DECIMAL(8, 2)  DEFAULT NULL,
    `valor05`            DECIMAL(8, 2)  DEFAULT NULL,
    `cliente_conv`       VARCHAR(15)    DEFAULT NULL,
    `convenio`           INT(11)        DEFAULT NULL,
    `cliente_cts`        INT(11)        DEFAULT NULL,
    `flag_cance`         VARCHAR(1)     DEFAULT NULL,
    `vlr_desc`           DECIMAL(8, 2)  DEFAULT NULL,
    `vlr_juros`          DECIMAL(8, 2)  DEFAULT NULL,
    `vendedor`           INT(11)        DEFAULT NULL,
    `vlr_comissao`       DECIMAL(8, 2)  DEFAULT NULL,
    `lanc01`             INT(11)        DEFAULT NULL,
    `lanc02`             INT(11)        DEFAULT NULL,
    `lanc03`             INT(11)        DEFAULT NULL,
    `lanc04`             INT(11)        DEFAULT NULL,
    `lanc05`             INT(11)        DEFAULT NULL,
    `farmacia`           INT(11)        DEFAULT NULL,
    `hora`               INT(11)        DEFAULT NULL,
    `minuto`             INT(11)        DEFAULT NULL,
    `flag_marca`         VARCHAR(1)     DEFAULT NULL,
    `descserv`           VARCHAR(50)    DEFAULT NULL,
    `percserv`           DECIMAL(4, 2)  DEFAULT NULL,
    `vlrserv`            DECIMAL(8, 2)  DEFAULT NULL,
    `base_subst`         DECIMAL(8, 2)  DEFAULT NULL,
    `valo_subst`         DECIMAL(8, 2)  DEFAULT NULL,
    `valo_ipi`           DECIMAL(8, 2)  DEFAULT NULL,
    `obs1`               VARCHAR(70)    DEFAULT NULL,
    `obs2`               VARCHAR(70)    DEFAULT NULL,
    `obs3`               VARCHAR(70)    DEFAULT NULL,
    `obs4`               VARCHAR(70)    DEFAULT NULL,
    `obs5`               VARCHAR(70)    DEFAULT NULL,
    `obs6`               VARCHAR(70)    DEFAULT NULL,
    `mod_frete`          VARCHAR(1)     DEFAULT NULL,
    `tnome`              VARCHAR(40)    DEFAULT NULL,
    `tie`                VARCHAR(15)    DEFAULT NULL,
    `tend`               VARCHAR(60)    DEFAULT NULL,
    `tuf`                VARCHAR(2)     DEFAULT NULL,
    `tmunic`             VARCHAR(30)    DEFAULT NULL,
    `tcnpj`              VARCHAR(14)    DEFAULT NULL,
    `tcpf`               VARCHAR(11)    DEFAULT NULL,
    `vl_serv_frete`      DECIMAL(14, 2) DEFAULT NULL,
    `base_icms_frete`    DECIMAL(14, 2) DEFAULT NULL,
    `alq_icms_frete`     DECIMAL(14, 2) DEFAULT NULL,
    `vl_icms_frete`      DECIMAL(4, 2)  DEFAULT NULL,
    `tcfop`              VARCHAR(4)     DEFAULT NULL,
    `tcod_mun`           VARCHAR(7)     DEFAULT NULL,
    `tplaca`             VARCHAR(8)     DEFAULT NULL,
    `tplaca_uf`          VARCHAR(2)     DEFAULT NULL,
    `volume`             VARCHAR(20)    DEFAULT NULL,
    `nr_volume`          VARCHAR(30)    DEFAULT NULL,
    `peso_liq`           DECIMAL(14, 2) DEFAULT NULL,
    `peso_brt`           DECIMAL(14, 2) DEFAULT NULL,
    `nr_lacre`           VARCHAR(30)    DEFAULT NULL,
    `uf_embarque`        VARCHAR(2)     DEFAULT NULL,
    `local_embarque`     VARCHAR(50)    DEFAULT NULL,
    `tantt`              VARCHAR(20)    DEFAULT NULL,
    `nome_vend`          VARCHAR(35)    DEFAULT NULL,
    `pre_vend`           INT(11)        DEFAULT NULL,
    `num_fatura`         INT(11)        DEFAULT NULL,
    `vencto06`           DATETIME       DEFAULT NULL,
    `valor06`            DECIMAL(8, 2)  DEFAULT NULL,
    `lanc06`             INT(11)        DEFAULT NULL,
    `pagina`             INT(11)        DEFAULT NULL,
    `cond_pag_fatura`    INT(11)        DEFAULT NULL,
    `valorf1`            DECIMAL(6, 2)  DEFAULT NULL,
    `valorf2`            DECIMAL(6, 2)  DEFAULT NULL,
    `valorf3`            DECIMAL(6, 2)  DEFAULT NULL,
    `valorf4`            DECIMAL(6, 2)  DEFAULT NULL,
    `valorf5`            DECIMAL(6, 2)  DEFAULT NULL,
    `valorf6`            DECIMAL(6, 2)  DEFAULT NULL,
    `lf1`                INT(11)        DEFAULT NULL,
    `lf2`                INT(11)        DEFAULT NULL,
    `lf3`                INT(11)        DEFAULT NULL,
    `lf4`                INT(11)        DEFAULT NULL,
    `lf5`                INT(11)        DEFAULT NULL,
    `lf6`                INT(11)        DEFAULT NULL,
    `vcf1`               DATETIME       DEFAULT NULL,
    `vcf2`               DATETIME       DEFAULT NULL,
    `vcf3`               DATETIME       DEFAULT NULL,
    `vcf4`               DATETIME       DEFAULT NULL,
    `vcf5`               DATETIME       DEFAULT NULL,
    `vcf6`               DATETIME       DEFAULT NULL,
    `data_caixa`         DATETIME       DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_fat_d093_nf_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_fat_d093_nf_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_fat_d093_nf_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_fat_d093_nf_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d093_nf_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d093_nf_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_fat_d094_corpo`;
CREATE TABLE `rdp_fat_d094_corpo`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `num_nf`             INT(11)        DEFAULT NULL,
    `num_item`           INT(11)        DEFAULT NULL,
    `codigo`             VARCHAR(13)    DEFAULT NULL,
    `digito`             VARCHAR(1)     DEFAULT NULL,
    `filial`             INT(11)        DEFAULT NULL,
    `descricao`          VARCHAR(35)    DEFAULT NULL,
    `unidade`            VARCHAR(10)    DEFAULT NULL,
    `qtde`               DECIMAL(8, 4)  DEFAULT NULL,
    `preco`              DECIMAL(8, 2)  DEFAULT NULL,
    `total`              DECIMAL(10, 2) DEFAULT NULL,
    `icms`               DECIMAL(4, 4)  DEFAULT NULL,
    `vlr_desc`           DECIMAL(8, 2)  DEFAULT NULL,
    `per_desc`           DECIMAL(4, 2)  DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_fat_d094_corpo_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_fat_d094_corpo_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_fat_d094_corpo_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_fat_d094_corpo_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d094_corpo_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d094_corpo_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_fat_d095_vendedor`;
CREATE TABLE `rdp_fat_d095_vendedor`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `codigo`             INT(11)       DEFAULT NULL,
    `nome`               VARCHAR(30)   DEFAULT NULL,
    `comissao`           DECIMAL(2, 2) DEFAULT NULL,
    `desc_auto`          DECIMAL(2, 2) DEFAULT NULL,
    `obs`                VARCHAR(40)   DEFAULT NULL,
    `senha`              VARCHAR(5)    DEFAULT NULL,
    UNIQUE KEY `codigo` (`codigo`),
    KEY `fat_d095_vendedor_idx1` (`nome`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_fat_d095_vendedor_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_fat_d095_vendedor_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_fat_d095_vendedor_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_fat_d095_vendedor_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d095_vendedor_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d095_vendedor_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_fat_d101_oficina`;
CREATE TABLE `rdp_fat_d101_oficina`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `codigo`             INT(11)     DEFAULT NULL,
    `descricao`          VARCHAR(35) DEFAULT NULL,
    `obs`                VARCHAR(50) DEFAULT NULL,
    `fone`               VARCHAR(15) DEFAULT NULL,
    `contato`            VARCHAR(20) DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_fat_d101_oficina_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_fat_d101_oficina_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_fat_d101_oficina_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_fat_d101_oficina_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d101_oficina_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d101_oficina_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_fat_d103_comissao`;
CREATE TABLE `rdp_fat_d103_comissao`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `codigo`             INT(11)       DEFAULT NULL,
    `descricao`          VARCHAR(40)   DEFAULT NULL,
    `comissao`           DECIMAL(4, 2) DEFAULT NULL,
    `des_ped_abe`        DECIMAL(4, 2) DEFAULT NULL,
    `des_dia_casa`       DECIMAL(4, 2) DEFAULT NULL,
    `des_dia_cartao`     DECIMAL(4, 2) DEFAULT NULL,
    `des_dia_cheque`     DECIMAL(4, 2) DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_fat_d103_comissao_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_fat_d103_comissao_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_fat_d103_comissao_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_fat_d103_comissao_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d103_comissao_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d103_comissao_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_fat_d200_pv`;
CREATE TABLE `rdp_fat_d200_pv`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `filial`             INT(11)        DEFAULT NULL,
    `numero_nf`          INT(11)        DEFAULT NULL,
    `emissao`            DATETIME       DEFAULT NULL,
    `natureza`           VARCHAR(3)     DEFAULT NULL,
    `digito`             VARCHAR(1)     DEFAULT NULL,
    `nome`               VARCHAR(70)    DEFAULT NULL,
    `ender`              VARCHAR(40)    DEFAULT NULL,
    `cidade`             VARCHAR(25)    DEFAULT NULL,
    `uf`                 VARCHAR(2)     DEFAULT NULL,
    `cep`                VARCHAR(9)     DEFAULT NULL,
    `cgc`                VARCHAR(20)    DEFAULT NULL,
    `insc`               VARCHAR(20)    DEFAULT NULL,
    `condicao_pagto`     INT(11)        DEFAULT NULL,
    `transporte`         VARCHAR(40)    DEFAULT NULL,
    `encargos`           DECIMAL(4, 4)  DEFAULT NULL,
    `descontos`          DECIMAL(4, 4)  DEFAULT NULL,
    `localizador`        INT(11)        DEFAULT NULL,
    `frete`              DECIMAL(8, 2)  DEFAULT NULL,
    `seguro`             DECIMAL(8, 2)  DEFAULT NULL,
    `sub_total`          DECIMAL(10, 2) DEFAULT NULL,
    `vlr_encargos`       DECIMAL(8, 2)  DEFAULT NULL,
    `total_nota`         DECIMAL(10, 2) DEFAULT NULL,
    `marca`              VARCHAR(20)    DEFAULT NULL,
    `numero`             VARCHAR(20)    DEFAULT NULL,
    `qtde`               DECIMAL(8, 2)  DEFAULT NULL,
    `especie`            VARCHAR(20)    DEFAULT NULL,
    `peso_bruto`         DECIMAL(8, 2)  DEFAULT NULL,
    `peso_liquido`       DECIMAL(8, 2)  DEFAULT NULL,
    `transportadora`     VARCHAR(40)    DEFAULT NULL,
    `ender_trans`        VARCHAR(40)    DEFAULT NULL,
    `cidade_trans`       VARCHAR(25)    DEFAULT NULL,
    `uf_trans`           VARCHAR(2)     DEFAULT NULL,
    `placa`              VARCHAR(8)     DEFAULT NULL,
    `vencto01`           DATETIME       DEFAULT NULL,
    `vencto02`           DATETIME       DEFAULT NULL,
    `vencto03`           DATETIME       DEFAULT NULL,
    `vencto04`           DATETIME       DEFAULT NULL,
    `vencto05`           DATETIME       DEFAULT NULL,
    `valor01`            DECIMAL(8, 2)  DEFAULT NULL,
    `valor02`            DECIMAL(8, 2)  DEFAULT NULL,
    `valor03`            DECIMAL(8, 2)  DEFAULT NULL,
    `valor04`            DECIMAL(8, 2)  DEFAULT NULL,
    `valor05`            DECIMAL(8, 2)  DEFAULT NULL,
    `cliente_conv`       VARCHAR(15)    DEFAULT NULL,
    `convenio`           INT(11)        DEFAULT NULL,
    `cliente_cts`        INT(11)        DEFAULT NULL,
    `flag_cance`         VARCHAR(1)     DEFAULT NULL,
    `vlr_desc`           DECIMAL(8, 2)  DEFAULT NULL,
    `vlr_juros`          DECIMAL(8, 2)  DEFAULT NULL,
    `vendedor`           INT(11)        DEFAULT NULL,
    `vlr_comissao`       DECIMAL(8, 2)  DEFAULT NULL,
    `lanc01`             INT(11)        DEFAULT NULL,
    `lanc02`             INT(11)        DEFAULT NULL,
    `lanc03`             INT(11)        DEFAULT NULL,
    `lanc04`             INT(11)        DEFAULT NULL,
    `lanc05`             INT(11)        DEFAULT NULL,
    `farmacia`           INT(11)        DEFAULT NULL,
    `hora`               INT(11)        DEFAULT NULL,
    `minuto`             INT(11)        DEFAULT NULL,
    `flag_marca`         VARCHAR(1)     DEFAULT NULL,
    `flag_be`            VARCHAR(1)     DEFAULT NULL,
    `flag_cr`            VARCHAR(1)     DEFAULT NULL,
    `flag_nf`            VARCHAR(1)     DEFAULT NULL,
    `veiculo`            VARCHAR(50)    DEFAULT NULL,
    `oficina`            INT(11)        DEFAULT NULL,
    `vencto06`           DATETIME       DEFAULT NULL,
    `valor06`            DECIMAL(8, 2)  DEFAULT NULL,
    `lanc06`             INT(11)        DEFAULT NULL,
    `flag_fec`           VARCHAR(1)     DEFAULT NULL,
    `flag_bxa_ger`       VARCHAR(1)     DEFAULT NULL,
    `data_lc`            DATETIME       DEFAULT NULL,
    `hora_lc`            INT(11)        DEFAULT NULL,
    `minu_lc`            INT(11)        DEFAULT NULL,
    `dta_trf`            DATETIME       DEFAULT NULL,
    `os`                 INT(11)        DEFAULT NULL,
    `vendedor_ext`       INT(11)        DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_fat_d200_pv_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_fat_d200_pv_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_fat_d200_pv_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_fat_d200_pv_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d200_pv_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d200_pv_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_fat_d201_corpo`;
CREATE TABLE `rdp_fat_d201_corpo`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `num_nf`             INT(11)        DEFAULT NULL,
    `num_item`           INT(11)        DEFAULT NULL,
    `codigo`             VARCHAR(13)    DEFAULT NULL,
    `digito`             VARCHAR(1)     DEFAULT NULL,
    `filial`             INT(11)        DEFAULT NULL,
    `descricao`          VARCHAR(35)    DEFAULT NULL,
    `unidade`            VARCHAR(10)    DEFAULT NULL,
    `qtde`               DECIMAL(8, 4)  DEFAULT NULL,
    `preco`              DECIMAL(8, 2)  DEFAULT NULL,
    `total`              DECIMAL(10, 2) DEFAULT NULL,
    `icms`               DECIMAL(4, 4)  DEFAULT NULL,
    `flag_baixa`         VARCHAR(1)     DEFAULT NULL,
    `placa`              VARCHAR(10)    DEFAULT NULL,
    `preco_cst`          DECIMAL(8, 2)  DEFAULT NULL,
    `preco_ven`          DECIMAL(8, 2)  DEFAULT NULL,
    `preco_cad`          DECIMAL(8, 2)  DEFAULT NULL,
    `data_lc`            DATETIME       DEFAULT NULL,
    `hora_lc`            INT(11)        DEFAULT NULL,
    `minu_lc`            INT(11)        DEFAULT NULL,
    `local`              VARCHAR(15)    DEFAULT NULL,
    `identificador`      INT(11)        DEFAULT NULL,
    `qtde_dep`           DECIMAL(4, 2)  DEFAULT NULL,
    `preco_orc`          DECIMAL(6, 2)  DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_fat_d201_corpo_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_fat_d201_corpo_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_fat_d201_corpo_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_fat_d201_corpo_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d201_corpo_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d201_corpo_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_fat_d300`;
CREATE TABLE `rdp_fat_d300`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `filial`             INT(11)        DEFAULT NULL,
    `numero_nf`          INT(11)        DEFAULT NULL,
    `emissao`            DATETIME       DEFAULT NULL,
    `natureza`           VARCHAR(3)     DEFAULT NULL,
    `digito`             VARCHAR(1)     DEFAULT NULL,
    `nome`               VARCHAR(70)    DEFAULT NULL,
    `ender`              VARCHAR(40)    DEFAULT NULL,
    `cidade`             VARCHAR(25)    DEFAULT NULL,
    `uf`                 VARCHAR(2)     DEFAULT NULL,
    `cep`                VARCHAR(9)     DEFAULT NULL,
    `cgc`                VARCHAR(20)    DEFAULT NULL,
    `insc`               VARCHAR(20)    DEFAULT NULL,
    `condicao_pagto`     INT(11)        DEFAULT NULL,
    `transporte`         VARCHAR(40)    DEFAULT NULL,
    `encargos`           DECIMAL(4, 4)  DEFAULT NULL,
    `descontos`          DECIMAL(4, 4)  DEFAULT NULL,
    `localizador`        INT(11)        DEFAULT NULL,
    `frete`              DECIMAL(8, 2)  DEFAULT NULL,
    `seguro`             DECIMAL(8, 2)  DEFAULT NULL,
    `sub_total`          DECIMAL(10, 2) DEFAULT NULL,
    `vlr_encargos`       DECIMAL(8, 2)  DEFAULT NULL,
    `total_nota`         DECIMAL(10, 2) DEFAULT NULL,
    `marca`              VARCHAR(20)    DEFAULT NULL,
    `numero`             VARCHAR(20)    DEFAULT NULL,
    `qtde`               DECIMAL(8, 2)  DEFAULT NULL,
    `especie`            VARCHAR(20)    DEFAULT NULL,
    `peso_bruto`         DECIMAL(8, 2)  DEFAULT NULL,
    `peso_liquido`       DECIMAL(8, 2)  DEFAULT NULL,
    `transportadora`     VARCHAR(40)    DEFAULT NULL,
    `ender_trans`        VARCHAR(40)    DEFAULT NULL,
    `cidade_trans`       VARCHAR(25)    DEFAULT NULL,
    `uf_trans`           VARCHAR(2)     DEFAULT NULL,
    `placa`              VARCHAR(8)     DEFAULT NULL,
    `vencto01`           DATETIME       DEFAULT NULL,
    `vencto02`           DATETIME       DEFAULT NULL,
    `vencto03`           DATETIME       DEFAULT NULL,
    `vencto04`           DATETIME       DEFAULT NULL,
    `vencto05`           DATETIME       DEFAULT NULL,
    `valor01`            DECIMAL(8, 2)  DEFAULT NULL,
    `valor02`            DECIMAL(8, 2)  DEFAULT NULL,
    `valor03`            DECIMAL(8, 2)  DEFAULT NULL,
    `valor04`            DECIMAL(8, 2)  DEFAULT NULL,
    `valor05`            DECIMAL(8, 2)  DEFAULT NULL,
    `cliente_conv`       VARCHAR(15)    DEFAULT NULL,
    `convenio`           INT(11)        DEFAULT NULL,
    `cliente_cts`        INT(11)        DEFAULT NULL,
    `flag_cance`         VARCHAR(1)     DEFAULT NULL,
    `vlr_desc`           DECIMAL(8, 2)  DEFAULT NULL,
    `vlr_juros`          DECIMAL(8, 2)  DEFAULT NULL,
    `vendedor`           INT(11)        DEFAULT NULL,
    `vlr_comissao`       DECIMAL(8, 2)  DEFAULT NULL,
    `lanc01`             INT(11)        DEFAULT NULL,
    `lanc02`             INT(11)        DEFAULT NULL,
    `lanc03`             INT(11)        DEFAULT NULL,
    `lanc04`             INT(11)        DEFAULT NULL,
    `lanc05`             INT(11)        DEFAULT NULL,
    `farmacia`           INT(11)        DEFAULT NULL,
    `hora`               INT(11)        DEFAULT NULL,
    `minuto`             INT(11)        DEFAULT NULL,
    `flag_marca`         VARCHAR(1)     DEFAULT NULL,
    `flag_be`            VARCHAR(1)     DEFAULT NULL,
    `flag_cr`            VARCHAR(1)     DEFAULT NULL,
    `flag_nf`            VARCHAR(1)     DEFAULT NULL,
    `veiculo`            VARCHAR(50)    DEFAULT NULL,
    `oficina`            INT(11)        DEFAULT NULL,
    `vencto06`           DATETIME       DEFAULT NULL,
    `valor06`            DECIMAL(8, 2)  DEFAULT NULL,
    `lanc06`             INT(11)        DEFAULT NULL,
    `flag_fec`           VARCHAR(1)     DEFAULT NULL,
    `prev_entrega`       DATETIME       DEFAULT NULL,
    `out_fpagto`         VARCHAR(30)    DEFAULT NULL,
    `obs01`              VARCHAR(75)    DEFAULT NULL,
    `obs02`              VARCHAR(75)    DEFAULT NULL,
    `obs03`              VARCHAR(75)    DEFAULT NULL,
    `obs04`              VARCHAR(75)    DEFAULT NULL,
    `obs05`              VARCHAR(75)    DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_fat_d300_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_fat_d300_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_fat_d300_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_fat_d300_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d300_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d300_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_fat_d301`;
CREATE TABLE `rdp_fat_d301`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `num_nf`             INT(11)        DEFAULT NULL,
    `num_item`           INT(11)        DEFAULT NULL,
    `codigo`             VARCHAR(13)    DEFAULT NULL,
    `digito`             VARCHAR(1)     DEFAULT NULL,
    `filial`             INT(11)        DEFAULT NULL,
    `descricao`          VARCHAR(35)    DEFAULT NULL,
    `unidade`            VARCHAR(10)    DEFAULT NULL,
    `qtde`               DECIMAL(8, 4)  DEFAULT NULL,
    `preco`              DECIMAL(8, 2)  DEFAULT NULL,
    `total`              DECIMAL(10, 2) DEFAULT NULL,
    `icms`               DECIMAL(4, 4)  DEFAULT NULL,
    `flag_baixa`         VARCHAR(1)     DEFAULT NULL,
    `placa`              VARCHAR(50)    DEFAULT NULL,
    `cod_forn`           INT(11)        DEFAULT NULL,
    `emissao`            DATETIME       DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_fat_d301_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_fat_d301_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_fat_d301_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_fat_d301_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d301_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_d301_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_fat_dtri`;
CREATE TABLE `rdp_fat_dtri`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `codigo`             INT(11)       DEFAULT NULL,
    `operacao`           INT(11)       DEFAULT NULL,
    `ufo`                VARCHAR(2)    DEFAULT NULL,
    `ufd`                VARCHAR(2)    DEFAULT NULL,
    `descricao`          VARCHAR(40)   DEFAULT NULL,
    `ncm`                VARCHAR(8)    DEFAULT NULL,
    `sit1`               VARCHAR(1)    DEFAULT NULL,
    `sit2`               VARCHAR(1)    DEFAULT NULL,
    `sit3`               VARCHAR(1)    DEFAULT NULL,
    `sit4`               VARCHAR(1)    DEFAULT NULL,
    `sit5`               VARCHAR(1)    DEFAULT NULL,
    `red1`               DECIMAL(2, 2) DEFAULT NULL,
    `red2`               DECIMAL(2, 2) DEFAULT NULL,
    `red3`               DECIMAL(2, 2) DEFAULT NULL,
    `red4`               DECIMAL(2, 2) DEFAULT NULL,
    `red5`               DECIMAL(2, 2) DEFAULT NULL,
    `ali1`               DECIMAL(2, 2) DEFAULT NULL,
    `ali2`               DECIMAL(2, 2) DEFAULT NULL,
    `ali3`               DECIMAL(2, 2) DEFAULT NULL,
    `ali4`               DECIMAL(2, 2) DEFAULT NULL,
    `ali5`               DECIMAL(2, 2) DEFAULT NULL,
    `cst1`               VARCHAR(4)    DEFAULT NULL,
    `cst2`               VARCHAR(4)    DEFAULT NULL,
    `cst3`               VARCHAR(4)    DEFAULT NULL,
    `cst4`               VARCHAR(4)    DEFAULT NULL,
    `cst5`               VARCHAR(4)    DEFAULT NULL,
    `cfo1`               VARCHAR(5)    DEFAULT NULL,
    `cfo2`               VARCHAR(5)    DEFAULT NULL,
    `cfo3`               VARCHAR(5)    DEFAULT NULL,
    `cfo4`               VARCHAR(5)    DEFAULT NULL,
    `cfo5`               VARCHAR(5)    DEFAULT NULL,
    `for1`               VARCHAR(40)   DEFAULT NULL,
    `for2`               VARCHAR(40)   DEFAULT NULL,
    `for3`               VARCHAR(40)   DEFAULT NULL,
    `for4`               VARCHAR(40)   DEFAULT NULL,
    `for5`               VARCHAR(40)   DEFAULT NULL,
    `usuario`            VARCHAR(10)   DEFAULT NULL,
    `data`               DATETIME      DEFAULT NULL,
    `hh`                 INT(11)       DEFAULT NULL,
    `mm`                 INT(11)       DEFAULT NULL,
    `ss`                 INT(11)       DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_fat_dtri_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_fat_dtri_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_fat_dtri_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_fat_dtri_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_dtri_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_dtri_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_fat_x200_pvb`;
CREATE TABLE `rdp_fat_x200_pvb`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `filial`             INT(11)        DEFAULT NULL,
    `numero_nf`          INT(11)        DEFAULT NULL,
    `emissao`            DATETIME       DEFAULT NULL,
    `natureza`           VARCHAR(3)     DEFAULT NULL,
    `digito`             VARCHAR(1)     DEFAULT NULL,
    `nome`               VARCHAR(70)    DEFAULT NULL,
    `ender`              VARCHAR(40)    DEFAULT NULL,
    `cidade`             VARCHAR(25)    DEFAULT NULL,
    `uf`                 VARCHAR(2)     DEFAULT NULL,
    `cep`                VARCHAR(9)     DEFAULT NULL,
    `cgc`                VARCHAR(20)    DEFAULT NULL,
    `insc`               VARCHAR(20)    DEFAULT NULL,
    `condicao_pagto`     INT(11)        DEFAULT NULL,
    `transporte`         VARCHAR(40)    DEFAULT NULL,
    `encargos`           DECIMAL(4, 4)  DEFAULT NULL,
    `descontos`          DECIMAL(4, 4)  DEFAULT NULL,
    `localizador`        INT(11)        DEFAULT NULL,
    `frete`              DECIMAL(8, 2)  DEFAULT NULL,
    `seguro`             DECIMAL(8, 2)  DEFAULT NULL,
    `sub_total`          DECIMAL(10, 2) DEFAULT NULL,
    `vlr_encargos`       DECIMAL(8, 2)  DEFAULT NULL,
    `total_nota`         DECIMAL(10, 2) DEFAULT NULL,
    `marca`              VARCHAR(20)    DEFAULT NULL,
    `numero`             VARCHAR(20)    DEFAULT NULL,
    `qtde`               DECIMAL(8, 2)  DEFAULT NULL,
    `especie`            VARCHAR(20)    DEFAULT NULL,
    `peso_bruto`         DECIMAL(8, 2)  DEFAULT NULL,
    `peso_liquido`       DECIMAL(8, 2)  DEFAULT NULL,
    `transportadora`     VARCHAR(40)    DEFAULT NULL,
    `ender_trans`        VARCHAR(40)    DEFAULT NULL,
    `cidade_trans`       VARCHAR(25)    DEFAULT NULL,
    `uf_trans`           VARCHAR(2)     DEFAULT NULL,
    `placa`              VARCHAR(8)     DEFAULT NULL,
    `vencto01`           DATETIME       DEFAULT NULL,
    `vencto02`           DATETIME       DEFAULT NULL,
    `vencto03`           DATETIME       DEFAULT NULL,
    `vencto04`           DATETIME       DEFAULT NULL,
    `vencto05`           DATETIME       DEFAULT NULL,
    `valor01`            DECIMAL(8, 2)  DEFAULT NULL,
    `valor02`            DECIMAL(8, 2)  DEFAULT NULL,
    `valor03`            DECIMAL(8, 2)  DEFAULT NULL,
    `valor04`            DECIMAL(8, 2)  DEFAULT NULL,
    `valor05`            DECIMAL(8, 2)  DEFAULT NULL,
    `cliente_conv`       VARCHAR(15)    DEFAULT NULL,
    `convenio`           INT(11)        DEFAULT NULL,
    `cliente_cts`        INT(11)        DEFAULT NULL,
    `flag_cance`         VARCHAR(1)     DEFAULT NULL,
    `vlr_desc`           DECIMAL(8, 2)  DEFAULT NULL,
    `vlr_juros`          DECIMAL(8, 2)  DEFAULT NULL,
    `vendedor`           INT(11)        DEFAULT NULL,
    `vlr_comissao`       DECIMAL(8, 2)  DEFAULT NULL,
    `lanc01`             INT(11)        DEFAULT NULL,
    `lanc02`             INT(11)        DEFAULT NULL,
    `lanc03`             INT(11)        DEFAULT NULL,
    `lanc04`             INT(11)        DEFAULT NULL,
    `lanc05`             INT(11)        DEFAULT NULL,
    `farmacia`           INT(11)        DEFAULT NULL,
    `hora`               INT(11)        DEFAULT NULL,
    `minuto`             INT(11)        DEFAULT NULL,
    `flag_marca`         VARCHAR(1)     DEFAULT NULL,
    `flag_be`            VARCHAR(1)     DEFAULT NULL,
    `flag_cr`            VARCHAR(1)     DEFAULT NULL,
    `flag_nf`            VARCHAR(1)     DEFAULT NULL,
    `veiculo`            VARCHAR(50)    DEFAULT NULL,
    `oficina`            INT(11)        DEFAULT NULL,
    `vencto06`           DATETIME       DEFAULT NULL,
    `valor06`            DECIMAL(8, 2)  DEFAULT NULL,
    `lanc06`             INT(11)        DEFAULT NULL,
    `flag_fec`           VARCHAR(1)     DEFAULT NULL,
    `flag_bxa_ger`       VARCHAR(1)     DEFAULT NULL,
    `data_lc`            DATETIME       DEFAULT NULL,
    `hora_lc`            INT(11)        DEFAULT NULL,
    `minu_lc`            INT(11)        DEFAULT NULL,
    `dta_trf`            DATETIME       DEFAULT NULL,
    `os`                 INT(11)        DEFAULT NULL,
    `vendedor_ext`       INT(11)        DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_fat_x200_pvb_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_fat_x200_pvb_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_fat_x200_pvb_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_fat_x200_pvb_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_x200_pvb_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_x200_pvb_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_fat_x201_corpob`;
CREATE TABLE `rdp_fat_x201_corpob`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `num_nf`             INT(11)        DEFAULT NULL,
    `num_item`           INT(11)        DEFAULT NULL,
    `codigo`             VARCHAR(13)    DEFAULT NULL,
    `digito`             VARCHAR(1)     DEFAULT NULL,
    `filial`             INT(11)        DEFAULT NULL,
    `descricao`          VARCHAR(35)    DEFAULT NULL,
    `unidade`            VARCHAR(10)    DEFAULT NULL,
    `qtde`               DECIMAL(8, 4)  DEFAULT NULL,
    `preco`              DECIMAL(8, 2)  DEFAULT NULL,
    `total`              DECIMAL(10, 2) DEFAULT NULL,
    `icms`               DECIMAL(4, 4)  DEFAULT NULL,
    `flag_baixa`         VARCHAR(1)     DEFAULT NULL,
    `placa`              VARCHAR(10)    DEFAULT NULL,
    `preco_cst`          DECIMAL(8, 2)  DEFAULT NULL,
    `preco_ven`          DECIMAL(8, 2)  DEFAULT NULL,
    `preco_cad`          DECIMAL(8, 2)  DEFAULT NULL,
    `data_lc`            DATETIME       DEFAULT NULL,
    `hora_lc`            INT(11)        DEFAULT NULL,
    `minu_lc`            INT(11)        DEFAULT NULL,
    `local`              VARCHAR(15)    DEFAULT NULL,
    `identificador`      INT(11)        DEFAULT NULL,
    `qtde_dep`           DECIMAL(4, 2)  DEFAULT NULL,
    `preco_orc`          DECIMAL(6, 2)  DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_fat_x201_corpob_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_fat_x201_corpob_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_fat_x201_corpob_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_fat_x201_corpob_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_x201_corpob_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_fat_x201_corpob_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_ibg_d001_codmunicipio`;
CREATE TABLE `rdp_ibg_d001_codmunicipio`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `cod_munic`          INT(11)     DEFAULT NULL,
    `municipio`          VARCHAR(50) DEFAULT NULL,
    `estado`             VARCHAR(20) DEFAULT NULL,
    `uf`                 VARCHAR(2)  DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_ibg_d001_codmunicipio_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_ibg_d001_codmunicipio_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_ibg_d001_codmunicipio_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_ibg_d001_codmunicipio_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_ibg_d001_codmunicipio_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_ibg_d001_codmunicipio_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_mot_d053`;
CREATE TABLE `rdp_mot_d053`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `lancto`             INT(11)       DEFAULT NULL,
    `prevenda`           INT(11)       DEFAULT NULL,
    `data`               DATETIME      DEFAULT NULL,
    `hh`                 INT(11)       DEFAULT NULL,
    `mm`                 INT(11)       DEFAULT NULL,
    `motoqueiro`         INT(11)       DEFAULT NULL,
    `flag_entrega`       VARCHAR(1)    DEFAULT NULL,
    `flag_pagto`         VARCHAR(1)    DEFAULT NULL,
    `data_pagto`         DATETIME      DEFAULT NULL,
    `valor_pagto`        DECIMAL(6, 2) DEFAULT NULL,
    `codigo_data`        INT(11)       DEFAULT NULL,
    `nome`               VARCHAR(30)   DEFAULT NULL,
    `usuario`            VARCHAR(20)   DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_mot_d053_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_mot_d053_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_mot_d053_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_mot_d053_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_mot_d053_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_mot_d053_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_mot_d064`;
CREATE TABLE `rdp_mot_d064`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `codigo`             INT(11)     DEFAULT NULL,
    `nome`               VARCHAR(40) DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_mot_d064_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_mot_d064_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_mot_d064_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_mot_d064_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_mot_d064_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_mot_d064_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;


DROP TABLE IF EXISTS `rdp_pes_d054_pesquisa`;
CREATE TABLE `rdp_pes_d054_pesquisa`
(
    `id`                 BIGINT(20) NOT NULL,
    `cliente_id`         BIGINT(20) NOT NULL,
    `q1_1`               VARCHAR(1)  DEFAULT NULL,
    `q1_2`               VARCHAR(1)  DEFAULT NULL,
    `q1_3`               VARCHAR(1)  DEFAULT NULL,
    `q2_1`               VARCHAR(1)  DEFAULT NULL,
    `q2_2`               VARCHAR(1)  DEFAULT NULL,
    `q2_3`               VARCHAR(1)  DEFAULT NULL,
    `q2_4`               VARCHAR(1)  DEFAULT NULL,
    `q3_1`               VARCHAR(1)  DEFAULT NULL,
    `q3_2`               VARCHAR(1)  DEFAULT NULL,
    `q3_3`               VARCHAR(1)  DEFAULT NULL,
    `q3_4`               VARCHAR(1)  DEFAULT NULL,
    `q3_5`               VARCHAR(1)  DEFAULT NULL,
    `q4_1`               VARCHAR(1)  DEFAULT NULL,
    `q4_2`               VARCHAR(1)  DEFAULT NULL,
    `q4_3`               VARCHAR(1)  DEFAULT NULL,
    `q4_4`               VARCHAR(1)  DEFAULT NULL,
    `q4_5`               VARCHAR(1)  DEFAULT NULL,
    `q4_6`               VARCHAR(1)  DEFAULT NULL,
    `q5_1`               VARCHAR(1)  DEFAULT NULL,
    `q5_2`               VARCHAR(1)  DEFAULT NULL,
    `s1`                 VARCHAR(80) DEFAULT NULL,
    `s2`                 VARCHAR(80) DEFAULT NULL,
    `s3`                 VARCHAR(80) DEFAULT NULL,
    `q6_1`               VARCHAR(1)  DEFAULT NULL,
    `q6_2`               VARCHAR(1)  DEFAULT NULL,
    `whatsapp`           VARCHAR(12) DEFAULT NULL,
    `email`              VARCHAR(80) DEFAULT NULL,
    `q4_7`               VARCHAR(1)  DEFAULT NULL,
    UNIQUE KEY `cliente` (`cliente_id`),
    CONSTRAINT `pes_d054_pesquisa_fk1` FOREIGN KEY (`cliente_id`) references `cts_d002_cliente` (`id`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_pes_d054_pesquisa_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_pes_d054_pesquisa_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_pes_d054_pesquisa_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_pes_d054_pesquisa_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_pes_d054_pesquisa_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_pes_d054_pesquisa_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_veiculos`;
CREATE TABLE `rdp_veiculos`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `placa`              VARCHAR(8)     DEFAULT NULL,
    `renavam`            VARCHAR(20)    DEFAULT NULL,
    `ano`                INT(11)        DEFAULT NULL,
    `valor`              DECIMAL(10, 2) DEFAULT NULL,


    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_veiculos_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_veiculos_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_veiculos_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_veiculos_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_veiculos_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_veiculos_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1;



DROP TABLE IF EXISTS `rdp_web_cliente`;
CREATE TABLE `rdp_web_cliente`
(
    `id`                          BIGINT(20)  NOT NULL AUTO_INCREMENT,
    `codigo`                      INT(11)     NOT NULL,
    `cliente`                     VARCHAR(40) NOT NULL,
    `cpf_cnpj`                    VARCHAR(14)    DEFAULT NULL,
    `cidade`                      VARCHAR(30)    DEFAULT NULL,
    `uf`                          VARCHAR(2)     DEFAULT NULL,
    `data`                        DATETIME       DEFAULT NULL,
    `flag_cliente_bloqueado`      VARCHAR(1)     DEFAULT null comment 'branco ou b de bloqueado',
    `flag_desbloqueio_temporario` VARCHAR(1)     DEFAULT NULL,
    `flag_libera_preco_custo`     VARCHAR(1)     DEFAULT NULL,
    `flag_bloqueio`               VARCHAR(1)     DEFAULT NULL,
    `flag_lib_prazo`              VARCHAR(1)     DEFAULT NULL,
    `flag_spc`                    VARCHAR(1)     DEFAULT NULL,
    `flag_cheque_dev`             VARCHAR(1)     DEFAULT NULL,
    `dias_max_faturamento`        INT(4)         DEFAULT NULL,
    `margem_especial`             DECIMAL(4, 2)  DEFAULT NULL,
    `perc_margem`                 DECIMAL(11, 2) DEFAULT NULL,
    `lim_compras`                 DECIMAL(11, 2) DEFAULT NULL,
    `cts_pagar_aberto`            DECIMAL(11, 2) DEFAULT NULL,
    UNIQUE KEY `codigo` (`codigo`),
    KEY `web_cliente_idx1` (`cliente`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id`          BIGINT(20)  NOT NULL,
    `inserted`                    DATETIME    NOT NULL,
    `updated`                     DATETIME    NOT NULL,
    `user_inserted_id`            BIGINT(20)  NOT NULL,
    `user_updated_id`             BIGINT(20)  NOT NULL,
    KEY `k_rdp_web_cliente_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_web_cliente_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_web_cliente_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_web_cliente_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_web_cliente_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_web_cliente_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  AUTO_INCREMENT = 2
  DEFAULT charset = latin1
  pack_keys = 0;



DROP TABLE IF EXISTS `rdp_web_condpag`;
CREATE TABLE `rdp_web_condpag`
(
    `id`                    BIGINT(20) NOT NULL,
    `codigo`                INT(11)    NOT NULL,
    `desc_condpag`          VARCHAR(40)    DEFAULT NULL,
    `num_parcelas`          INT(11)        DEFAULT NULL,
    `dias_primeiro_pagto`   INT(11)        DEFAULT NULL,
    `dias_entre_pagamentos` INT(11)        DEFAULT NULL,
    `juros`                 DECIMAL(11, 2) DEFAULT NULL,
    `descontos`             DECIMAL(11, 2) DEFAULT NULL,
    UNIQUE KEY `codigo` (`codigo`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id`    BIGINT(20) NOT NULL,
    `inserted`              DATETIME   NOT NULL,
    `updated`               DATETIME   NOT NULL,
    `user_inserted_id`      BIGINT(20) NOT NULL,
    `user_updated_id`       BIGINT(20) NOT NULL,
    KEY `k_rdp_web_condpag_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_web_condpag_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_web_condpag_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_web_condpag_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_web_condpag_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_web_condpag_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1
  pack_keys = 0;



DROP TABLE IF EXISTS `rdp_web_deposito`;
CREATE TABLE `rdp_web_deposito`
(
    `id`                 BIGINT(20)  NOT NULL AUTO_INCREMENT,
    `codigo`             INT(11)     NOT NULL,
    `deposito`           VARCHAR(30) NOT NULL,
    UNIQUE KEY `codigo` (`codigo`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20)  NOT NULL,
    `inserted`           DATETIME    NOT NULL,
    `updated`            DATETIME    NOT NULL,
    `user_inserted_id`   BIGINT(20)  NOT NULL,
    `user_updated_id`    BIGINT(20)  NOT NULL,
    KEY `k_rdp_web_deposito_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_web_deposito_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_web_deposito_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_web_deposito_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_web_deposito_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_web_deposito_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  AUTO_INCREMENT = 5
  DEFAULT charset = latin1
  pack_keys = 0;



DROP TABLE IF EXISTS `rdp_web_edi`;
CREATE TABLE `rdp_web_edi`
(
    `id`                 BIGINT(20) NOT NULL,
    `codigo_edi`         VARCHAR(20) DEFAULT NULL,
    `codigo_produto`     VARCHAR(13) DEFAULT NULL,
    UNIQUE KEY `codigo_edi` (`codigo_edi`),
    KEY `web_edi_idx1` (`codigo_produto`),
    CONSTRAINT `web_edi_fk1` FOREIGN KEY (`codigo_produto`) references `web_produto` (`codigo`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_web_edi_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_web_edi_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_web_edi_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_web_edi_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_web_edi_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_web_edi_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1
  pack_keys = 0;



DROP TABLE IF EXISTS `rdp_web_localizador`;
CREATE TABLE `rdp_web_localizador`
(
    `id`                 BIGINT(20) NOT NULL,
    `codigo`             INT(11)     DEFAULT NULL,
    `localizador`        VARCHAR(30) DEFAULT NULL,
    UNIQUE KEY `codigo` (`codigo`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_web_localizador_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_web_localizador_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_web_localizador_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_web_localizador_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_web_localizador_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_web_localizador_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1
  pack_keys = 0;



DROP TABLE IF EXISTS `rdp_web_oficina`;
CREATE TABLE `rdp_web_oficina`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `codigo`             INT(11)    NOT NULL,
    `oficina`            VARCHAR(30) DEFAULT NULL,
    UNIQUE KEY `codigo` (`codigo`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_web_oficina_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_web_oficina_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_web_oficina_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_web_oficina_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_web_oficina_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_web_oficina_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  AUTO_INCREMENT = 4
  DEFAULT charset = latin1
  pack_keys = 0;



DROP TABLE IF EXISTS `rdp_web_parametro`;
CREATE TABLE `rdp_web_parametro`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `limite_desconto`    DECIMAL(11, 2) DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_web_parametro_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_web_parametro_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_web_parametro_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_web_parametro_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_web_parametro_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_web_parametro_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  AUTO_INCREMENT = 2
  DEFAULT charset = latin1
  pack_keys = 0;



DROP TABLE IF EXISTS `rdp_web_permissao`;
CREATE TABLE `rdp_web_permissao`
(
    `id`                 BIGINT(20)  NOT NULL AUTO_INCREMENT,
    `permissao`          VARCHAR(20) NOT NULL,
    `observacao`         VARCHAR(80) DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20)  NOT NULL,
    `inserted`           DATETIME    NOT NULL,
    `updated`            DATETIME    NOT NULL,
    `user_inserted_id`   BIGINT(20)  NOT NULL,
    `user_updated_id`    BIGINT(20)  NOT NULL,
    KEY `k_rdp_web_permissao_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_web_permissao_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_web_permissao_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_web_permissao_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_web_permissao_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_web_permissao_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  AUTO_INCREMENT = 2
  DEFAULT charset = latin1
  pack_keys = 0;



DROP TABLE IF EXISTS `rdp_web_produto`;
CREATE TABLE `rdp_web_produto`
(
    `id`                 INT(11)     NOT NULL AUTO_INCREMENT,
    `codigo`             VARCHAR(13) NOT NULL,
    `recnum`             INT(11)     NOT NULL,
    `descricao`          VARCHAR(50)    DEFAULT NULL,
    `unidade`            VARCHAR(10)    DEFAULT NULL,
    `preco_custo`        DECIMAL(11, 2) DEFAULT NULL,
    `preco_medio`        DECIMAL(11, 2) DEFAULT NULL,
    `preco_venda`        DECIMAL(11, 2) DEFAULT NULL,
    `preco_minimo`       DECIMAL(11, 2) DEFAULT NULL,
    `qtde_atual_mtz`     DECIMAL(11, 4) DEFAULT NULL,
    `qtde_atual_dep`     DECIMAL(11, 4) DEFAULT NULL,
    `qtde_atual_tel`     DECIMAL(11, 4) DEFAULT NULL,
    `qtde_atual_dpz`     INT(11)        DEFAULT NULL,
    `data`               DATETIME       DEFAULT NULL,
    UNIQUE KEY `codigo` (`codigo`),
    UNIQUE KEY `recnum` (`recnum`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20)  NOT NULL,
    `inserted`           DATETIME    NOT NULL,
    `updated`            DATETIME    NOT NULL,
    `user_inserted_id`   BIGINT(20)  NOT NULL,
    `user_updated_id`    BIGINT(20)  NOT NULL,
    KEY `k_rdp_web_produto_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_web_produto_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_web_produto_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_web_produto_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_web_produto_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_web_produto_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  AUTO_INCREMENT = 2
  DEFAULT charset = latin1
  pack_keys = 0;



DROP TABLE IF EXISTS `rdp_web_venda_cabec`;
CREATE TABLE `rdp_web_venda_cabec`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `prevendaekt`        INT(11)    NOT NULL,
    `data_emissao`       DATE       NOT NULL,
    `tipo`               VARCHAR(1) NOT NULL comment 'venda, orcamento e transferencia',
    `deposito_id`        INT(11)        DEFAULT NULL,
    `vendedor_id`        INT(11)        DEFAULT NULL,
    `cliente_id`         INT(11)        DEFAULT NULL,
    `oficina_id`         INT(11)        DEFAULT NULL,
    `localizador_id`     INT(11)        DEFAULT NULL,
    `cond_pagto_id`      INT(11)        DEFAULT NULL,
    `venc1`              DATE           DEFAULT NULL,
    `venc2`              DATE           DEFAULT NULL,
    `venc3`              DATE           DEFAULT NULL,
    `venc4`              DATE           DEFAULT NULL,
    `venc5`              DATE           DEFAULT NULL,
    `venc6`              DATE           DEFAULT NULL,
    `valor1`             DECIMAL(11, 2) DEFAULT NULL,
    `valor2`             DECIMAL(11, 2) DEFAULT NULL,
    `valor3`             DECIMAL(11, 2) DEFAULT NULL,
    `valor4`             DECIMAL(11, 2) DEFAULT NULL,
    `valor5`             DECIMAL(11, 2) DEFAULT NULL,
    `valor6`             DECIMAL(11, 2) DEFAULT NULL,
    `observacao`         char(1)        DEFAULT NULL,
    `data`               DATETIME       DEFAULT NULL,
    KEY `web_venda_cabec_fk1` (`deposito_id`),
    KEY `web_venda_cabec_fk2` (`vendedor_id`),
    KEY `web_venda_cabec_fk3` (`cliente_id`),
    KEY `web_venda_cabec_fk4` (`oficina_id`),
    KEY `web_venda_cabec_fk5` (`cond_pagto_id`),
    KEY `web_venda_cabec_fk6` (`localizador_id`),
    CONSTRAINT `web_venda_cabec_fk6` FOREIGN KEY (`localizador_id`) references `web_localizador` (`codigo`),
    CONSTRAINT `web_venda_cabec_fk1` FOREIGN KEY (`deposito_id`) references `web_deposito` (`codigo`),
    CONSTRAINT `web_venda_cabec_fk2` FOREIGN KEY (`vendedor_id`) references `web_vendedor` (`codigo`),
    CONSTRAINT `web_venda_cabec_fk3` FOREIGN KEY (`cliente_id`) references `web_cliente` (`codigo`),
    CONSTRAINT `web_venda_cabec_fk4` FOREIGN KEY (`oficina_id`) references `web_oficina` (`codigo`),
    CONSTRAINT `web_venda_cabec_fk5` FOREIGN KEY (`cond_pagto_id`) references `web_condpag` (`codigo`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_web_venda_cabec_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_web_venda_cabec_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_web_venda_cabec_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_web_venda_cabec_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_web_venda_cabec_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_web_venda_cabec_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  AUTO_INCREMENT = 6
  DEFAULT charset = latin1
  pack_keys = 0;



DROP TABLE IF EXISTS `rdp_web_venda_corpo`;
CREATE TABLE `rdp_web_venda_corpo`
(
    `id`                 BIGINT(20)  NOT NULL AUTO_INCREMENT,
    `deposito_id`        BIGINT(20)  NOT NULL,
    `venda_id`           BIGINT(20)     DEFAULT NULL,
    `produto_id`         VARCHAR(13) NOT NULL,
    `descricao`          VARCHAR(50) NOT NULL,
    `qtde`               DECIMAL(11, 2) DEFAULT NULL,
    `preco_cad`          DECIMAL(11, 2) DEFAULT NULL,
    `preco_cus`          DECIMAL(11, 2) DEFAULT NULL,
    `preco_ven`          DECIMAL(11, 2) DEFAULT NULL,
    `preco_orc`          INT(11)        DEFAULT NULL,
    `total`              DECIMAL(11, 2) DEFAULT NULL,
    `data`               DATETIME       DEFAULT NULL,
    KEY `web_venda_corpo_fk1` (`produto_id`),
    CONSTRAINT `web_venda_corpo_fk1` FOREIGN KEY (`produto_id`) references `web_edi` (`codigo_edi`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20)  NOT NULL,
    `inserted`           DATETIME    NOT NULL,
    `updated`            DATETIME    NOT NULL,
    `user_inserted_id`   BIGINT(20)  NOT NULL,
    `user_updated_id`    BIGINT(20)  NOT NULL,
    KEY `k_rdp_web_venda_corpo_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_web_venda_corpo_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_web_venda_corpo_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_web_venda_corpo_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_web_venda_corpo_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_web_venda_corpo_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  DEFAULT charset = latin1
  pack_keys = 0;



DROP TABLE IF EXISTS `rdp_web_vendedor`;
CREATE TABLE `rdp_web_vendedor`
(
    `id`                 BIGINT(20)  NOT NULL AUTO_INCREMENT,
    `codigo`             INT(11)     NOT NULL,
    `vendedor`           VARCHAR(30) NOT NULL,
    `senha`              VARCHAR(20)    DEFAULT NULL,
    `perc_max_desconto`  DECIMAL(11, 2) DEFAULT NULL,
    UNIQUE KEY `codigo` (`codigo`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20)  NOT NULL,
    `inserted`           DATETIME    NOT NULL,
    `updated`            DATETIME    NOT NULL,
    `user_inserted_id`   BIGINT(20)  NOT NULL,
    `user_updated_id`    BIGINT(20)  NOT NULL,
    KEY `k_rdp_web_vendedor_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_web_vendedor_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_web_vendedor_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_web_vendedor_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_web_vendedor_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_web_vendedor_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  AUTO_INCREMENT = 4
  DEFAULT charset = latin1
  pack_keys = 0;



DROP TABLE IF EXISTS `rdp_zip_contas_pagrec`;
CREATE TABLE `rdp_zip_contas_pagrec`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `tipo`               char(1)        DEFAULT NULL,
    `ctspagrec_id`       BIGINT(20)     DEFAULT NULL,
    `data_mov`           DATE       NOT NULL,
    `data_ven`           DATE           DEFAULT NULL,
    `data_bxa`           DATE           DEFAULT NULL,
    `valor_titulo`       DECIMAL(11, 2) DEFAULT NULL,
    `valor_baixa`        DECIMAL(11, 2) DEFAULT NULL,

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_zip_contas_pagrec_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_zip_contas_pagrec_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_zip_contas_pagrec_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_zip_contas_pagrec_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_zip_contas_pagrec_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_zip_contas_pagrec_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  AUTO_INCREMENT = 3
  DEFAULT charset = latin1
  pack_keys = 0;



DROP TABLE IF EXISTS `rdp_zip_motoristas`;
CREATE TABLE `rdp_zip_motoristas`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `codigo`             INT(6)      DEFAULT NULL,
    `nome`               VARCHAR(40) DEFAULT NULL,
    `celular`            VARCHAR(50) DEFAULT NULL,
    `observacao`         VARCHAR(50) DEFAULT NULL,
    UNIQUE KEY `zip_motoristas_idx1` (`codigo`),
    KEY `zip_motoristas_idx2` (`nome`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_zip_motoristas_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_zip_motoristas_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_zip_motoristas_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_zip_motoristas_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_zip_motoristas_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_zip_motoristas_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  AUTO_INCREMENT = 5
  DEFAULT charset = latin1
  pack_keys = 0 comment ='cadastro de motoristas programa teste iapo';



DROP TABLE IF EXISTS `rdp_zip_refeicoes`;
CREATE TABLE `rdp_zip_refeicoes`
(
    `id`                 BIGINT(20) NOT NULL AUTO_INCREMENT,
    `data`               DATE       NOT NULL,
    `motorista_id`       BIGINT(20) NOT NULL,
    `cafe`               TINYINT(1)  DEFAULT NULL,
    `almoco`             TINYINT(1)  DEFAULT NULL,
    `jantar`             BINARY(20)  DEFAULT NULL,
    `observacao`         VARCHAR(50) DEFAULT NULL,
    UNIQUE KEY `zip_refeicoes_idx1` (`data`, `motorista_id`),
    KEY `zip_refeicoes_fk1` (`motorista_id`),
    CONSTRAINT `zip_refeicoes_fk1` FOREIGN KEY (`motorista_id`) references `zip_motoristas` (`id`),

    -- campos de controle do crosier
    PRIMARY KEY (`id`),
    `estabelecimento_id` BIGINT(20) NOT NULL,
    `inserted`           DATETIME   NOT NULL,
    `updated`            DATETIME   NOT NULL,
    `user_inserted_id`   BIGINT(20) NOT NULL,
    `user_updated_id`    BIGINT(20) NOT NULL,
    KEY `k_rdp_zip_refeicoes_estabelecimento` (`estabelecimento_id`),
    KEY `k_rdp_zip_refeicoes_user_inserted` (`user_inserted_id`),
    KEY `k_rdp_zip_refeicoes_user_updated` (`user_updated_id`),
    CONSTRAINT `fk_rdp_zip_refeicoes_user_updated` FOREIGN KEY (`user_updated_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_zip_refeicoes_user_inserted` FOREIGN KEY (`user_inserted_id`) references `sec_user` (`id`),
    CONSTRAINT `fk_rdp_zip_refeicoes_estabelecimento` FOREIGN KEY (`estabelecimento_id`) references `cfg_estabelecimento` (`id`)
) ENGINE = INNODB
  AUTO_INCREMENT = 4
  DEFAULT charset = latin1
  pack_keys = 0;
