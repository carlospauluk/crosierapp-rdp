<?php

namespace App\Controller\Relatorios;


use App\Entity\Relatorios\RelCompras01;
use App\Entity\Relatorios\RelVendas01;
use App\Repository\Relatorios\RelCompras01Repository;
use App\Repository\Relatorios\RelVendas01Repository;
use CrosierSource\CrosierLibBaseBundle\APIClient\Base\DiaUtilAPIClient;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @author Carlos Eduardo Pauluk
 */
class RelCompras01Controller extends FormListController
{

    /** @var DiaUtilAPIClient */
    private $diaUtilAPIClient;

    /** @var SessionInterface */
    private $session;

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
     *
     * @Route("/relCompras01/listComprasPorProduto/", name="relCompras01_listComprasPorProduto")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function listComprasPorProduto(Request $request): Response
    {
        $vParams = $request->query->all();
        /** @var RelCompras01Repository $repo */
        $repo = $this->getDoctrine()->getRepository(RelCompras01::class);
        if (!array_key_exists('filter', $vParams)) {

            if ($vParams['r'] ?? null) {
                $this->storedViewInfoBusiness->clear($this->crudParams['listRoute']);
            }
            $svi = $this->storedViewInfoBusiness->retrieve('relCompras01_listComprasPorProduto');
            if (isset($svi['filter'])) {
                $vParams['filter'] = $svi['filter'];
            } else {
                $vParams['filter'] = [];
                $vParams['filter']['loja'] = null;
            }
        }

        $vParams['filter']['loja'] = $vParams['filter']['loja'] ?? null;

        $r = $repo->itensDeComprasPorProduto($vParams['filter']['codProduto'], $vParams['filter']['loja']);

        $vParams['dados'] = $r;

        $viewInfo = [];
        $viewInfo['filter'] = $vParams['filter'];
        $this->storedViewInfoBusiness->store('relCompras01_listComprasPorProduto', $viewInfo);


        /** @var RelVendas01Repository $repo */
        $repoVendas = $this->getDoctrine()->getRepository(RelVendas01::class);
        $vParams['produto'] = $vParams['filter']['codProduto'] . ' - ' . $repoVendas->getProdutoByCodigo($vParams['filter']['codProduto']);

        return $this->doRender('Relatorios/relCompras01_listComprasPorProduto.html.twig', $vParams);
    }


    /**
     *
     * @Route("/relCompras01/listPreVendaItens/{pv}/", name="relCompras01_listPreVendaItens")
     * @param int $pv
     * @return Response
     * @throws Exception
     */
    public function listPreVendaItens(int $pv): Response
    {
        /** @var RelCompras01Repository $repo */
        $repo = $this->getDoctrine()->getRepository(RelCompras01::class);

        $r = $repo->itensDoPreVenda($pv);


        $vParams['dados'] = $r;
        $vParams['pv'] = $pv;

        try {
            $vParams['total'] = $repo->totaisPreVenda($pv);
            $vParams['total']['dt_emissao'] = DateTimeUtils::parseDateStr($vParams['total']['dt_emissao']);
        } catch (ViewException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->doRender('Relatorios/relCompras01_listItensDoPreVenda.html.twig', $vParams);
    }


}