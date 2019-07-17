<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\RelVendas01Repository")
 * @ORM\Table(name="rdp_rel_vendas01")
 *
 * @author Carlos Eduardo Pauluk
 */
class RelVendas01 implements EntityId
{

    use EntityIdTrait;


    /**
     *
     * @ORM\Column(name="ano", type="char", length=4, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $ano;

    /**
     *
     * @ORM\Column(name="ano", type="char", length=4, nullable=false)
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
     * @ORM\Column(name="nome_fornec", type="varchar", length=200, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $nomeFornecedor;

    /**
     *
     * @ORM\Column(name="cod_prod", type="integer", nullable=false)
     * @Groups("entity")
     *
     * @var int|null
     */
    private $codProduto;

    /**
     *
     * @ORM\Column(name="desc_produto", type="varchar", length=200, nullable=false)
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
     * @ORM\Column(name="nome_vendedor", type="varchar", length=200, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $nomeVendedor;

    /**
     * @return string|null
     */
    public function getAno(): ?string
    {
        return $this->ano;
    }

    /**
     * @param string|null $ano
     * @return RelVendas01
     */
    public function setAno(?string $ano): RelVendas01
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
     * @return RelVendas01
     */
    public function setMes(?string $mes): RelVendas01
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
     * @return RelVendas01
     */
    public function setCodFornecedor(?int $codFornecedor): RelVendas01
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
     * @return RelVendas01
     */
    public function setNomeFornecedor(?string $nomeFornecedor): RelVendas01
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
     * @return RelVendas01
     */
    public function setCodProduto(?int $codProduto): RelVendas01
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
     * @return RelVendas01
     */
    public function setDescProduto(?string $descProduto): RelVendas01
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
     * @return RelVendas01
     */
    public function setTotalPrecoVenda(?float $totalPrecoVenda): RelVendas01
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
     * @return RelVendas01
     */
    public function setTotalPrecoCusto(?float $totalPrecoCusto): RelVendas01
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
     * @return RelVendas01
     */
    public function setRentabilidade(?float $rentabilidade): RelVendas01
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
     * @return RelVendas01
     */
    public function setCodVendedor(?int $codVendedor): RelVendas01
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
     * @return RelVendas01
     */
    public function setNomeVendedor(?string $nomeVendedor): RelVendas01
    {
        $this->nomeVendedor = $nomeVendedor;
        return $this;
    }


}

