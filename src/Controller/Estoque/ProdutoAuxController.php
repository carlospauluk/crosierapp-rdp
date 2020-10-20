<?php


namespace App\Controller\Estoque;


use App\Business\Estoque\ProdutoBusiness;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use CrosierSource\CrosierLibRadxBundle\Entity\Estoque\Depto;
use CrosierSource\CrosierLibRadxBundle\Entity\Estoque\Produto;
use CrosierSource\CrosierLibRadxBundle\EntityHandler\Estoque\ProdutoEntityHandler;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller auxiliar ao ProdutoController
 *
 * Class ProdutoAuxController
 *
 * @package App\Controller\Estoque
 */
class ProdutoAuxController extends FormListController
{

    private ProdutoBusiness $produtoBusiness;

    /**
     * @required
     * @param ProdutoBusiness $produtoBusiness
     */
    public function setProdutoBusiness(ProdutoBusiness $produtoBusiness): void
    {
        $this->produtoBusiness = $produtoBusiness;
    }

    /**
     * @required
     * @param ProdutoEntityHandler $entityHandler
     */
    public function setEntityHandler(ProdutoEntityHandler $entityHandler): void
    {
        $this->entityHandler = $entityHandler;
    }


    /**
     *
     * @Route("/est/produto/exportarExcel/", name="est_produto_exportarExcel")
     * @param Request $request
     * @return Response
     * @IsGranted("ROLE_ESTOQUE", statusCode=403)
     */
    public function exportarExcel(Request $request): Response
    {
        $params = [];
        try {
            $apenasProdutosComTitulo = filter_var($request->get('apenasProdutosComTitulo') ?? true, FILTER_VALIDATE_BOOLEAN);
            $nomeArquivo = 'produtos.xlsx';
            $outputFile = $_SERVER['PASTA_ESTOQUE_PRODUTOS_EXCEL'] . $nomeArquivo;
            @unlink($outputFile);
            $params = $this->produtoBusiness->gerarExcel($apenasProdutosComTitulo);
        } catch (ViewException $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->doRender('Estoque/excelProdutos.html.twig', $params);
    }

    /**
     *
     * @Route("/est/produto/importarExcel/", name="est_produto_importarExcel")
     * @param Request $request
     * @return Response
     * @IsGranted("ROLE_ESTOQUE", statusCode=403)
     */
    public function importarExcel(Request $request): Response
    {
        $params = [];
        try {
            if ($request->files->get('arquivo')) {
                $r = $this->produtoBusiness->lerExcelProdutos($request->files->get('arquivo'));
                $this->addFlash('success', $r['ALTERADOS'] . ' produto(s) alterado(s) e ' . $r['NAO_ALTERADOS'] . ' produto(s) não alterado(s)');
            }


        } catch (ViewException $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->doRender('Estoque/excelProdutos_importar.html.twig', $params);
    }

    /**
     *
     * @Route("/est/produto/exportarCSV/", name="est_produto_exportarCSV")
     * @param Request $request
     * @return Response
     * @IsGranted("ROLE_ESTOQUE", statusCode=403)
     */
    public function exportarCSV(Request $request): Response
    {
        $params = [];
        try {
            $apenasProdutosComTitulo = filter_var($request->get('apenasProdutosComTitulo') ?? true, FILTER_VALIDATE_BOOLEAN);
            $nomeArquivo = 'produtos.csv';
            $outputFile = $_SERVER['PASTA_ESTOQUE_PRODUTOS_EXCEL'] . $nomeArquivo;
            @unlink($outputFile);
            $params = $this->produtoBusiness->gerarCSV($apenasProdutosComTitulo);
        } catch (ViewException $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->doRender('Estoque/excelProdutos.html.twig', $params);
    }


    /**
     *
     * @Route("/est/produto/list/", name="est_produto_list")
     * @param Request $request
     * @return Response
     * @throws \Exception
     *
     * @IsGranted("ROLE_ESTOQUE", statusCode=403)
     */
    public function list(Request $request): Response
    {
        $params = [
            'listView' => 'Estoque/produto_list.html.twig',
            'listRoute' => 'est_produto_list',
            'formUrl' => $_SERVER['CROSIERAPPRADX_URL'] . '/est/produto/form',
        ];

        $params['colunas'] = [
            'id',
            'nome',
            'jsonData.depto_nome',
            'jsonData.marca',
            'jsonData.porcent_preench',
            'jsonData.qtde_imagens',
            'jsonData.estoque_total',
            'jsonData.dt_integr_ecommerce',
            'updated'
        ];

        $params['listAuxDatas'] = json_encode(['crosierappradx_url' => $_SERVER['CROSIERAPPRADX_URL']]);

        $fnGetFilterDatas = function (array $params) use ($request) : array {
            $filterDatas = [
                new FilterData(['id'], 'EQ', 'id', $params),
                new FilterData(['erp_codigo'], 'LIKE', 'codigoFrom', $params, null, true),
                new FilterData(['composicao'], 'EQ', 'composicao', $params),
                new FilterData(['marca'], 'LIKE', 'marca', $params, null, true),
                new FilterData(['nome'], 'LIKE', 'nome', $params),
                new FilterData(['titulo'], 'LIKE', 'titulo', $params, null, true),
                new FilterData(['depto'], 'EQ', 'depto', $params, null, false),
                new FilterData(['grupo'], 'EQ', 'grupo', $params, null, false),
                new FilterData(['subgrupo'], 'EQ', 'subgrupo', $params, null, false),
                new FilterData(['porcent_preench'], 'BETWEEN_PORCENT', 'porcent_preench', $params, null, true),
                new FilterData(['ecommerce_dt_integr'], 'BETWEEN_DATE_CONCAT', 'dtIntegrEcommerce', $params, 'date', true)
            ];

            $requestFilter = $request->get('filter');

            $filterTodos_montadora = null;
            if (($requestFilter['montadora'] ?? false) && ($requestFilter['montadora'] !== '%')) {
                $filterTodos_montadora = new FilterData(['montadora', 'montadora_2', 'montadora_3'], 'LIKE');
                $filterTodos_montadora->jsonDataField = true;
                $filterTodos_montadora->val = 'TODOS';
            }
            $filterDatas[] = new FilterData(['montadora', 'montadora_2', 'montadora_3'], 'LIKE', 'montadora', $params, null, true, $filterTodos_montadora);


            $filterTodos_ano = null;
            if (($requestFilter['ano'] ?? false) && ($requestFilter['ano'] !== '%')) {
                $filterTodos_ano = new FilterData(['ano', 'ano_2', 'ano_3'], 'LIKE');
                $filterTodos_ano->jsonDataField = true;
                $filterTodos_ano->val = 'TODOS';
            }
            $filterDatas[] = new FilterData(['ano', 'ano_2', 'ano_3'], 'LIKE', 'ano', $params, null, true, $filterTodos_ano);

            $filterTodos_modelos = null;
            if (($requestFilter['modelos'] ?? false) && ($requestFilter['modelos'] !== '%')) {
                $filterTodos_modelos = new FilterData(['modelos', 'modelos_2', 'modelos_3'], 'LIKE');
                $filterTodos_modelos->jsonDataField = true;
                $filterTodos_modelos->val = 'TODOS';
            }
            $filterDatas[] = new FilterData(['modelos', 'modelos_2', 'modelos_3'], 'LIKE', 'modelos', $params, null, true, $filterTodos_modelos);

            return $filterDatas;
        };


        $params['limit'] = $request->get('limit') ?? 200;

        $repoDepto = $this->getDoctrine()->getRepository(Depto::class);

        $params['deptos'] = $repoDepto->buildDeptosGruposSubgruposSelect2(
            (int)($request->get('filter')['depto'] ?? null),
            (int)($request->get('filter')['grupo'] ?? null),
            (int)($request->get('filter')['subgrupo'] ?? null));
        $params['grupos'] = json_encode([['id' => 0, 'text' => 'Selecione...']]);
        $params['subgrupos'] = json_encode([['id' => 0, 'text' => 'Selecione...']]);

        $params['montadoras'] = $this->produtoBusiness->buildMontadorasAnosModelosSelect2(
            ($request->get('filter')['montadora'] ?? null),
            ($request->get('filter')['ano'] ?? null),
            ($request->get('filter')['modelo'] ?? null)
        );
        $params['anos'] = json_encode([['id' => 0, 'text' => 'Selecione...']]);
        $params['modelos'] = json_encode([['id' => 0, 'text' => 'Selecione...']]);


        $fnHandleDadosList = function (array &$dados, int $totalRegistros) use ($params) {
            if (count($dados) >= $params['limit'] && $totalRegistros > $params['limit']) {
                $this->addFlash('warn', 'Retornando apenas ' . $params['limit'] . ' registros de um total de ' . $totalRegistros . '. Utilize os filtros!');
            }
        };


        return $this->doListSimpl($request, $params, $fnGetFilterDatas, $fnHandleDadosList);
    }

    /**
     *
     * @Route("/est/produto/clearCaches", name="est_produto_clearCaches")
     * @return RedirectResponse
     *
     * @IsGranted("ROLE_ESTOQUE_ADMIN", statusCode=403)
     */
    public function clearCaches(): RedirectResponse
    {
        $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.produto.cache', 0, $_SERVER['CROSIER_SESSIONS_FOLDER']);
        $cache->clear();
        return $this->redirectToRoute('est_produto_list');
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getRepository(): \Doctrine\Common\Persistence\ObjectRepository
    {
        return $this->getDoctrine()->getRepository(Produto::class);
    }


    /**
     *
     * @Route("/est/produto/dashboard", name="est_produto_dashboard")
     * @param Connection $conn
     * @return Response
     *
     * @throws DBALException
     * @IsGranted("ROLE_ESTOQUE", statusCode=403)
     */
    public function dashboardEstoque(Connection $conn): Response
    {
        $hoje = (new \DateTime())->format('d/m/Y');

        $deptos = $conn->fetchAllAssociative('SELECT distinct(json_data->>"$.depto_nome") as deptoNome FROM est_produto ORDER BY json_data->>"$.depto_nome"');

        foreach ($deptos as $depto) {
            $qtde = $conn->fetchAssociative('SELECT count(*) as qtde FROM est_produto WHERE json_data->>"$.depto_nome" = :depto_nome', ['depto_nome' => $depto['deptoNome']]);
            $params['deptos'][$depto['deptoNome']] = $qtde['qtde'];
        }

        $params['porcentPreench'][] = $conn->fetchAssociative('SELECT \'Entre 0 e 10%\' as msg, 0 as i, 10 as f, count(*) as qtde FROM est_produto WHERE ifnull(json_data->>"$.porcent_preench",\'null\') != \'null\' AND json_data->>"$.porcent_preench" BETWEEN 0 AND 0.10');
        $params['porcentPreench'][] = $conn->fetchAssociative('SELECT \'Entre 11 e 20%\' as msg, 11 as i, 20 as f, count(*) as qtde FROM est_produto WHERE ifnull(json_data->>"$.porcent_preench",\'null\') != \'null\' AND json_data->>"$.porcent_preench" BETWEEN 0.11 AND 0.20');
        $params['porcentPreench'][] = $conn->fetchAssociative('SELECT \'Entre 21 e 30%\' as msg, 21 as i, 30 as f, count(*) as qtde FROM est_produto WHERE ifnull(json_data->>"$.porcent_preench",\'null\') != \'null\' AND json_data->>"$.porcent_preench" BETWEEN 0.21 AND 0.30');
        $params['porcentPreench'][] = $conn->fetchAssociative('SELECT \'Entre 31 e 40%\' as msg, 31 as i, 40 as f, count(*) as qtde FROM est_produto WHERE ifnull(json_data->>"$.porcent_preench",\'null\') != \'null\' AND json_data->>"$.porcent_preench" BETWEEN 0.31 AND 0.40');
        $params['porcentPreench'][] = $conn->fetchAssociative('SELECT \'Entre 41 e 50%\' as msg, 41 as i, 50 as f, count(*) as qtde FROM est_produto WHERE ifnull(json_data->>"$.porcent_preench",\'null\') != \'null\' AND json_data->>"$.porcent_preench" BETWEEN 0.41 AND 0.50');
        $params['porcentPreench'][] = $conn->fetchAssociative('SELECT \'Entre 51 e 60%\' as msg, 51 as i, 60 as f, count(*) as qtde FROM est_produto WHERE ifnull(json_data->>"$.porcent_preench",\'null\') != \'null\' AND json_data->>"$.porcent_preench" BETWEEN 0.51 AND 0.60');
        $params['porcentPreench'][] = $conn->fetchAssociative('SELECT \'Entre 61 e 70%\' as msg, 61 as i, 70 as f, count(*) as qtde FROM est_produto WHERE ifnull(json_data->>"$.porcent_preench",\'null\') != \'null\' AND json_data->>"$.porcent_preench" BETWEEN 0.61 AND 0.70');
        $params['porcentPreench'][] = $conn->fetchAssociative('SELECT \'Entre 71 e 80%\' as msg, 71 as i, 80 as f, count(*) as qtde FROM est_produto WHERE ifnull(json_data->>"$.porcent_preench",\'null\') != \'null\' AND json_data->>"$.porcent_preench" BETWEEN 0.71 AND 0.80');
        $params['porcentPreench'][] = $conn->fetchAssociative('SELECT \'Entre 81 e 90%\' as msg, 81 as i, 90 as f, count(*) as qtde FROM est_produto WHERE ifnull(json_data->>"$.porcent_preench",\'null\') != \'null\' AND json_data->>"$.porcent_preench" BETWEEN 0.81 AND 0.90');
        $params['porcentPreench'][] = $conn->fetchAssociative('SELECT \'Entre 91 e 99%\' as msg, 91 as i, 99 as f, count(*) as qtde FROM est_produto WHERE ifnull(json_data->>"$.porcent_preench",\'null\') != \'null\' AND json_data->>"$.porcent_preench" BETWEEN 0.91 AND 0.99');
        $params['porcentPreench'][] = $conn->fetchAssociative('SELECT \'100%\' as msg, 100 as i, 100 as f, count(*) as qtde FROM est_produto WHERE ifnull(json_data->>"$.porcent_preench",\'null\') != \'null\' AND json_data->>"$.porcent_preench" = 1');

        $qtdeProdutosComTitulo = $conn->fetchAssociative('SELECT count(*) as qtde FROM est_produto WHERE json_data->>"$.titulo" != \'null\' AND trim(json_data->>"$.titulo") != \'\'');
        $params['qtdeProdutosComTitulo'] = $qtdeProdutosComTitulo['qtde'];

        $qtdeProdutosComTituloESemFoto = $conn->fetchAssociative('SELECT count(*) as qtde FROM est_produto WHERE json_data->>"$.titulo" != \'null\' AND trim(json_data->>"$.titulo") != \'\' AND IFNULL(json_data->>"$.qtde_imagens", 0) = 0');
        $params['qtdeProdutosComTituloESemFoto'] = $qtdeProdutosComTituloESemFoto['qtde'];

        $params['hoje'] = $hoje;

        return $this->doRender('/Estoque/dashboardEstoque.html.twig', $params);
    }

    /**
     *
     * @Route("/est/graficoTotalEstoquePorFilial/", name="est_graficoTotalEstoquePorFilial")
     * @return JsonResponse
     *
     * @throws ViewException
     * @IsGranted("ROLE_RELVENDAS", statusCode=403)
     */
    public function graficoTotalEstoquePorFilial(): JsonResponse
    {
        $r = $this->produtoBusiness->totalEstoquePorFilial();
        return new JsonResponse($r);
    }

}
