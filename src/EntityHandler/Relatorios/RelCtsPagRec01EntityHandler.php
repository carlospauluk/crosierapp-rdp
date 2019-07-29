<?php

namespace App\EntityHandler\Relatorios;

use App\Entity\Relatorios\RelCtsPagRec01;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para RelCtsPagRec01.
 *
 * @author Carlos Eduardo Pauluk
 */
class RelCtsPagRec01EntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return RelCtsPagRec01::class;
    }
}