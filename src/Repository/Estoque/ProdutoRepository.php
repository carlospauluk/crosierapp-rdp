<?php

namespace App\Repository\Estoque;


use App\Entity\Estoque\Produto;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use Doctrine\DBAL\DBALException;

/**
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class ProdutoRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return Produto::class;
    }

    /**
     * Utilizado no gráfico de Total de Estoque por Filial
     *
     * @return mixed
     * @throws ViewException
     */
    public function totalEstoquePorFilial()
    {
        try {
            $sql = 'SELECT 
                        SUM(json_data->>"$.qtde_estoque_matriz") as total_qtde_atual, 
                        SUM(json_data->>"$.qtde_estoque_matriz" * json_data->>"$.preco_tabela") as total_venda, 
                        SUM(json_data->>"$.qtde_estoque_matriz" * json_data->>"$.preco_custo") as total_custo_medio ' .
                'FROM est_produto';
            $conn = $this->getEntityManager()->getConnection();
            $totalMatriz = $conn->fetchAssoc($sql);
            $sql = 'SELECT 
                        SUM(json_data->>"$.qtde_estoque_acessorios") as total_qtde_atual, 
                        SUM(json_data->>"$.qtde_estoque_acessorios" * json_data->>"$.preco_tabela") as total_venda, 
                        SUM(json_data->>"$.qtde_estoque_acessorios" * json_data->>"$.preco_custo") as total_custo_medio ' .
                'FROM est_produto';
            $conn = $this->getEntityManager()->getConnection();
            $totalAcessorios = $conn->fetchAssoc($sql);
            $r = [
                [
                    'desc_filial' => 'MATRIZ',
                    'total_qtde_atual' => bcmul('1.0', $totalMatriz['total_qtde_atual'], 2),
                    'total_venda' => bcmul('1.0', $totalMatriz['total_venda'], 2),
                    'total_custo_medio' => bcmul('1.0', $totalMatriz['total_custo_medio'], 2),
                ],
                [
                    'desc_filial' => 'ACESSORIOS',
                    'total_qtde_atual' => bcmul('1.0', $totalAcessorios['total_qtde_atual'], 2),
                    'total_venda' => bcmul('1.0', $totalAcessorios['total_venda'], 2),
                    'total_custo_medio' => bcmul('1.0', $totalAcessorios['total_custo_medio'], 2),
                ],
            ];
            return $r;
        } catch (DBALException $e) {
            throw new ViewException('Erro ao gerar totalEstoquePorFilial()');
        }
    }


    public function getProdutoByCodigo($codigo)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT * FROM est_produto WHERE json_data->>"$.erp_codigo" = :codigo';
        return $conn->fetchAssoc($sql, ['codigo' => $codigo]);
    }

}
