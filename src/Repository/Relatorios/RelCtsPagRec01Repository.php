<?php

namespace App\Repository\Relatorios;

use App\Entity\RelCtsPagRec01;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class RelCtsPagRec01Repository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return RelCtsPagRec01::class;
    }


    /**
     * @param \DateTime|null $dtIni
     * @param \DateTime|null $dtFim
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function relCtsPagRec01(\DateTime $dtIni = null, \DateTime $dtFim = null)
    {
        $dtIni = $dtIni ?? \DateTime::createFromFormat('d/m/Y', '01/01/0000');
        $dtIni->setTime(0, 0, 0, 0);
        $dtFim = $dtFim ?? \DateTime::createFromFormat('d/m/Y', '01/01/9999');
        $dtFim->setTime(23, 59, 59, 99999);


        $sqlDtsVenctos = 'SELECT distinct(dt_vencto) as dt_vencto FROM rdp_rel_ctspagrec01 WHERE dt_vencto BETWEEN :dtIni AND :dtFim GROUP BY dt_vencto ORDER BY dt_vencto';
        $rsmDtsVenctos = new ResultSetMapping();
        $rsmDtsVenctos->addScalarResult('dt_vencto', 'dt_vencto');
        $queryDtsVenctos = $this->getEntityManager()->createNativeQuery($sqlDtsVenctos, $rsmDtsVenctos);
        $queryDtsVenctos->setParameter('dtIni', $dtIni);
        $queryDtsVenctos->setParameter('dtFim', $dtFim);

        $dtsVenctos = $queryDtsVenctos->getResult();

        $r = null;

        foreach ($dtsVenctos as $dtVencto) {
            $dtVencto = DateTimeUtils::parseDateStr($dtVencto['dt_vencto']);

            $sql = 'SELECT DATE_FORMAT(dt_vencto, \'%d/%m/%y\') as dt_vencto, SUM(valor_titulo) as total FROM rdp_rel_ctspagrec01 WHERE tipo_pag_rec = :tipoPagRec AND dt_vencto = :dtVencto';

            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('dt_vencto', 'dt_vencto');
            $rsm->addScalarResult('total', 'total');

            $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
            $query->setParameter('tipoPagRec', 'P');
            $query->setParameter('dtVencto', $dtVencto->format('Y-m-d'));

            $rPag = $query->getSingleResult();

            $query->setParameter('tipoPagRec', 'R');
            $rRec = $query->getSingleResult();

            $r[] = ['dtVencto' => $dtVencto->format('d/m/Y'), 'aPagar' => $rPag['total'], 'aReceber' => $rRec['total']];
        }

        return $r;

    }


}