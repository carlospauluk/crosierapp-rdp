<?php

namespace App\Controller\Vendas;


use App\Entity\Relatorios\RelEstoque01;
use App\Entity\Vendas\PV;
use App\Entity\Vendas\PVItem;
use App\EntityHandler\Relatorios\RelEstoque01EntityHandler;
use App\EntityHandler\Vendas\PVItemEntityHandler;
use App\Repository\Relatorios\RelEstoque01Repository;
use App\Repository\Vendas\PVRepository;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
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
     * @param RelEstoque01EntityHandler $entityHandler
     */
    public function setEntityHandler(RelEstoque01EntityHandler $entityHandler): void
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
            new FilterData(['descProduto'], 'LIKE', 'descProduto', $params),
            new FilterData(['descFilial'], 'EQ', 'descFilial', $params),
            new FilterData(['codFornecedor'], 'EQ', 'codFornecedor', $params),
            new FilterData(['qtdeMinima'], 'GT', 'qtdeMinima', $params),
            new FilterData(['deficit'], 'GT', 'deficit', $params),
            new FilterData(['dtUltSaida'], 'GT', 'dtUltSaidaApartirDe', $params),
        ];
    }


    /**
     *
     * @Route("/ven/pv/relEstoque01/list/{pv}", name="ven_pv_relEstoque01_list", requirements={"pv"="\d+"})
     * @param Request $request
     * @param PV $pv
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @IsGranted({"ROLE_PV"}, statusCode=403)
     */
    public function list(Request $request, PV $pv): Response
    {
        $params = [
            'listView' => 'Vendas/relEstoque01_list.html.twig',
            'listRoute' => 'ven_pv_relEstoque01_list',
            'listRouteAjax' => 'ven_pv_relEstoque01_datatablesJsList',
            'listPageTitle' => 'Estoque',
            'listId' => 'ven_pv_relEstoque01List'
        ];


        /** @var RelEstoque01Repository $repo */
        $repo = $this->getDoctrine()->getRepository(RelEstoque01::class);

        $filiais = $repo->getFiliais();
        // array_unshift($filiais, ['id' => '', 'text' => 'TODAS']);
        $params['filiais'] = json_encode($filiais);
        $descFilial = $request->get('filter')['descFilial'] ?? 'MATRIZ'; // se não selecionou nenhuma, seta a MATRIZ
        $params['filter']['descFilial'] = $descFilial;

        $params['page_subTitle'] = 'PV ' . $pv->getId() . ' (' . $pv->getClienteNome() . ')';


        $fornecedores = $repo->getFornecedores();
        array_unshift($fornecedores, ['id' => '', 'text' => 'TODOS']);
        $params['fornecedores'] = json_encode($fornecedores);
        $codFornecedor = $request->get('filter')['codFornecedor'] ?? null;
        $codFornecedor = $codFornecedor ? (int)$codFornecedor : null;

        if ($request->get('filter')['dtUltSaidaApartirDe'] ?? null) {
            $dtUltSaidaApartirDe = DateTimeUtils::parseDateStr($request->get('filter')['dtUltSaidaApartirDe']);
        } else {
            $dtUltSaidaApartirDe = DateTimeUtils::parseDateStr('1900-01-01');
        }

        $totais = $repo->totalEstoque($dtUltSaidaApartirDe, $descFilial, $codFornecedor);
        $params['totais'] = $totais[0] ?? null;

        $params['qtdeProdutosNoCarrinho'] = isset($this->session->get('carrinho')['itens']) ? count($this->session->get('carrinho')['itens']) : '';

        $params['routeParams'] = ['pv' => $pv->getId()];

        return $this->doList($request, $params);
    }

    /**
     *
     * @Route("/ven/pv/relEstoque01/datatablesJsList/", name="ven_pv_relEstoque01_datatablesJsList")
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
        $codProduto = $request->get('codProduto');
        $filial = $request->get('filial');
        $qtde = $request->get('qtde');
        $codFornecedor = $request->get('codFornecedor');
        try {
            $results = [
                'msg' => null,
                'produto' => null
            ];
            /** @var RelEstoque01Repository $repoEstoque */
            $repoEstoque = $this->getDoctrine()->getRepository(RelEstoque01::class);
            /** @var RelEstoque01 $produto */
            $produto = $repoEstoque->findOneBy(['codProduto' => $codProduto, 'codFornecedor' => $codFornecedor, 'descFilial' => $filial], null, 1);

            /** @var PVRepository $repoPV */
            $repoPV = $this->getDoctrine()->getRepository(PV::class);
            /** @var PV $pv */
            $pv = $repoPV->find($pvId);

            if (!$produto) {
                $results['msg'] = 'Nenhum produto encontrado com o código "' . $codProduto . '"';
            } else {

                $achou = false;
                /** @var PVItem $item */
                foreach ($pv->getItens() as $item) {
                    if ($item->getProdutoCod() === $codProduto) {
                        $achou = true;
                        break;
                    }
                }
                if (!$achou) {
                    $pvItem = new PVItem();
                    $pvItem->setPv($pv);
                    $pvItem->setQtde($qtde);
                    $pvItem->setProdutoCod($produto->getCodProduto());
                    $pvItem->setProdutoDesc($produto->getDescProduto());
                    $pvItem->setCodFornecedor($produto->getCodFornecedor());
                    $pvItem->setNomeFornecedor($produto->getNomeFornecedor());
                    $pvItem->setPrecoCusto($produto->getCustoMedio());
                    $pvItem->setPrecoVenda($produto->getPrecoVenda());
                    $pvItem->setPrecoOrc($produto->getPrecoVenda());
                    $pvItem->setDesconto(0.0);
                    $pvItem->setTotal(bcmul($produto->getPrecoVenda(), $qtde));

                    $this->pvItemEntityHandler->save($pvItem);


                    $results['msg'] = '"' . $produto->getCodProduto() . ' - ' . $produto->getDescProduto() . '" adicionado com sucesso';
                    $results['produto'] = $produto->getCodProduto() . ' - ' . $produto->getDescProduto();
                } else {
                    $results['msg'] = '"' . $produto->getCodProduto() . ' - ' . $produto->getDescProduto() . '" já adicionado ao carrinho';
                    $results['produto'] = $produto->getCodProduto() . ' - ' . $produto->getDescProduto();

                }
            }

        } catch (\Exception $e) {
            $results['msg'] = 'Erro ao adicionar produto no carrinho';
        }
        return new JsonResponse($results);
    }


}
