<?php

namespace App\EntityHandler\Estoque;

use App\Entity\Estoque\PedidoCompra;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\EntityHandler;

/**
 *
 * @author Carlos Eduardo Pauluk
 */
class PedidoCompraEntityHandler extends EntityHandler
{

    public function getEntityClass(): string
    {
        return PedidoCompra::class;
    }

    public function beforeSave(/** @var PedidoCompra $pedidoCompra */ $pedidoCompra)
    {
        $total = 0.0;
        foreach ($pedidoCompra->itens as $item) {
            $total += $item->total;
        }
        $pedidoCompra->total = $total;
    }


}