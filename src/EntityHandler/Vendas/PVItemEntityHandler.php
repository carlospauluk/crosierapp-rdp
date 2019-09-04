<?php

namespace App\EntityHandler\Vendas;

use App\Entity\Vendas\PVItem;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 *
 * @author Carlos Eduardo Pauluk
 */
class PVItemEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return PVItem::class;
    }


}