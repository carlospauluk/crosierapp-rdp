<?php

namespace App\Repository\Relatorios;

use App\Entity\Relatorios\RelCtsPagRec01;
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
     * @return array
     */
    public function getFiliais(): array
    {
        $sql = 'SELECT CONCAT(filial, \' - \', desc_filial) as filial FROM rdp_rel_ctspagrec01 GROUP BY filial, desc_filial ORDER BY filial';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('filial', 'filial');
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $r = $query->getResult();
        $arr = [];
        foreach ($r as $item) {
            $e['id'] = urlencode($item['filial']);
            $e['text'] = $item['filial'];
            $arr[] = $e;
        }
        return $arr;
    }


    /**
     * @return array
     */
    public function getLocalizadores(): array
    {
        $sql = 'SELECT CONCAT(localizador, \' - \', localizador_desc) as localizador FROM rdp_rel_ctspagrec01 GROUP BY localizador, localizador_desc ORDER BY localizador';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('localizador', 'localizador');
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $r = $query->getResult();
        $arr = [];
        foreach ($r as $item) {
            $e['id'] = urlencode($item['localizador']);
            $e['text'] = $item['localizador'];
            $arr[] = $e;
        }
        return $arr;
    }


    /**
     * @param \DateTime|null $dtIni
     * @param \DateTime|null $dtFim
     * @param string|null $filial
     * @param string|null $localizador
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function relCtsPagRec01(\DateTime $dtIni = null, \DateTime $dtFim = null, ?string $filial = null, ?string $localizador = null)
    {
        $filial = $filial ?? $this->getFiliais()[0];

        $filialCod = (int)explode(' - ', $filial)[0];
        $localizadorCod = (int)explode(' - ', $localizador)[0];

        $dtIni = $dtIni ?? \DateTime::createFromFormat('d/m/Y', '01/01/0000');
        $dtIni->setTime(0, 0, 0, 0);
        $dtFim = $dtFim ?? \DateTime::createFromFormat('d/m/Y', '01/01/9999');
        $dtFim->setTime(23, 59, 59, 99999);

        $sql_filial = '';
        if ($filial) {
            $sql_filial = ' AND filial = :filial ';
        }

        $sql_localizador = '';
        if ($localizadorCod) {
            $sql_localizador = ' AND localizador = :localizador ';
        }

        $sqlDtsVenctos = 'SELECT distinct(dt_vencto) as dt_vencto FROM rdp_rel_ctspagrec01 WHERE dt_vencto BETWEEN :dtIni AND :dtFim ' .
            $sql_filial . $sql_localizador .
            ' GROUP BY dt_vencto ORDER BY dt_vencto';
        $rsmDtsVenctos = new ResultSetMapping();
        $rsmDtsVenctos->addScalarResult('dt_vencto', 'dt_vencto');
        $queryDtsVenctos = $this->getEntityManager()->createNativeQuery($sqlDtsVenctos, $rsmDtsVenctos);
        $queryDtsVenctos->setParameter('dtIni', $dtIni);
        $queryDtsVenctos->setParameter('dtFim', $dtFim);
        if ($filial) {
            $queryDtsVenctos->setParameter('filial', $filialCod);
        }
        if ($localizadorCod) {
            $queryDtsVenctos->setParameter('localizador', $localizadorCod);
        }

        $dtsVenctos = $queryDtsVenctos->getResult();

        $r = null;

        foreach ($dtsVenctos as $dtVencto) {
            $dtVencto = DateTimeUtils::parseDateStr($dtVencto['dt_vencto']);

            $sql = 'SELECT DATE_FORMAT(dt_vencto, \'%d/%m/%y\') as dt_vencto, SUM(valor_titulo) as total FROM rdp_rel_ctspagrec01 WHERE tipo_pag_rec = :tipoPagRec AND dt_vencto = :dtVencto' .
                $sql_filial . $sql_localizador;

            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('dt_vencto', 'dt_vencto');
            $rsm->addScalarResult('total', 'total');

            $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
            $query->setParameter('tipoPagRec', 'P');
            $query->setParameter('dtVencto', $dtVencto->format('Y-m-d'));
            if ($filial) {
                $query->setParameter('filial', $filialCod);
            }
            if ($localizadorCod) {
                $query->setParameter('localizador', $localizadorCod);
            }

            $rPag = $query->getSingleResult();

            $query->setParameter('tipoPagRec', 'R');
            $rRec = $query->getSingleResult();

            $r[] = ['dtVencto' => $dtVencto->format('d/m/Y'), 'aPagar' => $rPag['total'], 'aReceber' => $rRec['total']];
        }

        return $r;

    }


}