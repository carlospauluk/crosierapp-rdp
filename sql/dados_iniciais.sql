INSERT INTO `rdp_cts_d001_controle`
VALUES (1, 1, 1, 0, 0, 0, NULL, NULL, 0, 0, '', 0, 0.00, 0.00, 0, 0),
       (2, 1, 1, 1, 1, 1, '2019-06-11 00:00:00', '2019-06-12 00:00:00', 1, 1, '1', 1, 0.01, 0.01, 1, 1),
       (3, 1, 1, 1, 1, 0, NULL, NULL, 0, 0, '', 0, 0.00, 0.00, 0, 0);


INSERT INTO `rdp_cts_d002_cliente`
VALUES (8, 0, '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', 0.00, '', NULL, '',
        '', '', '', 0, '', '', '', NULL, '', 0.00, '', '', NULL, '', '', 1, '', 0.00, 0.00, 1, '', '', '', '', '', '',
        '', '', NULL, NULL, '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, '', '', '', '', '', 0, '', '', '', '', '', '', '', '',
        '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', 0, 0.00, '',
        '', '', '', 0, ''),
       (9, 1, 'CONSUMIDOR', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', 0.00, '',
        NULL, '', '', '', '', 0, '', '', '', NULL, '', 0.00, '', '', NULL, '', '', 1, '', 0.00, 0.00, 1, '', '', '', '',
        '', '', '', '', NULL, NULL, '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, '', '', '', '', '', 0, '', '', '', '', '', '',
        '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', 0,
        0.00, '', '', '', '', 0, '');


INSERT INTO `rdp_cts_d005_localizador`
VALUES (2, 1, 'CARTEIRA', NULL, 1, 1),
       (3, 90, 'BRADESCO PG', NULL, 0, 0);


INSERT INTO `rdp_cts_obs2_cliente`
VALUES (0, 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
        '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');


INSERT INTO `rdp_fat_d092_condpag`
VALUES (1, 1, 'A VISTA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1.00, 2.00, NULL, NULL, NULL, NULL, NULL, 1.00,
        NULL, NULL),
       (2, 10, 'A PRAZO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1.00, 2.00, NULL, NULL, NULL, NULL, NULL,
        2.00, NULL, NULL);



INSERT INTO `rdp_sec_logged`
VALUES ('admin', '1561322546.8851', 'm8l9i3ofm60e3e27chj7in4vh4', '177.92.52.130');


INSERT INTO `rdp_sec_users`
VALUES ('admin', 'admin#2012', 'Admin', 'ekt@ekt.com.br', 'Y', NULL, 'Y');



INSERT INTO `rdp_web_cliente`
VALUES (1, 1, 'EDSON K TOTSUGUI', '09624181888', 'PONTA GROSSA', 'PR', NULL, '', '', '', NULL, NULL, NULL, NULL, NULL,
        NULL, 0.00, 0.00, 0.00);


INSERT INTO `rdp_web_deposito`
VALUES (1, 0, 'Matriz'),
       (2, 30, 'Telemaco'),
       (3, 31, 'Acessorios'),
       (4, 1, 'Delpozo Almoxarifado');



INSERT INTO `rdp_web_oficina`
VALUES (1, 1, 'RODOPONTA'),
       (2, 2, 'ACESSORIOS'),
       (3, 3, 'OFICINA DO NEGO');




INSERT INTO `rdp_web_parametro`
VALUES (1, 5.00);




INSERT INTO `rdp_web_permissao`
VALUES (1, 'D', 'D');


INSERT INTO `rdp_web_produto`
VALUES (1, '1', 1, 'PRODUTO DIVERSOS', 'PC', 0.01, 0.01, 0.01, 0.01, 0.0001, NULL, NULL, NULL, '2019-06-06 00:00:00');


INSERT INTO `rdp_zip_contas_pagrec`
VALUES (1, 'R', 1, '2019-01-01', '2019-01-01', '2019-01-01', 10.00, 10.00),
       (2, 'R', 2, '2019-02-01', '2019-02-01', '2019-02-01', 20.00, 20.00);


INSERT INTO `rdp_zip_motoristas`
VALUES (1, 1, 'EDSON', '', ''),
       (2, 2, 'JOSÉ', '', ''),
       (3, 3, 'MARIA', '', ''),
       (4, 4, 'JESUS', '', '');




INSERT INTO `rdp_zip_refeicoes`
VALUES (1, '2019-06-13', 3, _binary '\0', 0, _binary 'N\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 'TESTE'),
       (2, '2019-06-13', 1, _binary '\0', 0, _binary 'S\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', ''),
       (3, '2019-06-13', 4, _binary '\0', 0, _binary 'N\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0', 'TESTE');