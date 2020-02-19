DROP VIEW vw_rdp_est_produto;
CREATE VIEW vw_rdp_est_produto AS

SELECT
    p.id,
    p.uuid,
    p.depto_id,
    p.json_data->>"$.depto_codigo" as depto_codigo,
    p.json_data->>"$.depto_nome" as depto_nome,
    p.grupo_id,
    p.json_data->>"$.grupo_codigo" as grupo_codigo,
    p.json_data->>"$.grupo_nome" as grupo_nome,
    p.subgrupo_id,
    p.json_data->>"$.subgrupo_codigo" as subgrupo_codigo,
    p.json_data->>"$.subgrupo_nome" as subgrupo_nome,
    p.fornecedor_id,
    p.json_data->>"$.fornecedor_documento" as fornecedor_documento,
    p.json_data->>"$.fornecedor_nome" as fornecedor_nome,
    p.nome,
    p.json_data->>"$.titulo" as titulo,
    p.status,
    p.composicao,
    p.inserted,
    p.updated,
    p.version,
    p.estabelecimento_id,
    p.user_inserted_id,
    p.user_updated_id,
    p.unidade_produto_id,

    CAST(IFNULL(p.json_data->>"$.qtde_estoque_matriz", '0.0') AS DECIMAL(15, 3)) as qtde_estoque_matriz,
    CAST(IFNULL(p.json_data->>"$.qtde_estoque_acessorios", '0.0') AS DECIMAL(15, 3)) as qtde_estoque_acessorios,
    CAST(IFNULL(p.json_data->>"$.qtde_estoque_total", '0.0') AS DECIMAL(15, 3)) as qtde_estoque_total,
    CAST(IFNULL(p.json_data->>"$.preco_tabela", '0.0') AS DECIMAL(15, 3)) as preco_tabela,
    CAST(IFNULL(p.json_data->>"$.preco_custo", '0.0') AS DECIMAL(15, 3)) as preco_custo,

    CAST(IFNULL(p.json_data->>"$.porcent_preench", '0.0') AS DECIMAL(15, 2)) as porcent_preench,

    p.json_data->>"$.imagem1" as imagem1,
    p.json_data->>"$.qtde_imagens" as qtde_imagens

FROM est_produto p;



