<?php

namespace App\Controller\Vendas;


use App\Entity\Relatorios\RelCliente01;
use App\Entity\Vendas\PV;
use App\Entity\Vendas\PVItem;
use App\EntityHandler\Vendas\PVEntityHandler;
use App\EntityHandler\Vendas\PVItemEntityHandler;
use App\Form\Vendas\PVItemType;
use App\Form\Vendas\PVType;
use App\Repository\Relatorios\RelCliente01Repository;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Utils\EntityIdUtils\EntityIdUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use CrosierSource\CrosierLibBaseBundle\Utils\ViewUtils\Select2JsUtils;
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
class PVController extends FormListController
{

    /** @var SessionInterface */
    private $session;

    /** @var EntityIdUtils */
    private $entityIdUtils;

    /** @var PVItemEntityHandler */
    private $pvItemEntityHandler;

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

    /**
     * @param Request $request
     * @param $pv
     */
    public function handleRequestOnValid(Request $request, $pv): void
    {
        if ($request->request->get('pv')) {
            /** @var PV $pv */
            $pv->requestToVenctos($request->request->get('pv'));
        }
    }


    /**
     *
     * @Route("/ven/pv/findClienteByStr/", name="ven_pv_findClienteByStr")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     *
     * @IsGranted({"ROLE_PV"}, statusCode=403)
     */
    public function findClienteByStr(Request $request): JsonResponse
    {
        $str = $request->get('term') ?? '';
        /** @var RelCliente01Repository $repoCliente */
        $repoCliente = $this->getDoctrine()->getRepository(RelCliente01::class);
        $clientes = $repoCliente->findClienteByStr($str);
        $select2js = Select2JsUtils::toSelect2DataFn($clientes, function ($e) {
            /** @var RelCliente01 $e */
            return $e->getNomeMontado();
        });
        return new JsonResponse(
            ['results' => $select2js]
        );
    }


    /**
     *
     * @Route("/ven/pvItem/form/{pv}/{pvItem}", name="ven_pvItem_form", defaults={"pvItem"=null}, requirements={"pv"="\d+","pvItem"="\d+"})
     * @param Request $request
     * @param PV|null $pv
     * @param PVItem|null $pvItem
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     * @IsGranted({"ROLE_PV"}, statusCode=403)
     */
    public function formItem(Request $request, PV $pv, PVItem $pvItem = null)
    {
        if (!$pvItem) {
            $pvItem = new PVItem();
            $pvItem->setPv($pv);
        }
        $params = [
            'typeClass' => PVItemType::class,
            'formView' => 'Vendas/pvItem_form.html.twig',
            'formRoute' => 'ven_pvItem_form',
            'formPageTitle' => 'Item do PV',
            'routeParams' => ['pv' => $pv],
            'entityHandler' => $this->pvItemEntityHandler
        ];
        return $this->doForm($request, $pvItem, $params);
    }

    /**
     * @Route("/ven/pvItem/delete/{pvItem}", name="ven_pvItem_delete", defaults={"pvItem"=null}, requirements={"pvItem"="\d+"})
     * @param PVItem $pvItem
     * @return Response
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     * @IsGranted({"ROLE_PV"}, statusCode=403)
     */
    public function removerItem(PVItem $pvItem): Response
    {
        $this->pvItemEntityHandler->delete($pvItem);
        return $this->redirectToRoute('ven_pv_form', ['id' => $pvItem->getPv()->getId(), '_fragment' => 'itens']);
    }


}
