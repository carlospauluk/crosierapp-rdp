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

    public function beforeSave(/** @var PVItem $pvItem */ $pvItem)
    {
        $pvItem->setTotal(bcsub(bcmul($pvItem->getPrecoOrc(), $pvItem->getQtde(), 2), $pvItem->getDesconto(), 2));
    }


}