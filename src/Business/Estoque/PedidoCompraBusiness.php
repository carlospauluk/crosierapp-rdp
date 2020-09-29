<?php


namespace App\Business\Estoque;


use CrosierSource\CrosierLibRadxBundle\Entity\Estoque\Fornecedor;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * @author Carlos Eduardo Pauluk
 */
class PedidoCompraBusiness
{

    private LoggerInterface $logger;

    private EntityManagerInterface $doctrine;

    /**
     * @required
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @required
     * @param EntityManagerInterface $doctrine
     */
    public function setDoctrine(EntityManagerInterface $doctrine): void
    {
        $this->doctrine = $doctrine;
    }


    /**
     * @return mixed
     */
    public function getReposicaoEstoqueTotalPorFilial()
    {
        $sql = 'SELECT SUM(json_data->>"$.qtde_minima" - json_data->>"$.qtde_estoque_acessorios") as deficit FROM est_produto WHERE qtde_minima > 0 AND json_data->>"$.qtde_estoque_acessorios" < json_data->>"$.qtde_minima" ORDER BY deficit DESC';


        $conn = $this->doctrine->getConnection();
        $results = $conn->fetchAllAssociative($sql);

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
     * @param \DateTime|null $dtUltSaidaAPartirDe
     * @param bool|null $apenasARepor
     * @return mixed
     */
    public function findReposicoesEstoque(string $filial, ?Fornecedor $fornecedor = null, \DateTime $dtUltSaidaAPartirDe = null, ?bool $apenasARepor = null)
    {
        $sql = 'SELECT p.*, f.nome as fornecedor_nome FROM est_produto p LEFT JOIN est_fornecedor f ON p.fornecedor_id = f.id WHERE JSON_IS_NULL_OR_EMPTY(p.json_data,\'qtde_estoque_min_' . strtolower($filial) . '\') = false';
        $params = [];
        if ($fornecedor) {
            $sql .= ' AND fornecedor_id = :fornecedor_id';
            $params['fornecedor_id'] = $fornecedor->getId();
        }
        if ($dtUltSaidaAPartirDe) {
            $sql .= ' AND p.json_data->>"$.dt_ult_saida_' . strtolower($filial) . '" >= :dtUltSaidaAPartirDe';
            $params['dtUltSaidaAPartirDe'] = $dtUltSaidaAPartirDe->format('Y-m-d');
        }
        if ($apenasARepor !== null) {
            $compar = $apenasARepor ? '<' : '>=';
            $sql .= ' AND p.json_data->>"$.deficit_estoque_' . strtolower($filial) . '" ' . $compar . ' 0';
        }
        $sql .= ' ORDER BY p.json_data->>"$.deficit_estoque" LIMIT 0,500';
        $rProdutos = $this->doctrine->getConnection()->fetchAllAssociative($sql, $params);
        $produtos = [];
        foreach ($rProdutos as $r) {
            $r['jsonData'] = json_decode($r['json_data'], true);
            $produtos[] = $r;
        }
        return $produtos;
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



        $conn = $this->doctrine->getConnection();

        $params = [
            'dtIni' => $dtIni->format('Y-m-d'),
            'dtFim' => $dtFim->format('Y-m-d'),
            'nomeFornec' => $nomeFornec
        ];

        return $conn->fetchAllAssociative($sql, $params);

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


        $conn = $this->doctrine->getConnection();

        $params = [
            'dtIni' => $dtIni->format('Y-m-d'),
            'dtFim' => $dtFim->format('Y-m-d'),
            'codProd' => $codProd
        ];

        return $conn->fetchAllAssociative($sql, $params);
    }

}