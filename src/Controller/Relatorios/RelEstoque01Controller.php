<?php

namespace App\Controller\Relatorios;


use App\Entity\Relatorios\RelEstoque01;
use App\EntityHandler\Relatorios\RelEstoque01EntityHandler;
use App\Repository\Relatorios\RelEstoque01Repository;
use CrosierSource\CrosierLibBaseBundle\APIClient\Base\DiaUtilAPIClient;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
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

    public function getFilterDatas(array $params): array
    {
        return [
            new FilterData(['descProduto'], 'LIKE', 'descProduto', $params),
            new FilterData(['descFilial'], 'EQ', 'descFilial', $params),
            new FilterData(['qtdeAtual'], 'GT', 'qtdeAtual', $params),
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
        $totais = $repo->totalEstoquePorFilial($descFilial);
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
        $totais = $repo->totalEstoquePorFilial($descFilial);
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
        /** @var RelEstoque01Repository $repo */
        $repo = $this->getDoctrine()->getRepository(RelEstoque01::class);

        $descFilial = $request->get('filter')['descFilial'] ?? null;

        $rParams = $request->request->all();
        $start = $rParams['start'] ?? 0;
        $limit = ($rParams['length'] ?? null) && $rParams['length'] !== '-1' ? $rParams['length'] : 10;

        $dados = $repo->getReposicaoEstoque($start, $limit, $descFilial);
        $count = $repo->getReposicaoEstoqueCount($descFilial);
        return $this->doDatatablesJsList($request, null, $dados, $count);
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


}
