<?php

namespace App\Repository\Relatorios;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class RelCompFor01Repository
{

    private Connection $conn;

    /**
     * @required
     * @param Connection $conn
     */
    public function setConn(Connection $conn): void
    {
        $this->conn = $conn;
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

        return $this->conn->fetchAllAssociative($sql, [
            'dtIni' => $dtIni->format('Y-m-d'),
            'dtFim' => $dtFim->format('Y-m-d'),
        ]);

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

        return $this->conn->fetchAllAssociative($sql, [
            'dtIni' => $dtIni->format('Y-m-d'),
            'dtFim' => $dtFim->format('Y-m-d'),
            'nomeFornec' => $nomeFornec,
        ]);

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

        return $this->conn->fetchAllAssociative($sql, [
            'dtIni' => $dtIni->format('Y-m-d'),
            'dtFim' => $dtFim->format('Y-m-d'),
            'codProd' => $codProd,
        ]);

    }


    /**
     * @param string $codigo
     * @return null|string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getProdutoByCodigo(string $codigo): ?string
    {
        $sql = 'SELECT desc_prod FROM rdp_rel_compfor01 WHERE cod_prod = :codigo GROUP BY cod_prod, desc_prod';

        return $this->conn->fetchAssociative($sql, [
            'codigo' => $codigo,
        ])['desc_prod'];
    }

}
