<?php

namespace App\EntityHandler\Vendas;

use App\Entity\Vendas\PV;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;
use CrosierSource\CrosierLibBaseBundle\Utils\StringUtils\StringUtils;

/**
 *
 * @author Carlos Eduardo Pauluk
 */
class PVEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return PV::class;
    }

    public function beforeSave(/** @var PV $pv */ $pv)
    {
        if (!$pv->getUuid()) {
            $pv->setUuid(StringUtils::guidv4());
        }

    }


}