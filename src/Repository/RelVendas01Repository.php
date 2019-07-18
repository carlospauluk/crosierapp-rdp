<?php

namespace App\Repository;

use App\Entity\RelVendas01;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Repository para a entidade RelatorioPush.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class RelVendas01Repository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return RelVendas01::class;
    }


    public function totalVendasPorFornecedor() {
        $sql = 'SELECT nome_fornec, sum(total_preco_venda) as total_venda FROM rdp_rel_vendas01 GROUP BY nome_fornec';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('nome_fornec', 'nome_fornec');
        $rsm->addScalarResult('total_venda', 'total_venda');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $rs = $query->getResult();

        return $rs;

    }


    public function totalVendasPorVendedor() {
        $sql = 'SELECT nome_vendedor, sum(total_preco_venda) as total_venda FROM rdp_rel_vendas01 GROUP BY nome_vendedor';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('nome_vendedor', 'nome_vendedor');
        $rsm->addScalarResult('total_venda', 'total_venda');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $rs = $query->getResult();

        return $rs;

    }


}

