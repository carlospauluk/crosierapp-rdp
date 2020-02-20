<?php

namespace App\Entity\Estoque;

use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\NotUppercase;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Estoque\ProdutoRepository")
 * @ORM\Table(name="est_produto")
 *
 * @author Carlos Eduardo Pauluk
 */
class Produto implements EntityId
{

    use EntityIdTrait;

    /**
     * @ORM\Column(name="uuid", type="string", nullable=false, length=36)
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var string|null
     */
    public ?string $UUID;

    /**
     *
     * @ORM\Column(name="nome", type="string", nullable=false)
     * @Groups("entity")
     *
     * @var null|string
     */
    public ?string $nome;

    /**
     *
     *
     * @ORM\Column(name="subgrupo_id", type="integer")
     * @Groups("entity")
     *
     * @var null|int
     */
    public ?int $subgrupoId;

    /**
     * ATIVO,INATIVO
     *
     * @ORM\Column(name="status", type="string", nullable=true)
     * @Groups("entity")
     *
     * @var null|string
     */
    public ?string $status;

    /**
     * S,N
     *
     * @ORM\Column(name="composicao", type="string", nullable=true)
     * @Groups("entity")
     *
     * @var null|string
     */
    public ?string $composicao = 'N';


    /**
     *
     * @ORM\Column(name="json_data", type="json")
     * @var null|array
     * @NotUppercase()
     * @Groups("entity")
     */
    public ?array $jsonData = null;


}