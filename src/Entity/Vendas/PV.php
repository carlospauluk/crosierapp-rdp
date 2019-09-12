<?php

namespace App\Entity\Vendas;

use App\Entity\Relatorios\RelCliente01;
use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\NotUppercase;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Vendas\PVRepository")
 * @ORM\Table(name="rdp_ven_pv")
 *
 * @author Carlos Eduardo Pauluk
 */
class PV implements EntityId
{

    use EntityIdTrait;

    /**
     *
     * @ORM\Column(name="uuid", type="string", length=36, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $uuid;

    /**
     *
     * @ORM\Column(name="pv_ekt", type="integer", nullable=true)
     * @Groups("entity")
     *
     * @var int|null
     */
    private $pvEkt;

    /**
     *
     * @ORM\Column(name="dt_emissao", type="datetime", nullable=false)
     * @Groups("entity")
     *
     * @var \DateTime|null
     */
    private $dtEmissao;

    /**
     * ABERTO: Ainda não enviado (em preenchimento).
     * CANCELADO: .
     * ENVIADO: enviado ao EKT, aguardando;
     * CONCLUÍDO: gerado no EKT.
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $status;

    /**
     *
     * @ORM\Column(name="filial", type="string", nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $filial;

    /**
     *
     * @ORM\Column(name="vendedor", type="string", nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $vendedor;


    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Relatorios\RelCliente01")
     * @ORM\JoinColumn(name="cliente_id", nullable=true)
     * @Groups("entity")
     * @MaxDepth(1)
     *
     * @var RelCliente01|null
     */
    private $cliente;

    /**
     *
     * @ORM\Column(name="cliente_cod", type="integer", nullable=false)
     * @Groups("entity")
     *
     * @var int|null
     */
    private $clienteCod;

    /**
     *
     * @ORM\Column(name="cliente_nome", type="string", nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $clienteNome;

    /**
     *
     * @ORM\Column(name="cliente_documento", type="string", nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $clienteDocumento;

    /**
     *
     * @ORM\Column(name="deposito", type="string", nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $deposito;

    /**
     *
     * @ORM\Column(name="localizador", type="string", nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $localizador;

    /**
     *
     * @ORM\Column(name="cond_pagto", type="string", nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $condPagto;

    /**
     * JSON.
     *
     * @ORM\Column(name="venctos", type="string", nullable=false)
     * @Groups("entity")
     * @NotUppercase()
     *
     * @var string|null
     */
    private $venctos;

    /**
     *
     * @ORM\Column(name="obs", type="string", nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $obs;


    /**
     *
     * @ORM\OneToMany(targetEntity="PVItem", mappedBy="pv")
     *
     * @var PVItem[]|ArrayCollection|null
     */
    private $itens;

    /**
     *
     * @ORM\Column(name="subtotal", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    private $subtotal;

    /**
     *
     * @ORM\Column(name="descontos", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    private $descontos;

    /**
     *
     * @ORM\Column(name="total", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    private $total;


    /**
     * PV constructor.
     */
    public function __construct()
    {
        $this->itens = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @param string|null $uuid
     * @return PV
     */
    public function setUuid(?string $uuid): PV
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPvEkt(): ?int
    {
        return $this->pvEkt;
    }

    /**
     * @param int|null $pvEkt
     * @return PV
     */
    public function setPvEkt(?int $pvEkt): PV
    {
        $this->pvEkt = $pvEkt;
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
     * @return PV
     */
    public function setDtEmissao(?\DateTime $dtEmissao): PV
    {
        $this->dtEmissao = $dtEmissao;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     * @return PV
     */
    public function setStatus(?string $status): PV
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFilial(): ?string
    {
        return $this->filial;
    }

    /**
     * @param string|null $filial
     * @return PV
     */
    public function setFilial(?string $filial): PV
    {
        $this->filial = $filial;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getVendedor(): ?string
    {
        return $this->vendedor;
    }

    /**
     * @param string|null $vendedor
     * @return PV
     */
    public function setVendedor(?string $vendedor): PV
    {
        $this->vendedor = $vendedor;
        return $this;
    }

    /**
     * @return RelCliente01|null
     */
    public function getCliente(): ?RelCliente01
    {
        return $this->cliente;
    }

    /**
     * @param RelCliente01|null $cliente
     * @return PV
     */
    public function setCliente(?RelCliente01 $cliente): PV
    {
        $this->cliente = $cliente;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getClienteCod(): ?int
    {
        return $this->clienteCod;
    }

    /**
     * @param int|null $clienteCod
     * @return PV
     */
    public function setClienteCod(?int $clienteCod): PV
    {
        $this->clienteCod = $clienteCod;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getClienteNome(): ?string
    {
        return $this->clienteNome;
    }

    /**
     * @param string|null $clienteNome
     * @return PV
     */
    public function setClienteNome(?string $clienteNome): PV
    {
        $this->clienteNome = $clienteNome;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getClienteDocumento(): ?string
    {
        return $this->clienteDocumento;
    }

    /**
     * @param string|null $clienteDocumento
     * @return PV
     */
    public function setClienteDocumento(?string $clienteDocumento): PV
    {
        $this->clienteDocumento = $clienteDocumento;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDeposito(): ?string
    {
        return $this->deposito;
    }

    /**
     * @param string|null $deposito
     * @return PV
     */
    public function setDeposito(?string $deposito): PV
    {
        $this->deposito = $deposito;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocalizador(): ?string
    {
        return $this->localizador;
    }

    /**
     * @param string|null $localizador
     * @return PV
     */
    public function setLocalizador(?string $localizador): PV
    {
        $this->localizador = $localizador;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCondPagto(): ?string
    {
        return $this->condPagto;
    }

    /**
     * @param string|null $condPagto
     * @return PV
     */
    public function setCondPagto(?string $condPagto): PV
    {
        $this->condPagto = $condPagto;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getVenctos(): ?string
    {
        return $this->venctos;
    }

    /**
     * @param string|null $venctos
     * @return PV
     */
    public function setVenctos(?string $venctos): PV
    {
        $this->venctos = $venctos;
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
     * @return PV
     */
    public function setObs(?string $obs): PV
    {
        $this->obs = $obs;
        return $this;
    }

    /**
     * @return PVItem[]|ArrayCollection|null
     */
    public function getItens()
    {
        return $this->itens;
    }

    /**
     * @param PVItem[]|ArrayCollection|null $itens
     * @return PV
     */
    public function setItens($itens): PV
    {
        $this->itens = $itens;
        return $this;
    }


    /**
     * Lê um PV de um array (vindo de um request) e faz o parse para JSON.
     * @param array $pvArr
     */
    public function requestToVenctos(array $pvArr): void
    {
        $venctos = [];
        for ($i = 1; $i <= 6; $i++) {
            $dt = $pvArr['venctos_dt0' . $i];
            $valor = $pvArr['venctos_valor0' . $i];
            $venctos[] = [
                'dtVencto' => $dt,
                'valor' => $valor
            ];
        }
        $this->setVenctos(json_encode($venctos));
    }

    /**
     * @return float|null
     */
    public function getSubtotal(): ?float
    {
        return $this->subtotal;
    }

    /**
     * @param float|null $subtotal
     * @return PV
     */
    public function setSubtotal(?float $subtotal): PV
    {
        $this->subtotal = $subtotal;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getDescontos(): ?float
    {
        return $this->descontos;
    }

    /**
     * @param float|null $descontos
     * @return PV
     */
    public function setDescontos(?float $descontos): PV
    {
        $this->descontos = $descontos;
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
     * @return PV
     */
    public function setTotal(?float $total): PV
    {
        $this->total = $total;
        return $this;
    }


}
