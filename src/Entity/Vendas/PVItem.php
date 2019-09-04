<?php

namespace App\Entity\Vendas;

use App\Entity\Vendas\PV;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Vendas\PVItemRepository")
 * @ORM\Table(name="rdp_ven_pv_item")
 *
 * @author Carlos Eduardo Pauluk
 */
class PVItem implements EntityId
{

    use EntityIdTrait;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Vendas\PV", inversedBy="itens")
     * @ORM\JoinColumn(name="ven_pv_id", nullable=false)
     * @Groups("entity")
     * @MaxDepth(1)
     *
     * @var PV|null
     */
    private $pv;


    /**
     *
     * @ORM\Column(name="produto_cod", type="string", nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $produtoCod;

    /**
     *
     * @ORM\Column(name="produto_desc", type="string", nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $produtoDesc;


    /**
     *
     * @ORM\Column(name="preco_custo", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    private $precoCusto;

    /**
     *
     * @ORM\Column(name="preco_venda", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    private $precoVenda;

    /**
     * O preço negociado no PV.
     *
     * @ORM\Column(name="preco_orc", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    private $precoOrc;

    /**
     *
     * @ORM\Column(name="qtde", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    private $qtde;

    /**
     *
     * @ORM\Column(name="obs", type="string", nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $obs;

    /**
     * @return PV|null
     */
    public function getPv(): ?PV
    {
        return $this->pv;
    }

    /**
     * @param PV|null $pv
     * @return PVItem
     */
    public function setPv(?PV $pv): PVItem
    {
        $this->pv = $pv;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getProdutoCod(): ?string
    {
        return $this->produtoCod;
    }

    /**
     * @param string|null $produtoCod
     * @return PVItem
     */
    public function setProdutoCod(?string $produtoCod): PVItem
    {
        $this->produtoCod = $produtoCod;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getProdutoDesc(): ?string
    {
        return $this->produtoDesc;
    }

    /**
     * @param string|null $produtoDesc
     * @return PVItem
     */
    public function setProdutoDesc(?string $produtoDesc): PVItem
    {
        $this->produtoDesc = $produtoDesc;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrecoCusto(): ?float
    {
        return $this->precoCusto;
    }

    /**
     * @param float|null $precoCusto
     * @return PVItem
     */
    public function setPrecoCusto(?float $precoCusto): PVItem
    {
        $this->precoCusto = $precoCusto;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrecoVenda(): ?float
    {
        return $this->precoVenda;
    }

    /**
     * @param float|null $precoVenda
     * @return PVItem
     */
    public function setPrecoVenda(?float $precoVenda): PVItem
    {
        $this->precoVenda = $precoVenda;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrecoOrc(): ?float
    {
        return $this->precoOrc;
    }

    /**
     * @param float|null $precoOrc
     * @return PVItem
     */
    public function setPrecoOrc(?float $precoOrc): PVItem
    {
        $this->precoOrc = $precoOrc;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getQtde(): ?float
    {
        return $this->qtde;
    }

    /**
     * @param float|null $qtde
     * @return PVItem
     */
    public function setQtde(?float $qtde): PVItem
    {
        $this->qtde = $qtde;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getObs(): ?string
    {
        return $this->obs;
    }

    /**
     * @param string|null $obs
     * @return PVItem
     */
    public function setObs(?string $obs): PVItem
    {
        $this->obs = $obs;
        return $this;
    }


}
