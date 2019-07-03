<?php

namespace App\Controller\Utils;


use App\Entity\Utils\RelatorioPush;
use App\EntityHandler\Utils\RelatorioPushEntityHandler;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * CRUD Controller para RelatoriosPush.
 *
 * @package App\Controller\Utils
 * @author Carlos Eduardo Pauluk
 */
class RelatoriosPushController extends FormListController
{

    protected $crudParams =
        [
            'typeClass' => null,

            'formView' => null,
//            'formRoute' => null,
            'formPageTitle' => null,
            'form_PROGRAM_UUID' => null,

            'listView' => '@CrosierLibBase/list.html.twig',
            'listRoute' => 'utils_relatorioPush_list',
            'listRouteAjax' => 'utils_relatorioPush_datatablesJsList',
            'listPageTitle' => 'Relatórios',
            'listId' => 'relatorioPushList',
            'list_PROGRAM_UUID' => null,
            'listJS' => 'utils/relatorioPushList.js',

            'role_access' => 'ROLE_ANY',
            'role_delete' => 'ROLE_ADMIN',

        ];

    /**
     * @required
     * @param RelatorioPushEntityHandler $entityHandler
     */
    public function setEntityHandler(RelatorioPushEntityHandler $entityHandler): void
    {
        $this->entityHandler = $entityHandler;
    }

    public function getFilterDatas(array $params): array
    {
        return [
            new FilterData(['descricao'], 'LIKE', 'descricao', $params),
            new FilterData(['userDestinatarioId'], 'EQ', 'userDestinatarioId', $params)
        ];
    }

    /**
     *
     * @Route("/utils/relatorioPush/list/", name="utils_relatorioPush_list")
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
     * @Route("/utils/relatorioPush/datatablesJsList/", name="utils_relatorioPush_datatablesJsList")
     * @param Request $request
     * @return Response
     * @throws ViewException
     */
    public function datatablesJsList(Request $request): Response
    {
        $defaultFilters['filter']['userDestinatarioId'] = $this->getUser()->getId();
        return $this->doDatatablesJsList($request, $defaultFilters);
    }

    /**
     *
     * @Route("/relatorioPush/delete/{id}/", name="relatorioPush_delete", requirements={"id"="\d+"})
     * @param Request $request
     * @param RelatorioPush $relatorioPush
     * @return RedirectResponse
     */
    public function delete(Request $request, RelatorioPush $relatorioPush): RedirectResponse
    {
        return $this->doDelete($request, $relatorioPush);
    }


}