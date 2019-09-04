<?php

namespace App\EntityHandler\Relatorios;

use App\Entity\Relatorios\RelVendas01;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 *
 * @author Carlos Eduardo Pauluk
 */
class RelVendas01EntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return RelVendas01::class;
    }
}