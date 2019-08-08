<?php

namespace App\Repository\Relatorios;

use App\Entity\Relatorios\RelCompras01;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class RelCompras01Repository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return RelCompras01::class;
    }


    /**
     *
     * @param int $codProduto
     * @param string|null $loja
     * @return mixed
     */
    public function itensDeComprasPorProduto(int $codProduto, ?string $loja = null)
    {
        $sql_AND_loja = $loja ? ' AND loja = :loja ' : '';

        $sql = 'SELECT pv_compra, qtde, dt_emissao, cod_fornec, nome_fornec, cod_prod, desc_prod, total_preco_venda, total_preco_custo, rentabilidade, loja, dt_prev_entrega
                    FROM rdp_rel_compras01
                     WHERE cod_prod = :codProduto 
                     ' . $sql_AND_loja . '
                     ORDER BY dt_emissao';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('pv_compra', 'pv_compra');
        $rsm->addScalarResult('qtde', 'qtde');
        $rsm->addScalarResult('dt_emissao', 'dt_emissao');
        $rsm->addScalarResult('cod_fornec', 'cod_fornec');
        $rsm->addScalarResult('nome_fornec', 'nome_fornec');
        $rsm->addScalarResult('cod_prod', 'cod_prod');
        $rsm->addScalarResult('desc_prod', 'desc_prod');
        $rsm->addScalarResult('total_preco_venda', 'total_preco_venda');
        $rsm->addScalarResult('total_preco_custo', 'total_preco_custo');
        $rsm->addScalarResult('rentabilidade', 'rentabilidade');
        $rsm->addScalarResult('loja', 'loja');
        $rsm->addScalarResult('dt_prev_entrega', 'dt_prev_entrega');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('codProduto', $codProduto);
        if ($loja) {
            $query->setParameter('loja', $loja);
        }
        return $query->getResult();
    }



    /**
     * @param int $pv
     * @return mixed
     */
    public function itensDoPreVenda(int $pv)
    {
        $sql = 'SELECT num_item, cod_prod, cod_fornec, nome_fornec, desc_prod, qtde, total_preco_custo, total_preco_venda, (((total_preco_venda / total_preco_custo) - 1) * 100.0) as rent
                    FROM rdp_rel_compras01
                     WHERE pv_compra = :prevenda ORDER BY num_item';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('num_item', 'num_item');
        $rsm->addScalarResult('cod_prod', 'cod_prod');
        $rsm->addScalarResult('desc_prod', 'desc_prod');
        $rsm->addScalarResult('cod_fornec', 'cod_fornec');
        $rsm->addScalarResult('nome_fornec', 'nome_fornec');
        $rsm->addScalarResult('qtde', 'qtde');
        $rsm->addScalarResult('total_preco_custo', 'total_preco_custo');
        $rsm->addScalarResult('total_preco_venda', 'total_preco_venda');
        $rsm->addScalarResult('rent', 'rent');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('prevenda', $pv);

        return $query->getResult();
    }


    /**
     * @param int $pv
     * @return mixed
     * @throws ViewException
     */
    public function totaisPreVenda(int $pv)
    {
        $sql = 'SELECT dt_emissao, grupo, loja, cod_vendedor, nome_vendedor, total_custo_pv, total_venda_pv, rentabilidade_pv, sum(total_preco_venda) as subtotal
                    FROM rdp_rel_compras01
                     WHERE pv_compra = :prevenda
                     GROUP BY dt_emissao, grupo, loja, cod_vendedor, nome_vendedor, total_custo_pv, total_venda_pv, rentabilidade_pv';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('dt_emissao', 'dt_emissao');
        $rsm->addScalarResult('grupo', 'grupo');
        $rsm->addScalarResult('loja', 'loja');
        $rsm->addScalarResult('cod_vendedor', 'cod_vendedor');
        $rsm->addScalarResult('nome_vendedor', 'nome_vendedor');
        $rsm->addScalarResult('total_custo_pv', 'total_custo_pv');
        $rsm->addScalarResult('total_venda_pv', 'total_venda_pv');
        $rsm->addScalarResult('rentabilidade_pv', 'rentabilidade_pv');
        $rsm->addScalarResult('subtotal', 'subtotal');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('prevenda', $pv);

        try {
            $r = $query->getOneOrNullResult();
            $descontos = $r['subtotal'] - $r['total_venda_pv'];
            $r['descontos'] = $descontos;
            return $r;
        } catch (NonUniqueResultException $e) {
            throw new ViewException('Erro ao totalizar PV ' . $pv);
        }


    }


}

