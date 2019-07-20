<?php

namespace App\Entity;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\RelVendas01Repository")
 * @ORM\Table(name="rdp_rel_ctspagrec01")
 *
 * @author Carlos Eduardo Pauluk
 */
class RelCtsPagRec01 implements EntityId
{

    use EntityIdTrait;


    /**
     *
     * @ORM\Column(name="lancto", type="int", nullable=false)
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
     * @ORM\Column(name="dt_vencto", type="date", nullable=false)
     * @Groups("entity")
     *
     * @var \DateTime|null
     */
    private $dtVencto;

    /**
     *
     * @ORM\Column(name="dt_pagto", type="date", nullable=false)
     * @Groups("entity")
     *
     * @var \DateTime|null
     */
    private $dtPagto;

    /**
     *
     * @ORM\Column(name="cod_cliente", type="integer", nullable=false)
     * @Groups("entity")
     *
     * @var int|null
     */
    private $codCliente;

    /**
     *
     * @ORM\Column(name="nome_cli_for", type="string", length=200, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $nomeCliFor;

    /**
     *
     * @ORM\Column(name="localizador", type="integer", nullable=false)
     * @Groups("entity")
     *
     * @var int|null
     */
    private $localizador;

    /**
     *
     * @ORM\Column(name="filial", type="integer", nullable=false)
     * @Groups("entity")
     *
     * @var int|null
     */
    private $filial;


    /**
     *
     * @ORM\Column(name="valor_titulo", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    private $valorTitulo;

    /**
     *
     * @ORM\Column(name="valor_baixa", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    private $valorBaixa;

    /**
     *
     * @ORM\Column(name="situacao", type="string", length=1, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $situacao;

    /**
     *
     * @ORM\Column(name="tipo_pag_rec", type="string", length=1, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $tipoPagRec;

    /**
     *
     * @ORM\Column(name="numero_nf", type="int", nullable=false)
     * @Groups("entity")
     *
     * @var int|null
     */
    private $numeroNF;

    /**
     *
     * @ORM\Column(name="dt_emissao_nf", type="date", nullable=false)
     * @Groups("entity")
     *
     * @var \DateTime|null
     */
    private $dtEmissaoNF;

    /**
     * @return int|null
     */
    public function getLancto(): ?int
    {
        return $this->lancto;
    }

    /**
     * @param int|null $lancto
     * @return RelVendas01
     */
    public function setLancto(?int $lancto): RelVendas01
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
     * @return RelVendas01
     */
    public function setDocto(?string $docto): RelVendas01
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
     * @return RelVendas01
     */
    public function setDtMovto(?\DateTime $dtMovto): RelVendas01
    {
        $this->dtMovto = $dtMovto;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDtVencto(): ?\DateTime
    {
        return $this->dtVencto;
    }

    /**
     * @param \DateTime|null $dtVencto
     * @return RelVendas01
     */
    public function setDtVencto(?\DateTime $dtVencto): RelVendas01
    {
        $this->dtVencto = $dtVencto;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDtPagto(): ?\DateTime
    {
        return $this->dtPagto;
    }

    /**
     * @param \DateTime|null $dtPagto
     * @return RelVendas01
     */
    public function setDtPagto(?\DateTime $dtPagto): RelVendas01
    {
        $this->dtPagto = $dtPagto;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCodCliente(): ?int
    {
        return $this->codCliente;
    }

    /**
     * @param int|null $codCliente
     * @return RelVendas01
     */
    public function setCodCliente(?int $codCliente): RelVendas01
    {
        $this->codCliente = $codCliente;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNomeCliFor(): ?string
    {
        return $this->nomeCliFor;
    }

    /**
     * @param string|null $nomeCliFor
     * @return RelVendas01
     */
    public function setNomeCliFor(?string $nomeCliFor): RelVendas01
    {
        $this->nomeCliFor = $nomeCliFor;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getLocalizador(): ?int
    {
        return $this->localizador;
    }

    /**
     * @param int|null $localizador
     * @return RelVendas01
     */
    public function setLocalizador(?int $localizador): RelVendas01
    {
        $this->localizador = $localizador;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getFilial(): ?int
    {
        return $this->filial;
    }

    /**
     * @param int|null $filial
     * @return RelVendas01
     */
    public function setFilial(?int $filial): RelVendas01
    {
        $this->filial = $filial;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getValorTitulo(): ?float
    {
        return $this->valorTitulo;
    }

    /**
     * @param float|null $valorTitulo
     * @return RelVendas01
     */
    public function setValorTitulo(?float $valorTitulo): RelVendas01
    {
        $this->valorTitulo = $valorTitulo;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getValorBaixa(): ?float
    {
        return $this->valorBaixa;
    }

    /**
     * @param float|null $valorBaixa
     * @return RelVendas01
     */
    public function setValorBaixa(?float $valorBaixa): RelVendas01
    {
        $this->valorBaixa = $valorBaixa;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSituacao(): ?string
    {
        return $this->situacao;
    }

    /**
     * @param string|null $situacao
     * @return RelVendas01
     */
    public function setSituacao(?string $situacao): RelVendas01
    {
        $this->situacao = $situacao;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTipoPagRec(): ?string
    {
        return $this->tipoPagRec;
    }

    /**
     * @param string|null $tipoPagRec
     * @return RelVendas01
     */
    public function setTipoPagRec(?string $tipoPagRec): RelVendas01
    {
        $this->tipoPagRec = $tipoPagRec;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getNumeroNF(): ?int
    {
        return $this->numeroNF;
    }

    /**
     * @param int|null $numeroNF
     * @return RelVendas01
     */
    public function setNumeroNF(?int $numeroNF): RelVendas01
    {
        $this->numeroNF = $numeroNF;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDtEmissaoNF(): ?\DateTime
    {
        return $this->dtEmissaoNF;
    }

    /**
     * @param \DateTime|null $dtEmissaoNF
     * @return RelVendas01
     */
    public function setDtEmissaoNF(?\DateTime $dtEmissaoNF): RelVendas01
    {
        $this->dtEmissaoNF = $dtEmissaoNF;
        return $this;
    }


}

