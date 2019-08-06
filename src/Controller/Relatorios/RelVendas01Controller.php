<?php

namespace App\Controller\Relatorios;


use App\Entity\Relatorios\RelVendas01;
use App\Repository\Relatorios\RelVendas01Repository;
use CrosierSource\CrosierLibBaseBundle\APIClient\Base\DiaUtilAPIClient;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
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
     * @Route("/relVendas01/listItensVendidosPorFornecedor/", name="relVendas01_listItensVendidosPorFornecedor")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function listItensVendidosPorFornecedor(Request $request): Response
    {
        $vParams = $request->query->all();
        /** @var RelVendas01Repository $repo */
        $repo = $this->getDoctrine()->getRepository(RelVendas01::class);
        if (!array_key_exists('filter', $vParams)) {

            if ($vParams['r'] ?? null) {
                $this->storedViewInfoBusiness->clear($this->crudParams['listRoute']);
            }
            $svi = $this->storedViewInfoBusiness->retrieve('relVendas01_listItensVendidosPorFornecedor');
            if (isset($svi['filter'])) {
                $vParams['filter'] = $svi['filter'];
            } else {
                $vParams['filter'] = [];
                $vParams['filter']['dts'] = '01/' . date('m/Y') . ' - ' . date('t/m/Y');
                $vParams['filter']['lojas'] = null;
                $vParams['filter']['grupos'] = null;
            }
        }

        $dtIni = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 0, 10)) ?: new DateTime();
        $dtFim = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 13, 10)) ?: new DateTime();

        $nomeFornec = $vParams['filter']['nomeFornec'] ?? $repo->getNomeFornecedorMaisVendido($dtIni, $dtFim);
        $vParams['filter']['nomeFornec'] = $nomeFornec;

        $vParams['filter']['lojas'] = $vParams['filter']['lojas'] ?? null;
        $vParams['filter']['grupos'] = $vParams['filter']['grupos'] ?? null;

        $r = $repo->itensVendidos($dtIni, $dtFim, $nomeFornec, $vParams['filter']['lojas'], $vParams['filter']['grupos']);

        $total = $repo->itensVendidos($dtIni, $dtFim, $nomeFornec, $vParams['filter']['lojas'], $vParams['filter']['grupos'], true)[0];

        $dtAnterior = clone $dtIni;
        $dtAnterior->setTime(12, 0, 0, 0)->modify('last day');

        $prox = $this->diaUtilAPIClient->incPeriodo($dtIni, $dtFim, true);
        $ante = $this->diaUtilAPIClient->incPeriodo($dtIni, $dtFim, false);
        $vParams['antePeriodoI'] = $ante['dtIni'];
        $vParams['antePeriodoF'] = $ante['dtFim'];
        $vParams['proxPeriodoI'] = $prox['dtIni'];
        $vParams['proxPeriodoF'] = $prox['dtFim'];


        $lojas = $repo->getLojas();
        array_unshift($lojas, ['id' => '', 'text' => 'TODAS']);
        $vParams['lojas'] = json_encode($lojas);
        $grupos = $repo->getGrupos();
        array_unshift($grupos, ['id' => '', 'text' => 'TODOS']);
        $vParams['grupos'] = json_encode($grupos);

        $vParams['fornecedores'] = json_encode($repo->getFornecedores());

        $vParams['dados'] = $r;
        $vParams['total'] = $total;

        $viewInfo = [];
        $viewInfo['filter'] = $vParams['filter'];
        $this->storedViewInfoBusiness->store('relVendas01_listItensVendidosPorFornecedor', $viewInfo);

        return $this->doRender('Relatorios/relVendas01_listItensVendidosPorFornecedor.html.twig', $vParams);
    }


    /**
     *
     * @Route("/relVendas01/graficoTotalPorFornecedor/", name="relVendas01_graficoTotalPorFornecedor")
     * @param Request $request
     * @return JsonResponse
     */
    public function graficoTotalPorFornecedor(Request $request): JsonResponse
    {
        $dts = $request->get('filterDts') ?? '';

        $lojas = $request->get('lojas') ?: null;
        $grupos = $request->get('grupos') ?: null;

        $this->session->set('dashboard.filter.chartVendasTotalPorFornecedor.dts', $dts);
        $this->session->set('dashboard.filter.chartVendasTotalPorFornecedor.lojas', $lojas);
        $this->session->set('dashboard.filter.chartVendasTotalPorFornecedor.grupos', $grupos);

        $dtIni = DateTimeUtils::parseDateStr(substr($dts, 0, 10));
        $dtFim = DateTimeUtils::parseDateStr(substr($dts, 13, 10));

        /** @var RelVendas01Repository $repoRelVendas01 */
        $repoRelVendas01 = $this->getDoctrine()->getRepository(RelVendas01::class);
        $r = $repoRelVendas01->totalVendasPorFornecedor($dtIni, $dtFim, $lojas, $grupos);
        return new JsonResponse($r);
    }


    /**
     *
     * @Route("/relVendas01/graficoTotalPorVendedor/", name="relVendas01_graficoTotalPorVendedor")
     * @param Request $request
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function graficoTotalPorVendedor(Request $request): JsonResponse
    {
        $dts = $request->get('filterDts') ?? '';
        $lojas = $request->get('lojas') ?: null;
        $grupos = $request->get('grupos') ?: null;

        $dtIni = DateTimeUtils::parseDateStr(substr($dts, 0, 10));
        $dtFim = DateTimeUtils::parseDateStr(substr($dts, 13, 10));

        /** @var RelVendas01Repository $repoRelVendas01 */
        $repoRelVendas01 = $this->getDoctrine()->getRepository(RelVendas01::class);
        $r = $repoRelVendas01->totalVendasPorVendedor($dtIni, $dtFim, $lojas, $grupos);

        return new JsonResponse($r);
    }


    /**
     *
     * @Route("/relVendas01/listPreVendasPorVendedor/", name="relVendas01_listPreVendasPorVendedor")
     * @param Request $request
     * @return Response
     * @throws Exception
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
                $vParams['filter']['lojas'] = null;
                $vParams['filter']['grupos'] = null;
            }
        }

        $dtIni = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 0, 10)) ?: new DateTime();
        $dtFim = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 13, 10)) ?: new DateTime();

        $codVendedor = explode(' - ', $vParams['filter']['vendedor'])[0] ?? 0;

        $vParams['filter']['lojas'] = $vParams['filter']['lojas'] ?: null;
        $vParams['filter']['grupos'] = $vParams['filter']['grupos'] ?: null;

        $r = $repo->preVendasPorPeriodoEVendedor($dtIni, $dtFim, $codVendedor, $vParams['filter']['lojas'], $vParams['filter']['grupos']);

        $vParams['total'] = 0.0;
        foreach ($r as $pv) {
            $vParams['total'] += $pv['total_venda_pv'];
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

        $lojas = $repo->getLojas();
        array_unshift($lojas, ['id' => '', 'text' => 'TODAS']);
        $vParams['lojas'] = json_encode($lojas);
        $grupos = $repo->getGrupos();
        array_unshift($grupos, ['id' => '', 'text' => 'TODOS']);
        $vParams['grupos'] = json_encode($grupos);

        $viewInfo = [];
        $viewInfo['filter'] = $vParams['filter'];
        $this->storedViewInfoBusiness->store('relVendas01_listPreVendasPorVendedor', $viewInfo);

        return $this->doRender('Relatorios/relVendas01_listPreVendasPorVendedor.html.twig', $vParams);
    }


    /**
     *
     * @Route("/relVendas01/listPreVendasPorProduto/", name="relVendas01_listPreVendasPorProduto")
     * @param Request $request
     * @return Response
     * @throws Exception
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
            $svi = $this->storedViewInfoBusiness->retrieve('relVendas01_listPreVendasPorProduto');
            if (isset($svi['filter'])) {
                $vParams['filter'] = $svi['filter'];
            } else {
                $vParams['filter'] = [];
                $vParams['filter']['dts'] = '01/' . date('m/Y') . ' - ' . date('t/m/Y');
                $vParams['filter']['lojas'] = null;
                $vParams['filter']['grupos'] = null;
            }
        }

        $dtIni = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 0, 10)) ?: new DateTime();
        $dtFim = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 13, 10)) ?: new DateTime();

        $vParams['produto'] = $vParams['filter']['produto'];

        $vParams['filter']['lojas'] = $vParams['filter']['lojas'] ?? null;
        $vParams['filter']['grupos'] = $vParams['filter']['grupos'] ?? null;

        $r = $repo->preVendasPorPeriodoEProduto($dtIni, $dtFim, $vParams['produto'], $vParams['filter']['lojas'], $vParams['filter']['grupos']);

        $vParams['total'] = 0.0;
        foreach ($r as $pv) {
            $vParams['total'] += $pv['total_venda_pv'];
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

        $lojas = $repo->getLojas();
        array_unshift($lojas, ['id' => '', 'text' => 'TODAS']);
        $vParams['lojas'] = json_encode($lojas);
        $grupos = $repo->getGrupos();
        array_unshift($grupos, ['id' => '', 'text' => 'TODOS']);
        $vParams['grupos'] = json_encode($grupos);

        $viewInfo = [];
        $viewInfo['filter'] = $vParams['filter'];
        $this->storedViewInfoBusiness->store('relVendas01_listPreVendasPorProduto', $viewInfo);

        return $this->doRender('Relatorios/relVendas01_listPreVendasPorProduto.html.twig', $vParams);
    }


    /**
     *
     * @Route("/relVendas01/listPreVendaItens/{pv}/", name="relVendas01_listPreVendaItens")
     * @param int $pv
     * @return Response
     * @throws Exception
     */
    public function listPreVendaItens(int $pv): Response
    {
        /** @var RelVendas01Repository $repo */
        $repo = $this->getDoctrine()->getRepository(RelVendas01::class);

        $r = $repo->itensDoPreVenda($pv);


        $vParams['dados'] = $r;
        $vParams['pv'] = $pv;

        try {
            $vParams['total'] = $repo->totaisPreVenda($pv);
            $vParams['total']['dt_emissao'] = DateTimeUtils::parseDateStr($vParams['total']['dt_emissao']);
        } catch (ViewException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->doRender('Relatorios/relVendas01_listItensDoPreVenda.html.twig', $vParams);
    }


}
