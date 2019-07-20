<?php

namespace App\Controller;


use App\Entity\RelCtsPagRec01;
use App\EntityHandler\RelCtsPagRec01EntityHandler;
use App\Repository\RelCtsPagRec01Repository;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * CRUD Controller para Push.
 *
 * @package App\Controller\Utils
 * @author Carlos Eduardo Pauluk
 */
class RelCtsPagRec01Controller extends FormListController
{
    /** @var SessionInterface */
    private $session;

    protected $crudParams =
        [
            'typeClass' => null,

            'formView' => null,
//            'formRoute' => null,
            'formPageTitle' => null,
            'form_PROGRAM_UUID' => null,

            'listView' => 'relCtsPagRec01_list.html.twig',
            'listRoute' => 'relCtsPagRec01_list',
            'listRouteAjax' => 'relCtsPagRec01_datatablesJsList',
            'listPageTitle' => 'CtsPagRec',
            'listId' => 'relCtsPagRec01List',
            'list_PROGRAM_UUID' => null,
            'listJS' => '',

            'role_access' => 'ROLE_RELVENDAS01',
            'role_delete' => 'ROLE_ADMIN',

        ];

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


    public function getFilterDatas(array $params): array
    {
        return [
            new FilterData(['descProduto'], 'LIKE', 'descProduto', $params),
            new FilterData(['nomeFornecedor'], 'LIKE', 'nomeFornecedor', $params)
        ];
    }

    /**
     *
     * @Route("/relCtsPagRec01/list/", name="relCtsPagRec01_list")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function list(Request $request): Response
    {
        return $this->doList($request);
    }

    /**
     *
     * @Route("/relCtsPagRec01/datatablesJsList/", name="relCtsPagRec01_datatablesJsList")
     * @param Request $request
     * @return Response
     * @throws ViewException
     */
    public function datatablesJsList(Request $request): Response
    {
        return $this->doDatatablesJsList($request);
    }

    /**
     *
     * @Route("/relCtsPagRec01/gerarSql/", name="relCtsPagRec01_gerarSql")
     * @param Request $request
     * @param ParameterBagInterface $params
     * @return Response
     * @throws \Doctrine\DBAL\DBALException
     */
    public function gerarSql(Request $request, ParameterBagInterface $params): Response
    {
        $conn = $this->entityHandler->getDoctrine()->getEntityManager()->getConnection();

        $time_start = microtime(true);

        $str = 'TRUNCATE TABLE rdp_rel_ctspagrec01;' . PHP_EOL . PHP_EOL;
        try {
            $linhas = file($params->get('kernel.project_dir') . '/sql/rdp_rel_ctspagrec01.txt');
            $totalRegistros = count($linhas);

            for ($i = 1; $i < $totalRegistros; $i++) {
                $linha = $linhas[$i];
                $campos = explode('|', $linha);
                if (count($campos) !== 15) {
                    throw new ViewException('Qtde de campos difere de 15 para a linha "$linha"');
                }

                $campos[2] = DateTimeUtils::parseDateStr($campos[2])->format('Y-m-d');
                $campos[3] = DateTimeUtils::parseDateStr($campos[3])->format('Y-m-d');
                $campos[4] = trim($campos[4]) ? DateTimeUtils::parseDateStr($campos[4])->format('Y-m-d') : '';
                $campos[14] = trim($campos[14]) ? DateTimeUtils::parseDateStr($campos[14])->format('Y-m-d') : '';


                $cMax = count($campos);
                for ($c = 0; $c < $cMax; $c++) {
                    $campos[$c] = $campos[$c] ? "'" . trim(str_replace("'", "''", $campos[$c])) . "'" : 'null';
                }


                $str .= sprintf(
                        "INSERT INTO rdp_rel_ctspagrec01 VALUES(null,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s, 1, now(), now(), 1, 1)",
                        ($campos[0]),
                        ($campos[1]),
                        ($campos[2]),
                        ($campos[3]),
                        ($campos[4]),
                        ($campos[5]),
                        ($campos[6]),
                        ($campos[7]),
                        ($campos[8]),
                        ($campos[9]),
                        ($campos[10]),
                        ($campos[11]),
                        ($campos[12]),
                        ($campos[13]),
                        ($campos[14])
                    ) . ';' . PHP_EOL;

                $time_now = microtime(true);
                $exec_time = ($time_now - $time_start);

                $med = ($totalRegistros - $i) * $exec_time / $i;

                $this->logger->info('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> ' . $i . " ($med)");
            }
            file_put_contents($params->get('kernel.project_dir') . '/sql/inserts/rdp_rel_ctspagrec01.sql', $str);
        } catch (ViewException $e) {
            $this->addFlash('error', 'Erro ao gerar sql');
            $this->addFlash('error', $e->getMessage());
        }

        return new Response('OK');
    }


    /**
     *
     * @Route("/relCtsPagRec01/rel01/", name="relCtsPagRec01_rel01")
     * @param Request $request
     * @return JsonResponse
     */
    public function rel01(Request $request): JsonResponse
    {
        $dts = $request->get('filterDts') ?? '';
        $this->session->set('dashboard.filter.dts', $dts);
        $dtIni = DateTimeUtils::parseDateStr(substr($dts, 0, 10));
        $dtFim = DateTimeUtils::parseDateStr(substr($dts, 13, 10));

        /** @var RelCtsPagRec01Repository $repoRelCtsPagRec01 */
        $repoRelCtsPagRec01 = $this->getDoctrine()->getRepository(RelCtsPagRec01::class);
        $r = $repoRelCtsPagRec01->totalCtsPagRecPorFornecedor($dtIni, $dtFim);
        return new JsonResponse($r);
    }


    /**
     *
     * @Route("/relCtsPagRec01/totalPorVendedor/", name="relCtsPagRec01_totalPorVendedor")
     * @param Request $request
     * @return JsonResponse
     */
    public function totalPorVendedor(Request $request): JsonResponse
    {
        $dts = $request->get('filterDts') ?? '';
        $dtIni = DateTimeUtils::parseDateStr(substr($dts, 0, 10));
        $dtFim = DateTimeUtils::parseDateStr(substr($dts, 13, 10));

        /** @var RelCtsPagRec01Repository $repoRelCtsPagRec01 */
        $repoRelCtsPagRec01 = $this->getDoctrine()->getRepository(RelCtsPagRec01::class);
        $r = $repoRelCtsPagRec01->totalCtsPagRecPorVendedor($dtIni, $dtFim);
        return new JsonResponse($r);
    }


}
