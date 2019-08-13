<?php

namespace App\Entity\Relatorios;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Relatorios\RelEstoque01Repository")
 * @ORM\Table(name="rdp_rel_estoque01")
 *
 * @author Carlos Eduardo Pauluk
 */
class RelEstoque01 implements EntityId
{

    use EntityIdTrait;

    /**
     *
     * @ORM\Column(name="cod_prod", type="string", length=50, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
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
     * @ORM\Column(name="custo_medio", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    private $custoMedio;

    /**
     *
     * @var float|null
     */
    private $totalCustoMedio;

    /**
     *
     * @ORM\Column(name="preco_venda", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    private $precoVenda;


    /**
     *
     * @var float|null
     */
    private $totalPrecoVenda;

    /**
     *
     * @ORM\Column(name="desc_filial", type="string", length=200, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $descFilial;

    /**
     *
     * @ORM\Column(name="qtde_minima", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    private $qtdeMinima;

    /**
     *
     * @ORM\Column(name="qtde_maxima", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    private $qtdeMaxima;

    /**
     *
     * @ORM\Column(name="qtde_atual", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    private $qtdeAtual;

    /**
     *
     * @Groups("entity")
     * @ORM\Column(name="deficit", type="decimal", nullable=true)
     * @var float|null
     */
    private $deficit;

    /**
     *
     * @ORM\Column(name="dt_ult_saida", type="datetime", nullable=true)
     * @Groups("entity")
     *
     * @var \DateTime|null
     */
    private $dtUltSaida;


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
     * @Groups("entity")
     * @var bool
     */
    private $temCompras = false;

    /**
     * @return string|null
     */
    public function getCodProduto(): ?string
    {
        return $this->codProduto;
    }

    /**
     * @param string|null $codProduto
     * @return RelEstoque01
     */
    public function setCodProduto(?string $codProduto): RelEstoque01
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
     * @return RelEstoque01
     */
    public function setDescProduto(?string $descProduto): RelEstoque01
    {
        $this->descProduto = $descProduto;
        return $this;
    }

    /**
     * @Groups("entity")
     */
    public function getTotalCustoMedio()
    {
        if (is_numeric($this->getCustoMedio()) && is_numeric($this->getQtdeAtual())) {
            $this->totalCustoMedio = $this->getCustoMedio() * $this->getQtdeAtual();
        }
        return $this->totalCustoMedio;
    }

    /**
     * @return float|null
     */
    public function getCustoMedio(): ?float
    {
        return $this->custoMedio;
    }

    /**
     * @param float|null $custoMedio
     * @return RelEstoque01
     */
    public function setCustoMedio(?float $custoMedio): RelEstoque01
    {
        $this->custoMedio = $custoMedio;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getQtdeAtual(): ?float
    {
        return $this->qtdeAtual;
    }

    /**
     * @param float|null $qtdeAtual
     * @return RelEstoque01
     */
    public function setQtdeAtual(?float $qtdeAtual): RelEstoque01
    {
        $this->qtdeAtual = $qtdeAtual;
        return $this;
    }

    /**
     * @Groups("entity")
     */
    public function getDeficit()
    {
        return $this->deficit;
    }

    /**
     * @Groups("entity")
     */
    public function getTotalPrecoVenda()
    {
        if (is_numeric($this->getPrecoVenda()) && is_numeric($this->getQtdeAtual())) {
            $this->totalPrecoVenda = $this->getPrecoVenda() * $this->getQtdeAtual();
        }
        return $this->totalPrecoVenda;
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
     * @return RelEstoque01
     */
    public function setPrecoVenda(?float $precoVenda): RelEstoque01
    {
        $this->precoVenda = $precoVenda;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescFilial(): ?string
    {
        return $this->descFilial;
    }

    /**
     * @param string|null $descFilial
     * @return RelEstoque01
     */
    public function setDescFilial(?string $descFilial): RelEstoque01
    {
        $this->descFilial = $descFilial;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getQtdeMinima(): ?float
    {
        return $this->qtdeMinima;
    }

    /**
     * @param float|null $qtdeMinima
     * @return RelEstoque01
     */
    public function setQtdeMinima(?float $qtdeMinima): RelEstoque01
    {
        $this->qtdeMinima = $qtdeMinima;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getQtdeMaxima(): ?float
    {
        return $this->qtdeMaxima;
    }

    /**
     * @param float|null $qtdeMaxima
     * @return RelEstoque01
     */
    public function setQtdeMaxima(?float $qtdeMaxima): RelEstoque01
    {
        $this->qtdeMaxima = $qtdeMaxima;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDtUltSaida(): ?\DateTime
    {
        return $this->dtUltSaida;
    }

    /**
     * @param \DateTime|null $dtUltSaida
     * @return RelEstoque01
     */
    public function setDtUltSaida(?\DateTime $dtUltSaida): RelEstoque01
    {
        $this->dtUltSaida = $dtUltSaida;
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
     * @return RelEstoque01
     */
    public function setCodFornecedor(?string $codFornecedor): RelEstoque01
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
     * @return RelEstoque01
     */
    public function setNomeFornecedor(?string $nomeFornecedor): RelEstoque01
    {
        $this->nomeFornecedor = $nomeFornecedor;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTemCompras(): bool
    {
        return $this->temCompras;
    }

    /**
     * @param bool $temCompras
     * @return RelEstoque01
     */
    public function setTemCompras(bool $temCompras): RelEstoque01
    {
        $this->temCompras = $temCompras;
        return $this;
    }


}

