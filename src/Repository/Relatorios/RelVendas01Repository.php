<?php

namespace App\Repository\Relatorios;

use App\Entity\Relatorios\RelVendas01;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class RelVendas01Repository extends FilterRepository
{

    public function getEntityClass(): string
    {
        return RelVendas01::class;
    }


    /**
     * Utilizado no gráfico de Total de Vendas por Fornecedor.
     *
     * @param \DateTime|null $dtIni
     * @param \DateTime|null $dtFim
     * @param string|null $lojas
     * @param string|null $grupos
     * @return mixed
     */
    public function totalVendasPorFornecedor(\DateTime $dtIni = null, \DateTime $dtFim = null, ?string $lojas = null, ?string $grupos = null)
    {
        $dtIni = $dtIni ?? \DateTime::createFromFormat('d/m/Y', '01/01/0000');
        $dtIni->setTime(0, 0, 0, 0);
        $dtFim = $dtFim ?? \DateTime::createFromFormat('d/m/Y', '01/01/9999');
        $dtFim->setTime(23, 59, 59, 99999);

        $sql = 'SELECT nome_fornec, sum(total_preco_venda) as total_venda FROM rdp_rel_vendas01 WHERE dt_emissao BETWEEN :dtIni and :dtFim ';

        if ($grupos) {
            $sql .= 'AND grupo IN (:grupos) ';
        }
        if ($lojas) {
            $sql .= 'AND loja IN (:lojas) ';
        }

        $sql .= ' GROUP BY nome_fornec ORDER BY total_venda';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('nome_fornec', 'nome_fornec');
        $rsm->addScalarResult('total_venda', 'total_venda');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('dtIni', $dtIni);
        $query->setParameter('dtFim', $dtFim);
        if ($grupos) {
            $grupos = explode(',', $grupos);
            $query->setParameter('grupos', $grupos);
        }
        if ($lojas) {
            $lojas = explode(',', $lojas);
            $query->setParameter('lojas', $lojas);
        }

        return $query->getResult();
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

        $sql .= 'sum(qtde) as qtde_total, sum(total_preco_custo) as tpc, sum(total_preco_venda) as tpv, (((sum(total_preco_venda) / sum(total_preco_custo)) - 1) * 100.0) as rent 
                    FROM rdp_rel_vendas01
                     WHERE nome_fornec = :nomeFornec AND dt_emissao BETWEEN :dtIni AND :dtFim ';

        if ($grupos) {
            $sql .= ' AND grupo IN (:grupos)';
        }
        if ($lojas) {
            $sql .= ' AND loja IN (:lojas)';
        }

        if (!$totalGeral) {
            $sql .= ' GROUP BY cod_prod, desc_prod ';
        }

        $sql .= ' ORDER BY rent';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('cod_prod', 'cod_prod');
        $rsm->addScalarResult('desc_prod', 'desc_prod');
        $rsm->addScalarResult('qtde_total', 'qtde_total');
        $rsm->addScalarResult('tpc', 'tpc');
        $rsm->addScalarResult('tpv', 'tpv');
        $rsm->addScalarResult('rent', 'rent');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('dtIni', $dtIni);
        $query->setParameter('dtFim', $dtFim);
        $query->setParameter('nomeFornec', $nomeFornec);
        if ($grupos) {
            $query->setParameter('grupos', $grupos);
        }
        if ($lojas) {
            $query->setParameter('lojas', $lojas);
        }

        return $query->getResult();

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
            $sql_AND_grupo .= ' AND grupo = :grupos';
        }
        $sql_AND_loja = '';
        if ($lojas) {
            $sql_AND_loja .= ' AND loja = :lojas';
        }

        $sql = '
            SELECT 
                CONCAT(cod_vendedor, \' - \', nome_vendedor) as nome_vendedor, 
                SUM(total_venda_pv) as total_venda,
                SUM(total_custo_pv) as total_custo,
                (((SUM(total_venda_pv) / SUM(total_custo_pv)) - 1) * 100.0) as rent
            FROM 
                (
                    SELECT cod_vendedor, nome_vendedor, prevenda, total_venda_pv, total_custo_pv 
                    FROM rdp_rel_vendas01 
                    WHERE 
                        dt_emissao BETWEEN :dtIni AND :dtFim
                        ' . $sql_AND_grupo . $sql_AND_loja . '
                    GROUP BY cod_vendedor, nome_vendedor, prevenda, total_venda_pv, total_custo_pv) a 
            GROUP BY cod_vendedor, nome_vendedor ORDER BY total_venda';


        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('nome_vendedor', 'nome_vendedor');
        $rsm->addScalarResult('total_venda', 'total_venda');
        $rsm->addScalarResult('rent', 'rent');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('dtIni', $dtIni);
        $query->setParameter('dtFim', $dtFim);
        if ($grupos) {
            $query->setParameter('grupos', $grupos);
        }
        if ($lojas) {
            $query->setParameter('lojas', $lojas);
        }

        $dados = $query->getResult();

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
                (((SUM(total_venda_pv) / SUM(total_custo_pv)) - 1) * 100.0) as rent
            FROM 
                (
                    SELECT cod_vendedor, nome_vendedor, prevenda, total_venda_pv, total_custo_pv 
                    FROM rdp_rel_vendas01 
                    WHERE 
                        dt_emissao BETWEEN :dtIni AND :dtFim
                        ' . $sql_AND_grupo . $sql_AND_loja . '
                    GROUP BY cod_vendedor, nome_vendedor, prevenda, total_venda_pv, total_custo_pv) a 
            ';


        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('rent', 'rent');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('dtIni', $dtIni);
        $query->setParameter('dtFim', $dtFim);
        if ($grupos) {
            $query->setParameter('grupos', $grupos);
        }
        if ($lojas) {
            $query->setParameter('lojas', $lojas);
        }

        return $query->getOneOrNullResult();
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

        $sql = 'SELECT nome_fornec, sum(total_venda_pv) as tpv
                    FROM rdp_rel_vendas01
                     WHERE dt_emissao BETWEEN :dtIni AND :dtFim GROUP BY nome_fornec ORDER BY tpv LIMIT 1';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('nome_fornec', 'nome_fornec');
        $rsm->addScalarResult('tpv', 'tpv');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('dtIni', $dtIni);
        $query->setParameter('dtFim', $dtFim);

        $r = $query->getOneOrNullResult();

        return $r['nome_fornec'] ?? null;

    }


    /**
     * @return array
     */
    public function getFornecedores(): array
    {
        $sql = 'SELECT nome_fornec FROM rdp_rel_vendas01 GROUP BY cod_fornec, nome_fornec ORDER BY nome_fornec';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('nome_fornec', 'nome_fornec');
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $r = $query->getResult();
        $arr = [];
        foreach ($r as $item) {
            $e['id'] = $item['nome_fornec'];
            $e['text'] = $item['nome_fornec'];
            $arr[] = $e;
        }
        return $arr;
    }

    /**
     * @return array
     */
    public function getGrupos(): array
    {
        $sql = 'SELECT grupo FROM rdp_rel_vendas01 GROUP BY grupo ORDER BY grupo';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('grupo', 'grupo');
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $r = $query->getResult();
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
        $sql = 'SELECT loja FROM rdp_rel_vendas01 GROUP BY loja ORDER BY loja';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('loja', 'loja');
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $r = $query->getResult();
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
        $sql = 'SELECT CONCAT(cod_vendedor, \' - \', nome_vendedor) as vendedor FROM rdp_rel_vendas01 GROUP BY cod_vendedor, nome_vendedor ORDER BY nome_vendedor';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('vendedor', 'vendedor');
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $r = $query->getResult();
        $arr = [];
        foreach ($r as $item) {
            $e['id'] = $item['vendedor'];
            $e['text'] = $item['vendedor'];
            $arr[] = $e;
        }
        return $arr;
    }


    /**
     * @param string $codigo
     * @return null|string
     */
    public function getProdutoByCodigo(string $codigo): ?string
    {
        $sql = 'SELECT desc_prod FROM rdp_rel_vendas01 WHERE cod_prod = :codigo GROUP BY cod_prod, desc_prod';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('desc_prod', 'desc_prod');
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('codigo', $codigo);
        try {
            return $query->getOneOrNullResult()['desc_prod'] ?? null;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }


    /**
     * @return null|string
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCodigoFornecedorByNome(string $nomeFornecedor): ?string
    {
        $sql = 'SELECT cod_fornec FROM rdp_rel_vendas01 WHERE nome_fornec = :nomeFornec LIMIT 1';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('cod_fornec', 'cod_fornec');
        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('nomeFornec', $nomeFornecedor);
        $r = $query->getOneOrNullResult();

        return $r['cod_fornec'] ?? null;
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

        $sql = 'SELECT prevenda, dt_emissao, cod_vendedor, nome_vendedor, total_venda_pv, total_custo_pv, (((total_venda_pv / total_custo_pv) - 1) * 100.0) as rent, cliente_pv
                    FROM rdp_rel_vendas01
                     WHERE CONCAT(cod_prod, \' - \', desc_prod) = :produto AND dt_emissao BETWEEN :dtIni AND :dtFim ';

        if ($grupos) {
            $sql .= 'AND grupo IN (:grupos) ';
        }
        if ($lojas) {
            $sql .= 'AND loja IN (:lojas) ';
        }

        $sql .= 'GROUP BY prevenda, dt_emissao, cod_vendedor, nome_vendedor, total_venda_pv, total_custo_pv, cliente_pv ORDER BY dt_emissao, total_venda_pv';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('prevenda', 'prevenda');
        $rsm->addScalarResult('dt_emissao', 'dt_emissao');
        $rsm->addScalarResult('cod_vendedor', 'cod_vendedor');
        $rsm->addScalarResult('nome_vendedor', 'nome_vendedor');
        $rsm->addScalarResult('total_custo_pv', 'total_custo_pv');
        $rsm->addScalarResult('total_venda_pv', 'total_venda_pv');
        $rsm->addScalarResult('rent', 'rent');
        $rsm->addScalarResult('cliente_pv', 'cliente_pv');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('dtIni', $dtIni);
        $query->setParameter('dtFim', $dtFim);
        $query->setParameter('produto', $produto);
        if ($grupos) {
            $query->setParameter('grupos', $grupos);
        }
        if ($lojas) {
            $query->setParameter('lojas', $lojas);
        }

        return $query->getResult();
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
            $sql_AND_grupo .= ' AND grupo IN (:grupos)';
        }
        $sql_AND_loja = '';
        if ($lojas) {
            $sql_AND_loja .= ' AND loja IN (:lojas)';
        }

        $sql = 'SELECT prevenda, dt_emissao, cod_vendedor, nome_vendedor, total_venda_pv, total_custo_pv, (((total_venda_pv / total_custo_pv) - 1) * 100.0) as rent, cliente_pv
                    FROM rdp_rel_vendas01
                     WHERE cod_vendedor = :codVendedor AND dt_emissao BETWEEN :dtIni AND :dtFim 
                     ' . $sql_AND_grupo . $sql_AND_loja . '
                     GROUP BY prevenda, dt_emissao, cod_vendedor, nome_vendedor, total_venda_pv, total_custo_pv, cliente_pv ORDER BY dt_emissao, total_venda_pv';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('prevenda', 'prevenda');
        $rsm->addScalarResult('dt_emissao', 'dt_emissao');
        $rsm->addScalarResult('cod_vendedor', 'cod_vendedor');
        $rsm->addScalarResult('nome_vendedor', 'nome_vendedor');
        $rsm->addScalarResult('total_custo_pv', 'total_custo_pv');
        $rsm->addScalarResult('total_venda_pv', 'total_venda_pv');
        $rsm->addScalarResult('cliente_pv', 'cliente_pv');
        $rsm->addScalarResult('rent', 'rent');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('dtIni', $dtIni);
        $query->setParameter('dtFim', $dtFim);
        $query->setParameter('codVendedor', $codVendedor);
        if ($grupos) {
            $query->setParameter('grupos', $grupos);
        }
        if ($lojas) {
            $query->setParameter('lojas', $lojas);
        }

        return $query->getResult();
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

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('num_item', 'num_item');
        $rsm->addScalarResult('cod_prod', 'cod_prod');
        $rsm->addScalarResult('desc_prod', 'desc_prod');
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
        $sql = 'SELECT dt_emissao, cliente_pv, grupo, loja, cod_vendedor, nome_vendedor, total_custo_pv, total_venda_pv, rentabilidade_pv, sum(total_preco_venda) as subtotal
                    FROM rdp_rel_vendas01
                     WHERE prevenda = :prevenda
                     GROUP BY dt_emissao, cliente_pv, grupo, loja, cod_vendedor, nome_vendedor, total_custo_pv, total_venda_pv, rentabilidade_pv';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('dt_emissao', 'dt_emissao');
        $rsm->addScalarResult('cliente_pv', 'cliente_pv');
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

