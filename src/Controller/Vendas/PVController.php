<?php

namespace App\Controller\Vendas;


use App\Entity\Vendas\PV;
use App\EntityHandler\Vendas\PVEntityHandler;
use App\Form\Vendas\PVType;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @author Carlos Eduardo Pauluk
 */
class PVController extends FormListController
{

    /** @var SessionInterface */
    private $session;

    /**
     * @required
     * @param PVEntityHandler $entityHandler
     */
    public function setEntityHandler(PVEntityHandler $entityHandler): void
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
            new FilterData(['dtEmissao'], 'BETWEEN', 'dtEmissao', $params),
            new FilterData(['clienteNome'], 'EQ', 'clienteNome', $params)
        ];
    }

    /**
     *
     * @Route("/ven/pv/list/", name="ven_pv_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     *
     * @IsGranted({"ROLE_PV"}, statusCode=403)
     */
    public function list(Request $request): Response
    {
        $params = [
            'formRoute' => 'ven_pv_form',
            'listView' => 'Vendas/pv_list.html.twig',
            'listRoute' => 'ven_pv_list',
            'listRouteAjax' => 'ven_pv_datatablesJsList',
            'listPageTitle' => 'PVs',
            'listId' => 'pv_list'
        ];
        return $this->doList($request, $params);
    }

    /**
     *
     * @Route("/ven/pv/datatablesJsList/", name="ven_pv_datatablesJsList")
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
     *
     * @Route("/ven/pv/form/{id}", name="ven_pv_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param PV|null $pv
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     *
     * @IsGranted({"ROLE_PV"}, statusCode=403)
     */
    public function form(Request $request, PV $pv = null)
    {
        if (!$pv) {
            $pv = new PV();
            $pv->setDtEmissao(new \DateTime());
        }
        $params = [
            'typeClass' => PVType::class,
            'formView' => 'Vendas/pv_form.html.twig',
            'formRoute' => 'ven_pv_form',
            'formPageTitle' => 'PV'
        ];
        return $this->doForm($request, $pv, $params);
    }


}
