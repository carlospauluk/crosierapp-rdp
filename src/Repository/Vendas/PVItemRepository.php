<?php

namespace App\Repository\Vendas;

use App\Entity\Vendas\PVItem;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class PVItemRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return PVItem::class;
    }

}

