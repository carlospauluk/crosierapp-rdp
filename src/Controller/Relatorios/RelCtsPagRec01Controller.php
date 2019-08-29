<?php

namespace App\Controller\Relatorios;


use App\Entity\Relatorios\RelCtsPagRec01;
use App\EntityHandler\Relatorios\RelCtsPagRec01EntityHandler;
use App\Repository\Relatorios\RelCtsPagRec01Repository;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Entity\Base\DiaUtil;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
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
class RelCtsPagRec01Controller extends FormListController
{


    /** @var SessionInterface */
    private $session;

    /**
     * @required
     * @param RelCtsPagRec01EntityHandler $entityHandler
     */
    public function setEntityHandler(RelCtsPagRec01EntityHandler $entityHandler): void
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
     *
     * @Route("/relCtsPagRec01/list/", name="relCtsPagRec01_list")
     * @param Request $request
     * @return Response
     * @throws \Exception
     *
     * @IsGranted({"ROLE_FINAN"}, statusCode=403)
     *
     */
    public function list(Request $request): Response
    {
        $parameters = $this->buildListData($request);
        $parameters['listRoute'] = 'relCtsPagRec01_list';
        $parameters['listView'] = 'Relatorios/relCtsPagRec01_list.html.twig';
        return $this->doList($request, $parameters);
    }

    /**
     * @param Request $request
     * @return array
     * @throws ViewException
     */
    private function buildListData(Request $request): array
    {
        /** @var RelCtsPagRec01Repository $repo */
        $repo = $this->getDoctrine()->getRepository(RelCtsPagRec01::class);

        $parameters = $request->query->all();
        if (!array_key_exists('filter', $parameters)) {

            if ($parameters['r'] ?? null) {
                $this->storedViewInfoBusiness->clear('relCtsPagRec01_list');
            }
            $svi = $this->storedViewInfoBusiness->retrieve('relCtsPagRec01_list');
            if (isset($svi['filter'])) {
                $parameters['filter'] = $svi['filter'];
            } else {
                $parameters['filter'] = [];
                $parameters['filter']['dts'] = date('d/m/Y') . ' - ' . date('d/m/Y');
                $parameters['filter']['filial'] = $repo->getFiliais()[0]['id'];
                $parameters['filter']['localizador'] = $repo->getLocalizadores()[0]['id'];
            }
        }

        $dtIni = DateTimeUtils::parseDateStr(substr($parameters['filter']['dts'], 0, 10)) ?: new \DateTime();
        $dtFim = DateTimeUtils::parseDateStr(substr($parameters['filter']['dts'], 13, 10)) ?: new \DateTime();

        $parameters['filter']['dtVencto']['i'] = $dtIni->format('Y-m-d');
        $parameters['filter']['dtVencto']['f'] = $dtFim->format('Y-m-d');

//        $localizador = urldecode($parameters['filter']['localizador']);
//        $parameters['filter']['localizador'] = (int)explode(' - ', $localizador)[0];

        $filterDatas = $this->getFilterDatas($parameters);


        $orders = [
            'e.dtVencto' => 'asc',
            'e.tipoPagRec' => 'asc',
            'e.valorTitulo' => 'asc'
        ];
        $dados = $repo->findByFilters($filterDatas, $orders, 0, null);


        $dtAnterior = clone $dtIni;
        $dtAnterior->setTime(12, 0, 0, 0)->modify('last day');

        $dia = null;
        $dias = array();
        $i = -1;
        /** @var RelCtsPagRec01 $movimentacao */
        $totalAReceber = 0.0;
        $totalRecebido = 0.0;
        $totalAPagar = 0.0;
        $totalPago = 0.0;

        foreach ($dados as $movimentacao) {
            if ($movimentacao->getDtVencto() && $movimentacao->getDtVencto()->format('d/m/Y') !== $dia) {
                $i++;
                $dia = $movimentacao->getDtVencto()->format('d/m/Y');
                $dias[$i]['dtVencto'] = $movimentacao->getDtVencto();
            }
            $dias[$i]['movs'][] = $movimentacao;
        }


        foreach ($dias as $k => $dia) {
            foreach ($dia['movs'] as $movimentacao) {
                $totalAReceber += $movimentacao->getTipoPagRec() === 'R' && !$movimentacao->getValorBaixa() ? $movimentacao->getValorTitulo() : 0.0;
                $totalRecebido += $movimentacao->getTipoPagRec() === 'R' && $movimentacao->getValorBaixa() ? $movimentacao->getValorBaixa() : 0.0;
                $totalAPagar += $movimentacao->getTipoPagRec() === 'P' && !$movimentacao->getValorBaixa() ? $movimentacao->getValorTitulo() : 0.0;
                $totalPago += $movimentacao->getTipoPagRec() === 'P' && $movimentacao->getValorBaixa() ? $movimentacao->getValorBaixa() : 0.0;
            }
            $dia['totalAReceber'] = $totalAReceber;
            $dia['totalRecebido'] = $totalRecebido;
            $dia['totalAPagar'] = $totalAPagar;
            $dia['totalPago'] = $totalPago;

            $dia['total'] = $this->somarMovimentacoes($dia['movs']);
            $dias[$k] = $dia;
        }

        $parameters['totalGeral'] = $this->somarMovimentacoes($dados);


        $parameters['dias'] = $dias;

        /** @var DiaUtilRepository $repoDiaUtil */
        $repoDiaUtil = $this->getDoctrine()->getRepository(DiaUtil::class);

        $prox = $repoDiaUtil->incPeriodo($dtIni, $dtFim, true);
        $ante = $repoDiaUtil->incPeriodo($dtIni, $dtFim, false);

        $parameters['antePeriodoI'] = $ante['dtIni'];
        $parameters['antePeriodoF'] = $ante['dtFim'];
        $parameters['proxPeriodoI'] = $prox['dtIni'];
        $parameters['proxPeriodoF'] = $prox['dtFim'];

        $filiais = $repo->getFiliais();
        $localizadores = $repo->getLocalizadores();
        array_unshift($localizadores, ['id' => '', 'text' => 'TODOS']);

        $parameters['filiais'] = json_encode($filiais);
        $parameters['localizadores'] = json_encode($localizadores);

        $parameters['page_title'] = 'Contas a Pagar/Receber';

        //        $parameters['filter']['localizador'] = urlencode($localizador);

        $viewInfo = [];
        $viewInfo['filter'] = $parameters['filter'];
        $this->storedViewInfoBusiness->store('relCtsPagRec01_list', $viewInfo);

        return $parameters;
    }

    /**
     * @param array $params
     * @return array
     */
    public function getFilterDatas(array $params): array
    {
        return [
            new FilterData(['dtVencto'], 'BETWEEN', 'dtVencto', $params),
            new FilterData(['tipoPagRec'], 'EQ', 'tipoPagRec', $params),
            new FilterData(['filial'], 'EQ', 'filial', $params),
        ];
    }

    /**
     * @param $movs
     * @return float|null
     */
    public function somarMovimentacoes($movs): ?float
    {
        $total = 0.0;
        /** @var RelCtsPagRec01 $m */
        foreach ($movs as $m) {
            $total = $m->getTipoPagRec() === 'R' ? $total + $m->getValorTitulo() : $total - $m->getValorTitulo();
        }
        return $total;
    }

    /**
     *
     * @Route("/relCtsPagRec01/datatablesJsList/", name="relCtsPagRec01_datatablesJsList")
     * @param Request $request
     * @return Response
     * @throws ViewException
     *
     * @IsGranted({"ROLE_FINAN"}, statusCode=403)
     */
    public function datatablesJsList(Request $request): Response
    {
        return $this->doDatatablesJsList($request);
    }

    /**
     *
     * @Route("/relCtsPagRec01/graficoCtsPagRec01/", name="relCtsPagRec01_graficoCtsPagRec01")
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @IsGranted({"ROLE_FINAN"}, statusCode=403)
     */
    public function graficoCtsPagRec01(Request $request): JsonResponse
    {
        $dts = $request->get('filterDts') ?? null;
        $filial = $request->get('filial') ?? null;
        $localizador = $request->get('localizador') ?? null;
        $localizador = strtolower($localizador) === 'null' ? null : $localizador;

        $this->session->set('dashboard.filter.contasPagRec.dts', $dts);
        $this->session->set('dashboard.filter.contasPagRec.filial', $filial);
        $this->session->set('dashboard.filter.contasPagRec.localizador', $localizador);

        $dtIni = DateTimeUtils::parseDateStr(substr($dts, 0, 10));
        $dtFim = DateTimeUtils::parseDateStr(substr($dts, 13, 10));

        /** @var RelCtsPagRec01Repository $repoRelCtsPagRec01 */
        $repoRelCtsPagRec01 = $this->getDoctrine()->getRepository(RelCtsPagRec01::class);
        $r = $repoRelCtsPagRec01->relCtsPagRec01($dtIni, $dtFim, $filial, $localizador);
        return new JsonResponse($r);
    }


}
