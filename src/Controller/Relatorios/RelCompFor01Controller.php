<?php

namespace App\Controller\Relatorios;


use App\Entity\Relatorios\RelCompFor01;
use App\EntityHandler\Relatorios\RelCompFor01EntityHandler;
use App\Repository\Relatorios\RelCompFor01Repository;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
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
class RelCompFor01Controller extends FormListController
{
    protected $crudParams =
        [
            'typeClass' => null,

            'formView' => null,
//            'formRoute' => null,
            'formPageTitle' => null,
            'form_PROGRAM_UUID' => null,

            'listView' => 'relCompFor01_list.html.twig',
            'listRoute' => 'relCompFor01_list',
            'listRouteAjax' => 'relCompFor01_datatablesJsList',
            'listPageTitle' => 'CompFor',
            'listId' => 'relCompFor01List',
            'list_PROGRAM_UUID' => null,
            'listJS' => '',

            'role_access' => 'ROLE_RELVENDAS01',
            'role_delete' => 'ROLE_ADMIN',

        ];
    /** @var SessionInterface */
    private $session;

    /**
     * @required
     * @param RelCompFor01EntityHandler $entityHandler
     */
    public function setEntityHandler(RelCompFor01EntityHandler $entityHandler): void
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


    public function getFilterDatas(array $params): array
    {
        return [
            new FilterData(['descProduto'], 'LIKE', 'descProduto', $params),
            new FilterData(['nomeFornecedor'], 'LIKE', 'nomeFornecedor', $params)
        ];
    }

    /**
     *
     * @Route("/relCompFor01/list/", name="relCompFor01_list")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function list(Request $request): Response
    {
        return $this->doList($request);
    }

    /**
     *
     * @Route("/relCompFor01/datatablesJsList/", name="relCompFor01_datatablesJsList")
     * @param Request $request
     * @return Response
     * @throws ViewException
     */
    public function datatablesJsList(Request $request): Response
    {
        return $this->doDatatablesJsList($request);
    }


    /**
     *
     * @Route("/relCompFor01/totalPorFornecedor/", name="relCompFor01_totalPorFornecedor")
     * @param Request $request
     * @return JsonResponse
     */
    public function totalPorFornecedor(Request $request): JsonResponse
    {
        $dts = $request->get('filterDts') ?? '';
        $this->session->set('dashboard.filter.relCompFor01.dts', $dts);
        $dtIni = DateTimeUtils::parseDateStr(substr($dts, 0, 10));
        $dtFim = DateTimeUtils::parseDateStr(substr($dts, 13, 10));

        /** @var RelCompFor01Repository $repoRelCompFor01 */
        $repoRelCompFor01 = $this->getDoctrine()->getRepository(RelCompFor01::class);
        $r = $repoRelCompFor01->totalComprasPorFornecedor($dtIni, $dtFim);
        return new JsonResponse($r);
    }


}
