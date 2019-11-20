<?php

namespace App\EntityHandler\Estoque;

use App\Entity\Estoque\Produto;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 *
 * @author Carlos Eduardo Pauluk
 */
class ProdutoEntityHandler extends EntityHandler
{

    public function getEntityClass()
    {
        return Produto::class;
    }


}