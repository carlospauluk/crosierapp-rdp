<?php

namespace App\EntityHandler\Relatorios;

use App\Entity\Relatorios\RelCompFor01;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para RelVendas01.
 *
 * @author Carlos Eduardo Pauluk
 */
class RelCompFor01EntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return RelCompFor01::class;
    }
}