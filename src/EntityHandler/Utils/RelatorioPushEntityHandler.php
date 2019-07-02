<?php

namespace App\EntityHandler\Utils;

use App\Entity\Base\CategoriaPessoa;
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
        return CategoriaPessoa::class;
    }
}