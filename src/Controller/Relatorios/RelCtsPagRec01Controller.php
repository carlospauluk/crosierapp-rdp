<?php

namespace App\Controller\Relatorios;


use App\Entity\Relatorios\RelCtsPagRec01;
use App\EntityHandler\RelCtsPagRec01EntityHandler;
use App\Repository\Relatorios\RelCtsPagRec01Repository;
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
 * CRUD Controller para Push.
 *
 * @package App\Controller\Utils
 * @author Carlos Eduardo Pauluk
 */
class RelCtsPagRec01Controller extends FormListController
{
    protected $crudParams =
        [
            'typeClass' => null,

            'formView' => null,
//            'formRoute' => null,
            'formPageTitle' => null,
            'form_PROGRAM_UUID' => null,

            'listView' => 'relCtsPagRec01_list.html.twig',
            'listRoute' => 'relCtsPagRec01_list',
            'listRouteAjax' => 'relCtsPagRec01_datatablesJsList',
            'listPageTitle' => 'CtsPagRec',
            'listId' => 'relCtsPagRec01List',
            'list_PROGRAM_UUID' => null,
            'listJS' => '',

            'role_access' => 'ROLE_RELVENDAS01',
            'role_delete' => 'ROLE_ADMIN',

        ];
    /** @var SessionInterface */
    private $session;

    /**
     * @required
     * @param RelCtsPagRec01EntityHandler $entityHandler
     */
    public function setEntityHandler(RelCtsPagRec01EntityHandler $entityHandler): void
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
     * @Route("/relCtsPagRec01/list/", name="relCtsPagRec01_list")
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
     * @Route("/relCtsPagRec01/datatablesJsList/", name="relCtsPagRec01_datatablesJsList")
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
     * @Route("/relCtsPagRec01/rel01/", name="relCtsPagRec01_rel01")
     * @param Request $request
     * @return JsonResponse
     */
    public function rel01(Request $request): JsonResponse
    {
        $dts = $request->get('filterDts') ?? '';

        $this->session->set('dashboard.filter.contasPagRec.dts', $dts);

        $dtIni = DateTimeUtils::parseDateStr(substr($dts, 0, 10));
        $dtFim = DateTimeUtils::parseDateStr(substr($dts, 13, 10));

        /** @var RelCtsPagRec01Repository $repoRelCtsPagRec01 */
        $repoRelCtsPagRec01 = $this->getDoctrine()->getRepository(RelCtsPagRec01::class);
        $r = $repoRelCtsPagRec01->relCtsPagRec01($dtIni, $dtFim);
        return new JsonResponse($r);
    }


}
