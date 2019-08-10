<?php

namespace App\Controller\Relatorios;


use App\Business\Relatorios\RelEstoque01Business;
use App\Entity\Financeiro\Movimentacao;
use App\Entity\Relatorios\RelCompras01;
use App\Entity\Relatorios\RelEstoque01;
use App\EntityHandler\Financeiro\MovimentacaoEntityHandler;
use App\EntityHandler\Relatorios\RelEstoque01EntityHandler;
use App\Form\Financeiro\MovimentacaoAlterarEmLoteType;
use App\Repository\Relatorios\RelCompras01Repository;
use App\Repository\Relatorios\RelEstoque01Repository;
use CrosierSource\CrosierLibBaseBundle\APIClient\Base\DiaUtilAPIClient;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @author Carlos Eduardo Pauluk
 */
class RelEstoque01Controller extends FormListController
{

    protected $crudParams =
        [
            'typeClass' => null,

            'listView' => 'Relatorios/relEstoque01_list.html.twig',
            'listRoute' => 'relEstoque01_list',
            'listRouteAjax' => 'relEstoque01_datatablesJsList',
            'listPageTitle' => 'Estoque',
            'listId' => 'relEstoque01List',
            'list_PROGRAM_UUID' => '',

            'role_access' => 'ROLE_RELVENDAS01',
            'role_delete' => 'ROLE_ADMIN',

        ];


    /** @var DiaUtilAPIClient */
    private $diaUtilAPIClient;

    /** @var SessionInterface */
    private $session;

    /** @var RelEstoque01Business */
    private $relEstoque01Business;

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
     * @param DiaUtilAPIClient $diaUtilAPIClient
     */
    public function setDiaUtilAPIClient(DiaUtilAPIClient $diaUtilAPIClient): void
    {
        $this->diaUtilAPIClient = $diaUtilAPIClient;
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
     * @param RelEstoque01Business $relEstoque01Business
     */
    public function setRelEstoque01Business(RelEstoque01Business $relEstoque01Business): void
    {
        $this->relEstoque01Business = $relEstoque01Business;
    }


    public function getFilterDatas(array $params): array
    {
        return [
            new FilterData(['descProduto'], 'LIKE', 'descProduto', $params),
            new FilterData(['descFilial'], 'EQ', 'descFilial', $params),
            new FilterData(['nomeFornecedor'], 'EQ', 'nomeFornecedor', $params),
            new FilterData(['qtdeMinima'], 'GT', 'qtdeMinima', $params),
            new FilterData(['deficit'], 'GT', 'deficit', $params),
            new FilterData(['dtUltSaida'], 'GT', 'dtUltSaidaApartirDe', $params),
        ];
    }

    /**
     *
     * @Route("/relEstoque01/list/", name="relEstoque01_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function list(Request $request): Response
    {
        /** @var RelEstoque01Repository $repo */
        $repo = $this->getDoctrine()->getRepository(RelEstoque01::class);

        $filiais = $repo->getFiliais();
        array_unshift($filiais, ['id' => '', 'text' => 'TODAS']);
        $params['filiais'] = json_encode($filiais);
        $descFilial = $request->get('filter')['descFilial'] ?? null;

        $fornecedores = $repo->getFornecedores();
        array_unshift($fornecedores, ['id' => '', 'text' => 'TODOS']);
        $params['fornecedores'] = json_encode($fornecedores);
        $nomeFornecedor = urldecode($request->get('filter')['nomeFornecedor'] ?? null);

        if ($request->get('filter')['dtUltSaidaApartirDe'] ?? null) {
            $dtUltSaidaApartirDe = DateTimeUtils::parseDateStr($request->get('filter')['dtUltSaidaApartirDe']);
        } else {
            $dtUltSaidaApartirDe = DateTimeUtils::parseDateStr('1900-01-01');
        }

        $totais = $repo->totalEstoque($dtUltSaidaApartirDe, $descFilial, $nomeFornecedor);
        $params['totais'] = $totais[0] ?? null;

        return $this->doList($request, $params);
    }

    /**
     *
     * @Route("/relEstoque01/datatablesJsList/", name="relEstoque01_datatablesJsList")
     * @param Request $request
     * @return Response
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     */
    public function datatablesJsList(Request $request): Response
    {
        $defaultFilters = [
            'filter' => [
                'qtdeAtual' => 0
            ]
        ];
        // RTA: como o nomeFornecedor é também o id do select2, ele está com urlencode, então...
        if ($request->get('formPesquisar') ?? null) {
            parse_str($request->get('formPesquisar'), $formPesquisar);
            if ($formPesquisar['filter']['nomeFornecedor'] ?? null) {
                $defaultFilters['filter']['nomeFornecedor'] = urldecode($formPesquisar['filter']['nomeFornecedor']);
            }
        }
        return $this->doDatatablesJsList($request, $defaultFilters);
    }


    /**
     *
     * @Route("/relEstoque01/listReposicao/", name="relEstoque01_listReposicao")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function listReposicao(Request $request): Response
    {
        /** @var RelEstoque01Repository $repo */
        $repo = $this->getDoctrine()->getRepository(RelEstoque01::class);
        $filiais = $repo->getFiliais();
        array_unshift($filiais, ['id' => '', 'text' => 'TODAS']);
        $params['filiais'] = json_encode($filiais);
        $descFilial = $request->get('filter')['descFilial'] ?? null;

        $fornecedores = $repo->getFornecedores();
        array_unshift($fornecedores, ['id' => '', 'text' => 'TODOS']);
        $params['fornecedores'] = json_encode($fornecedores);
        $nomeFornecedor = urldecode($request->get('filter')['nomeFornecedor'] ?? null);

        if ($request->get('filter')['dtUltSaidaApartirDe'] ?? null) {
            $dtUltSaidaApartirDe = DateTimeUtils::parseDateStr($request->get('filter')['dtUltSaidaApartirDe']);
        } else {
            $dtUltSaidaApartirDe = DateTimeUtils::parseDateStr('1900-01-01');
        }

        $totais = $repo->totalEstoque($dtUltSaidaApartirDe, $descFilial, $nomeFornecedor);

        $params['totais'] = $totais[0] ?? null;


        $this->crudParams['listId'] = 'relEstoque01List_reposicao';
        $this->crudParams['listRoute'] = 'relEstoque01_listReposicao';
        $this->crudParams['listRouteAjax'] = 'relEstoque01_listReposicao_datatablesJsList';
        $this->crudParams['listPageTitle'] = 'Reposição de Estoque';
        $this->crudParams['listView'] = 'Relatorios/relEstoque01_reposicao_list.html.twig';

        return $this->doList($request, $params);
    }

    /**
     *
     * @Route("/relEstoque01/listReposicao/datatablesJsList/", name="relEstoque01_listReposicao_datatablesJsList")
     * @param Request $request
     * @return Response
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     */
    public function listReposicao_datatablesJsList(Request $request): Response
    {
        $defaultFilters = [
            'filter' => [
                'qtdeAtual' => 0,
                'deficit' => 0
            ]
        ];
        // RTA: como o nomeFornecedor é também o id do select2, ele está com urlencode, então...
        if ($request->get('formPesquisar') ?? null) {
            parse_str($request->get('formPesquisar'), $formPesquisar);
            if ($formPesquisar['filter']['nomeFornecedor'] ?? null) {
                $defaultFilters['filter']['nomeFornecedor'] = urldecode($formPesquisar['filter']['nomeFornecedor']);
            }
        }

        $this->crudParams['listId'] = 'relEstoque01List_reposicao';
        $this->crudParams['listRoute'] = 'relEstoque01_listReposicao';
        $this->crudParams['listRouteAjax'] = 'relEstoque01_listReposicao_datatablesJsList';
        $this->crudParams['listPageTitle'] = 'Reposição de Estoque';
        $this->crudParams['listView'] = 'Relatorios/relEstoque01_reposicao_list.html.twig';

        return $this->doDatatablesJsList($request, $defaultFilters);
    }

    /**
     * @param array $dados
     */
    public function handleDadosList(array &$dados)
    {
        /** @var RelCompras01Repository $repoCompras */
        $repoCompras = $this->getDoctrine()->getRepository(RelCompras01::class);
        /** @var RelEstoque01 $r */
        foreach ($dados as $r) {
            $compras = $repoCompras->itensDeComprasPorProduto($r->getCodProduto());
            if ($compras) {
                $r->setTemCompras(true);
            }
        }
    }

    /**
     *
     * @Route("/relEstoque01/graficoTotalEstoquePorFilial/", name="relEstoque01_graficoTotalEstoquePorFilial")
     * @param Request $request
     * @return JsonResponse
     */
    public function graficoTotalEstoquePorFilial(Request $request): JsonResponse
    {
        /** @var RelEstoque01Repository $repoRelEstoque01 */
        $repoRelEstoque01 = $this->getDoctrine()->getRepository(RelEstoque01::class);
        $r = $repoRelEstoque01->totalEstoquePorFilial();
        return new JsonResponse($r);
    }


    /**
     *
     * @Route("/relEstoque01/gerarPedidoCompra/", name="relEstoque01_gerarPedidoCompra")
     * @param Request $request
     * @return RedirectResponse
     * @throws \Exception
     */
    public function gerarPedidoCompra(Request $request): RedirectResponse
    {
        try {
            if ($request->get('btnGerarPedidoCompra')) {
                if (!$request->get('rSel')) {
                    $this->addFlash('warn', 'Nenhuma movimentação selecionada.');
                } else {
                    $rSel = $request->get('rSel');
                    $ids = [];
                    foreach ($rSel as $id => $on) {
                        $ids[] = $id;
                    }
                    $arquivo = $this->relEstoque01Business->gerarPedidoCompra($ids);
                    $this->addFlash('info', $arquivo);
                    $this->addFlash('success', 'Pedido de compra gerado com sucesso!');
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->logger->error('Erro ao gerar pedido de compra');
            $this->addFlash('error', 'Erro ao gerar pedido de compra');
        }

        return $this->redirectToRoute('relEstoque01_listReposicao');
    }


    /**
     *
     * @Route("/relEstoque01/imprimirListaReposicao/", name="relEstoque01_imprimirListaReposicao")
     *
     * @param Request $request
     * @return void
     * @throws ViewException
     * @throws \Exception
     */
    public function imprimirListaReposicao(Request $request): void
    {
        /** @var RelEstoque01Repository $repoEstoque */
        $repoEstoque = $this->getDoctrine()->getRepository(RelEstoque01::class);


        $params['filter'] = $request->get('filter');


        if ($params['filter']['nomeFornecedor'] ?? null) {
            $params['filter']['nomeFornecedor'] = urldecode($params['filter']['nomeFornecedor']);
        }

        $params['filter']['qtdeAtual'] = 0;
        $params['filter']['deficit'] = 0;

        $filterDatas = $this->getSomenteFilterDatasComValores($params);
        $dados = $repoEstoque->findByFilters($filterDatas, null, 0, -1);

        $parameters['dados'] = $dados;
        $parameters['hoje'] = (new \DateTime())->format('d/m/Y H:i');

        gc_collect_cycles();
        gc_disable();

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('enable_remote', true);
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $pdfOptions->setIsRemoteEnabled(true);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('Relatorios/relEstoque01_listaReposicao_PDF.html.twig', $parameters);
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);


        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream('aPagarReceber_rel.pdf', [
            'Attachment' => false
        ]);

        gc_collect_cycles();
        gc_enable();

    }


}
