<?php

namespace App\Entity\Relatorios;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Relatorios\RelCompras01Repository")
 * @ORM\Table(name="rdp_rel_vendas01")
 *
 * @author Carlos Eduardo Pauluk
 */
class RelCompras01 implements EntityId
{

    use EntityIdTrait;

    /**
     *
     * @ORM\Column(name="pv_compra", type="bigint", nullable=false)
     * @Groups("entity")
     *
     * @var int|null
     */
    private $pvCompra;

    /**
     *
     * @ORM\Column(name="num_item", type="integer", nullable=false)
     * @Groups("entity")
     *
     * @var int|null
     */
    private $numItem;

    /**
     *
     * @ORM\Column(name="qtde", type="integer", nullable=false)
     * @Groups("entity")
     *
     * @var int|null
     */
    private $qtde;

    /**
     *
     * @ORM\Column(name="dt_emissao", type="date", nullable=false)
     * @Groups("entity")
     *
     * @var \DateTime|null
     */
    private $dtEmissao;

    /**
     *
     * @ORM\Column(name="ano", type="string", length=4, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $ano;

    /**
     *
     * @ORM\Column(name="mes", type="string", length=2, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $mes;

    /**
     *
     * @ORM\Column(name="cod_fornec", type="integer", nullable=false)
     * @Groups("entity")
     *
     * @var int|null
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
     * @ORM\Column(name="cod_prod", type="varchar", length=50, nullable=false)
     * @Groups("entity")
     *
     * @var int|null
     */
    private $codProduto;

    /**
     *
     * @ORM\Column(name="desc_prod", type="string", length=200, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $descProduto;


    /**
     *
     * @ORM\Column(name="total_preco_venda", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    private $totalPrecoVenda;

    /**
     *
     * @ORM\Column(name="total_preco_custo", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    private $totalPrecoCusto;

    /**
     *
     * @ORM\Column(name="rentabilidade", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    private $rentabilidade;

    /**
     *
     * @ORM\Column(name="cod_vendedor", type="integer", nullable=false)
     * @Groups("entity")
     *
     * @var int|null
     */
    private $codVendedor;

    /**
     *
     * @ORM\Column(name="nome_vendedor", type="string", length=200, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $nomeVendedor;

    /**
     *
     * @ORM\Column(name="dt_prev_entrega", type="date", nullable=false)
     * @Groups("entity")
     *
     * @var \DateTime|null
     */
    private $dtPrevEntrega;

    /**
     * @return int|null
     */
    public function getPvCompra(): ?int
    {
        return $this->pvCompra;
    }

    /**
     * @param int|null $pvCompra
     * @return RelCompras01
     */
    public function setPvCompra(?int $pvCompra): RelCompras01
    {
        $this->pvCompra = $pvCompra;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getNumItem(): ?int
    {
        return $this->numItem;
    }

    /**
     * @param int|null $numItem
     * @return RelCompras01
     */
    public function setNumItem(?int $numItem): RelCompras01
    {
        $this->numItem = $numItem;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getQtde(): ?int
    {
        return $this->qtde;
    }

    /**
     * @param int|null $qtde
     * @return RelCompras01
     */
    public function setQtde(?int $qtde): RelCompras01
    {
        $this->qtde = $qtde;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDtEmissao(): ?\DateTime
    {
        return $this->dtEmissao;
    }

    /**
     * @param \DateTime|null $dtEmissao
     * @return RelCompras01
     */
    public function setDtEmissao(?\DateTime $dtEmissao): RelCompras01
    {
        $this->dtEmissao = $dtEmissao;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAno(): ?string
    {
        return $this->ano;
    }

    /**
     * @param string|null $ano
     * @return RelCompras01
     */
    public function setAno(?string $ano): RelCompras01
    {
        $this->ano = $ano;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMes(): ?string
    {
        return $this->mes;
    }

    /**
     * @param string|null $mes
     * @return RelCompras01
     */
    public function setMes(?string $mes): RelCompras01
    {
        $this->mes = $mes;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCodFornecedor(): ?int
    {
        return $this->codFornecedor;
    }

    /**
     * @param int|null $codFornecedor
     * @return RelCompras01
     */
    public function setCodFornecedor(?int $codFornecedor): RelCompras01
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
     * @return RelCompras01
     */
    public function setNomeFornecedor(?string $nomeFornecedor): RelCompras01
    {
        $this->nomeFornecedor = $nomeFornecedor;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCodProduto(): ?int
    {
        return $this->codProduto;
    }

    /**
     * @param int|null $codProduto
     * @return RelCompras01
     */
    public function setCodProduto(?int $codProduto): RelCompras01
    {
        $this->codProduto = $codProduto;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescProduto(): ?string
    {
        return $this->descProduto;
    }

    /**
     * @param string|null $descProduto
     * @return RelCompras01
     */
    public function setDescProduto(?string $descProduto): RelCompras01
    {
        $this->descProduto = $descProduto;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotalPrecoVenda(): ?float
    {
        return $this->totalPrecoVenda;
    }

    /**
     * @param float|null $totalPrecoVenda
     * @return RelCompras01
     */
    public function setTotalPrecoVenda(?float $totalPrecoVenda): RelCompras01
    {
        $this->totalPrecoVenda = $totalPrecoVenda;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotalPrecoCusto(): ?float
    {
        return $this->totalPrecoCusto;
    }

    /**
     * @param float|null $totalPrecoCusto
     * @return RelCompras01
     */
    public function setTotalPrecoCusto(?float $totalPrecoCusto): RelCompras01
    {
        $this->totalPrecoCusto = $totalPrecoCusto;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getRentabilidade(): ?float
    {
        return $this->rentabilidade;
    }

    /**
     * @param float|null $rentabilidade
     * @return RelCompras01
     */
    public function setRentabilidade(?float $rentabilidade): RelCompras01
    {
        $this->rentabilidade = $rentabilidade;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCodVendedor(): ?int
    {
        return $this->codVendedor;
    }

    /**
     * @param int|null $codVendedor
     * @return RelCompras01
     */
    public function setCodVendedor(?int $codVendedor): RelCompras01
    {
        $this->codVendedor = $codVendedor;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNomeVendedor(): ?string
    {
        return $this->nomeVendedor;
    }

    /**
     * @param string|null $nomeVendedor
     * @return RelCompras01
     */
    public function setNomeVendedor(?string $nomeVendedor): RelCompras01
    {
        $this->nomeVendedor = $nomeVendedor;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDtPrevEntrega(): ?\DateTime
    {
        return $this->dtPrevEntrega;
    }

    /**
     * @param \DateTime|null $dtPrevEntrega
     * @return RelCompras01
     */
    public function setDtPrevEntrega(?\DateTime $dtPrevEntrega): RelCompras01
    {
        $this->dtPrevEntrega = $dtPrevEntrega;
        return $this;
    }


}

