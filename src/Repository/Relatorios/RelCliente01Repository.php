<?php

namespace App\Repository\Relatorios;

use App\Entity\Relatorios\RelCliente01;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class RelCliente01Repository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return RelCliente01::class;
    }

    public function findClienteByStr(string $str): array
    {
        $sql = 'SELECT c FROM App\Entity\Relatorios\RelCliente01 c WHERE c.documento LIKE :documento OR c.nome LIKE :nome OR c.codigo LIKE :codigo ORDER BY c.nome';
        $query = $this->getEntityManager()->createQuery($sql);
        $query->setParameter('documento', $str);
        $query->setParameter('nome', '%' . $str . '%');
        $query->setParameter('codigo', $str);
        return $query->getResult();
    }

}

