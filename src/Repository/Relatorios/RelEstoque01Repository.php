<?php

namespace App\Repository\Relatorios;

use App\Entity\Relatorios\RelEstoque01;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class RelEstoque01Repository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return RelEstoque01::class;
    }

    /**
     * @return array
     */
    public function getFiliais(): array
    {
        $sql = 'SELECT desc_filial FROM rdp_rel_estoque01 GROUP BY desc_filial ORDER BY desc_filial';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('desc_filial', 'desc_filial');
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $r = $query->getResult();
        $arr = [];
        foreach ($r as $item) {
            $e['id'] = urlencode($item['desc_filial']);
            $e['text'] = $item['desc_filial'];
            $arr[] = $e;
        }
        return $arr;
    }


    /**
     * Utilizado no gráfico de Total de Estoque por Filial
     *
     * @param string|null $descFilial
     * @return mixed
     */
    public function totalEstoquePorFilial(string $descFilial = null)
    {
        $sql_descFilial = '';
        if ($descFilial) {
            $sql_descFilial = ' WHERE desc_filial = :descFilial';
        }

        $sql = 'SELECT desc_filial, SUM(qtde_atual) as total_qtde_atual, SUM(qtde_atual * preco_venda) as total_venda, SUM(qtde_atual * custo_medio) as total_custo_medio FROM rdp_rel_estoque01 ' . $sql_descFilial . ' GROUP BY desc_filial;';


        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('desc_filial', 'desc_filial');
        $rsm->addScalarResult('total_qtde_atual', 'total_qtde_atual');
        $rsm->addScalarResult('total_venda', 'total_venda');
        $rsm->addScalarResult('total_custo_medio', 'total_custo_medio');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        if ($sql_descFilial) {
            $query->setParameter('descFilial', $descFilial);
        }
        return $query->getResult();
    }


    /**
     * @param int|null $start
     * @param int|null $limit
     * @param string|null $descFilial
     * @return mixed
     */
    public function getReposicaoEstoque(?int $start = 0, ?int $limit = 10, ?string $descFilial = null)
    {
        $dql = 'SELECT e FROM App\Entity\Relatorios\RelEstoque01 e WHERE e.qtdeMinima > 0 AND e.qtdeAtual < e.qtdeMinima';
        $dql .= $descFilial ? ' AND e.descFilial = :descFilial' : '';
        $query = $this->getEntityManager()->createQuery($dql);
        if ($descFilial) {
            $query->setParameter('descFilial', $descFilial);
        }
        $query->setFirstResult($start);
        $query->setMaxResults($limit);
        return $query->getResult();
    }


    /**
     * @return mixed
     */
    public function getReposicaoEstoqueTotalPorFilial()
    {

        $sql = 'SELECT desc_filial, SUM(qtde_minima - qtde_atual) as deficit FROM rdp_rel_estoque01 WHERE qtde_minima > 0 AND qtde_atual < qtde_minima GROUP BY desc_filial ORDER BY deficit DESC';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('desc_filial', 'desc_filial');
        $rsm->addScalarResult('deficit', 'deficit');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $results = $query->getResult();

        $filiais = $this->getFiliais();

        $ret = [];

        foreach ($filiais as $filial) {
            $deficit = 0;
            foreach ($results as $r) {
                if ($r['desc_filial'] === $filial['text']) {
                    $deficit = $r['deficit'];
                    break;
                }
            }
            $ret[] = ['desc_filial' => $filial['text'],
                'deficit' => $deficit];
        }

        return $ret;


    }

}

