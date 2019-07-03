<?php

namespace App\Entity\Utils;

use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\NotUppercase;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Utils\RelatorioPushRepository")
 * @ORM\Table(name="rdp_utl_relatoriospush")
 *
 * @Vich\Uploadable()
 *
 *
 * @author Carlos Eduardo Pauluk
 */
class RelatorioPush implements EntityId
{

    use EntityIdTrait;


    /**
     *
     * @ORM\Column(name="dt_envio", type="datetime", nullable=false)
     * @Groups("entity")
     */
    private $dtEnvio;

    /**
     *
     * @ORM\Column(name="descricao", type="string", nullable=true, length=400)
     * @Groups("entity")
     */
    private $descricao;

    /**
     *
     * @ORM\Column(name="user_destinatario_id", type="integer", nullable=false)
     * @Groups("entity")
     */
    private $userDestinatarioId;

    /**
     *
     * @ORM\Column(name="dt_aberto_em", type="datetime", nullable=true)
     * @Groups("entity")
     */
    private $abertoEm;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="relatoriospush", fileNameProperty="arquivo")
     *
     * @var UploadedFile
     */
    private $file;

    /**
     * @ORM\Column(name="arquivo", type="string", length=300)
     *
     * @var string
     * @NotUppercase()
     * @Groups("entity")
     */
    private $arquivo;


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


    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param null $image
     * @throws \Exception
     */
    public function setFile($image = null): void
    {
        $this->file = $image;

        if (null !== $image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->setUpdated(new \DateTime());
        }
    }

    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getArquivo(): string
    {
        return $this->arquivo;
    }

    /**
     * @param string $arquivo
     * @return RelatorioPush
     */
    public function setArquivo(string $arquivo): RelatorioPush
    {
        $this->arquivo = $arquivo;
        return $this;
    }




}

