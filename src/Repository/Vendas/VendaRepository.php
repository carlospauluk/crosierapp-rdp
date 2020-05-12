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
     * @return array|null
     * @throws \Exception
     */
    public function findByDtVendaAndPV(\DateTime $dtVenda, $pv): ?array
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
     * @return array
     * @throws \Exception
     */
    public function findByPVAndMesAno($pv, $mesano): array
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
     * @return array
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


        $sql = 'SELECT i.fornecedor_nome, sum(i.preco_venda * i.qtde) as total_venda FROM ven_venda_item i, ven_venda v WHERE i.fornecedor_nome IS NOT NULL AND v.id = i.venda_id AND v.dt_nota BETWEEN :dtIni and :dtFim ';
        $sql .= $grupos ? 'AND v.json_data->>"$.grupo" IN (:grupos) ' : '';
        $sql .= $lojas ? 'AND v.json_data->>"$.loja" IN (:lojas) ' : '';
        $sql .= ' GROUP BY i.fornecedor_nome ORDER BY total_venda DESC';


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
            $sql .= 'i.json_data->>"$.erp_codigo" as cod_prod, i.json_data->>"$.produto_nome" as desc_prod, ';
        }

        $sql .= 'sum(qtde) as qtde_total, 
                    sum(i.json_data->>"$.total_preco_custo") as tpc, 
                    sum(qtde * preco_venda) as tpv, 
                    (((sum(qtde * preco_venda) / sum(i.json_data->>"$.total_preco_custo")) - 1) * 100.0) as rent 
                    FROM ven_venda_item i, ven_venda v
                     WHERE i.venda_id = v.id AND fornecedor_nome = :nomeFornec AND dt_nota BETWEEN :dtIni AND :dtFim ';

        if ($grupos) {
            $sql .= ' AND v.json_data->>"$.grupo" IN (:grupos)';
        }
        if ($lojas) {
            $sql .= ' AND v.json_data->>"$.loja" IN (:lojas)';
        }

        if (!$totalGeral) {
            $sql .= ' GROUP BY i.json_data->>"$.erp_codigo", i.json_data->>"$.produto_nome"';
        }

        // $sql .= ' ORDER BY i.json_data->>"$.rentabilidade_item"';

        $params = [
            'dtIni' => $dtIni->format('Y-m-d'),
            'dtFim' => $dtFim->format('Y-m-d'),
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
     * @throws DBALException
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
                CONCAT(vendedor_codigo, \' - \', vendedor_nome) as nome_vendedor, 
                SUM(valor_total) as total_venda,
                SUM(json_data->>"$.total_custo_pv") as total_custo,
                (((SUM(valor_total) / SUM(json_data->>"$.total_custo_pv")) - 1) * 100.0) as margem_bruta,
                   SUM(json_data->>"$.nova_comissao") as total_comissoes,
                (SUM(valor_total * json_data->>"$.margem_liquida" / 100.0) / SUM(valor_total) * 100.0) as margem_liquida
            FROM 
                 ven_venda 
                    WHERE 
                        dt_nota BETWEEN :dtIni AND :dtFim
                        ' . $sql_AND_grupo . $sql_AND_loja . '
            GROUP BY nome_vendedor ORDER BY total_venda';


        $params = [
            'dtIni' => $dtIni->format('Y-m-d'),
            'dtFim' => $dtFim->format('Y-m-d'),
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

        $margemLiquidaGeral = ($this->totalMargemLiquida($dtIni, $dtFim, $lojas, $grupos)['margem_liquida'] ?? 0.0) * 100.0;

        return ['dados' => $dados, 'margemLiquidaGeral' => $margemLiquidaGeral];
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
     * @throws DBALException
     */
    public function totalMargemLiquida(\DateTime $dtIni = null, \DateTime $dtFim = null, ?string $lojas = null, ?string $grupos = null)
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
                (SUM(valor_total * json_data->>"$.margem_liquida" / 100.0) / SUM(valor_total)) as margem_liquida
            FROM 
                 ven_venda
                    WHERE 
                        dt_nota BETWEEN :dtIni AND :dtFim
                        ' . $sql_AND_grupo . $sql_AND_loja;

        $params = [
            'dtIni' => $dtIni->format('Y-m-d'),
            'dtFim' => $dtFim->format('Y-m-d'),
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
     * @throws DBALException
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
            'dtIni' => $dtIni->format('Y-m-d'),
            'dtFim' => $dtFim->format('Y-m-d'),
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

        $sql = 'SELECT 
                    json_data->>"$.prevenda_ekt" as prevenda, 
                    dt_nota, 
                    json_data->>"$.vendedor_codigo" as vendedor_codigo, 
                    json_data->>"$.vendedor_nome" as vendedor_nome, 
                    valor_total, 
                    json_data->>"$.total_custo_pv" as total_custo_pv,
                    json_data->>"$.cliente_nome" as cliente_nome,
                    (((valor_total / json_data->>"$.total_custo_pv") - 1) * 100.0) as rent
                    FROM ven_venda
                     WHERE id IN 
                           (SELECT venda_id FROM ven_venda_item WHERE CONCAT(json_data->>"$.produto_erp_codigo", \' - \', json_data->>"$.produto_nome") = :produto) 
                            AND dt_nota BETWEEN :dtIni AND :dtFim ';

        if ($grupos) {
            $sql .= 'AND json_data->>"$.grupo" IN (:grupos) ';
        }
        if ($lojas) {
            $sql .= 'AND json_data->>"$.loja" IN (:lojas) ';
        }

        $sql .= 'GROUP BY json_data->>"$.prevenda_ekt", dt_nota, json_data->>"$.vendedor_codigo", json_data->>"$.vendedor_nome", valor_total, json_data->>"$.total_custo_pv", cliente_nome, rent 
         ORDER BY dt_nota,  valor_total';

        $params = [
            'dtIni' => $dtIni->format('Y-m-d'),
            'dtFim' => $dtFim->format('Y-m-d'),
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

        $sql = 'SELECT 
                    json_data->>"$.prevenda_ekt" as prevenda, 
                    dt_nota, 
                    vendedor_codigo, 
                    vendedor_nome, 
                    valor_total, 
                    json_data->>"$.total_custo_pv" as total_custo_pv,
                    (((valor_total / json_data->>"$.total_custo_pv") - 1) * 100.0) as rent, 
                    json_data->>"$.cliente_nome" as cliente_nome
                    FROM ven_venda
                     WHERE vendedor_codigo = :codVendedor AND dt_nota BETWEEN :dtIni AND :dtFim 
                     ' . $sql_AND_grupo . $sql_AND_loja . '
                    ORDER BY dt_nota, valor_total';


        $params = [
            'dtIni' => $dtIni->format('Y-m-d'),
            'dtFim' => $dtFim->format('Y-m-d'),
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
