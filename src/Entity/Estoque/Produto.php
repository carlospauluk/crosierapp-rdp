<?php

namespace App\Entity\Estoque;

use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\NotUppercase;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

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
    public ?string $UUID = null;

    /**
     *
     * @ORM\Column(name="nome", type="string", nullable=false)
     * @Groups("entity")
     *
     * @var null|string
     */
    public ?string $nome = null;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\Depto")
     * @ORM\JoinColumn(name="depto_id", nullable=false)
     * @Groups("entity")
     * @MaxDepth(1)
     * @var $depto null|Depto
     */
    public ?Depto $depto = null;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\Grupo")
     * @ORM\JoinColumn(name="grupo_id", nullable=false)
     * @Groups("entity")
     * @MaxDepth(1)
     * @var $grupo null|Grupo
     */
    public ?Grupo $grupo = null;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Estoque\Subgrupo")
     * @ORM\JoinColumn(name="subgrupo_id", nullable=false)
     * @Groups("entity")
     * @MaxDepth(1)
     * @var $subgrupo null|Subgrupo
     */
    public ?Subgrupo $subgrupo = null;

    /**
     *
     *
     * @ORM\Column(name="depto_id", type="integer")
     * @Groups("entity")
     *
     * @var null|int
     */
    public ?int $deptoId = null;

    /**
     *
     *
     * @ORM\Column(name="grupo_id", type="integer")
     * @Groups("entity")
     *
     * @var null|int
     */
    public ?int $grupoId = null;

    /**
     *
     *
     * @ORM\Column(name="subgrupo_id", type="integer")
     * @Groups("entity")
     *
     * @var null|int
     */
    public ?int $subgrupoId = null;

    /**
     * ATIVO,INATIVO
     *
     * @ORM\Column(name="status", type="string", nullable=true)
     * @Groups("entity")
     *
     * @var null|string
     */
    public ?string $status = null;

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

    /**
     *
     * @ORM\OneToMany(targetEntity="ProdutoImagem", mappedBy="produto", cascade={"all"}, orphanRemoval=true)
     * @var ProdutoImagem[]|ArrayCollection|null
     * @ORM\OrderBy({"ordem" = "ASC"})
     *
     */
    public $imagens;

    // ---------- CAMPOS GENERATEDS (para exibição na list e poder dar ORDER BY)

    /**
     * @ORM\Column(name="qtde_imagens", type="integer")
     * @Groups("entity")
     *
     * @var null|int
     */
    public ?int $qtdeImagens = null;

    /**
     * @ORM\Column(name="qtde_estoque_total", type="integer")
     * @Groups("entity")
     *
     * @var null|int
     */
    public ?int $qtdeEstoqueTotal = null;

    /**
     * @ORM\Column(name="depto_nome", type="string")
     * @Groups("entity")
     *
     * @var null|string
     */
    public ?string $deptoNome = null;

    /**
     * @ORM\Column(name="titulo", type="string")
     * @Groups("entity")
     *
     * @var null|string
     */
    public ?string $titulo = null;

    /**
     * @ORM\Column(name="porcent_preench", type="decimal")
     * @Groups("entity")
     *
     * @var null|float
     */
    public ?float $porcentPreench = null;


    public function __construct()
    {
        $this->imagens = new ArrayCollection();
    }


}