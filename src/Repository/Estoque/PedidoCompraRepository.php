<?php

namespace App\Repository\Estoque;


use App\Entity\Estoque\Fornecedor;
use App\Entity\Estoque\PedidoCompra;
use App\Entity\Estoque\Produto;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use Doctrine\DBAL\Connection;

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

        /** @var Connection $conn */
        $conn = $this->getEntityManager()->getConnection();
        $results = $conn->fetchAll($sql);

        $filiais = ['MATRIZ', 'ACESSORIOS'];

        $ret = [];

        foreach ($filiais as $filial) {
            $deficit = 0;
            foreach ($results as $r) {
                if ($r['desc_filial'] === $filial) {
                    $deficit = $r['deficit'];
                    break;
                }
            }
            $ret[] = ['desc_filial' => $filial,
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

    /**
     * @param \DateTime|null $dtIni
     * @param \DateTime|null $dtFim
     * @return mixed
     */
    public function totalComprasPorFornecedor(\DateTime $dtIni = null, \DateTime $dtFim = null)
    {
        $dtIni = $dtIni ?? \DateTime::createFromFormat('d/m/Y', '01/01/0000');
        $dtIni->setTime(0, 0, 0, 0);
        $dtFim = $dtFim ?? \DateTime::createFromFormat('d/m/Y', '01/01/9999');
        $dtFim->setTime(23, 59, 59, 99999);

        $sql = 'SELECT * FROM (SELECT nome_fornec, sum(total) as total_compras FROM rdp_rel_compfor01 WHERE dt_movto BETWEEN :dtIni and :dtFim GROUP BY nome_fornec) a WHERE total_compras > 0  ORDER BY total_compras';

        /** @var Connection $conn */
        $conn = $this->getEntityManager()->getConnection();

        $params = [
            'dtIni' => $dtIni->format('Y-m-d'),
            'dtFim' => $dtFim->format('Y-m-d')
        ];

        return $conn->fetchAll($sql, $params);
    }


    /**
     * @param \DateTime|null $dtIni
     * @param \DateTime|null $dtFim
     * @param string $nomeFornec
     * @param bool $totalGeral
     * @return mixed
     */
    public function itensComprados(\DateTime $dtIni, \DateTime $dtFim, string $nomeFornec, bool $totalGeral = false)
    {
        $dtIni = $dtIni ?? \DateTime::createFromFormat('d/m/Y', '01/01/0000');
        $dtIni->setTime(0, 0, 0, 0);
        $dtFim = $dtFim ?? \DateTime::createFromFormat('d/m/Y', '01/01/9999');
        $dtFim->setTime(23, 59, 59, 99999);

        $sql = 'SELECT ';

        if (!$totalGeral) {
            $sql .= 'cod_prod, desc_prod, ';
        }

        $sql .= 'sum(qtde) as qtde_total, sum(total) as total 
                    FROM rdp_rel_compfor01
                     WHERE nome_fornec = :nomeFornec AND dt_movto BETWEEN :dtIni AND :dtFim ';

        if (!$totalGeral) {
            $sql .= ' GROUP BY cod_prod, desc_prod ';
        }

        $sql .= ' ORDER BY total DESC';


        /** @var Connection $conn */
        $conn = $this->getEntityManager()->getConnection();

        $params = [
            'dtIni' => $dtIni->format('Y-m-d'),
            'dtFim' => $dtFim->format('Y-m-d'),
            'nomeFornec' => $nomeFornec
        ];

        return $conn->fetchAll($sql, $params);

    }

    /**
     * @param \DateTime|null $dtIni
     * @param \DateTime|null $dtFim
     * @param string $codProd
     * @return mixed
     */
    public function itensCompradosPorProduto(\DateTime $dtIni, \DateTime $dtFim, string $codProd)
    {
        $dtIni = $dtIni ?? \DateTime::createFromFormat('d/m/Y', '01/01/0000');
        $dtIni->setTime(0, 0, 0, 0);
        $dtFim = $dtFim ?? \DateTime::createFromFormat('d/m/Y', '01/01/9999');
        $dtFim->setTime(23, 59, 59, 99999);

        $sql = 'SELECT lancto, docto, dt_movto, qtde, preco_custo, total
                    FROM rdp_rel_compfor01
                     WHERE cod_prod = :codProd AND dt_movto BETWEEN :dtIni AND :dtFim ORDER BY dt_movto';

        /** @var Connection $conn */
        $conn = $this->getEntityManager()->getConnection();

        $params = [
            'dtIni' => $dtIni->format('Y-m-d'),
            'dtFim' => $dtFim->format('Y-m-d'),
            'codProd' => $codProd
        ];

        return $conn->fetchAll($sql, $params);
    }

}
