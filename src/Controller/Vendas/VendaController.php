<?php

namespace App\Controller\Vendas;


use App\Entity\Vendas\Venda;
use App\Repository\Vendas\VendaRepository;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Entity\Base\DiaUtil;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\Base\DiaUtilRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Doctrine\ORM\NonUniqueResultException;
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
class VendaController extends FormListController
{

    private SessionInterface $session;

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
     * @Route("/ven/venda/listItensVendidosPorFornecedor/", name="ven_venda_listItensVendidosPorFornecedor")
     * @param Request $request
     * @return Response
     * @throws \Exception
     *
     * @IsGranted("ROLE_RELVENDAS", statusCode=403)
     */
    public function listItensVendidosPorFornecedor(Request $request): Response
    {
        $vParams = $request->query->all();

        if (!array_key_exists('filter', $vParams)) {

            if ($vParams['r'] ?? null) {
                $this->storedViewInfoBusiness->clear('ven_venda_listItensVendidosPorFornecedor');
            }
            $svi = $this->storedViewInfoBusiness->retrieve('ven_venda_listItensVendidosPorFornecedor');
            if (isset($svi['filter'])) {
                $vParams['filter'] = $svi['filter'];
            } else {
                $vParams['filter'] = [];
                $vParams['filter']['dts'] = '01/' . date('m/Y') . ' - ' . date('t/m/Y');
                $vParams['filter']['lojas'] = null;
                $vParams['filter']['grupos'] = null;
            }
        }

        /** @var VendaRepository $repoVenda */
        $repoVenda = $this->getDoctrine()->getRepository(Venda::class);

        $dtIni = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 0, 10)) ?: new \DateTime();
        $dtFim = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 13, 10)) ?: new \DateTime();

        $nomeFornec = $vParams['filter']['nomeFornec'] ?? $repoVenda->getNomeFornecedorMaisVendido($dtIni, $dtFim);
        $vParams['filter']['nomeFornec'] = $nomeFornec;

        $vParams['filter']['lojas'] = $vParams['filter']['lojas'] ?? null;
        $vParams['filter']['grupos'] = $vParams['filter']['grupos'] ?? null;


        $r = $repoVenda->itensVendidos($dtIni, $dtFim, $nomeFornec, $vParams['filter']['lojas'], $vParams['filter']['grupos']);

        $total = $repoVenda->itensVendidos($dtIni, $dtFim, $nomeFornec, $vParams['filter']['lojas'], $vParams['filter']['grupos'], true)[0];

        $dtAnterior = clone $dtIni;
        $dtAnterior->setTime(12, 0, 0, 0)->modify('last day');

        /** @var DiaUtilRepository $repoDiaUtil */
        $repoDiaUtil = $this->getDoctrine()->getRepository(DiaUtil::class);

        $prox = $repoDiaUtil->incPeriodo($dtIni, $dtFim, true);
        $ante = $repoDiaUtil->incPeriodo($dtIni, $dtFim, false);

        $vParams['antePeriodoI'] = $ante['dtIni'];
        $vParams['antePeriodoF'] = $ante['dtFim'];
        $vParams['proxPeriodoI'] = $prox['dtIni'];
        $vParams['proxPeriodoF'] = $prox['dtFim'];


        $lojas = $repoVenda->getLojas();
        array_unshift($lojas, ['id' => '', 'text' => 'TODAS']);
        $vParams['lojas'] = json_encode($lojas);
        $grupos = $repoVenda->getGrupos();
        array_unshift($grupos, ['id' => '', 'text' => 'TODOS']);
        $vParams['grupos'] = json_encode($grupos);

        $vParams['fornecedores'] = json_encode($repoVenda->getFornecedores());

        $vParams['dados'] = $r;
        $vParams['total'] = $total;

        $viewInfo = [];
        $viewInfo['filter'] = $vParams['filter'];
        $this->storedViewInfoBusiness->store('ven_venda_listItensVendidosPorFornecedor', $viewInfo);

        return $this->doRender('Relatorios/ven_venda_listItensVendidosPorFornecedor.html.twig', $vParams);
    }


    /**
     *
     * @Route("/ven/venda/graficoTotalPorFornecedor/", name="ven_venda_graficoTotalPorFornecedor")
     * @param Request $request
     * @return JsonResponse
     *
     * @IsGranted("ROLE_ADMIN", statusCode=403)
     *
     *
     * @throws ViewException
     */
    public function graficoTotalPorFornecedor(Request $request): JsonResponse
    {
        $dts = $request->get('filterDts') ?? '';

        $lojas = $request->get('lojas') ?? null;
        $grupos = $request->get('grupos') ?? null;

        $this->session->set('dashboard.filter.chartVendasTotalPorFornecedor.dts', $dts);
        $this->session->set('dashboard.filter.chartVendasTotalPorFornecedor.lojas', $lojas);
        $this->session->set('dashboard.filter.chartVendasTotalPorFornecedor.grupos', $grupos);

        $dtIni = DateTimeUtils::parseDateStr(substr($dts, 0, 10));
        $dtFim = DateTimeUtils::parseDateStr(substr($dts, 13, 10));

        /** @var VendaRepository $repoVenda */
        $repoVenda = $this->getDoctrine()->getRepository(Venda::class);
        $r = $repoVenda->totalVendasPorFornecedor($dtIni, $dtFim, $lojas, $grupos);
        return new JsonResponse($r);
    }


    /**
     *
     * @Route("/ven/venda/relatorioTotalPorFornecedor/", name="ven_venda_relatorioTotalPorFornecedor")
     * @param Request $request
     * @return Response
     *
     * @IsGranted("ROLE_RELVENDAS", statusCode=403)
     * @throws ViewException
     */
    public function relatorioTotalPorFornecedor(Request $request): Response
    {

        $vParams = $request->query->all();

        if (!array_key_exists('filter', $vParams)) {

            if ($vParams['r'] ?? null) {
                $this->storedViewInfoBusiness->clear('ven_venda_listItensVendidosPorFornecedor');
            }
            $svi = $this->storedViewInfoBusiness->retrieve('ven_venda_listItensVendidosPorFornecedor');
            if (isset($svi['filter'])) {
                $vParams['filter'] = $svi['filter'];
            } else {
                $vParams['filter'] = [];
                $vParams['filter']['dts'] = '01/' . date('m/Y') . ' - ' . date('t/m/Y');
                $vParams['filter']['lojas'] = null;
                $vParams['filter']['grupos'] = null;
            }
        }

        $dtIni = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 0, 10)) ?: new \DateTime();
        $dtFim = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 13, 10)) ?: new \DateTime();

        $vParams['filter']['lojas'] = $vParams['filter']['lojas'] ?? null;
        $vParams['filter']['grupos'] = $vParams['filter']['grupos'] ?? null;

        /** @var VendaRepository $repoVenda */
        $repoVenda = $this->getDoctrine()->getRepository(Venda::class);

        $r = $repoVenda->totalVendasPorFornecedor($dtIni, $dtFim, $vParams['filter']['lojas'], $vParams['filter']['grupos']);


        $dtAnterior = clone $dtIni;
        $dtAnterior->setTime(12, 0, 0, 0)->modify('last day');

        /** @var DiaUtilRepository $repoDiaUtil */
        $repoDiaUtil = $this->getDoctrine()->getRepository(DiaUtil::class);

        $prox = $repoDiaUtil->incPeriodo($dtIni, $dtFim, true);
        $ante = $repoDiaUtil->incPeriodo($dtIni, $dtFim, false);

        $vParams['antePeriodoI'] = $ante['dtIni'];
        $vParams['antePeriodoF'] = $ante['dtFim'];
        $vParams['proxPeriodoI'] = $prox['dtIni'];
        $vParams['proxPeriodoF'] = $prox['dtFim'];


        $lojas = $repoVenda->getLojas();
        array_unshift($lojas, ['id' => '', 'text' => 'TODAS']);
        $vParams['lojas'] = json_encode($lojas);
        $grupos = $repoVenda->getGrupos();
        array_unshift($grupos, ['id' => '', 'text' => 'TODOS']);
        $vParams['grupos'] = json_encode($grupos);

        $vParams['fornecedores'] = json_encode($repoVenda->getFornecedores());

        $vParams['dados'] = $r;
        $vParams['total'] = $repoVenda->totalVendasPor($dtIni, $dtFim, $vParams['filter']['lojas'], $vParams['filter']['grupos']);

        $viewInfo = [];
        $viewInfo['filter'] = $vParams['filter'];
        $this->storedViewInfoBusiness->store('ven_venda_listItensVendidosPorFornecedor', $viewInfo);

        return $this->doRender('Relatorios/ven_venda_totaisVendasPorFornecedor.html.twig', $vParams);
    }


    /**
     *
     * @Route("/ven/venda/graficoTotalPorVendedor/", name="ven_venda_graficoTotalPorVendedor")
     * @param Request $request
     * @return JsonResponse
     * @throws NonUniqueResultException
     *
     * @IsGranted("ROLE_RELVENDAS", statusCode=403)
     */
    public function graficoTotalPorVendedor(Request $request): JsonResponse
    {
        $dts = $request->get('filterDts') ?? '';
        $lojas = $request->get('lojas') ?? null;
        $grupos = $request->get('grupos') ?? null;

        $this->session->set('dashboard.filter.chartVendasTotalPorVendedor.dts', $dts);
        $this->session->set('dashboard.filter.chartVendasTotalPorVendedor.lojas', $lojas);
        $this->session->set('dashboard.filter.chartVendasTotalPorVendedor.grupos', $grupos);

        $dtIni = DateTimeUtils::parseDateStr(substr($dts, 0, 10));
        $dtFim = DateTimeUtils::parseDateStr(substr($dts, 13, 10));

        /** @var VendaRepository $repoVenda */
        $repoVenda = $this->getDoctrine()->getRepository(Venda::class);
        $r = $repoVenda->totalVendasPorVendedor($dtIni, $dtFim, $lojas, $grupos);

        return new JsonResponse($r);
    }


    /**
     *
     * @Route("/ven/venda/listPreVendasPorVendedor/", name="ven_venda_listPreVendasPorVendedor")
     * @param Request $request
     * @return Response
     * @throws \Exception
     *
     * @IsGranted("ROLE_RELVENDAS", statusCode=403)
     */
    public function listPreVendasPorVendedor(Request $request): Response
    {
        $vParams = $request->query->all();
        /** @var VendaRepository $repo */
        $repo = $this->getDoctrine()->getRepository(Venda::class);
        if (!array_key_exists('filter', $vParams)) {

            if ($vParams['r'] ?? null) {
                $this->storedViewInfoBusiness->clear('ven_venda_listPreVendasPorVendedor');
            }
            $svi = $this->storedViewInfoBusiness->retrieve('ven_venda_listPreVendasPorVendedor');
            if (isset($svi['filter'])) {
                $vParams['filter'] = $svi['filter'];
            } else {
                $vParams['filter'] = [];
                $vParams['filter']['dts'] = '01/' . date('m/Y') . ' - ' . date('t/m/Y');
                $vParams['filter']['lojas'] = null;
                $vParams['filter']['grupos'] = null;
            }
        }

        $dtIni = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 0, 10)) ?: new \DateTime();
        $dtFim = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 13, 10)) ?: new \DateTime();

        $codVendedor = explode(' - ', $vParams['filter']['vendedor'])[0] ?? 0;

        $vParams['filter']['lojas'] = $vParams['filter']['lojas'] ?? null;
        $vParams['filter']['grupos'] = $vParams['filter']['grupos'] ?? null;

        $r = $repo->preVendasPorPeriodoEVendedor($dtIni, $dtFim, $codVendedor, $vParams['filter']['lojas'], $vParams['filter']['grupos']);

        $vParams['total'] = 0.0;
        foreach ($r as $pv) {
            $vParams['total'] += $pv['total_venda_pv'];
        }

        $dtAnterior = clone $dtIni;
        $dtAnterior->setTime(12, 0, 0, 0)->modify('last day');

        /** @var DiaUtilRepository $repoDiaUtil */
        $repoDiaUtil = $this->getDoctrine()->getRepository(DiaUtil::class);

        $prox = $repoDiaUtil->incPeriodo($dtIni, $dtFim, true);
        $ante = $repoDiaUtil->incPeriodo($dtIni, $dtFim, false);

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
        $this->storedViewInfoBusiness->store('ven_venda_listPreVendasPorVendedor', $viewInfo);

        return $this->doRender('Relatorios/ven_venda_listPreVendasPorVendedor.html.twig', $vParams);
    }


    /**
     *
     * @Route("/ven/venda/listPreVendasPorProduto/", name="ven_venda_listPreVendasPorProduto")
     * @param Request $request
     * @return Response
     * @throws \Exception
     *
     * @IsGranted("ROLE_RELVENDAS", statusCode=403)
     */
    public function listPreVendasPorProduto(Request $request): Response
    {
        $vParams = $request->query->all();
        /** @var VendaRepository $repo */
        $repo = $this->getDoctrine()->getRepository(Venda::class);
        if (!array_key_exists('filter', $vParams)) {

            if ($vParams['r'] ?? null) {
                $this->storedViewInfoBusiness->clear('ven_venda_listPreVendasPorProduto');
            }
            $svi = $this->storedViewInfoBusiness->retrieve('ven_venda_listPreVendasPorProduto');
            if (isset($svi['filter'])) {
                $vParams['filter'] = $svi['filter'];
            } else {
                $vParams['filter'] = [];
                $vParams['filter']['dts'] = '01/' . date('m/Y') . ' - ' . date('t/m/Y');
                $vParams['filter']['lojas'] = null;
                $vParams['filter']['grupos'] = null;
            }
        }

        $dtIni = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 0, 10)) ?: new \DateTime();
        $dtFim = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 13, 10)) ?: new \DateTime();

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

        /** @var DiaUtilRepository $repoDiaUtil */
        $repoDiaUtil = $this->getDoctrine()->getRepository(DiaUtil::class);

        $prox = $repoDiaUtil->incPeriodo($dtIni, $dtFim, true);
        $ante = $repoDiaUtil->incPeriodo($dtIni, $dtFim, false);

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
        $this->storedViewInfoBusiness->store('ven_venda_listPreVendasPorProduto', $viewInfo);

        return $this->doRender('Relatorios/ven_venda_listPreVendasPorProduto.html.twig', $vParams);
    }


    /**
     *
     * @Route("/ven/venda/listPreVendaItens/{pv}/", name="ven_venda_listPreVendaItens")
     * @param int $pv
     * @return Response
     * @throws \Exception
     *
     * @IsGranted("ROLE_RELVENDAS", statusCode=403)
     */
    public function listPreVendaItens(int $pv): Response
    {
        /** @var VendaRepository $repo */
        $repo = $this->getDoctrine()->getRepository(Venda::class);

        $r = $repo->itensDoPreVenda($pv);


        $vParams['dados'] = $r;
        $vParams['pv'] = $pv;

        try {
            $vParams['total'] = $repo->totaisPreVenda($pv);
            $vParams['total']['dt_nota'] = DateTimeUtils::parseDateStr($vParams['total']['dt_nota']);
        } catch (ViewException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->doRender('Relatorios/ven_venda_listItensDoPreVenda.html.twig', $vParams);
    }


}
