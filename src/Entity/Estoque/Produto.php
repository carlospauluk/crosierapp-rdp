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
     * @ORM\Column(name="uuid", type="string", length=36)
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var string|null
     */
    public $UUID;


    /**
     * Redundante: apenas para auxiliar acesso.
     *
     * @ORM\Column(name="depto_codigo", type="string")
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var string|null
     */
    public $codigoDepto;

    /**
     * Redundante: apenas para auxiliar acesso.
     *
     * @ORM\Column(name="depto_nome", type="string")
     * @Groups("entity")
     *
     * @var string|null
     */
    public $nomeDepto;

    /**
     * Redundante: apenas para auxiliar acesso.
     *
     * @ORM\Column(name="grupo_codigo", type="string")
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var string|null
     */
    public $codigoGrupo;

    /**
     * Redundante: apenas para auxiliar acesso.
     *
     * @ORM\Column(name="grupo_nome", type="string")
     * @Groups("entity")
     *
     * @var string|null
     */
    public $nomeGrupo;

    /**
     * Redundante: apenas para auxiliar acesso.
     *
     * @ORM\Column(name="subgrupo_codigo", type="string")
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var string|null
     */
    public $codigoSubgrupo;

    /**
     * Redundante: apenas para auxiliar acesso.
     *
     * @ORM\Column(name="subgrupo_nome", type="string")
     * @Groups("entity")
     *
     * @var string|null
     */
    public $nomeSubgrupo;

    /**
     * Redundante: apenas para auxiliar acesso.
     *
     * @ORM\Column(name="fornecedor_nome", type="string")
     * @Groups("entity")
     *
     * @var string|null
     */
    public $nomeFornecedor;

    /**
     * Redundante: apenas para auxiliar acesso.
     *
     * @ORM\Column(name="fornecedor_documento", type="string")
     * @Groups("entity")
     *
     * @var string|null
     */
    public $documentoFornecedor;

    /**
     *
     * @ORM\Column(name="nome", type="string")
     * @Groups("entity")
     *
     * @var null|string
     */
    public $nome;

    /**
     *
     * @ORM\Column(name="titulo", type="string")
     * @Groups("entity")
     * @NotUppercase()
     * @var null|string
     */
    public $titulo;

    /**
     *
     * @ORM\Column(name="caracteristicas", type="string")
     * @Groups("entity")
     * @NotUppercase()
     *
     * @var null|string
     */
    public $caracteristicas;

    /**
     *
     * @ORM\Column(name="ean", type="string")
     * @Groups("entity")
     *
     * @var null|string
     */
    public $ean;

    /**
     *
     * @ORM\Column(name="referencia", type="string")
     * @Groups("entity")
     *
     * @var null|string
     */
    public $referencia;

    /**
     *
     * @ORM\Column(name="ncm", type="string")
     * @Groups("entity")
     *
     * @var null|string
     */
    public $ncm;

    /**
     * ATIVO,INATIVO
     *
     * @ORM\Column(name="status", type="string")
     * @Groups("entity")
     *
     * @var null|string
     */
    public $status;

    /**
     *
     * @ORM\Column(name="obs", type="string")
     * @Groups("entity")
     *
     * @var string|null
     */
    public $obs;

    /**
     * Caso este produto tenha sido importado de outro sistema, marca o código original.
     *
     * @ORM\Column(name="codigo_from", type="string")
     * @Groups("entity")
     *
     * @var string|null
     */
    public $codigoFrom;

    /**
     * Porcentagem de preenchimento dos atributos deste produto.
     *
     * @ORM\Column(name="porcent_preench", type="float")
     * @Groups("entity")
     *
     * @var float|null
     */
    public $porcentPreench;

    /**
     * Redundante: apenas para auxiliar acesso.
     *
     * @ORM\Column(name="imagem1", type="string")
     * @NotUppercase()
     * @Groups("entity")
     *
     * @var string|null
     */
    public $imagem1;

    /**
     * Redundante: apenas para auxiliar acesso.
     *
     * @ORM\Column(name="saldo_estoque_matriz", type="float")
     * @Groups("entity")
     *
     * @var float|null
     */
    public $saldoEstoqueMatriz;

    /**
     * Redundante: apenas para auxiliar acesso.
     *
     * @ORM\Column(name="saldo_estoque_acessorios", type="float")
     * @Groups("entity")
     *
     * @var float|null
     */
    public $saldoEstoqueAcessorios;

    /**
     * Redundante: apenas para auxiliar acesso.
     *
     * @ORM\Column(name="saldo_estoque_total", type="float")
     * @Groups("entity")
     *
     * @var float|null
     */
    public $saldoEstoqueTotal;


    private function __construct()
    {
    }

}