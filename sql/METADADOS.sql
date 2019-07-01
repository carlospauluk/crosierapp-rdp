DROP TABLE IF EXISTS `hw_coisa`;

CREATE TABLE `hw_coisa` (
  `id` bigint(20) AUTO_INCREMENT NOT NULL,
  `nome` varchar(200) COLLATE utf8_swedish_ci NOT NULL,
  `obs` varchar(3000) COLLATE utf8_swedish_ci NULL,
  `dt_coisa` datetime NOT NULL,
  `ordem` int NOT NULL,
  `importante` boolean NOT NULL,

  `estabelecimento_id` bigint(20) NOT NULL,
  `inserted` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `user_inserted_id` bigint(20) NOT NULL,
  `user_updated_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_hw_coisa` (`nome`),
  KEY `K_hw_coisa_estabelecimento` (`estabelecimento_id`),
  KEY `K_hw_coisa_user_inserted` (`user_inserted_id`),
  KEY `K_hw_coisa_user_updated` (`user_updated_id`),
  CONSTRAINT `FK_hw_coisa_user_updated` FOREIGN KEY (`user_updated_id`) REFERENCES `sec_user` (`id`),
  CONSTRAINT `FK_hw_coisa_user_inserted` FOREIGN KEY (`user_inserted_id`) REFERENCES `sec_user` (`id`),
  CONSTRAINT `FK_hw_coisa_estabelecimento` FOREIGN KEY (`estabelecimento_id`) REFERENCES `cfg_estabelecimento` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;


