<?php

namespace App\Entity\Vendas;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

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
     * @ORM\Column(name="dt_emissao", type="date", nullable=false)
     * @Groups("entity")
     *
     * @var \DateTime|null
     */
    private $dtEmissao;

    /**
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
     * @ORM\Column(name="vendedor_cod", type="integer", nullable=false)
     * @Groups("entity")
     *
     * @var int|null
     */
    private $vendedorCod;

    /**
     *
     * @ORM\Column(name="vendedor_nome", type="string", nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $vendedorNome;

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
     * JSON.
     *
     * @ORM\Column(name="venctos", type="string", nullable=false)
     * @Groups("entity")
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
     * @return int|null
     */
    public function getVendedorCod(): ?int
    {
        return $this->vendedorCod;
    }

    /**
     * @param int|null $vendedorCod
     * @return PV
     */
    public function setVendedorCod(?int $vendedorCod): PV
    {
        $this->vendedorCod = $vendedorCod;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getVendedorNome(): ?string
    {
        return $this->vendedorNome;
    }

    /**
     * @param string|null $vendedorNome
     * @return PV
     */
    public function setVendedorNome(?string $vendedorNome): PV
    {
        $this->vendedorNome = $vendedorNome;
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
    public function setItens($itens)
    {
        $this->itens = $itens;
        return $this;
    }


}


class Vencto
{
    /** @var \DateTime */
    private $dt;

    /** @var float */
    private $valor;

    /**
     * @return \DateTime
     */
    public function getDt(): \DateTime
    {
        return $this->dt;
    }

    /**
     * @param \DateTime $dt
     * @return Vencto
     */
    public function setDt(\DateTime $dt): Vencto
    {
        $this->dt = $dt;
        return $this;
    }

    /**
     * @return float
     */
    public function getValor(): float
    {
        return $this->valor;
    }

    /**
     * @param float $valor
     * @return Vencto
     */
    public function setValor(float $valor): Vencto
    {
        $this->valor = $valor;
        return $this;
    }


}