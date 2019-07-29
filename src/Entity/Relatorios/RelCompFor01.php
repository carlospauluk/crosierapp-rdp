<?php

namespace App\Entity\Relatorios;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Relatorios\RelCompFor01Repository")
 * @ORM\Table(name="rdp_rel_vendas01")
 *
 * @author Carlos Eduardo Pauluk
 */
class RelCompFor01 implements EntityId
{

    use EntityIdTrait;


    /**
     *
     * @ORM\Column(name="lancto", type="integer", nullable=false)
     * @Groups("entity")
     *
     * @var int|null
     */
    private $lancto;

    /**
     *
     * @ORM\Column(name="docto", type="string", length=50, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $docto;

    /**
     *
     * @ORM\Column(name="dt_movto", type="date", nullable=false)
     * @Groups("entity")
     *
     * @var \DateTime|null
     */
    private $dtMovto;


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
     * @ORM\Column(name="desc_prod", type="string", length=200, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $descProduto;


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
     * @ORM\Column(name="preco_custo", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    private $precoCusto;

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
     * @ORM\Column(name="obs", type="string", length=2000, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $obs;

    /**
     * @return int|null
     */
    public function getLancto(): ?int
    {
        return $this->lancto;
    }

    /**
     * @param int|null $lancto
     * @return RelCompFor01
     */
    public function setLancto(?int $lancto): RelCompFor01
    {
        $this->lancto = $lancto;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDocto(): ?string
    {
        return $this->docto;
    }

    /**
     * @param string|null $docto
     * @return RelCompFor01
     */
    public function setDocto(?string $docto): RelCompFor01
    {
        $this->docto = $docto;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDtMovto(): ?\DateTime
    {
        return $this->dtMovto;
    }

    /**
     * @param \DateTime|null $dtMovto
     * @return RelCompFor01
     */
    public function setDtMovto(?\DateTime $dtMovto): RelCompFor01
    {
        $this->dtMovto = $dtMovto;
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
     * @return RelCompFor01
     */
    public function setCodProduto(?int $codProduto): RelCompFor01
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
     * @return RelCompFor01
     */
    public function setDescProduto(?string $descProduto): RelCompFor01
    {
        $this->descProduto = $descProduto;
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
     * @return RelCompFor01
     */
    public function setQtde(?float $qtde): RelCompFor01
    {
        $this->qtde = $qtde;
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
     * @return RelCompFor01
     */
    public function setPrecoCusto(?float $precoCusto): RelCompFor01
    {
        $this->precoCusto = $precoCusto;
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
     * @return RelCompFor01
     */
    public function setTotal(?float $total): RelCompFor01
    {
        $this->total = $total;
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
     * @return RelCompFor01
     */
    public function setCodFornecedor(?int $codFornecedor): RelCompFor01
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
     * @return RelCompFor01
     */
    public function setNomeFornecedor(?string $nomeFornecedor): RelCompFor01
    {
        $this->nomeFornecedor = $nomeFornecedor;
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
     * @return RelCompFor01
     */
    public function setObs(?string $obs): RelCompFor01
    {
        $this->obs = $obs;
        return $this;
    }


}

