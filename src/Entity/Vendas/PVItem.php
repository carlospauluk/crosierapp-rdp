<?php

namespace App\Entity\Vendas;

use App\Entity\Estoque\Produto;
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\Produto")
     * @ORM\JoinColumn(name="produto_id", nullable=false)
     * @Groups("entity")
     * @MaxDepth(1)
     *
     * @var Produto|null
     */
    private $produto;

    /**
     *
     * @ORM\Column(name="cod_fornec", type="bigint", length=20, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $codFornecedor;

    /**
     *
     * @ORM\Column(name="nome_fornec", type="string", length=200, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $nomeFornecedor;


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
     * @ORM\Column(name="desconto", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    private $desconto;

    /**
     *
     * @ORM\Column(name="total", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    private $total;

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
     * @return Produto|null
     */
    public function getProduto(): ?Produto
    {
        return $this->produto;
    }

    /**
     * @param Produto|null $produto
     * @return PVItem
     */
    public function setProduto(?Produto $produto): PVItem
    {
        $this->produto = $produto;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCodFornecedor(): ?string
    {
        return $this->codFornecedor;
    }

    /**
     * @param string|null $codFornecedor
     * @return PVItem
     */
    public function setCodFornecedor(?string $codFornecedor): PVItem
    {
        $this->codFornecedor = $codFornecedor;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNomeFornecedor(): ?string
    {
        return $this->nomeFornecedor;
    }

    /**
     * @param string|null $nomeFornecedor
     * @return PVItem
     */
    public function setNomeFornecedor(?string $nomeFornecedor): PVItem
    {
        $this->nomeFornecedor = $nomeFornecedor;
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
     * @return float|null
     */
    public function getDesconto(): ?float
    {
        return $this->desconto;
    }

    /**
     * @param float|null $desconto
     * @return PVItem
     */
    public function setDesconto(?float $desconto): PVItem
    {
        $this->desconto = $desconto;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotal(): ?float
    {
        return $this->total;
    }

    /**
     * @param float|null $total
     * @return PVItem
     */
    public function setTotal(?float $total): PVItem
    {
        $this->total = $total;
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


//
//    public function getProdutoMontado(): ?string {
//        return $this->produtoCod ?? '' . ' - ' . $this->produtoDesc;
//    }
//
//    public function getFornecedorMontado(): ?string {
//        return $this->produtoCod ?? '' . ' - ' . $this->produtoDesc;
//    }


}
