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
class RelEstoque01Controller_ extends FormListController
{

    private SessionInterface $session;

    private RelEstoque01Business $relEstoque01Business;


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
            new FilterData(['filial'], 'EQ', 'filial', $params),
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
     * @return Response
     * @throws \Exception
     *
     * @IsGranted("ROLE_RELVENDAS", statusCode=403)
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
        $filial = $request->get('filter')['filial'] ?? 'MATRIZ'; // se não selecionou nenhuma, seta a MATRIZ
        $params['filter']['filial'] = $filial;

        $params['page_subTitle'] = $filial;


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

        $totais = $repo->totalEstoque($dtUltSaidaApartirDe, $filial, $codFornecedor);
        $params['totais'] = $totais[0] ?? null;

        $params['qtdeProdutosNoCarrinho'] = isset($this->session->get('carrinho')['itens']) ? count($this->session->get('carrinho')['itens']) : '';

        return $this->doList($request, $params);
    }

    /**
     *
     * @Route("/relEstoque01/datatablesJsList/", name="relEstoque01_datatablesJsList")
     * @param Request $request
     * @return Response
     * @throws ViewException
     *
     * @IsGranted("ROLE_RELVENDAS", statusCode=403)
     */
    public function datatablesJsList(Request $request): Response
    {
        // Comentei pq não tem funcionalidade, visto os filters definidos no getFilterDatas()
        // Mas pq estava aqui???
//        $defaultFilters = [
//            'filter' => [
//                'qtdeAtual' => 0
//            ]
//        ];
        return $this->doDatatablesJsList($request); // , $defaultFilters);
    }

    /**
     *
     * @Route("/relEstoque01/graficoTotalEstoquePorFilial/", name="relEstoque01_graficoTotalEstoquePorFilial")
     * @param Request $request
     * @return JsonResponse
     *
     * @IsGranted("ROLE_RELVENDAS", statusCode=403)
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
     * @IsGranted("ROLE_RELVENDAS", statusCode=403)
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





}
