<?php

namespace App\Repository\Relatorios;

use App\Entity\Relatorios\RelEstoque01;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class RelEstoque01Repository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return RelEstoque01::class;
    }


    /**
     * Utilizado no gráfico de Total de Estoque por Filial
     *
     * @return mixed
     */
    public function totalEstoquePorFilial()
    {

        $sql = 'SELECT desc_filial, SUM(qtde_atual * preco_venda) as total_venda, SUM(qtde_atual * custo_medio) as total_custo_medio FROM rdp_rel_estoque01 GROUP BY desc_filial;';


        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('desc_filial', 'desc_filial');
        $rsm->addScalarResult('total_venda', 'total_venda');
        $rsm->addScalarResult('total_custo_medio', 'total_custo_medio');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        return $query->getResult();
    }

}

