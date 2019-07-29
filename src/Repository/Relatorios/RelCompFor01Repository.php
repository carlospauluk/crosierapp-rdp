<?php

namespace App\Repository\Relatorios;

use App\Entity\Relatorios\RelVendas01;
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
        return RelVendas01::class;
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


}

