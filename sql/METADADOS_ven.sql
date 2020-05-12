
ALTER TABLE ven_venda DROP `dt_nota`;
ALTER TABLE ven_venda ADD `dt_nota` DATE GENERATED ALWAYS AS (IF(json_data->"$.dt_nota" = CAST('null' AS JSON) OR trim(json_data->>"$.dt_nota") = '', NULL, CAST(json_data->>"$.dt_nota" AS DATE)));



drop table rdp_ven_pv_item;
drop table rdp_ven_pv;


ALTER TABLE ven_venda_item DROP `fornecedor_id`;
ALTER TABLE ven_venda_item ADD `fornecedor_id` bigint(20) GENERATED ALWAYS AS (IF(json_data->"$.fornecedor_id" = CAST('null' AS JSON) OR trim(json_data->>"$.fornecedor_id") = '', NULL, CAST(json_data->>"$.fornecedor_id" AS unsigned)));


ALTER TABLE ven_venda_item DROP `fornecedor_codigo`;
ALTER TABLE ven_venda_item ADD `fornecedor_codigo` varchar(50) GENERATED ALWAYS AS (IF(json_data->"$.fornecedor_codigo" = CAST('null' AS JSON) OR trim(json_data->>"$.fornecedor_codigo") = '', NULL, json_data->>"$.fornecedor_codigo"));


ALTER TABLE ven_venda_item DROP `fornecedor_nome`;
ALTER TABLE ven_venda_item ADD `fornecedor_nome` varchar(255) GENERATED ALWAYS AS (IF(json_data->"$.fornecedor_nome" = CAST('null' AS JSON) OR trim(json_data->>"$.fornecedor_nome") = '', NULL, json_data->>"$.fornecedor_nome"));



ALTER TABLE ven_venda DROP `vendedor_codigo`;
ALTER TABLE ven_venda ADD `vendedor_codigo` varchar(50) GENERATED ALWAYS AS (IF(json_data->"$.vendedor_codigo" = CAST('null' AS JSON) OR trim(json_data->>"$.vendedor_codigo") = '', NULL, json_data->>"$.vendedor_codigo"));

ALTER TABLE ven_venda DROP `vendedor_nome`;
ALTER TABLE ven_venda ADD `vendedor_nome` varchar(255) GENERATED ALWAYS AS (IF(json_data->"$.vendedor_nome" = CAST('null' AS JSON) OR trim(json_data->>"$.vendedor_nome") = '', NULL, json_data->>"$.vendedor_nome"));


ALTER TABLE ven_venda_item DROP `total_item`;
ALTER TABLE ven_venda_item ADD `total_item` decimal(15,2) GENERATED ALWAYS AS (qtde * preco_venda);