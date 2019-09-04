<?php

namespace App\Controller\Relatorios;


use App\Business\Relatorios\RelEstoque01Business;
use App\Entity\Relatorios\RelCompras01;
use App\Entity\Relatorios\RelEstoque01;
use App\Entity\Relatorios\RelVendas01;
use App\EntityHandler\Relatorios\RelEstoque01EntityHandler;
use App\Repository\Relatorios\RelCompras01Repository;
use App\Repository\Relatorios\RelEstoque01Repository;
use App\Repository\Relatorios\RelVendas01Repository;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use Dompdf\Dompdf;
use Dompdf\Options;
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
class RelEstoque01Controller extends FormListController
{

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
            new FilterData(['codFornecedor'], 'EQ', 'codFornecedor', $params),
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
     *
     * @IsGranted({"ROLE_RELVENDAS"}, statusCode=403)
     */
    public function list(Request $request): Response
    {
        $params = [
            'listView' => 'Relatorios/relEstoque01_list.html.twig',
            'listRoute' => 'relEstoque01_list',
            'listRouteAjax' => 'relEstoque01_datatablesJsList',
            'listPageTitle' => 'Estoque',
            'listId' => 'relEstoque01List'
        ];


        /** @var RelEstoque01Repository $repo */
        $repo = $this->getDoctrine()->getRepository(RelEstoque01::class);

        $filiais = $repo->getFiliais();
        // array_unshift($filiais, ['id' => '', 'text' => 'TODAS']);
        $params['filiais'] = json_encode($filiais);
        $descFilial = $request->get('filter')['descFilial'] ?? 'MATRIZ'; // se não selecionou nenhuma, seta a MATRIZ
        $params['filter']['descFilial'] = $descFilial;

        $params['page_subTitle'] = $descFilial;


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

        return $this->doList($request, $params);
    }

    /**
     *
     * @Route("/relEstoque01/datatablesJsList/", name="relEstoque01_datatablesJsList")
     * @param Request $request
     * @return Response
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     *
     * @IsGranted({"ROLE_RELVENDAS"}, statusCode=403)
     */
    public function datatablesJsList(Request $request): Response
    {
        $defaultFilters = [
            'filter' => [
                'qtdeAtual' => 0
            ]
        ];
        return $this->doDatatablesJsList($request, $defaultFilters);
    }


    /**
     *
     * @Route("/relEstoque01/listReposicao/", name="relEstoque01_listReposicao")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     *
     * @IsGranted({"ROLE_RELVENDAS"}, statusCode=403)
     */
    public function listReposicao(Request $request): Response
    {
        /** @var RelEstoque01Repository $repo */
        $repo = $this->getDoctrine()->getRepository(RelEstoque01::class);
        $filiais = $repo->getFiliais();
        array_unshift($filiais, ['id' => '', 'text' => 'TODAS']);
        $params['filiais'] = json_encode($filiais);

        $fornecedores = $repo->getFornecedores();
        array_unshift($fornecedores, ['id' => '', 'text' => 'TODOS']);
        $params['fornecedores'] = json_encode($fornecedores);


        $queryParams = $request->query->all();
        if (!array_key_exists('filter', $queryParams)) {
            // inicializa para evitar o erro
            $queryParams['filter'] = null;

            if (isset($queryParams['r']) and $queryParams['r']) {
                $this->storedViewInfoBusiness->clear('relEstoque01_listReposicao');
            } else {
                if ($svi = $this->storedViewInfoBusiness->retrieve('relEstoque01_listReposicao')) {
                    $formPesquisar = $svi['formPesquisar'] ?? null;
                    if ($formPesquisar and $formPesquisar !== $queryParams) {
                        return $this->redirectToRoute('relEstoque01_listReposicao', $formPesquisar);
                    }
                }
            }
        }

        $descFilial = $queryParams['filter']['descFilial'] ?? null;
        $codFornecedor = $queryParams['filter']['codFornecedor'] ?? null;
        $codFornecedor = $codFornecedor ?: null;


        if ($request->get('filter')['dtUltSaidaApartirDe'] ?? null) {
            $dtUltSaidaApartirDe = DateTimeUtils::parseDateStr($request->get('filter')['dtUltSaidaApartirDe']);
        } else {
            $dtUltSaidaApartirDe = DateTimeUtils::parseDateStr('1900-01-01');
        }

        $totais = $repo->totalEstoque($dtUltSaidaApartirDe, $descFilial, $codFornecedor);

        $params['totais'] = $totais[0] ?? null;


        $params['listId'] = 'relEstoque01List_reposicao';
        $params['listRoute'] = 'relEstoque01_listReposicao';
        $params['listRouteAjax'] = 'relEstoque01_listReposicao_datatablesJsList';
        $params['listPageTitle'] = 'Reposição de Estoque';
        $params['listView'] = 'Relatorios/relEstoque01_reposicao_list.html.twig';

        $params['qtdeProdutosNoCarrinho'] = isset($this->session->get('carrinho')['itens']) ? count($this->session->get('carrinho')['itens']) : '';

        return $this->doList($request, $params);
    }

    /**
     *
     * @Route("/relEstoque01/listReposicao/datatablesJsList/", name="relEstoque01_listReposicao_datatablesJsList")
     * @param Request $request
     * @return Response
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     *
     * @IsGranted({"ROLE_RELVENDAS"}, statusCode=403)
     */
    public function listReposicao_datatablesJsList(Request $request): Response
    {
        $defaultFilters = [
            'filter' => [
                'qtdeAtual' => 0,
                'deficit' => 0
            ]
        ];
        $params['listId'] = 'relEstoque01List_reposicao';
        $params['listRoute'] = 'relEstoque01_listReposicao';
        $params['listRouteAjax'] = 'relEstoque01_listReposicao_datatablesJsList';
        $params['listPageTitle'] = 'Reposição de Estoque';
        $params['listView'] = 'Relatorios/relEstoque01_reposicao_list.html.twig';

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
     *
     * @IsGranted({"ROLE_RELVENDAS"}, statusCode=403)
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
     * @Route("/relEstoque01/imprimirListaReposicao/", name="relEstoque01_imprimirListaReposicao")
     *
     * @param Request $request
     * @return void
     * @throws ViewException
     * @throws \Exception
     *
     * @IsGranted({"ROLE_RELVENDAS"}, statusCode=403)
     */
    public function imprimirListaReposicao(Request $request): void
    {
        /** @var RelEstoque01Repository $repoEstoque */
        $repoEstoque = $this->getDoctrine()->getRepository(RelEstoque01::class);


        $params['filter'] = $request->get('filter');


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


    /**
     * @Route("/relEstoque01/carrinho/adicionar", name="relEstoque01_carrinho_adicionar")
     * @param Request $request
     * @return JsonResponse
     *
     * @IsGranted({"ROLE_RELVENDAS"}, statusCode=403)
     */
    public function adicionarNoCarrinho(Request $request): JsonResponse
    {
        $codProduto = $request->get('codProduto');
        $filial = $request->get('filial');
        try {
            $results = [
                'msg' => null,
                'produto' => null
            ];
            /** @var RelEstoque01Repository $repoEstoque */
            $repoEstoque = $this->getDoctrine()->getRepository(RelEstoque01::class);
            /** @var RelEstoque01 $produto */
            $produto = $repoEstoque->findOneBy(['codProduto' => $codProduto, 'descFilial' => $filial], null, 1);

            if (!$produto) {
                $results['msg'] = 'Nenhum produto encontrado com o código "' . $codProduto . '"';
            } else {
                $carrinho = $this->session->get('carrinho') ?? ['itens' => []];
                $achou = false;
                /** @var RelEstoque01 $item */
                foreach ($carrinho['itens'] as $item) {
                    if ($item->getCodProduto() === $codProduto) {
                        $achou = true;
                        break;
                    }
                }
                if (!$achou) {
                    if (!($carrinho['fornecedor'] ?? null)) {
                        $carrinho['fornecedor'] = $produto->getCodFornecedor();
                    }
                    $carrinho['itens'][] = $produto;
                    $results['msg'] = '"' . $produto->getCodProduto() . ' - ' . $produto->getDescProduto() . '" adicionado com sucesso';
                    $results['produto'] = $produto->getCodProduto() . ' - ' . $produto->getDescProduto();
                } else {
                    $results['msg'] = '"' . $produto->getCodProduto() . ' - ' . $produto->getDescProduto() . '" já adicionado ao carrinho';
                    $results['produto'] = $produto->getCodProduto() . ' - ' . $produto->getDescProduto();

                }
                $this->session->set('carrinho', $carrinho);
            }

        } catch (\Exception $e) {
            $results['msg'] = 'Erro ao adicionar produto no carrinho';
        }
        return new JsonResponse($results);
    }

    /**
     * @Route("/relEstoque01/carrinho/removerEExibir/{codProduto}/", name="relEstoque01_carrinho_removerEExibir")
     * @param string $codProduto
     * @return Response
     * @throws \Exception
     *
     * @IsGranted({"ROLE_RELVENDAS"}, statusCode=403)
     */
    public function removerDoCarrinhoEExibir(string $codProduto): Response
    {
        $codProduto = urldecode($codProduto);
        $carrinho = $this->session->get('carrinho') ?? ['itens' => []];
        /** @var RelEstoque01 $item */
        foreach ($carrinho['itens'] as $key => $item) {
            if ($item->getCodProduto() === $codProduto) {
                array_splice($carrinho['itens'], $key, 1);
                break;
            }
        }
        $this->session->set('carrinho', $carrinho);
        return $this->redirectToRoute('relEstoque01_carrinho_exibir');
    }

    /**
     * @Route("/relEstoque01/carrinho/limparCarrinho", name="relEstoque01_carrinho_limparCarrinho")
     * @return Response
     * @throws \Exception
     *
     * @IsGranted({"ROLE_RELVENDAS"}, statusCode=403)
     */
    public function limparCarrinho(): Response
    {
        $this->session->set('carrinho', null);
        return $this->redirectToRoute('relEstoque01_carrinho_exibir');
    }


    /**
     *
     * @Route("/relEstoque01/carrinho/exibir", name="relEstoque01_carrinho_exibir")
     * @param Request $request
     * @return Response
     * @throws \Exception
     *
     * @IsGranted({"ROLE_RELVENDAS"}, statusCode=403)
     */
    public function exibirCarrinho(Request $request): Response
    {
        $vParams['carrinho'] = $this->session->get('carrinho') ?? ['itens' => []];

        /** @var RelEstoque01Repository $repoEstoque */
        $repoEstoque = $this->getDoctrine()->getRepository(RelEstoque01::class);
        $vParams['fornecedores'] = $repoEstoque->getFornecedores();
        array_unshift($vParams['fornecedores'], ['id' => '', 'text' => '']);
        $vParams['fornecedores'] = json_encode($vParams['fornecedores']);
        $vParams['carrinho']['fornecedor'] = $vParams['carrinho']['fornecedor'] ?? '';

        /** @var RelVendas01Repository $repoVendas */
        $repoVendas = $this->getDoctrine()->getRepository(RelVendas01::class);
        $vParams['compradores'] = $repoVendas->getVendedores();
        array_unshift($vParams['compradores'], ['id' => '', 'text' => '']);
        $vParams['compradores'] = json_encode($vParams['compradores']);
        $vParams['carrinho']['comprador'] = $vParams['carrinho']['comprador'] ?? '';

        $total = 0.0;
        if (isset($vParams['carrinho']['itens']) && count($vParams['carrinho']['itens']) > 0) {
            /** @var RelEstoque01 $item */
            foreach ($vParams['carrinho']['itens'] as $item) {
                $total += $item->getDeficit() * $item->getCustoMedio();
            }
        } else {
            $this->addFlash('info', 'Carrinho de compras vazio.');
        }
        $vParams['carrinho']['total'] = $total;

        if (strpos($request->headers->get('referer'), 'carrinho') === false) {
            $this->session->set('backUrl_carrinho', $request->headers->get('referer'));
        }
        $vParams['backUrl'] = $this->session->get('backUrl_carrinho') ?? $this->generateUrl('relEstoque01_listReposicao');

        return $this->doRender('Relatorios/relEstoque01_carrinhoDeCompras.html.twig', $vParams);
    }


    /**
     *
     * @Route("/relEstoque01/carrinho/salvar", name="relEstoque01_carrinho_salvar")
     * @param Request $request
     * @return Response
     * @throws \Exception
     *
     * @IsGranted({"ROLE_RELVENDAS"}, statusCode=403)
     */
    public function salvarCarrinho(Request $request): Response
    {
        $carrinho = $this->session->get('carrinho');
        $carrinho['fornecedor'] = $request->get('fornecedor');
        $carrinho['comprador'] = $request->get('comprador');
        if (isset($carrinho['itens']) && count($carrinho['itens']) > 0) {
            /** @var RelEstoque01 $item */
            foreach ($carrinho['itens'] as $item) {
                foreach ($request->get('itens') as $rItem) {
                    if ($rItem['codProduto'] === $item->getCodProduto()) {
                        $item->setDeficit($rItem['qtde']);
                        continue;
                    }
                }
            }
        }
        $this->session->set('carrinho', $carrinho);

        if ($request->get('btnGerarPedidoCompra')) {
            $this->gerarPedidoCompra($carrinho);
        }

        return $this->redirectToRoute('relEstoque01_carrinho_exibir');
    }


    /**
     *
     * @Route("/relEstoque01/gerarPedidoCompra/", name="relEstoque01_gerarPedidoCompra")
     * @param array $carrinho
     *
     * @IsGranted({"ROLE_RELVENDAS"}, statusCode=403)
     */
    public function gerarPedidoCompra(array $carrinho): void
    {
        try {
            if (!($carrinho['fornecedor'] ?? null)) {
                $this->addFlash('warn', 'É necessário selecionar um fornecedor.');
                throw new ViewException('É necessário selecionar um fornecedor.');
            }
            if (!($carrinho['comprador'] ?? null)) {
                $this->addFlash('warn', 'É necessário selecionar um comprador.');
                throw new ViewException('É necessário selecionar um comprador.');
            }
            $arquivo = $this->relEstoque01Business->gerarPedidoCompra($carrinho);
            $this->addFlash('info', $arquivo);
            $this->addFlash('success', 'Pedido de compra gerado com sucesso!');
            $this->session->set('carrinho', null);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->logger->error('Erro ao gerar pedido de compra');
            $this->addFlash('error', 'Erro ao gerar pedido de compra');
        }
    }


    /**
     * @Route("/relEstoque01/carrinho/adicionarTudoNoCarrinho", name="relEstoque01_carrinho_adicionarTudoNoCarrinho")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @IsGranted({"ROLE_RELVENDAS"}, statusCode=403)
     * @throws ViewException
     */
    public function adicionarTudoNoCarrinho(Request $request): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        /** @var RelEstoque01Repository $repoRelEstoque01 */
        $repoRelEstoque01 = $this->getDoctrine()->getRepository(RelEstoque01::class);
        $filters['filter'] = $request->get('filter');
        if ($request->get('reposicao')) {
            $filters['filter']['qtdeAtual'] = 0;
            $filters['filter']['deficit'] = 0;
        }

        $filterDatas = $this->getSomenteFilterDatasComValores($filters);
        $produtos = $repoRelEstoque01->findByFilters($filterDatas, null, 0, -1);

        $carrinho = $this->session->get('carrinho') ?? ['itens' => []];
        $q = 0;
        /** @var RelEstoque01 $produto */
        foreach ($produtos as $produto) {
            $achou = false;
            /** @var RelEstoque01 $item */
            foreach ($carrinho['itens'] as $item) {
                if ($item->getCodProduto() === $produto->getCodProduto()) {
                    $achou = true;
                    break;
                }
            }
            if (!$achou) {
                if (!($carrinho['fornecedor'] ?? null)) {
                    $carrinho['fornecedor'] = $produto->getCodFornecedor();
                }
                $carrinho['itens'][] = $produto;
                $q++;
            }
        }
        $this->session->set('carrinho', $carrinho);
        $this->addFlash('info', $q . ' produto(s) adicionado(s) ao carrinho');
        return $this->redirectToRoute('relEstoque01_carrinho_exibir');

    }


}
