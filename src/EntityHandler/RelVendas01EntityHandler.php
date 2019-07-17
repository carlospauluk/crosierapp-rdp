<?php

namespace App\EntityHandler;

use App\Entity\RelVendas01;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para RelVendas01.
 *
 * @package App\EntityHandler\Utils
 * @author Carlos Eduardo Pauluk
 */
class RelVendas01EntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return RelVendas01::class;
    }
}