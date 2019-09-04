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

}

