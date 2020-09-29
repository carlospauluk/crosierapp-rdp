<?php

namespace App\Controller\Vendas;


use App\Business\Vendas\VendaRepositoryBusiness;
use CrosierSource\CrosierLibBaseBundle\Controller\BaseController;
use CrosierSource\CrosierLibBaseBundle\Entity\Base\DiaUtil;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\Base\DiaUtilRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Doctrine\DBAL\Connection;
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
class VendaAuxController extends BaseController
{

    private SessionInterface $session;

    private VendaRepositoryBusiness $vendaRepositoryBusiness;

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
     * @param VendaRepositoryBusiness $vendaRepositoryBusiness
     */
    public function setVendaRepositoryBusiness(VendaRepositoryBusiness $vendaRepositoryBusiness): void
    {
        $this->vendaRepositoryBusiness = $vendaRepositoryBusiness;
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

        $dtIni = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 0, 10)) ?: new \DateTime();
        $dtFim = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 13, 10)) ?: new \DateTime();

        $nomeFornec = $vParams['filter']['nomeFornec'] ?? $this->vendaRepositoryBusiness->getNomeFornecedorMaisVendido($dtIni, $dtFim);
        $vParams['filter']['nomeFornec'] = $nomeFornec;

        $vParams['filter']['lojas'] = $vParams['filter']['lojas'] ?? null;
        $vParams['filter']['grupos'] = $vParams['filter']['grupos'] ?? null;


        $r = $this->vendaRepositoryBusiness->itensVendidos($dtIni, $dtFim, $nomeFornec, $vParams['filter']['lojas'], $vParams['filter']['grupos']);

        $total = $this->vendaRepositoryBusiness->itensVendidos($dtIni, $dtFim, $nomeFornec, $vParams['filter']['lojas'], $vParams['filter']['grupos'], true)[0];

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


        $lojas = $this->vendaRepositoryBusiness->getLojas();
        array_unshift($lojas, ['id' => '', 'text' => 'TODAS']);
        $vParams['lojas'] = json_encode($lojas);
        $grupos = $this->vendaRepositoryBusiness->getGrupos();
        array_unshift($grupos, ['id' => '', 'text' => 'TODOS']);
        $vParams['grupos'] = json_encode($grupos);

        $vParams['fornecedores'] = json_encode($this->vendaRepositoryBusiness->getFornecedores());

        $vParams['dados'] = $r;
        $vParams['total'] = $total;

        $viewInfo = [];
        $viewInfo['filter'] = $vParams['filter'];
        $this->storedViewInfoBusiness->store('ven_venda_listItensVendidosPorFornecedor', $viewInfo);

        return $this->doRender('Relatorios/itensVendidosPorFornecedor.html.twig', $vParams);
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

        $r = $this->vendaRepositoryBusiness->totalVendasPorFornecedor($dtIni, $dtFim, $lojas, $grupos);
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

        $r = $this->vendaRepositoryBusiness->totalVendasPorFornecedor($dtIni, $dtFim, $vParams['filter']['lojas'], $vParams['filter']['grupos']);


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


        $lojas = $this->vendaRepositoryBusiness->getLojas();
        array_unshift($lojas, ['id' => '', 'text' => 'TODAS']);
        $vParams['lojas'] = json_encode($lojas);
        $grupos = $this->vendaRepositoryBusiness->getGrupos();
        array_unshift($grupos, ['id' => '', 'text' => 'TODOS']);
        $vParams['grupos'] = json_encode($grupos);

        $vParams['fornecedores'] = json_encode($this->vendaRepositoryBusiness->getFornecedores());

        $vParams['dados'] = $r;
        $vParams['total'] = $this->vendaRepositoryBusiness->totalVendasPor($dtIni, $dtFim, $vParams['filter']['lojas'], $vParams['filter']['grupos']);

        $viewInfo = [];
        $viewInfo['filter'] = $vParams['filter'];
        $this->storedViewInfoBusiness->store('ven_venda_listItensVendidosPorFornecedor', $viewInfo);

        return $this->doRender('Relatorios/vendas_totalPorFornecedor.html.twig', $vParams);
    }


    /**
     *
     * @Route("/ven/venda/graficoTotalPorVendedor/", name="ven_venda_graficoTotalPorVendedor")
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\DBAL\DBALException
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

        $r = $this->vendaRepositoryBusiness->totalVendasPorVendedor($dtIni, $dtFim, $lojas, $grupos);

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

        $r = $this->vendaRepositoryBusiness->preVendasPorPeriodoEVendedor($dtIni, $dtFim, $codVendedor, $vParams['filter']['lojas'], $vParams['filter']['grupos']);

        $vParams['total'] = 0.0;
        foreach ($r as $pv) {
            $vParams['total'] += $pv['valor_total'];
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

        $vParams['vendedores'] = json_encode($this->vendaRepositoryBusiness->getVendedores());

        $vParams['dados'] = $r;

        $lojas = $this->vendaRepositoryBusiness->getLojas();
        array_unshift($lojas, ['id' => '', 'text' => 'TODAS']);
        $vParams['lojas'] = json_encode($lojas);
        $grupos = $this->vendaRepositoryBusiness->getGrupos();
        array_unshift($grupos, ['id' => '', 'text' => 'TODOS']);
        $vParams['grupos'] = json_encode($grupos);

        $viewInfo = [];
        $viewInfo['filter'] = $vParams['filter'];
        $this->storedViewInfoBusiness->store('ven_venda_listPreVendasPorVendedor', $viewInfo);

        return $this->doRender('Relatorios/preVendasPorVendedor.html.twig', $vParams);
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

        $r = $this->vendaRepositoryBusiness->preVendasPorPeriodoEProduto($dtIni, $dtFim, $vParams['produto'], $vParams['filter']['lojas'], $vParams['filter']['grupos']);

        $vParams['total'] = 0.0;
        foreach ($r as $pv) {
            $vParams['total'] += $pv['valor_total'];
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

        $lojas = $this->vendaRepositoryBusiness->getLojas();
        array_unshift($lojas, ['id' => '', 'text' => 'TODAS']);
        $vParams['lojas'] = json_encode($lojas);
        $grupos = $this->vendaRepositoryBusiness->getGrupos();
        array_unshift($grupos, ['id' => '', 'text' => 'TODOS']);
        $vParams['grupos'] = json_encode($grupos);

        $viewInfo = [];
        $viewInfo['filter'] = $vParams['filter'];
        $this->storedViewInfoBusiness->store('ven_venda_listPreVendasPorProduto', $viewInfo);

        return $this->doRender('Relatorios/preVendasPorProduto.html.twig', $vParams);
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

        $conn = $this->getDoctrine()->getConnection();

        $venda = $conn->fetchAssociative('SELECT * FROM ven_venda WHERE json_data->>"$.prevenda_ekt" = :pv', ['pv' => $pv]);

        $venda['json_data'] = json_decode($venda['json_data'], true);

        $itens = $conn->fetchAllAssociative('SELECT * FROM ven_venda_item WHERE venda_id = :venda_id', ['venda_id' => $venda['id']]);

        foreach ($itens as $item) {
            $item['json_data'] = json_decode($item['json_data'], true);
            $venda['itens'][] = $item;
        }

        return $this->doRender('Relatorios/itensDoPreVenda.html.twig', ['venda' => $venda]);
    }


}
