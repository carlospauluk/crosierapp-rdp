<?php

namespace App\Entity\Estoque;

use CrosierSource\CrosierLibBaseBundle\Doctrine\Annotations\NotUppercase;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityId;
use CrosierSource\CrosierLibBaseBundle\Entity\EntityIdTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(readOnly=true, repositoryClass="App\Repository\Estoque\ProdutoRepository")
 * @ORM\Table(name="vw_rdp_est_produto")
 *
 * @author Carlos Eduardo Pauluk
 */
class Produto implements EntityId
{
    use EntityIdTrait;

    /**
     *
     * @ORM\Column(name="depto_nome", type="string")
     * @Groups("entity")
     *
     * @var string|null
     */
    public ?string $nomeDepto;

    /**
     *
     * @ORM\Column(name="nome", type="string")
     * @Groups("entity")
     *
     * @var null|string
     */
    public ?string $nome;

    /**
     *
     * @ORM\Column(name="titulo", type="string")
     * @Groups("entity")
     * @NotUppercase()
     * @var null|string
     */
    public ?string $titulo;

    /**
     * ATIVO,INATIVO
     *
     * @ORM\Column(name="status", type="string")
     * @Groups("entity")
     *
     * @var null|string
     */
    public ?string $status;

    /**
     * Porcentagem de preenchimento dos atributos deste produto.
     *
     * @ORM\Column(name="porcent_preench", type="float")
     * @Groups("entity")
     *
     * @var float|null
     */
    public ?float $porcentPreench;

    /**
     *
     * @ORM\Column(name="qtde_estoque_matriz", type="float")
     * @Groups("entity")
     *
     * @var float|null
     */
    public ?float $qtdeEstoqueMatriz;

    /**
     *
     * @ORM\Column(name="qtde_estoque_acessorios", type="float")
     * @Groups("entity")
     *
     * @var float|null
     */
    public ?float $qtdeEstoqueAcessorios;

    /**
     *
     * @ORM\Column(name="qtde_estoque_total", type="float")
     * @Groups("entity")
     *
     * @var float|null
     */
    public ?float $qtdeEstoqueTotal;

    /**
     * Redundante: apenas para auxiliar acesso.
     *
     * @ORM\Column(name="imagem1", type="string")
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var string|null
     */
    public ?string $imagem1;

    /**
     * Redundante: apenas para auxiliar acesso.
     *
     * @ORM\Column(name="qtde_imagens", type="integer")
     * @Groups("entity")
     *
     * @var int|null
     */
    public ?int $qtdeImagens;

    private function __construct()
    {
    }

}