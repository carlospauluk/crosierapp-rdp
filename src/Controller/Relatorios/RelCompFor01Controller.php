<?php

namespace App\Controller\Relatorios;


use App\Entity\Relatorios\RelCompFor01;
use App\Entity\Relatorios\RelVendas01;
use App\EntityHandler\Relatorios\RelCompFor01EntityHandler;
use App\Repository\Relatorios\RelCompFor01Repository;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Entity\Base\DiaUtil;
use CrosierSource\CrosierLibBaseBundle\Repository\Base\DiaUtilRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
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

    /** @var SessionInterface */
    private $session;

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
     * @IsGranted({"ROLE_RELVENDAS"}, statusCode=403)
     */
    public function list(Request $request): Response
    {
        $codProd = $request->get('filter')['codProd'];

        /** @var RelCompFor01Repository $repo */
        $repo = $this->getDoctrine()->getRepository(RelCompFor01::class);
        $produto = $repo->getProdutoByCodigo($codProd);


        $vParams = $request->query->all();


        $dtIni = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 0, 10)) ?: new \DateTime();
        $dtFim = DateTimeUtils::parseDateStr(substr($vParams['filter']['dts'], 13, 10)) ?: new \DateTime();

        $r = $repo->itensCompradosPorProduto($dtIni, $dtFim, $codProd);

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
     * @IsGranted({"ROLE_RELVENDAS"}, statusCode=403)
     */
    public function totalPorFornecedor(Request $request): JsonResponse
    {
        $dts = $request->get('filterDts') ?? '';
        $this->session->set('dashboard.filter.relCompFor01.dts', $dts);
        $dtIni = DateTimeUtils::parseDateStr(substr($dts, 0, 10));
        $dtFim = DateTimeUtils::parseDateStr(substr($dts, 13, 10));

        /** @var RelCompFor01Repository $repoRelCompFor01 */
        $repoRelCompFor01 = $this->getDoctrine()->getRepository(RelCompFor01::class);
        $r = $repoRelCompFor01->totalComprasPorFornecedor($dtIni, $dtFim);
        return new JsonResponse($r);
    }


    /**
     *
     * @Route("/relCompFor01/listItensCompradosPorFornecedor/", name="relCompFor01_listItensCompradosPorFornecedor")
     * @param Request $request
     * @return Response
     * @throws \Exception
     *
     * @IsGranted({"ROLE_RELVENDAS"}, statusCode=403)
     */
    public function listItensCompradosPorFornecedor(Request $request): Response
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_RELVENDAS']);

        $vParams = $request->query->all();
        /** @var RelCompFor01Repository $repo */
        $repo = $this->getDoctrine()->getRepository(RelCompFor01::class);
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

        $nomeFornec = $vParams['filter']['nomeFornec'] ?? $this->getDoctrine()->getRepository(RelVendas01::class)->getNomeFornecedorMaisVendido($dtIni, $dtFim);
        $vParams['filter']['nomeFornec'] = $nomeFornec;

        $r = $repo->itensComprados($dtIni, $dtFim, $nomeFornec);

        $total = $repo->itensComprados($dtIni, $dtFim, $nomeFornec, true)[0];

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


        $vParams['fornecedores'] = json_encode($this->getDoctrine()->getRepository(RelVendas01::class)->getFornecedores());

        $vParams['dados'] = $r;
        $vParams['total'] = $total;

        $viewInfo = [];
        $viewInfo['filter'] = $vParams['filter'];
        $this->storedViewInfoBusiness->store('relCompFor01_listItensCompradosPorFornecedor', $viewInfo);

        return $this->doRender('Relatorios/relCompFor01_listItensCompradosPorFornecedor.html.twig', $vParams);
    }


}
