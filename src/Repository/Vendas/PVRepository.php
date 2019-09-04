<?php

namespace App\Repository\Vendas;

use App\Entity\Vendas\PV;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class PVRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return PV::class;
    }

    /**
     * @return array
     */
    public function getFiliais(): array
    {
        $sql = 'SELECT desc_filial FROM rdp_rel_estoque01 GROUP BY desc_filial ORDER BY desc_filial';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('desc_filial', 'desc_filial');
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $r = $query->getResult();
        $arr = [];
        foreach ($r as $item) {
            $arr[$item['desc_filial']] = urlencode($item['desc_filial']);
        }
        return $arr;
    }

    /**
     * @return array
     */
    public function getVendedores(): array
    {
        $sql = 'SELECT CONCAT(cod_vendedor, \' - \', nome_vendedor) as vendedor FROM rdp_rel_vendas01 GROUP BY cod_vendedor, nome_vendedor ORDER BY nome_vendedor';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('vendedor', 'vendedor');
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $r = $query->getResult();
        $arr = [];
        foreach ($r as $item) {
            $arr[$item['vendedor']] = urlencode($item['vendedor']);
        }
        return $arr;
    }




}

