<?php

namespace App\Controller\Relatorios;


use App\Business\Vendas\VendaRepositoryBusiness;
use App\EntityHandler\Relatorios\RelCompFor01EntityHandler;
use App\Repository\Relatorios\RelCompFor01Repository;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Entity\Base\DiaUtil;
use CrosierSource\CrosierLibBaseBundle\Repository\Base\DiaUtilRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use CrosierSource\CrosierLibRadxBundle\Entity\Vendas\Venda;
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
class RelCompFor01Controller extends FormListController
{

    private SessionInterface $session;

    private RelCompFor01Repository $repoRelCompFor01;

    private VendaRepositoryBusiness $vendaRepositoryBusiness;

    /**
     * @required
     * @param RelCompFor01EntityHandler $entityHandler
     */
    public function setEntityHandler(RelCompFor01EntityHandler $entityHandler): void
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
     * @param VendaRepositoryBusiness $vendaRepositoryBusiness
     */
    public function setVendaRepositoryBusiness(VendaRepositoryBusiness $vendaRepositoryBusiness): void
    {
        $this->vendaRepositoryBusiness = $vendaRepositoryBusiness;
    }


    /**
     * @required
     * @param RelCompFor01Repository $repoRelCompFor01
     */
    public function setRepoRelCompFor01(RelCompFor01Repository $repoRelCompFor01): void
    {
        $this->repoRelCompFor01 = $repoRelCompFor01;
    }


    public function getFilterDatas(array $params): array
    {
        return [
            new FilterData(['codProd'], 'LIKE', 'codProd', $params)
        ];
    }

    /**
     *
     * @Route("/relCompFor01/list/", name="relCompFor01_list")
     * @param Request $request
     * @return Response
     * @throws \Exception
     *
     * @IsGranted("ROLE_RELVENDAS", statusCode=403)
     */
    public function list(Request $request): Response
    {
        $codProd = $request->get('filter')['codProd'];

        $produto = $this->repoRelCompFor01->getProdutoByCodigo($codProd);

        $vParams = $request->query->all();

        $dtIni = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 0, 10)) ?: new \DateTime();
        $dtFim = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 13, 10)) ?: new \DateTime();

        $r = $this->repoRelCompFor01->itensCompradosPorProduto($dtIni, $dtFim, $codProd);

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

        $vParams['produto'] = $codProd . ' - ' . $produto;

        return $this->doRender('Relatorios/relCompFor01_list.html.twig', $vParams);
    }


    /**
     *
     * @Route("/relCompFor01/graficoTotalPorFornecedor/", name="relCompFor01_graficoTotalPorFornecedor")
     * @param Request $request
     * @return JsonResponse
     *
     * @IsGranted("ROLE_RELVENDAS", statusCode=403)
     */
    public function totalPorFornecedor(Request $request): JsonResponse
    {
        $dts = $request->get('filterDts') ?? '';
        $this->session->set('dashboard.filter.relCompFor01.dts', $dts);
        $dtIni = DateTimeUtils::parseDateStr(substr($dts, 0, 10));
        $dtFim = DateTimeUtils::parseDateStr(substr($dts, 13, 10));

        $r = $this->repoRelCompFor01->totalComprasPorFornecedor($dtIni, $dtFim);
        return new JsonResponse($r);
    }


    /**
     *
     * @Route("/relCompFor01/listItensCompradosPorFornecedor/", name="relCompFor01_listItensCompradosPorFornecedor")
     * @param Request $request
     * @return Response
     * @throws \Exception
     *
     * @IsGranted("ROLE_RELVENDAS", statusCode=403)
     */
    public function listItensCompradosPorFornecedor(Request $request): Response
    {
        $vParams = $request->query->all();

        if (!array_key_exists('filter', $vParams)) {

            if ($vParams['r'] ?? null) {
                $this->storedViewInfoBusiness->clear('relCompFor01_listItensCompradosPorFornecedor');
            }
            $svi = $this->storedViewInfoBusiness->retrieve('relCompFor01_listItensCompradosPorFornecedor');
            if (isset($svi['filter'])) {
                $vParams['filter'] = $svi['filter'];
            } else {
                $vParams['filter'] = [];
                $vParams['filter']['dts'] = '01/' . date('m/Y') . ' - ' . date('t/m/Y');
            }
        }

        $dtIni = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 0, 10)) ?: new \DateTime();
        $dtFim = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 13, 10)) ?: new \DateTime();

        $nomeFornec = $vParams['filter']['nomeFornec'] ?? $this->getDoctrine()->getRepository(Venda::class)->getNomeFornecedorMaisVendido($dtIni, $dtFim);
        $vParams['filter']['nomeFornec'] = $nomeFornec;

        $r = $this->repoRelCompFor01->itensComprados($dtIni, $dtFim, $nomeFornec);

        $total = $this->repoRelCompFor01->itensComprados($dtIni, $dtFim, $nomeFornec, true)[0];

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


        $vParams['fornecedores'] = json_encode($this->vendaRepositoryBusiness->getFornecedores());

        $vParams['dados'] = $r;
        $vParams['total'] = $total;

        $viewInfo = [];
        $viewInfo['filter'] = $vParams['filter'];
        $this->storedViewInfoBusiness->store('relCompFor01_listItensCompradosPorFornecedor', $viewInfo);

        return $this->doRender('Relatorios/relCompFor01_listItensCompradosPorFornecedor.html.twig', $vParams);
    }


}
