<?php

namespace App\Repository\Vendas;


use App\Entity\Vendas\Venda;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Repository para a entidade Venda.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class VendaRepository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return Venda::class;
    }

    /**
     * @param \DateTime $dtVenda
     * @param $pv
     * @throws \Exception
     */
    public function findByDtVendaAndPV(\DateTime $dtVenda, $pv)
    {
        $dtVenda->setTime(0, 0, 0, 0);
        $ql = "SELECT v FROM App\Entity\Vendas\Venda v WHERE v.dtVenda = :dtVenda AND v.pv = :pv";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setParameters(array(
            'dtVenda' => $dtVenda,
            'pv' => $pv
        ));

        $results = $query->getResult();

        if (count($results) > 1) {
            throw new \Exception('Mais de uma venda encontrada para [' . $dtVenda->format('Y-m-d') . '] e [' . $pv . ']');
        }

        return count($results) == 1 ? $results[0] : null;
    }

    /**
     * @param $pv
     * @param $mesano
     * @throws \Exception
     */
    public function findByPVAndMesAno($pv, $mesano)
    {
        $ql = "SELECT v FROM App\Entity\Vendas\Venda v WHERE v.mesano = :mesano AND v.pv = :pv";
        $query = $this->getEntityManager()->createQuery($ql);
        $query->setParameters(array(
            'mesano' => $mesano,
            'pv' => $pv
        ));

        $results = $query->getResult();

        if (count($results) > 1) {
            throw new \Exception('Mais de uma venda encontrada para [' . $pv . '] e [' . $mesano . ']');
        }

        return count($results) == 1 ? $results[0] : null;
    }

    /**
     * @param $pv
     * @throws \Exception
     */
    public function findByPV($pv)
    {
        $hoje = new \DateTime();
        $mesano = $hoje->format('Ym');
        return $this->findByPVAndMesAno($pv, $mesano);
    }

    /**
     * Utilizado no gráfico de Total de Vendas por Fornecedor.
     *
     * @param \DateTime|null $dtIni
     * @param \DateTime|null $dtFim
     * @param string|null $lojas
     * @param string|null $grupos
     * @return mixed
     * @throws ViewException
     */
    public function totalVendasPorFornecedor(\DateTime $dtIni = null, \DateTime $dtFim = null, ?string $lojas = null, ?string $grupos = null)
    {
        $dtIni = $dtIni ?? \DateTime::createFromFormat('d/m/Y', '01/01/0000');
        $dtIni->setTime(0, 0, 0, 0);
        $dtFim = $dtFim ?? \DateTime::createFromFormat('d/m/Y', '01/01/9999');
        $dtFim->setTime(23, 59, 59, 99999);


        $sql = 'SELECT i.fornecedor_nome, sum(i.preco_venda * i.qtde) as total_venda FROM ven_venda_item i, ven_venda v WHERE v.id = i.venda_id AND v.dt_nota BETWEEN :dtIni and :dtFim ';
        $sql .= $grupos ? 'AND v.json_data->>"$.grupo" IN (:grupos) ' : '';
        $sql .= $lojas ? 'AND v.json_data->>"$.loja" IN (:lojas) ' : '';
        $sql .= ' GROUP BY i.fornecedor_nome';


        $params['dtIni'] = $dtIni->format('Y-m-d');
        $params['dtFim'] = $dtFim->format('Y-m-d');
        if ($grupos) {
            $params['grupos'] = $grupos;
        }
        if ($lojas) {
            $params['lojas'] = $lojas;
        }

        $total = $this->totalVendasPor($dtIni, $dtFim, $lojas, $grupos);
        $results = $this->getEntityManager()->getConnection()->fetchAll($sql, $params);

        foreach ($results as $k => $r) {
            $results[$k]['participacao'] = bcmul(bcdiv($r['total_venda'], $total, 6), 100, 4);
        }


        return $results;
    }


    /**
     * Utilizado no gráfico de Total de Vendas por Fornecedor.
     *
     * @param \DateTime|null $dtIni
     * @param \DateTime|null $dtFim
     * @param string|null $lojas
     * @param string|null $grupos
     * @return mixed
     * @throws ViewException
     */
    public function totalVendasPor(\DateTime $dtIni = null, \DateTime $dtFim = null, ?string $lojas = null, ?string $grupos = null)
    {
        try {
            $dtIni = $dtIni ?? \DateTime::createFromFormat('d/m/Y', '01/01/0000');
            $dtIni->setTime(0, 0, 0, 0);
            $dtFim = $dtFim ?? \DateTime::createFromFormat('d/m/Y', '01/01/9999');
            $dtFim->setTime(23, 59, 59, 99999);
            $sqlTotal = 'SELECT sum(valor_total) as total_venda FROM ven_venda WHERE dt_nota BETWEEN :dtIni and :dtFim ';
            $sqlTotal .= $grupos ? 'AND json_data->>"$.grupo" IN (:grupos) ' : '';
            $sqlTotal .= $lojas ? 'AND json_data->>"$.loja" IN (:lojas) ' : '';
            $params['dtIni'] = $dtIni->format('Y-m-d');
            $params['dtFim'] = $dtFim->format('Y-m-d');
            if ($grupos) {
                $params['grupos'] = $grupos;
            }
            if ($lojas) {
                $params['lojas'] = $lojas;
            }
            return $this->getEntityManager()->getConnection()->fetchAssoc($sqlTotal, $params)['total_venda'];
        } catch (DBALException | \Throwable $e) {
            $this->getLogger()->error('Erro ao calcular total');
            $this->getLogger()->error($e->getMessage());
            throw new ViewException('Erro ao calcular total');
        }
    }


    /**
     * Utilizado na listagem de produtos vendidos por fornecedor.
     *
     * @param \DateTime|null $dtIni
     * @param \DateTime|null $dtFim
     * @param string $nomeFornec
     * @param array|null $lojas
     * @param array|null $grupos
     * @param bool $totalGeral
     * @return mixed
     */
    public function itensVendidos(\DateTime $dtIni, \DateTime $dtFim, string $nomeFornec, ?array $lojas = null, ?array $grupos = null, bool $totalGeral = false)
    {
        $dtIni = $dtIni ?? \DateTime::createFromFormat('d/m/Y', '01/01/0000');
        $dtIni->setTime(0, 0, 0, 0);
        $dtFim = $dtFim ?? \DateTime::createFromFormat('d/m/Y', '01/01/9999');
        $dtFim->setTime(23, 59, 59, 99999);

        $sql = 'SELECT ';

        if (!$totalGeral) {
            $sql .= 'cod_prod, desc_prod, ';
        }

        $sql .= 'sum(qtde) as qtde_total, sum(json_data->>"$.total_preco_custo") as tpc, sum(qtde * preco_venda) as tpv, (((sum(qtde * preco_venda) / sum(json_data->>"$.total_preco_custo")) - 1) * 100.0) as rent 
                    FROM ven_venda
                     WHERE fornecedor_nome = :nomeFornec AND dt_nota BETWEEN :dtIni AND :dtFim ';

        if ($grupos) {
            $sql .= ' AND json_data->>"$.grupo" IN (:grupos)';
        }
        if ($lojas) {
            $sql .= ' AND json_data->>"$.loja" IN (:lojas)';
        }

        if (!$totalGeral) {
            $sql .= ' GROUP BY json_data->>"$.produto_erp_codigo", json_data->>"$.produto_nome"';
        }

        $sql .= ' ORDER BY json_data->>"$.rentabilidade_item"';

        $params = [
            'dtIni' => $dtIni,
            'dtFim' => $dtFim,
            'nomeFornec' => $nomeFornec
        ];

        if ($grupos) {
            $params['grupos'] = $grupos;
        }
        if ($lojas) {
            $params['lojas'] = $lojas;
        }
        /** @var Connection $conn */
        $conn = $this->getEntityManager()->getConnection();
        return $conn->fetchAll($sql, $params);
    }

    /**
     * Utilizado no gráfico de Total de Vendas por Vendedor.
     *
     * @param \DateTime|null $dtIni
     * @param \DateTime|null $dtFim
     * @param string|null $lojas
     * @param string|null $grupos
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function totalVendasPorVendedor(\DateTime $dtIni = null, \DateTime $dtFim = null, ?string $lojas = null, ?string $grupos = null)
    {
        $dtIni = $dtIni ?? \DateTime::createFromFormat('d/m/Y', '01/01/0000');
        $dtIni->setTime(0, 0, 0, 0);
        $dtFim = $dtFim ?? \DateTime::createFromFormat('d/m/Y', '01/01/9999');
        $dtFim->setTime(23, 59, 59, 99999);

        $sql_AND_grupo = '';
        if ($grupos) {
            $sql_AND_grupo .= ' AND json_data->>"$.grupo" IN (:grupos)';
        }
        $sql_AND_loja = '';
        if ($lojas) {
            $sql_AND_loja .= ' AND json_data->>"$.loja" IN (:lojas)';
        }

        $sql = '
            SELECT 
                CONCAT(cod_vendedor, \' - \', nome_vendedor) as nome_vendedor, 
                SUM(total_venda_pv) as total_venda,
                SUM(total_custo_pv) as total_custo,
                (((SUM(total_venda_pv) / SUM(total_custo_pv)) - 1) * 100.0) as rent
            FROM 
                (
                    SELECT json_data->>"$.vendedor_codigo", json_data->>"$.vendedor_nome", json_data->>"$.prevenda_ekt", valor_total, json_data->>"$.total_custo_pv" 
                    FROM ven_venda 
                    WHERE 
                        dt_nota BETWEEN :dtIni AND :dtFim
                        ' . $sql_AND_grupo . $sql_AND_loja . '
                    GROUP BY cod_vendedor, nome_vendedor, prevenda, total_venda_pv, total_custo_pv) a 
            GROUP BY json_data->>"$.vendedor_codigo", json_data->>"$.vendedor_nome" ORDER BY valor_total';


        $params = [
            'dtIni' => $dtIni,
            'dtFim' => $dtFim,
        ];

        if ($grupos) {
            $params['grupos'] = $grupos;
        }
        if ($lojas) {
            $params['lojas'] = $lojas;
        }
        /** @var Connection $conn */
        $conn = $this->getEntityManager()->getConnection();
        $dados = $conn->fetchAll($sql, $params);

        $rentabilidadeGeral = $this->totalRentabilidade($dtIni, $dtFim, $lojas, $grupos)['rent'] ?? 0.0;

        return ['dados' => $dados, 'rentabilidadeGeral' => $rentabilidadeGeral];
    }

    /**
     * Utilizado no gráfico de Total de Vendas por Vendedor.
     *
     * @param \DateTime|null $dtIni
     * @param \DateTime|null $dtFim
     * @param string|null $lojas
     * @param string|null $grupos
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function totalRentabilidade(\DateTime $dtIni = null, \DateTime $dtFim = null, ?string $lojas = null, ?string $grupos = null)
    {
        $dtIni = $dtIni ?? \DateTime::createFromFormat('d/m/Y', '01/01/0000');
        $dtIni->setTime(0, 0, 0, 0);
        $dtFim = $dtFim ?? \DateTime::createFromFormat('d/m/Y', '01/01/9999');
        $dtFim->setTime(23, 59, 59, 99999);

        $sql_AND_grupo = '';
        if ($grupos) {
            $sql_AND_grupo .= ' AND grupo IN (:grupos)';
        }
        $sql_AND_loja = '';
        if ($lojas) {
            $sql_AND_loja .= ' AND loja IN (:lojas)';
        }

        $sql = '
            SELECT 
                (((SUM(valor_total) / SUM(json_data->>"$.total_custo_pv")) - 1) * 100.0) as rent
            FROM 
                (
                    SELECT json_data->>"$.vendedor_codigo", json_data->>"$.vendedor_nome", json_data->>"$.prevenda_ekt", valor_total, json_data->>"$.total_custo_pv"
                    FROM ven_venda
                    WHERE 
                        dt_nota BETWEEN :dtIni AND :dtFim
                        ' . $sql_AND_grupo . $sql_AND_loja . '
                    GROUP BY json_data->>"$.vendedor_codigo", json_data->>"$.vendedor_nome", json_data->>"$.prevenda", valor_total, json_data->>"$.total_custo_pv") a 
            ';

        $params = [
            'dtIni' => $dtIni,
            'dtFim' => $dtFim,
        ];

        if ($grupos) {
            $params['grupos'] = $grupos;
        }
        if ($lojas) {
            $params['lojas'] = $lojas;
        }
        /** @var Connection $conn */
        $conn = $this->getEntityManager()->getConnection();
        return $conn->fetchAssoc($sql, $params);
    }


    /**
     * @param \DateTime|null $dtIni
     * @param \DateTime|null $dtFim
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNomeFornecedorMaisVendido(\DateTime $dtIni, \DateTime $dtFim): ?string
    {
        $dtIni = $dtIni ?? \DateTime::createFromFormat('d/m/Y', '01/01/0000');
        $dtIni->setTime(0, 0, 0, 0);
        $dtFim = $dtFim ?? \DateTime::createFromFormat('d/m/Y', '01/01/9999');
        $dtFim->setTime(23, 59, 59, 99999);

        $sql = 'SELECT fornecedor_nome, sum(valor_total) as tpv
                    FROM ven_venda
                     WHERE dt_nota BETWEEN :dtIni AND :dtFim GROUP BY fornecedor_nome ORDER BY tpv LIMIT 1';

        $params = [
            'dtIni' => $dtIni,
            'dtFim' => $dtFim,
        ];

        /** @var Connection $conn */
        $conn = $this->getEntityManager()->getConnection();
        $r = $conn->fetchAssoc($sql, $params);

        return $r['fornecedor_nome'] ?? null;

    }


    /**
     * @return array
     */
    public function getFornecedores(): array
    {
        $sql = 'SELECT fornecedor_nome FROM ven_venda_item GROUP BY fornecedor_codigo, fornecedor_nome ORDER BY fornecedor_nome';
        /** @var Connection $conn */
        $conn = $this->getEntityManager()->getConnection();
        $r = $conn->fetchAll($sql);
        $arr = [];
        foreach ($r as $item) {
            $e['id'] = $item['fornecedor_nome'];
            $e['text'] = $item['fornecedor_nome'];
            $arr[] = $e;
        }
        return $arr;
    }

    /**
     * @return array
     */
    public function getGrupos(): array
    {
        $sql = 'SELECT distinct(json_data->>"$.grupo") as grupo FROM ven_venda ORDER BY json_data->>"$.grupo"';
        /** @var Connection $conn */
        $conn = $this->getEntityManager()->getConnection();
        $r = $conn->fetchAll($sql);
        $arr = [];
        foreach ($r as $item) {
            $e['id'] = $item['grupo'];
            $e['text'] = $item['grupo'];
            $arr[] = $e;
        }

        return $arr;
    }

    /**
     * @return array
     */
    public function getLojas(): array
    {
        $sql = 'SELECT distinct(json_data->>"$.loja") as loja FROM ven_venda ORDER BY json_data->>"$.loja"';
        /** @var Connection $conn */
        $conn = $this->getEntityManager()->getConnection();
        $r = $conn->fetchAll($sql);
        $arr = [];
        foreach ($r as $item) {
            $e['id'] = $item['loja'];
            $e['text'] = $item['loja'];
            $arr[] = $e;
        }

        return $arr;
    }


    /**
     * @return array
     */
    public function getVendedores(): array
    {
        $sql = 'SELECT CONCAT(vendedor_codigo, \' - \', vendedor_nome) as vendedor FROM ven_venda GROUP BY vendedor_codigo, vendedor_nome ORDER BY vendedor_nome';
        /** @var Connection $conn */
        $conn = $this->getEntityManager()->getConnection();
        $r = $conn->fetchAll($sql);
        $arr = [];
        foreach ($r as $item) {
            $e['id'] = $item['vendedor'];
            $e['text'] = $item['vendedor'];
            $arr[] = $e;
        }
        return $arr;
    }

    /**
     * @param \DateTime|null $dtIni
     * @param \DateTime|null $dtFim
     * @param string $produto
     * @param array|null $lojas
     * @param array|null $grupos
     * @return mixed
     */
    public function preVendasPorPeriodoEProduto(\DateTime $dtIni, \DateTime $dtFim, string $produto, ?array $lojas = null, ?array $grupos = null)
    {
        $dtIni = $dtIni ?? \DateTime::createFromFormat('d/m/Y', '01/01/0000');
        $dtIni->setTime(0, 0, 0, 0);
        $dtFim = $dtFim ?? \DateTime::createFromFormat('d/m/Y', '01/01/9999');
        $dtFim->setTime(23, 59, 59, 99999);

        $sql = 'SELECT json_data->>"$.prevenda_ekt" as prevenda, dt_nota, json_data->>"$.vendedor_codigo", json_data->>"$.vendedor_nome", valor_total, json_data->>"$.total_custo_pv", 
                    (((valor_total / json_data->>"$.total_custo_pv") - 1) * 100.0) as rent, cliente_pv
                    FROM ven_venda
                     WHERE id IN (SELECT venda_id FROM ven_venda_ITEM WHERE CONCAT(json_data->>"$.produto_erp_codigo", \' - \', json_data->>"$.produto_nome") = :produto) AND dt_nota BETWEEN :dtIni AND :dtFim ';

        if ($grupos) {
            $sql .= 'AND json_data->>"$.grupo" IN (:grupos) ';
        }
        if ($lojas) {
            $sql .= 'AND json_data->>"$.loja" IN (:lojas) ';
        }

        $sql .= 'GROUP BY json_data->>"$.prevenda_ekt" as prevenda, dt_nota, json_data->>"$.vendedor_codigo", json_data->>"$.vendedor_nome", valor_total, json_data->>"$.total_custo_pv", 
        json_data->>"$.cliente_cnpj" ORDER BY dt_nota,  valor_total';

        $params = [
            'dtIni' => $dtIni,
            'dtFim' => $dtFim,
            'produto' => $produto,
        ];

        if ($grupos) {
            $params['grupos'] = $grupos;
        }
        if ($lojas) {
            $params['lojas'] = $lojas;
        }

        /** @var Connection $conn */
        $conn = $this->getEntityManager()->getConnection();
        return $conn->fetchAll($sql, $params);
    }

    /**
     * Utilizado na listagem de PVs por período e vendedor.
     *
     * @param \DateTime|null $dtIni
     * @param \DateTime|null $dtFim
     * @param int $codVendedor
     * @param array|null $lojas
     * @param array|null $grupos
     * @return mixed
     */
    public function preVendasPorPeriodoEVendedor(\DateTime $dtIni, \DateTime $dtFim, int $codVendedor, ?array $lojas = null, ?array $grupos = null)
    {
        $dtIni = $dtIni ?? \DateTime::createFromFormat('d/m/Y', '01/01/0000');
        $dtIni->setTime(0, 0, 0, 0);
        $dtFim = $dtFim ?? \DateTime::createFromFormat('d/m/Y', '01/01/9999');
        $dtFim->setTime(23, 59, 59, 99999);


        $sql_AND_grupo = '';
        if ($grupos) {
            $sql_AND_grupo .= ' AND json_data->>"$.grupo" IN (:grupos)';
        }
        $sql_AND_loja = '';
        if ($lojas) {
            $sql_AND_loja .= ' AND json_data->>"$.loja" IN (:lojas)';
        }

        $sql = 'SELECT json_data->>"$.prevenda_ekt" as prevenda, dt_nota, json_data->>"$.vendedor_codigo", json_data->>"$.vendedor_nome", valor_total, json_data->>"$.total_custo_pv",
       (((valor_total / json_data->>"$.total_custo_pv") - 1) * 100.0) as rent, json_data->>"$.cliente_nome"
                    FROM ven_venda
                     WHERE json_data->>"$.vendedor_codigo" = :codVendedor AND dt_nota BETWEEN :dtIni AND :dtFim 
                     ' . $sql_AND_grupo . $sql_AND_loja . '
                     GROUP BY prevenda, dt_nota, cod_vendedor, nome_vendedor, total_venda_pv, total_custo_pv, cliente_pv ORDER BY dt_nota, total_venda_pv';


        $params = [
            'dtIni' => $dtIni,
            'dtFim' => $dtFim,
            'codVendedor' => $codVendedor
        ];

        if ($grupos) {
            $params['grupos'] = $grupos;
        }
        if ($lojas) {
            $params['lojas'] = $lojas;
        }

        /** @var Connection $conn */
        $conn = $this->getEntityManager()->getConnection();
        return $conn->fetchAll($sql, $params);
    }


    /**
     * @param int $pv
     * @return mixed
     */
    public function itensDoPreVenda(int $pv)
    {
        $sql = 'SELECT num_item, cod_prod, desc_prod, qtde, total_preco_custo, total_preco_venda, (((total_preco_venda / total_preco_custo) - 1) * 100.0) as rent
                    FROM rdp_rel_vendas01
                     WHERE prevenda = :prevenda ORDER BY num_item';

        /** @var Connection $conn */
        $conn = $this->getEntityManager()->getConnection();
        return $conn->fetchAll($sql, ['prevenda' => $pv]);
    }


    /**
     * @param int $pv
     * @return mixed
     * @throws ViewException
     * @throws DBALException
     */
    public function totaisPreVenda(int $pv)
    {
        $sql = 'SELECT dt_nota, cliente_pv, grupo, loja, cod_vendedor, nome_vendedor, total_custo_pv, total_venda_pv, rentabilidade_pv, sum(total_preco_venda) as subtotal
                    FROM rdp_rel_vendas01
                     WHERE prevenda = :prevenda
                     GROUP BY dt_nota, cliente_pv, grupo, loja, cod_vendedor, nome_vendedor, total_custo_pv, total_venda_pv, rentabilidade_pv';

        /** @var Connection $conn */
        $conn = $this->getEntityManager()->getConnection();

        try {
            $r = $conn->fetchAssoc($sql, ['prevenda' => $pv]);
            $descontos = $r['subtotal'] - $r['total_venda_pv'];
            $r['descontos'] = $descontos;
            return $r;
        } catch (NonUniqueResultException $e) {
            throw new ViewException('Erro ao totalizar PV ' . $pv);
        }
    }


    /**
     * @return mixed[]
     */
    public function findLojas()
    {
        $sql = 'select distinct(json_data->>"$.loja") as loja from ven_venda';
        /** @var Connection $conn */
        $conn = $this->getEntityManager()->getConnection();
        return $conn->fetchAll($sql);
    }

    /**
     * @return mixed[]
     */
    public function findGrupos()
    {
        $sql = 'select distinct(json_data->>"$.grupo") as grupo from ven_venda';
        /** @var Connection $conn */
        $conn = $this->getEntityManager()->getConnection();
        return $conn->fetchAll($sql);
    }


}
