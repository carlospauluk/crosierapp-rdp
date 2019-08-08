<?php

namespace App\EntityHandler\Relatorios;

use App\Entity\Relatorios\RelCompras01;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para RelCompras01.
 *
 * @author Carlos Eduardo Pauluk
 */
class RelCompras01EntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return RelCompras01::class;
    }
}