DROP VIEW vw_rdp_est_produto;
CREATE VIEW vw_rdp_est_produto AS

SELECT p.*,
       CAST(IFNULL(atribEstoqueMatriz.valor, '0.0') AS DECIMAL(15, 3))              as saldo_estoque_matriz,
       CAST(IFNULL(atribEstoqueAcessorios.valor, '0.0') AS DECIMAL(15, 3))          as saldo_estoque_acessorios,
       CAST(IFNULL(atribEstoqueTotal.valor, '0.0') AS DECIMAL(15, 3))               as saldo_estoque_total,
       img1.image_name                                                              as imagem1,
       (SELECT count(id) FROM est_produto_imagem imgs WHERE imgs.produto_id = p.id) as qtde_imagens,

       CAST(IFNULL(atribPrecoTabela.valor, '0.0') AS DECIMAL(15, 3))                as preco_tabela,
       CAST(IFNULL(atribPrecoCusto.valor, '0.0') AS DECIMAL(15, 3))                 as preco_custo

FROM est_produto p
         LEFT JOIN est_produto_atributo atribEstoqueMatriz
                   ON (p.id = atribEstoqueMatriz.produto_id AND atribEstoqueMatriz.atributo_id IN (SELECT id
                                                                                                   FROM est_atributo
                                                                                                   WHERE uuid = '3edb71db-375d-4d37-b36d-8287f291606b'))

         LEFT JOIN est_produto_atributo atribEstoqueAcessorios
                   ON (p.id = atribEstoqueAcessorios.produto_id AND atribEstoqueAcessorios.atributo_id IN (SELECT id
                                                                                                           FROM est_atributo
                                                                                                           WHERE uuid = 'c37e9985-53f2-47f4-833a-52ace1f84e60'))

         LEFT JOIN est_produto_atributo atribEstoqueTotal
                   ON (p.id = atribEstoqueTotal.produto_id AND atribEstoqueTotal.atributo_id IN (SELECT id
                                                                                                 FROM est_atributo
                                                                                                 WHERE uuid = '8f25a3e6-cf93-4111-be2b-a46dedc30107'))

         LEFT JOIN est_produto_atributo atribPrecoCusto
                   ON (p.id = atribPrecoCusto.produto_id AND atribPrecoCusto.atributo_id IN (SELECT id
                                                                                             FROM est_atributo
                                                                                             WHERE uuid = '84ec35ff-22c1-4479-8368-baf766702e5e	'))
         LEFT JOIN est_produto_atributo atribPrecoTabela
                   ON (p.id = atribPrecoTabela.produto_id AND atribPrecoTabela.atributo_id IN (SELECT id
                                                                                               FROM est_atributo
                                                                                               WHERE uuid = 'c22e79c5-4dfd-4506-b3f5-53473f88bf2f'))

         LEFT JOIN est_produto_imagem img1 ON (img1.produto_id = p.id AND img1.ordem = 1)
;



