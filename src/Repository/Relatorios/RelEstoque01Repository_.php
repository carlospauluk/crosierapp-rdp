<?php

namespace App\Repository\Relatorios;

use App\Entity\Relatorios\RelEstoque01;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class RelEstoque01Repository_ extends FilterRepository
{

    public function getEntityClass(): string
    {
        return RelEstoque01::class;
    }

    /**
     * @param int $codFornecedor
     * @return null|string
     * @throws NonUniqueResultException
     */
    public function getNomeFornecedorByCodigo(int $codFornecedor): ?string
    {
        $sql = 'SELECT nome_fornec FROM rdp_rel_estoque01 WHERE cod_fornec = :codFornecedor LIMIT 1';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('nome_fornec', 'nome_fornec');
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('codFornecedor', $codFornecedor);
        $r = $query->getOneOrNullResult();

        return $r['nome_fornec'] ?? null;
    }

    /**
     * Utilizado no gráfico de Total de Estoque por Filial
     *
     * @return mixed
     */
    public function totalEstoquePorFilial()
    {
        $sql = 'SELECT desc_filial, SUM(qtde_atual) as total_qtde_atual, SUM(qtde_atual * preco_venda) as total_venda, SUM(qtde_atual * custo_medio) as total_custo_medio ' .
            'FROM rdp_rel_estoque01 GROUP BY desc_filial';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('desc_filial', 'desc_filial');
        $rsm->addScalarResult('total_qtde_atual', 'total_qtde_atual');
        $rsm->addScalarResult('total_venda', 'total_venda');
        $rsm->addScalarResult('total_custo_medio', 'total_custo_medio');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        return $query->getResult();
    }


    /**
     *
     * @param \DateTime $dtUltSaidaApartirDe
     * @param string|null $filial
     * @param int|null $codFornecedor
     * @return mixed
     */
    public function totalEstoque(\DateTime $dtUltSaidaApartirDe, ?string $filial = null, ?int $codFornecedor = null)
    {
        $sql_filial = $filial ? ' AND desc_filial = :filial' : '';

        $sql_codFornecedor = $codFornecedor ? ' AND cod_fornec = :codFornecedor' : '';

        $sql = 'SELECT SUM(qtde_atual) as total_qtde_atual, SUM(qtde_atual * preco_venda) as total_venda, SUM(qtde_atual * custo_medio) as total_custo_medio ' .
            'FROM rdp_rel_estoque01 WHERE dt_ult_saida >= :dtUltSaidaApartirDe ' .
            $sql_filial .
            $sql_codFornecedor;


        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('desc_filial', 'desc_filial');
        $rsm->addScalarResult('total_qtde_atual', 'total_qtde_atual');
        $rsm->addScalarResult('total_venda', 'total_venda');
        $rsm->addScalarResult('total_custo_medio', 'total_custo_medio');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('dtUltSaidaApartirDe', $dtUltSaidaApartirDe->setTime(0,0,0,0));
        if ($sql_filial) {
            $query->setParameter('filial', $filial);
        }
        if ($sql_codFornecedor) {
            $query->setParameter('codFornecedor', $codFornecedor);
        }
        return $query->getResult();
    }

    /**
     * @param \DateTime $dtUltSaidaApartirDe
     * @param string|null $filial
     * @param string|null $nomeFornecedor
     * @param int|null $start
     * @param int|null $limit
     * @return mixed
     */
    public function getReposicaoEstoque(\DateTime $dtUltSaidaApartirDe, ?string $filial = null, ?string $nomeFornecedor = null, ?int $start = 0, ?int $limit = 10)
    {
        $dql = 'SELECT e FROM App\Entity\Relatorios\RelEstoque01 e WHERE e.qtdeMinima > 0 AND e.qtdeAtual < e.qtdeMinima AND e.dtUltSaida >= :dtUltSaidaApartirDe';
        $dql .= $filial ? ' AND e.filial = :filial' : '';
        $dql .= $nomeFornecedor ? ' AND e.nomeFornecedor = :nomeFornecedor' : '';
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('dtUltSaidaApartirDe', $dtUltSaidaApartirDe->setTime(0,0,0,0));
        if ($filial) {
            $query->setParameter('filial', $filial);
        }
        if ($nomeFornecedor) {
            $query->setParameter('nomeFornecedor', $nomeFornecedor);
        }
        $query->setFirstResult($start);
        $query->setMaxResults($limit);
        return $query->getResult();
    }

    /**
     * @param \DateTime $dtUltSaidaApartirDe
     * @param string|null $filial
     * @param string|null $nomeFornecedor
     * @return mixed
     */
    public function getReposicaoEstoqueCount(\DateTime $dtUltSaidaApartirDe, ?string $filial = null, ?string $nomeFornecedor = null)
    {
        try {
            $sql = 'SELECT count(*) as counted FROM rdp_rel_estoque01 WHERE qtde_minima > 0 AND qtde_atual < qtde_minima AND dt_ult_saida >= :dtUltSaidaApartirDe';
            $sql .= $filial ? ' AND desc_filial = :filial' : '';
            $sql .= $nomeFornecedor ? ' AND nome_fornec = :nomeFornecedor' : '';
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('counted', 'counted');
            $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
            $query->setParameter('dtUltSaidaApartirDe', $dtUltSaidaApartirDe->setTime(0,0,0,0));
            if ($filial) {
                $query->setParameter('filial', $filial);
            }
            if ($nomeFornecedor) {
                $query->setParameter('nomeFornecedor', $nomeFornecedor);
            }
            return $query->getOneOrNullResult()['counted'] ?? null;
        } catch (NonUniqueResultException $e) {
            return null;
        }
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
     * @return array
     */
    public function getFornecedores(): array
    {
        $sql = 'SELECT cod_fornec, nome_fornec FROM rdp_rel_vendas01 GROUP BY cod_fornec, nome_fornec ORDER BY nome_fornec';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('cod_fornec', 'cod_fornec');
        $rsm->addScalarResult('nome_fornec', 'nome_fornec');
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $r = $query->getResult();
        $arr = [];
        foreach ($r as $item) {
            $e['id'] = $item['cod_fornec'];
            $e['text'] = $item['nome_fornec'];
            $arr[] = $e;
        }
        return $arr;
    }


}

