<?php

namespace App\EntityHandler\Relatorios;

use App\Entity\Relatorios\RelCliente01;
use App\Repository\Relatorios\RelCliente01Repository;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 *
 * @author Carlos Eduardo Pauluk
 */
class RelCliente01EntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return RelCliente01::class;
    }

    public function beforeSave($cliente)
    {
        /** @var RelCliente01 $cliente */
        if (!$cliente->getCodigo()) {
            /** @var RelCliente01Repository $repoRelCliente01 */
            $repoRelCliente01 = $this->doctrine->getRepository(RelCliente01::class);
            $cliente->setCodigo($repoRelCliente01->findProx('codigo'));
        }

    }


}