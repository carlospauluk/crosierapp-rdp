<?php

namespace App\Repository\Utils;

use App\Entity\Utils\RelatorioPush;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 * Repository para a entidade RelatorioPush.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class RelatorioPushRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return RelatorioPush::class;
    }


}

