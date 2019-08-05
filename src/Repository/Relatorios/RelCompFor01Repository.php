<?php

namespace App\Repository\Relatorios;

use App\Entity\Relatorios\RelCompFor01;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class RelCompFor01Repository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return RelCompFor01::class;
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

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('nome_fornec', 'nome_fornec');
        $rsm->addScalarResult('total_compras', 'total_compras');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('dtIni', $dtIni);
        $query->setParameter('dtFim', $dtFim);

        return $query->getResult();

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

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('cod_prod', 'cod_prod');
        $rsm->addScalarResult('desc_prod', 'desc_prod');
        $rsm->addScalarResult('qtde_total', 'qtde_total');
        $rsm->addScalarResult('total', 'total');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('dtIni', $dtIni);
        $query->setParameter('dtFim', $dtFim);
        $query->setParameter('nomeFornec', $nomeFornec);

        return $query->getResult();

    }


}

