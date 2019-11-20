<?php

namespace App\Controller\Vendas;


use App\Entity\Estoque\Produto;
use App\Entity\Vendas\PV;
use App\Entity\Vendas\PVItem;
use App\EntityHandler\Estoque\ProdutoEntityHandler;
use App\EntityHandler\Vendas\PVItemEntityHandler;
use App\Repository\Estoque\ProdutoRepository;
use App\Repository\Vendas\PVRepository;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Utils\EntityIdUtils\EntityIdUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @author Carlos Eduardo Pauluk
 */
class PVListEstoqueController extends FormListController
{

    /** @var SessionInterface */
    private $session;

    /** @var EntityIdUtils */
    private $entityIdUtils;

    /** @var PVItemEntityHandler */
    private $pvItemEntityHandler;

    /**
     * @required
     * @param ProdutoEntityHandler $entityHandler
     */
    public function setEntityHandler(ProdutoEntityHandler $entityHandler): void
    {
        $this->entityHandler = $entityHandler;
    }

    /**
     * @required
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session): void
    {
        $this->session = $session;
    }

    /**
     * @required
     * @param PVItemEntityHandler $pvItemEntityHandler
     */
    public function setPvItemEntityHandler(PVItemEntityHandler $pvItemEntityHandler): void
    {
        $this->pvItemEntityHandler = $pvItemEntityHandler;
    }


    /**
     * @required
     * @param EntityIdUtils $entityIdUtils
     */
    public function setEntityIdUtils(EntityIdUtils $entityIdUtils): void
    {
        $this->entityIdUtils = $entityIdUtils;
    }

    public function getFilterDatas(array $params): array
    {
        return [
            new FilterData(['nome'], 'LIKE', 'nome', $params),
        ];
    }


    /**
     *
     * @Route("/ven/pv/produto/list/{pv}", name="ven_pv_produto_list", requirements={"pv"="\d+"})
     * @param Request $request
     * @param PV $pv
     * @return Response
     * @throws \Exception
     * @IsGranted({"ROLE_PV"}, statusCode=403)
     */
    public function list(Request $request, PV $pv): Response
    {
        $params = [
            'listView' => 'Vendas/produto_list.html.twig',
            'listRoute' => 'ven_pv_produto_list',
            'listRouteAjax' => 'ven_pv_produto_datatablesJsList',
            'listPageTitle' => 'Estoque',
            'listId' => 'ven_pv_produtoList'
        ];


        /** @var ProdutoRepository $repo */
        $repo = $this->getDoctrine()->getRepository(Produto::class);

        $params['page_subTitle'] = 'PV ' . $pv->getId() . ' (' . $pv->getClienteNome() . ')';

        $params['qtdeProdutosNoCarrinho'] = isset($this->session->get('carrinho')['itens']) ? count($this->session->get('carrinho')['itens']) : '';

        $params['routeParams'] = ['pv' => $pv->getId()];

        return $this->doList($request, $params);
    }

    /**
     *
     * @Route("/ven/pv/produto/datatablesJsList/", name="ven_pv_produto_datatablesJsList")
     * @param Request $request
     * @return Response
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     *
     * @IsGranted({"ROLE_PV"}, statusCode=403)
     */
    public function datatablesJsList(Request $request): Response
    {
        return $this->doDatatablesJsList($request);
    }


    /**
     * @Route("/ven/pv/adicionarItem/", name="ven_pv_adicionarItem")
     * @param Request $request
     * @return JsonResponse
     *
     * @IsGranted({"ROLE_PV"}, statusCode=403)
     */
    public function adicionarNoPV(Request $request): JsonResponse
    {
        $pvId = $request->get('pv');
        $produtoId = $request->get('produtoId');
        $qtde = $request->get('qtde');
        try {
            $results = [
                'msg' => null,
                'produto' => null
            ];
            /** @var ProdutoRepository $repoEstoque */
            $repoEstoque = $this->getDoctrine()->getRepository(Produto::class);
            /** @var Produto $produto */
            $produto = $repoEstoque->find($produtoId);

            /** @var PVRepository $repoPV */
            $repoPV = $this->getDoctrine()->getRepository(PV::class);
            /** @var PV $pv */
            $pv = $repoPV->find($pvId);


            $achou = false;
            /** @var PVItem $item */
            foreach ($pv->getItens() as $item) {
                if ($item->getProdutoCod() === $produtoId) {
                    $achou = true;
                    break;
                }
            }
            if (!$achou) {
                $pvItem = new PVItem();
                $pvItem->setPv($pv);
                $pvItem->setQtde($qtde);
                $pvItem->setProdutoCod($produto->getId());
                $pvItem->setProdutoDesc($produto->nome);
                $pvItem->setCodFornecedor($produto->fornecedorId);
                $pvItem->setNomeFornecedor($produto->nomeFornecedor);
                $pvItem->setPrecoCusto($produto->precoCusto);
                $pvItem->setPrecoVenda($produto->precoTabela);
                $pvItem->setPrecoOrc($produto->precoTabela);
                $pvItem->setDesconto(0.0);
                $pvItem->setTotal(bcmul($produto->precoTabela, $qtde));

                $this->pvItemEntityHandler->save($pvItem);

                $results['msg'] = '"' . $produto->getId() . ' - ' . $produto->nome . '" adicionado com sucesso';
                $results['produto'] = $produto->getId() . ' - ' . $produto->nome;
            } else {
                $results['msg'] = '"' . $produto->getId() . ' - ' . $produto->nome . '" já adicionado ao carrinho';
                $results['produto'] = $produto->getId() . ' - ' . $produto->nome;

            }

        } catch (\Exception $e) {
            $results['msg'] = 'Erro ao adicionar produto no carrinho';
        }
        return new JsonResponse($results);
    }


}
