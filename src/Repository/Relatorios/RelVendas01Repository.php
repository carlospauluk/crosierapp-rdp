<?php

namespace App\Repository\Relatorios;

use App\Entity\Relatorios\RelVendas01;
use CrosierSource\CrosierLibBaseBundle\Repository\FilterRepository;
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
     * @param \DateTime|null $dtIni
     * @param \DateTime|null $dtFim
     * @return mixed
     */
    public function totalVendasPorFornecedor(\DateTime $dtIni = null, \DateTime $dtFim = null)
    {
        $dtIni = $dtIni ?? \DateTime::createFromFormat('d/m/Y', '01/01/0000');
        $dtIni->setTime(0, 0, 0, 0);
        $dtFim = $dtFim ?? \DateTime::createFromFormat('d/m/Y', '01/01/9999');
        $dtFim->setTime(23, 59, 59, 99999);

        $sql = 'SELECT nome_fornec, sum(total_preco_venda) as total_venda FROM rdp_rel_vendas01 WHERE dt_emissao BETWEEN :dtIni and :dtFim GROUP BY nome_fornec ORDER BY total_venda';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('nome_fornec', 'nome_fornec');
        $rsm->addScalarResult('total_venda', 'total_venda');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('dtIni', $dtIni);
        $query->setParameter('dtFim', $dtFim);

        return $query->getResult();
    }

    /**
     * @param \DateTime|null $dtIni
     * @param \DateTime|null $dtFim
     * @return mixed
     */
    public function totalVendasPorVendedor(\DateTime $dtIni = null, \DateTime $dtFim = null)
    {
        $dtIni = $dtIni ?? \DateTime::createFromFormat('d/m/Y', '01/01/0000');
        $dtIni->setTime(0, 0, 0, 0);
        $dtFim = $dtFim ?? \DateTime::createFromFormat('d/m/Y', '01/01/9999');
        $dtFim->setTime(23, 59, 59, 99999);
        $sql = 'SELECT CONCAT(cod_vendedor, \' - \', nome_vendedor) as nome_vendedor, sum(total_preco_venda) as total_venda FROM rdp_rel_vendas01 WHERE dt_emissao BETWEEN :dtIni and :dtFim GROUP BY cod_vendedor, nome_vendedor ORDER BY total_venda';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('nome_vendedor', 'nome_vendedor');
        $rsm->addScalarResult('total_venda', 'total_venda');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('dtIni', $dtIni);
        $query->setParameter('dtFim', $dtFim);

        return $query->getResult();
    }


    /**
     * @param \DateTime|null $dtIni
     * @param \DateTime|null $dtFim
     * @return mixed
     */
    public function itensVendidosPorFornecedor(\DateTime $dtIni, \DateTime $dtFim, string $nomeFornec, bool $totalGeral = false)
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

        if (!$totalGeral) {
            $sql .= 'GROUP BY cod_prod, desc_prod';
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

        return $query->getResult();

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

        $sql = 'SELECT nome_fornec, sum(total_preco_venda) as tpv
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
     * @param int $codVendedor
     * @return mixed
     */
    public function preVendasPorPeriodoEVendedor(\DateTime $dtIni, \DateTime $dtFim, int $codVendedor)
    {
        $dtIni = $dtIni ?? \DateTime::createFromFormat('d/m/Y', '01/01/0000');
        $dtIni->setTime(0, 0, 0, 0);
        $dtFim = $dtFim ?? \DateTime::createFromFormat('d/m/Y', '01/01/9999');
        $dtFim->setTime(23, 59, 59, 99999);

        $sql = 'SELECT prevenda, dt_emissao, cod_vendedor, nome_vendedor, sum(total_preco_venda) as total_pv
                    FROM rdp_rel_vendas01
                     WHERE cod_vendedor = :codVendedor AND dt_emissao BETWEEN :dtIni AND :dtFim GROUP BY prevenda, dt_emissao, cod_vendedor, nome_vendedor ORDER BY dt_emissao, total_pv';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('prevenda', 'prevenda');
        $rsm->addScalarResult('dt_emissao', 'dt_emissao');
        $rsm->addScalarResult('cod_vendedor', 'cod_vendedor');
        $rsm->addScalarResult('nome_vendedor', 'nome_vendedor');
        $rsm->addScalarResult('total_pv', 'total_pv');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('dtIni', $dtIni);
        $query->setParameter('dtFim', $dtFim);
        $query->setParameter('codVendedor', $codVendedor);

        return $query->getResult();
    }


    /**
     * @param \DateTime|null $dtIni
     * @param \DateTime|null $dtFim
     * @param string $codProduto
     * @return mixed
     */
    public function preVendasPorPeriodoEProduto(\DateTime $dtIni, \DateTime $dtFim, string $codProduto)
    {
        $dtIni = $dtIni ?? \DateTime::createFromFormat('d/m/Y', '01/01/0000');
        $dtIni->setTime(0, 0, 0, 0);
        $dtFim = $dtFim ?? \DateTime::createFromFormat('d/m/Y', '01/01/9999');
        $dtFim->setTime(23, 59, 59, 99999);

        $sql = 'SELECT prevenda, dt_emissao, cod_vendedor, nome_vendedor, sum(total_preco_venda) as total_pv
                    FROM rdp_rel_vendas01
                     WHERE cod_prod = :codProduto AND dt_emissao BETWEEN :dtIni AND :dtFim GROUP BY prevenda, dt_emissao, cod_vendedor, nome_vendedor ORDER BY dt_emissao, total_pv';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('prevenda', 'prevenda');
        $rsm->addScalarResult('dt_emissao', 'dt_emissao');
        $rsm->addScalarResult('cod_vendedor', 'cod_vendedor');
        $rsm->addScalarResult('nome_vendedor', 'nome_vendedor');
        $rsm->addScalarResult('total_pv', 'total_pv');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('dtIni', $dtIni);
        $query->setParameter('dtFim', $dtFim);
        $query->setParameter('codProduto', $codProduto);

        return $query->getResult();
    }


    /**
     * @param \DateTime|null $dtIni
     * @param \DateTime|null $dtFim
     * @param int $codVendedor
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


}

