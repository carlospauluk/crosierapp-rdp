<?php

namespace App\EntityHandler\Relatorios;

use App\Entity\Relatorios\RelEstoque01;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para RelEstoque01.
 *
 * @author Carlos Eduardo Pauluk
 */
class RelEstoque01EntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return RelEstoque01::class;
    }
}