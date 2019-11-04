DROP VIEW vw_rdp_est_produto;
CREATE VIEW vw_rdp_est_produto AS

SELECT p.*,
       CAST(IFNULL(atribEstoqueMatriz.valor, '0.0') AS DECIMAL(15,3))     as saldo_estoque_matriz,
       CAST(IFNULL(atribEstoqueAcessorios.valor, '0.0') AS DECIMAL(15,3)) as saldo_estoque_acessorios,
       CAST(IFNULL(atribEstoqueTotal.valor, '0.0') AS DECIMAL(15,3))      as saldo_estoque_total,
       img1.image_name              as imagem1
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
         LEFT JOIN est_produto_imagem img1 ON (img1.produto_id = p.id AND img1.ordem = 1)
;



