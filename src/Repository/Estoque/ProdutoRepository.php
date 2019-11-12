<?php

namespace App\Repository\Estoque;


use App\Entity\Estoque\Produto;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class ProdutoRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return Produto::class;
    }

    /**
     * @return mixed
     */
    public function findDeptos(): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('deptoNome', 'deptoNome');

        $qry = $this->getEntityManager()->createNativeQuery('SELECT distinct(depto_nome) as deptoNome FROM vw_rdp_est_produto ORDER BY depto_nome', $rsm);

        return $qry->getArrayResult();
    }
}
