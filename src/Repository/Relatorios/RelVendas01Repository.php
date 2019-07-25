<?php

namespace App\Repository\Relatorios;

use App\Entity\Relatorios\RelVendas01;
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


    /**
     * @param \DateTime|null $dtIni
     * @param \DateTime|null $dtFim
     * @return mixed
     */
    public function totalVendasPorFornecedor(\DateTime $dtIni = null, \DateTime $dtFim = null)
    {
        $dtIni = $dtIni ?? \DateTime::createFromFormat('d/m/Y', '01/01/0000');
        $dtIni->setTime(0, 0, 0, 0);
        $dtFim = $dtFim ?? \DateTime::createFromFormat('d/m/Y', '01/01/9999');
        $dtFim->setTime(23, 59, 59, 99999);

        $sql = 'SELECT nome_fornec, sum(total_preco_venda) as total_venda FROM rdp_rel_vendas01 WHERE dt_emissao BETWEEN :dtIni and :dtFim GROUP BY nome_fornec ORDER BY total_venda';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('nome_fornec', 'nome_fornec');
        $rsm->addScalarResult('total_venda', 'total_venda');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('dtIni', $dtIni);
        $query->setParameter('dtFim', $dtFim);

        return $query->getResult();

    }

    /**
     * @param \DateTime|null $dtIni
     * @param \DateTime|null $dtFim
     * @return mixed
     */
    public function totalVendasPorVendedor(\DateTime $dtIni = null, \DateTime $dtFim = null)
    {
        $dtIni = $dtIni ?? \DateTime::createFromFormat('d/m/Y', '01/01/0000');
        $dtIni->setTime(0, 0, 0, 0);
        $dtFim = $dtFim ?? \DateTime::createFromFormat('d/m/Y', '01/01/9999');
        $dtFim->setTime(23, 59, 59, 99999);
        $sql = 'SELECT nome_vendedor, sum(total_preco_venda) as total_venda FROM rdp_rel_vendas01 WHERE dt_emissao BETWEEN :dtIni and :dtFim GROUP BY nome_vendedor ORDER BY total_venda';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('nome_vendedor', 'nome_vendedor');
        $rsm->addScalarResult('total_venda', 'total_venda');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('dtIni', $dtIni);
        $query->setParameter('dtFim', $dtFim);

        return $query->getResult();

    }


}

