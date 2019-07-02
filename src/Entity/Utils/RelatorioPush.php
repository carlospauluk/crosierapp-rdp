<?php

namespace App\Entity\Utils;

use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\NotUppercase;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Utils\RelatorioPushRepository")
 * @ORM\Table(name="rdp_utl_relatoriospush")
 * @author Carlos Eduardo Pauluk
 */
class RelatorioPush implements EntityId
{

    use EntityIdTrait;

    /**
     * @var string
     * @ORM\Column(name="uuid", type="string", nullable=false, length=36)
     * @NotUppercase()
     */
    private $UUID;

    /**
     *
     * @ORM\Column(name="dt_envio", type="datetime", nullable=false)
     */
    private $dtEnvio;

    /**
     *
     * @ORM\Column(name="descricao", type="string", nullable=true, length=200)
     */
    private $descricao;

    /**
     *
     * @ORM\Column(name="user_destinatario_id", type="integer", nullable=false)
     */
    private $userDestinatarioId;

    /**
     *
     * @ORM\Column(name="dt_aberto_em", type="datetime", nullable=true)
     */
    private $abertoEm;

    /**
     * @return string
     */
    public function getUUID(): string
    {
        return $this->UUID;
    }

    /**
     * @param string $UUID
     * @return RelatorioPush
     */
    public function setUUID(string $UUID): RelatorioPush
    {
        $this->UUID = $UUID;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDtEnvio()
    {
        return $this->dtEnvio;
    }

    /**
     * @param mixed $dtEnvio
     * @return RelatorioPush
     */
    public function setDtEnvio($dtEnvio)
    {
        $this->dtEnvio = $dtEnvio;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * @param mixed $descricao
     * @return RelatorioPush
     */
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserDestinatarioId()
    {
        return $this->userDestinatarioId;
    }

    /**
     * @param mixed $userDestinatarioId
     * @return RelatorioPush
     */
    public function setUserDestinatarioId($userDestinatarioId)
    {
        $this->userDestinatarioId = $userDestinatarioId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAbertoEm()
    {
        return $this->abertoEm;
    }

    /**
     * @param mixed $abertoEm
     * @return RelatorioPush
     */
    public function setAbertoEm($abertoEm)
    {
        $this->abertoEm = $abertoEm;
        return $this;
    }


}

