<?php

namespace App\Controller\Relatorios;


use App\Entity\Relatorios\RelVendas01;
use App\Repository\Relatorios\RelVendas01Repository;
use CrosierSource\CrosierLibBaseBundle\APIClient\Base\DiaUtilAPIClient;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @author Carlos Eduardo Pauluk
 */
class RelVendas01Controller extends FormListController
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
     * @Route("/relVendas01/itensVendidosPorFornecedor/", name="relVendas01_itensVendidosPorFornecedor")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function itensVendidosPorFornecedor(Request $request): Response
    {
        $vParams = $request->query->all();
        /** @var RelVendas01Repository $repo */
        $repo = $this->getDoctrine()->getRepository(RelVendas01::class);
        if (!array_key_exists('filter', $vParams)) {

            if ($vParams['r'] ?? null) {
                $this->storedViewInfoBusiness->clear($this->crudParams['listRoute']);
            }
            $svi = $this->storedViewInfoBusiness->retrieve('relVendas01_itensVendidosPorFornecedor');
            if (isset($svi['filter'])) {
                $vParams['filter'] = $svi['filter'];
            } else {
                $vParams['filter'] = [];
                $vParams['filter']['dts'] = '01/' . date('m/Y') . ' - ' . date('t/m/Y');
            }
        }

        $dtIni = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 0, 10)) ?: new \DateTime();
        $dtFim = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 13, 10)) ?: new \DateTime();

        $nomeFornec = $vParams['filter']['nomeFornec'] ?? $repo->getNomeFornecedorMaisVendido($dtIni, $dtFim);
        $vParams['filter']['nomeFornec'] = $nomeFornec;

        $r = $repo->itensVendidosPorFornecedor($dtIni, $dtFim, $nomeFornec);
        $total = $repo->itensVendidosPorFornecedor($dtIni, $dtFim, $nomeFornec, true);
        $total = $total[0] ?? null;


        $dtAnterior = clone $dtIni;
        $dtAnterior->setTime(12, 0, 0, 0)->modify('last day');

        $prox = $this->diaUtilAPIClient->incPeriodo($dtIni, $dtFim, true);
        $ante = $this->diaUtilAPIClient->incPeriodo($dtIni, $dtFim, false);
        $vParams['antePeriodoI'] = $ante['dtIni'];
        $vParams['antePeriodoF'] = $ante['dtFim'];
        $vParams['proxPeriodoI'] = $prox['dtIni'];
        $vParams['proxPeriodoF'] = $prox['dtFim'];

        $vParams['fornecedores'] = json_encode($repo->getFornecedores());

        $vParams['dados'] = $r;
        $vParams['total'] = $total;

        $viewInfo = [];
        $viewInfo['filter'] = $vParams['filter'];
        $this->storedViewInfoBusiness->store('relVendas01_itensVendidosPorFornecedor', $viewInfo);

        return $this->doRender('Relatorios/relVendas01_itensVendidosPorFornecedor.html.twig', $vParams);
    }


    /**
     *
     * @Route("/relVendas01/totalPorFornecedor/", name="relVendas01_totalPorFornecedor")
     * @param Request $request
     * @return JsonResponse
     */
    public function totalPorFornecedor(Request $request): JsonResponse
    {
        $dts = $request->get('filterDts') ?? '';
        $this->session->set('dashboard.filter.vendas.dts', $dts);
        $dtIni = DateTimeUtils::parseDateStr(substr($dts, 0, 10));
        $dtFim = DateTimeUtils::parseDateStr(substr($dts, 13, 10));

        /** @var RelVendas01Repository $repoRelVendas01 */
        $repoRelVendas01 = $this->getDoctrine()->getRepository(RelVendas01::class);
        $r = $repoRelVendas01->totalVendasPorFornecedor($dtIni, $dtFim);
        return new JsonResponse($r);
    }


    /**
     *
     * @Route("/relVendas01/totalPorVendedor/", name="relVendas01_totalPorVendedor")
     * @param Request $request
     * @return JsonResponse
     */
    public function totalPorVendedor(Request $request): JsonResponse
    {
        $dts = $request->get('filterDts') ?? '';
        $dtIni = DateTimeUtils::parseDateStr(substr($dts, 0, 10));
        $dtFim = DateTimeUtils::parseDateStr(substr($dts, 13, 10));

        /** @var RelVendas01Repository $repoRelVendas01 */
        $repoRelVendas01 = $this->getDoctrine()->getRepository(RelVendas01::class);
        $r = $repoRelVendas01->totalVendasPorVendedor($dtIni, $dtFim);
        return new JsonResponse($r);
    }


    /**
     *
     * @Route("/relVendas01/listPreVendasPorVendedor/", name="relVendas01_listPreVendasPorVendedor")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function listPreVendasPorVendedor(Request $request): Response
    {
        $vParams = $request->query->all();
        /** @var RelVendas01Repository $repo */
        $repo = $this->getDoctrine()->getRepository(RelVendas01::class);
        if (!array_key_exists('filter', $vParams)) {

            if ($vParams['r'] ?? null) {
                $this->storedViewInfoBusiness->clear($this->crudParams['listRoute']);
            }
            $svi = $this->storedViewInfoBusiness->retrieve('relVendas01_listPreVendasPorVendedor');
            if (isset($svi['filter'])) {
                $vParams['filter'] = $svi['filter'];
            } else {
                $vParams['filter'] = [];
                $vParams['filter']['dts'] = '01/' . date('m/Y') . ' - ' . date('t/m/Y');
            }
        }

        $dtIni = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 0, 10)) ?: new \DateTime();
        $dtFim = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 13, 10)) ?: new \DateTime();

        $codVendedor = explode(' - ', $vParams['filter']['vendedor'])[0] ?? 0;


        $r = $repo->preVendasPorPeriodoEVendedor($dtIni, $dtFim, $codVendedor);

        $vParams['total'] = 0.0;
        foreach ($r as $pv) {
            $vParams['total'] += $pv['total_pv'];
        }

        $dtAnterior = clone $dtIni;
        $dtAnterior->setTime(12, 0, 0, 0)->modify('last day');

        $prox = $this->diaUtilAPIClient->incPeriodo($dtIni, $dtFim, true);
        $ante = $this->diaUtilAPIClient->incPeriodo($dtIni, $dtFim, false);
        $vParams['antePeriodoI'] = $ante['dtIni'];
        $vParams['antePeriodoF'] = $ante['dtFim'];
        $vParams['proxPeriodoI'] = $prox['dtIni'];
        $vParams['proxPeriodoF'] = $prox['dtFim'];

        $vParams['vendedores'] = json_encode($repo->getVendedores());

        $vParams['dados'] = $r;

        $viewInfo = [];
        $viewInfo['filter'] = $vParams['filter'];
        $this->storedViewInfoBusiness->store('relVendas01_listPreVendasPorVendedor', $viewInfo);

        return $this->doRender('Relatorios/relVendas01_preVendasPorVendedor.html.twig', $vParams);
    }


    /**
     *
     * @Route("/relVendas01/listPreVendasPorProduto/", name="relVendas01_listPreVendasPorProduto")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function listPreVendasPorProduto(Request $request): Response
    {
        $vParams = $request->query->all();
        /** @var RelVendas01Repository $repo */
        $repo = $this->getDoctrine()->getRepository(RelVendas01::class);
        if (!array_key_exists('filter', $vParams)) {

            if ($vParams['r'] ?? null) {
                $this->storedViewInfoBusiness->clear($this->crudParams['listRoute']);
            }
            $svi = $this->storedViewInfoBusiness->retrieve('relVendas01_listPreVendasPorVendedor');
            if (isset($svi['filter'])) {
                $vParams['filter'] = $svi['filter'];
            } else {
                $vParams['filter'] = [];
                $vParams['filter']['dts'] = '01/' . date('m/Y') . ' - ' . date('t/m/Y');
            }
        }

        $dtIni = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 0, 10)) ?: new \DateTime();
        $dtFim = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 13, 10)) ?: new \DateTime();

        $codProduto = $vParams['filter']['codProduto'];

        $r = $repo->preVendasPorPeriodoEProduto($dtIni, $dtFim, $codProduto);

        $vParams['total'] = 0.0;
        foreach ($r as $pv) {
            $vParams['total'] += $pv['total_pv'];
        }

        $dtAnterior = clone $dtIni;
        $dtAnterior->setTime(12, 0, 0, 0)->modify('last day');

        $prox = $this->diaUtilAPIClient->incPeriodo($dtIni, $dtFim, true);
        $ante = $this->diaUtilAPIClient->incPeriodo($dtIni, $dtFim, false);
        $vParams['antePeriodoI'] = $ante['dtIni'];
        $vParams['antePeriodoF'] = $ante['dtFim'];
        $vParams['proxPeriodoI'] = $prox['dtIni'];
        $vParams['proxPeriodoF'] = $prox['dtFim'];

        $vParams['dados'] = $r;

        $viewInfo = [];
        $viewInfo['filter'] = $vParams['filter'];
        $this->storedViewInfoBusiness->store('relVendas01_listPreVendasPorVendedor', $viewInfo);

        return $this->doRender('Relatorios/relVendas01_preVendasPorVendedor.html.twig', $vParams);
    }


    /**
     *
     * @Route("/relVendas01/preVendaItens/{pv}/", name="relVendas01_preVendaItens")
     * @param int $pv
     * @return Response
     * @throws \Exception
     */
    public function preVendaItens(int $pv): Response
    {
        /** @var RelVendas01Repository $repo */
        $repo = $this->getDoctrine()->getRepository(RelVendas01::class);

        $r = $repo->itensDoPreVenda($pv);


        $vParams['dados'] = $r;
        $vParams['pv'] = $pv;

        $vParams['total']['qtde'] = 0.0;
        $vParams['total']['tpc'] = 0.0;
        $vParams['total']['tpv'] = 0.0;
        $vParams['total']['rent'] = 0.0;
        foreach ($r as $prevenda) {
            $vParams['total']['qtde'] += $prevenda['qtde'];
            $vParams['total']['tpc'] += $prevenda['total_preco_custo'];
            $vParams['total']['tpv'] += $prevenda['total_preco_venda'];
            $vParams['total']['rent'] += $prevenda['rent'];
        }

        return $this->doRender('Relatorios/relVendas01_itensDoPreVenda.html.twig', $vParams);
    }


}
