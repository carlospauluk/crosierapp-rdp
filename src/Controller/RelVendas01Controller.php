<?php

namespace App\Controller;


use App\Entity\RelVendas01;
use App\EntityHandler\RelVendas01EntityHandler;
use App\Repository\RelVendas01Repository;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * CRUD Controller para Push.
 *
 * @package App\Controller\Utils
 * @author Carlos Eduardo Pauluk
 */
class RelVendas01Controller extends FormListController
{

    protected $crudParams =
        [
            'typeClass' => null,

            'formView' => null,
//            'formRoute' => null,
            'formPageTitle' => null,
            'form_PROGRAM_UUID' => null,

            'listView' => 'relVendas01_list.html.twig',
            'listRoute' => 'relVendas01_list',
            'listRouteAjax' => 'relVendas01_datatablesJsList',
            'listPageTitle' => 'Vendas',
            'listId' => 'relVendas01List',
            'list_PROGRAM_UUID' => null,
            'listJS' => '',

            'role_access' => 'ROLE_RELVENDAS01',
            'role_delete' => 'ROLE_ADMIN',

        ];

    /**
     * @required
     * @param RelVendas01EntityHandler $entityHandler
     */
    public function setEntityHandler(RelVendas01EntityHandler $entityHandler): void
    {
        $this->entityHandler = $entityHandler;
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
     * @Route("/relVendas01/list/", name="relVendas01_list")
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
     * @Route("/relVendas01/datatablesJsList/", name="relVendas01_datatablesJsList")
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
     * @Route("/relVendas01/gerarSql/", name="relVendas01_gerarSql")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function gerarSql(Request $request, ParameterBagInterface $params): Response
    {
        $conn = $this->entityHandler->getDoctrine()->getEntityManager()->getConnection();

        file_put_contents('/home/carlos/dev/a/teste.sql', 'bla');

        $time_start = microtime(true);

        $str = '';
        try {
            $linhas = file($params->get('kernel.project_dir') . '/sql/rdp_rel_vendas01.txt');
            $totalRegistros = count($linhas);
            $conn->query('DELETE FROM rdp_rel_vendas01');

            for ($i = 1; $i < $totalRegistros; $i++) {
                // for ($i = 1350; $i < 1450; $i++) {
                $linha = $linhas[$i];
                $campos = explode('|', $linha);
                if (count($campos) !== 11) {
                    throw new ViewException('Qtde de campos difere de 11 para a linha "$linha"');
                }
                $cMax = count($campos);
                for ($c = 0; $c < $cMax; $c++) {
                    $campos[$c] = str_replace("'", "''", $campos[$c]);
                }

                $str .= sprintf(
                        "INSERT INTO rdp_rel_vendas01 VALUES(null,'%s','%s', %d, '%s', %d, '%s', %f, %f, %f, %d, '%s', 1, now(), now(), 1, 1)",
                        trim($campos[0]),
                        trim($campos[1]),
                        (int)trim($campos[2]),
                        trim($campos[3]),
                        trim($campos[4]),
                        trim($campos[5]),
                        (float)trim($campos[6]),
                        (float)trim($campos[7]),
                        (float)trim($campos[8]),
                        (int)trim($campos[9]),
                        trim($campos[10])) . ';' . PHP_EOL;

                $time_now = microtime(true);
                $exec_time = ($time_now - $time_start);

                $med = ($totalRegistros - $i) * $exec_time / $i;

                $this->logger->info('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> ' . $i . " ($med)");
            }
            file_put_contents('/home/carlos/dev/a/rdp_rel_vendas01.sql', $str);
        } catch (ViewException $e) {
            $this->addFlash('error', 'Erro ao gerar sql');
            $this->addFlash('error', $e->getMessage());
        }

        return new Response('OK');
    }


    /**
     *
     * @Route("/relVendas01/totalPorFornecedor/", name="relVendas01_totalPorFornecedor")
     * @return JsonResponse
     */
    public function totalPorFornecedor(): JsonResponse
    {
        /** @var RelVendas01Repository $repoRelVendas01 */
        $repoRelVendas01 = $this->getDoctrine()->getRepository(RelVendas01::class);
        $r = $repoRelVendas01->totalVendasPorFornecedor();
        return new JsonResponse($r);
    }


    /**
     *
     * @Route("/relVendas01/totalPorVendedor/", name="relVendas01_totalPorVendedor")
     * @return JsonResponse
     */
    public function totalPorVendedor(): JsonResponse
    {
        /** @var RelVendas01Repository $repoRelVendas01 */
        $repoRelVendas01 = $this->getDoctrine()->getRepository(RelVendas01::class);
        $r = $repoRelVendas01->totalVendasPorVendedor();
        return new JsonResponse($r);
    }


}
