<?php

namespace App\Entity\Relatorios;

use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Relatorios\RelCliente01Repository")
 * @ORM\Table(name="rdp_rel_cliente01")
 *
 * @author Carlos Eduardo Pauluk
 */
class RelCliente01 implements EntityId
{

    // CODIGO|NOME|CPF|RG|ENDER|CIDADE|UF|CEP|DDD|FONE|BAIRRO|LOCALIZADOR|COND_PAGTO|DESBLOQUEIO_TMP|AC_COMPRAS|FLAG_LIB_PRECO|SUGERE_CONSULTA|MARGEM_ESPECIAL|LIMITE_COMPRAS|CLIENTE_BLOQUEADO

    use EntityIdTrait;


    /**
     *
     * @ORM\Column(name="codigo", type="integer", nullable=false)
     * @Groups("entity")
     *
     * @var int|null
     */
    private $codigo;

    /**
     *
     * @ORM\Column(name="nome", type="string", length=250, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $nome;

    /**
     *
     * @ORM\Column(name="documento", type="string", length=50, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $documento;

    /**
     *
     * @ORM\Column(name="rg", type="string", length=50, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $rg;

    /**
     *
     * @ORM\Column(name="endereco", type="string", length=250, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $endereco;

    /**
     *
     * @ORM\Column(name="cidade", type="string", length=50, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $cidade;

    /**
     *
     * @ORM\Column(name="estado", type="string", length=50, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $estado;

    /**
     *
     * @ORM\Column(name="cep", type="string", length=15, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $cep;

    /**
     *
     * @ORM\Column(name="fone", type="string", length=50, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $fone;

    /**
     *
     * @ORM\Column(name="bairro", type="string", length=50, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $bairro;

    /**
     *
     * @ORM\Column(name="localizador", type="string", length=10, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $localizador;

    /**
     *
     * @ORM\Column(name="cond_pagto", type="string", length=10, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $condPagto;

    /**
     *
     * @ORM\Column(name="desbloqueio_tmp", type="string", length=10, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $desbloqueioTmp;

    /**
     *
     * @ORM\Column(name="ac_compras", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    private $acCompras;

    /**
     *
     * @ORM\Column(name="flag_lib_preco", type="string", length=1, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $flagLibPreco;

    /**
     *
     * @ORM\Column(name="sugere_consulta", type="string", length=1, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $sugereConsulta;

    /**
     *
     * @ORM\Column(name="margem_especial", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    private $margemEspecial;

    /**
     *
     * @ORM\Column(name="limite_compras", type="decimal", nullable=false)
     * @Groups("entity")
     *
     * @var float|null
     */
    private $limiteCompras;

    /**
     *
     * @ORM\Column(name="cliente_bloqueado", type="string", length=1, nullable=false)
     * @Groups("entity")
     *
     * @var string|null
     */
    private $clienteBloqueado;

    /**
     * @return int|null
     */
    public function getCodigo(): ?int
    {
        return $this->codigo;
    }

    /**
     * @param int|null $codigo
     * @return RelCliente01
     */
    public function setCodigo(?int $codigo): RelCliente01
    {
        $this->codigo = $codigo;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNome(): ?string
    {
        return $this->nome;
    }

    /**
     * @param string|null $nome
     * @return RelCliente01
     */
    public function setNome(?string $nome): RelCliente01
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDocumento(): ?string
    {
        return $this->documento;
    }

    /**
     * @param string|null $documento
     * @return RelCliente01
     */
    public function setDocumento(?string $documento): RelCliente01
    {
        $this->documento = $documento;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRg(): ?string
    {
        return $this->rg;
    }

    /**
     * @param string|null $rg
     * @return RelCliente01
     */
    public function setRg(?string $rg): RelCliente01
    {
        $this->rg = $rg;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEndereco(): ?string
    {
        return $this->endereco;
    }

    /**
     * @param string|null $endereco
     * @return RelCliente01
     */
    public function setEndereco(?string $endereco): RelCliente01
    {
        $this->endereco = $endereco;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCidade(): ?string
    {
        return $this->cidade;
    }

    /**
     * @param string|null $cidade
     * @return RelCliente01
     */
    public function setCidade(?string $cidade): RelCliente01
    {
        $this->cidade = $cidade;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEstado(): ?string
    {
        return $this->estado;
    }

    /**
     * @param string|null $estado
     * @return RelCliente01
     */
    public function setEstado(?string $estado): RelCliente01
    {
        $this->estado = $estado;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCep(): ?string
    {
        return $this->cep;
    }

    /**
     * @param string|null $cep
     * @return RelCliente01
     */
    public function setCep(?string $cep): RelCliente01
    {
        $this->cep = $cep;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFone(): ?string
    {
        return $this->fone;
    }

    /**
     * @param string|null $fone
     * @return RelCliente01
     */
    public function setFone(?string $fone): RelCliente01
    {
        $this->fone = $fone;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBairro(): ?string
    {
        return $this->bairro;
    }

    /**
     * @param string|null $bairro
     * @return RelCliente01
     */
    public function setBairro(?string $bairro): RelCliente01
    {
        $this->bairro = $bairro;
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
     * @return RelCliente01
     */
    public function setLocalizador(?string $localizador): RelCliente01
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
     * @return RelCliente01
     */
    public function setCondPagto(?string $condPagto): RelCliente01
    {
        $this->condPagto = $condPagto;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDesbloqueioTmp(): ?string
    {
        return $this->desbloqueioTmp;
    }

    /**
     * @param string|null $desbloqueioTmp
     * @return RelCliente01
     */
    public function setDesbloqueioTmp(?string $desbloqueioTmp): RelCliente01
    {
        $this->desbloqueioTmp = $desbloqueioTmp;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getAcCompras(): ?float
    {
        return $this->acCompras;
    }

    /**
     * @param float|null $acCompras
     * @return RelCliente01
     */
    public function setAcCompras(?float $acCompras): RelCliente01
    {
        $this->acCompras = $acCompras;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFlagLibPreco(): ?string
    {
        return $this->flagLibPreco;
    }

    /**
     * @param string|null $flagLibPreco
     * @return RelCliente01
     */
    public function setFlagLibPreco(?string $flagLibPreco): RelCliente01
    {
        $this->flagLibPreco = $flagLibPreco;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSugereConsulta(): ?string
    {
        return $this->sugereConsulta;
    }

    /**
     * @param string|null $sugereConsulta
     * @return RelCliente01
     */
    public function setSugereConsulta(?string $sugereConsulta): RelCliente01
    {
        $this->sugereConsulta = $sugereConsulta;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMargemEspecial(): ?float
    {
        return $this->margemEspecial;
    }

    /**
     * @param float|null $margemEspecial
     * @return RelCliente01
     */
    public function setMargemEspecial(?float $margemEspecial): RelCliente01
    {
        $this->margemEspecial = $margemEspecial;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getLimiteCompras(): ?float
    {
        return $this->limiteCompras;
    }

    /**
     * @param float|null $limiteCompras
     * @return RelCliente01
     */
    public function setLimiteCompras(?float $limiteCompras): RelCliente01
    {
        $this->limiteCompras = $limiteCompras;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getClienteBloqueado(): ?string
    {
        return $this->clienteBloqueado;
    }

    /**
     * @param string|null $clienteBloqueado
     * @return RelCliente01
     */
    public function setClienteBloqueado(?string $clienteBloqueado): RelCliente01
    {
        $this->clienteBloqueado = $clienteBloqueado;
        return $this;
    }


}

