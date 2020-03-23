<?php

namespace App\Entity\Relatorios;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Relatorios\RelVendas01Repository")
 * @ORM\Table(name="rdp_rel_vendas01")
 *
 * @author Carlos Eduardo Pauluk
 */
class RelVendas01 implements EntityId
{

    use EntityIdTrait;

    /**
     *
     * @ORM\Column(name="prevenda", type="bigint", nullable=false)
     * @Groups("entity")
     *
     * @var int|null
     */
    public ?int $preVenda = null;

    /**
     *
     * @ORM\Column(name="num_item", type="integer", nullable=false)
     * @Groups("entity")
     *
     * @var int|null
     */
    public ?int $numItem = null;

    /**
     *
     * @ORM\Column(name="qtde", type="integer", nullable=false)
     * @Groups("entity")
     *
     * @var int|null
     */
    public ?int $qtde = null;

    /**
     *
     * @ORM\Column(name="dt_emissao", type="date", nullable=false)
     * @Groups("entity")
     *
     * @var \DateTime|null
     */
    public ?\DateTime $dtEmissao = null;


    /**
     *
     * @ORM\Column(name="ano", type="string", length=4, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    public ?string $ano = null;

    /**
     *
     * @ORM\Column(name="mes", type="string", length=2, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    public ?string $mes = null;

    /**
     *
     * @ORM\Column(name="cod_fornec", type="integer", nullable=false)
     * @Groups("entity")
     *
     * @var int|null
     */
    public ?int $codFornecedor = null;

    /**
     *
     * @ORM\Column(name="nome_fornec", type="string", length=200, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    public ?string $nomeFornecedor = null;

    /**
     *
     * @ORM\Column(name="cod_prod", type="string", length=50, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    public ?string $codProduto = null;

    /**
     *
     * @ORM\Column(name="desc_prod", type="string", length=200, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    public ?string $descProduto = null;


    /**
     *
     * @ORM\Column(name="total_preco_venda", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    public ?float $totalPrecoVenda = null;

    /**
     *
     * @ORM\Column(name="total_preco_custo", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    public ?float $totalPrecoCusto = null;

    /**
     *
     * @ORM\Column(name="rentabilidade", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    public ?float $rentabilidade = null;

    /**
     *
     * @ORM\Column(name="cod_vendedor", type="integer", nullable=false)
     * @Groups("entity")
     *
     * @var int|null
     */
    public ?int $codVendedor = null;

    /**
     *
     * @ORM\Column(name="nome_vendedor", type="string", length=200, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    public ?string $nomeVendedor = null;

    /**
     *
     * @ORM\Column(name="loja", type="string", length=200, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    public ?string $loja = null;

    /**
     *
     * @ORM\Column(name="total_custo_pv", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    public ?float $totalCustoPV = null;

    /**
     *
     * @ORM\Column(name="total_venda_pv", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    public ?float $totalVendaPV = null;

    /**
     *
     * @ORM\Column(name="rentabilidade_pv", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    public ?float $rentabilidadePV = null;

    /**
     *
     * @ORM\Column(name="cliente_pv", type="string", length=200, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    public ?string $clientePV = null;

    /**
     *
     * @ORM\Column(name="grupo", type="string", length=200, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    public ?string $grupo = null;

    /**
     *
     * @ORM\Column(name="numero_nf", type="string")
     * @Groups("entity")
     *
     * @var string|null
     */
    public ?string $numeroNF = null;

    /**
     *
     * @ORM\Column(name="dt_nf", type="date")
     * @Groups("entity")
     *
     * @var \DateTime|null
     */
    public ?\DateTime $dtNF;


}

