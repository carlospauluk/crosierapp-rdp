<?php

namespace App\Controller\Utils;


use App\Entity\Utils\RelatorioPush;
use App\EntityHandler\Utils\RelatorioPushEntityHandler;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * CRUD Controller para Push.
 *
 * @package App\Controller\Utils
 * @author Carlos Eduardo Pauluk
 */
class RelatoriosPushController extends FormListController
{

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
            new FilterData(['userDestinatarioId'], 'EQ', 'userDestinatarioId', $params),
            new FilterData(['tipoArquivo'], 'LIKE', 'tipoArquivo', $params)
        ];
    }

    /**
     *
     * @Route("/utils/push/list/", name="utils_relatorioPush_list")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function list(Request $request): Response
    {
        $params = [
            'listView' => 'Utils/pushList.html.twig',
            'listRoute' => 'utils_relatorioPush_list',
            'listRouteAjax' => 'utils_relatorioPush_datatablesJsList',
            'listPageTitle' => 'Push',
            'listId' => 'relatorioPushList',
            'list_PROGRAM_UUID' => null,
            'listJS' => 'utils/relatorioPushList'
        ];
        return $this->doList($request, $params);
    }

    /**
     *
     * @Route("/utils/push/datatablesJsList/", name="utils_relatorioPush_datatablesJsList")
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

    /**
     *
     * @Route("/relatorioPush/abrir/{id}/", name="relatorioPush_abrir", requirements={"id"="\d+"})
     * @param RelatorioPush $relatorioPush
     * @return RedirectResponse
     * @throws ViewException
     */
    public function abrirArquivo(RelatorioPush $relatorioPush): RedirectResponse
    {
        if (!$relatorioPush->getAbertoEm()) {
            $relatorioPush->setAbertoEm(new \DateTime());
            $this->getEntityHandler()->save($relatorioPush);
        }
        if ($relatorioPush->getUserDestinatarioId() !== $this->getUser()->getId()) {
            throw new AccessDeniedException('Acesso negado [' . $relatorioPush->getUserDestinatarioId() . '>>' . $this->getUser()->getId() . ']');
        }
        return $this->redirect('/uploads/relatoriospush/' . $relatorioPush->getArquivo());
    }


}
