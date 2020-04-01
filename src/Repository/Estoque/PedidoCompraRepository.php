<?php

namespace App\Repository\Estoque;


use App\Entity\Estoque\Fornecedor;
use App\Entity\Estoque\PedidoCompra;
use App\Entity\Estoque\Produto;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class PedidoCompraRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return PedidoCompra::class;
    }



    /**
     * @return mixed
     */
    public function getReposicaoEstoqueTotalPorFilial()
    {


        $sql = 'SELECT SUM(json_data->>"$.qtde_minima" - json_data->>"$.qtde_estoque_acessorios") as deficit FROM est_produto WHERE qtde_minima > 0 AND json_data->>"$.qtde_estoque_acessorios" < json_data->>"$.qtde_minima" ORDER BY deficit DESC';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('desc_filial', 'desc_filial');
        $rsm->addScalarResult('deficit', 'deficit');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $results = $query->getResult();

        $filiais = $this->getFiliais();

        $ret = [];

        foreach ($filiais as $filial) {
            $deficit = 0;
            foreach ($results as $r) {
                if ($r['desc_filial'] === $filial['text']) {
                    $deficit = $r['deficit'];
                    break;
                }
            }
            $ret[] = ['desc_filial' => $filial['text'],
                'deficit' => $deficit];
        }

        return $ret;
    }

    /**
     * @param string $filial
     * @param Fornecedor $fornecedor
     * @param array $params
     * @return mixed
     * @throws ViewException
     */
    public function findReposicoesEstoque(string $filial, ?Fornecedor $fornecedor = null, \DateTime $dtUltSaidaAPartirDe = null, ?bool $apenasARepor = null)
    {
        $filtros = null;
        $filtros[] = ['qtdeEstoqueMin' . ucfirst(strtolower($filial)), 'IS_NOT_NULL'];
        if ($fornecedor) {
            $filtros[] = ['fornecedor', 'EQ', $fornecedor];
        }
        if ($dtUltSaidaAPartirDe) {
            $filtros[] = ['dtUltSaida' . ucfirst(strtolower($filial)), 'GTE', $dtUltSaidaAPartirDe];
        }
        if ($apenasARepor !== null) {
            $compar = $apenasARepor ? 'LT' : 'GTE';
            $filtros[] = ['deficitEstoque' . ucfirst(strtolower($filial)), $compar, 0];
        }

        /** @var ProdutoRepository $repoProduto */
        $repoProduto = $this->getEntityManager()->getRepository(Produto::class);

        $produtos = $repoProduto->findByFiltersSimpl($filtros, ['deficitEstoque' . ucfirst(strtolower($filial)) => 'ASC'], 0, 1000);
        return $produtos;

    }

}
