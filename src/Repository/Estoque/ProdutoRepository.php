<?php

namespace App\Repository\Estoque;


use App\Entity\Estoque\Produto;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;

/**
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class ProdutoRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return Produto::class;
    }

}
