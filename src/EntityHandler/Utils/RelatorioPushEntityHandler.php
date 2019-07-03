<?php

namespace App\EntityHandler\Utils;

use App\Entity\Utils\RelatorioPush;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 * EntityHandler para RelatorioPush.
 *
 * @package App\EntityHandler\Utils
 * @author Carlos Eduardo Pauluk
 */
class RelatorioPushEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return RelatorioPush::class;
    }
}