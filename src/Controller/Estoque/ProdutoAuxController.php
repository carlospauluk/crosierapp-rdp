<?php


namespace App\Controller\Estoque;


use App\Business\Estoque\ProdutoBusiness;
use App\Entity\Estoque\Produto;
use App\Repository\Estoque\ProdutoRepository;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Twig\FilterInput;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @param array $params
     * @return array
     */
    public function getFilterDatas(array $params): array
    {
        return [
            new FilterData(['id'], 'EQ', 'id', $params),
            new FilterData(['erp_codigo'], 'LIKE', 'codigoFrom', $params, null, true),
            new FilterData(['nome'], 'LIKE', 'nome', $params),
            new FilterData(['titulo'], 'LIKE', 'titulo', $params, null, true),
            new FilterData(['depto_nome'], 'LIKE', 'nomeDepto', $params, null, true),
            new FilterData(['porcent_preench'], 'BETWEEN_PORCENT', 'porcent_preench', $params, null, true),
            new FilterData(['ecommerce_dt_integr'], 'BETWEEN_DATE', 'ecommerce_dt_integr', $params, 'date', false)
        ];
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
            'listView' => '@CrosierLibBase/list.html.twig',
            'listJS' => 'Estoque/produto_list.js',
            'listRoute' => 'est_produto_list',
            'listRouteAjax' => 'est_produto_datatablesJsList',
            'listPageTitle' => 'Produtos',
            'formUrl' => $_SERVER['CROSIERAPPRADX_URL'] . '/est/produto/form',
            'listId' => 'produto_list'
        ];
        $params['filterInputs'] = [
            new FilterInput('Código', 'id'),
            new FilterInput('Código (ERP)', 'codigoFrom'),
            new FilterInput('Nome', 'nome'),
            new FilterInput('Título', 'titulo'),
            new FilterInput('Depto', 'nomeDepto'),
            new FilterInput('Qtde Imagens', 'qtdeImagens', 'INTEGER'),
            new FilterInput('', 'tituloIsNotEmpty', 'HIDDEN'),
            new FilterInput('% preench', 'porcent_preench', 'BETWEEN_PORCENT'),
            new FilterInput('Dt Integr E-commerce', 'ecommerce_dt_integr', 'BETWEEN_DATE'),
        ];
        $params['listAuxDatas'] = json_encode(['crosierappradx_url' => $_SERVER['CROSIERAPPRADX_URL']]);
        return $this->doList($request, $params);
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
     * @Route("/est/produto/datatablesJsList/", name="est_produto_datatablesJsList")
     * @param Request $request
     * @return Response
     * @throws ViewException
     *
     * @IsGranted("ROLE_ESTOQUE", statusCode=403)
     */
    public function datatablesJsList(Request $request): Response
    {
        return $this->doDatatablesJsList($request);
    }


    /**
     *
     * @Route("/est/produto/dashboard", name="est_produto_dashboard")
     * @param Request $request
     * @param Connection $conn
     * @return Response
     *
     * @throws DBALException
     * @IsGranted("ROLE_ESTOQUE", statusCode=403)
     */
    public function dashboardEstoque(Request $request, Connection $conn): Response
    {
        $hoje = (new \DateTime())->format('d/m/Y');

        $deptos = $conn->fetchAll('SELECT distinct(json_data->>"$.depto_nome") as deptoNome FROM est_produto ORDER BY json_data->>"$.depto_nome"');

        foreach ($deptos as $depto) {
            $qtde = $conn->fetchAssoc('SELECT count(*) as qtde FROM est_produto WHERE json_data->>"$.depto_nome" = :depto_nome', ['depto_nome' => $depto['deptoNome']]);
            $params['deptos'][$depto['deptoNome']] = $qtde['qtde'];
        }

        $params['porcentPreench'][] = $conn->fetchAssoc('SELECT \'Entre 0 e 10%\' as msg, 0 as i, 10 as f, count(*) as qtde FROM est_produto WHERE ifnull(json_data->>"$.porcent_preench",\'null\') != \'null\' AND json_data->>"$.porcent_preench" BETWEEN 0 AND 0.10');
        $params['porcentPreench'][] = $conn->fetchAssoc('SELECT \'Entre 11 e 20%\' as msg, 11 as i, 20 as f, count(*) as qtde FROM est_produto WHERE ifnull(json_data->>"$.porcent_preench",\'null\') != \'null\' AND json_data->>"$.porcent_preench" BETWEEN 0.11 AND 0.20');
        $params['porcentPreench'][] = $conn->fetchAssoc('SELECT \'Entre 21 e 30%\' as msg, 21 as i, 30 as f, count(*) as qtde FROM est_produto WHERE ifnull(json_data->>"$.porcent_preench",\'null\') != \'null\' AND json_data->>"$.porcent_preench" BETWEEN 0.21 AND 0.30');
        $params['porcentPreench'][] = $conn->fetchAssoc('SELECT \'Entre 31 e 40%\' as msg, 31 as i, 40 as f, count(*) as qtde FROM est_produto WHERE ifnull(json_data->>"$.porcent_preench",\'null\') != \'null\' AND json_data->>"$.porcent_preench" BETWEEN 0.31 AND 0.40');
        $params['porcentPreench'][] = $conn->fetchAssoc('SELECT \'Entre 41 e 50%\' as msg, 41 as i, 50 as f, count(*) as qtde FROM est_produto WHERE ifnull(json_data->>"$.porcent_preench",\'null\') != \'null\' AND json_data->>"$.porcent_preench" BETWEEN 0.41 AND 0.50');
        $params['porcentPreench'][] = $conn->fetchAssoc('SELECT \'Entre 51 e 60%\' as msg, 51 as i, 60 as f, count(*) as qtde FROM est_produto WHERE ifnull(json_data->>"$.porcent_preench",\'null\') != \'null\' AND json_data->>"$.porcent_preench" BETWEEN 0.51 AND 0.60');
        $params['porcentPreench'][] = $conn->fetchAssoc('SELECT \'Entre 61 e 70%\' as msg, 61 as i, 70 as f, count(*) as qtde FROM est_produto WHERE ifnull(json_data->>"$.porcent_preench",\'null\') != \'null\' AND json_data->>"$.porcent_preench" BETWEEN 0.61 AND 0.70');
        $params['porcentPreench'][] = $conn->fetchAssoc('SELECT \'Entre 71 e 80%\' as msg, 71 as i, 80 as f, count(*) as qtde FROM est_produto WHERE ifnull(json_data->>"$.porcent_preench",\'null\') != \'null\' AND json_data->>"$.porcent_preench" BETWEEN 0.71 AND 0.80');
        $params['porcentPreench'][] = $conn->fetchAssoc('SELECT \'Entre 81 e 90%\' as msg, 81 as i, 90 as f, count(*) as qtde FROM est_produto WHERE ifnull(json_data->>"$.porcent_preench",\'null\') != \'null\' AND json_data->>"$.porcent_preench" BETWEEN 0.81 AND 0.90');
        $params['porcentPreench'][] = $conn->fetchAssoc('SELECT \'Entre 91 e 99%\' as msg, 91 as i, 99 as f, count(*) as qtde FROM est_produto WHERE ifnull(json_data->>"$.porcent_preench",\'null\') != \'null\' AND json_data->>"$.porcent_preench" BETWEEN 0.91 AND 0.99');
        $params['porcentPreench'][] = $conn->fetchAssoc('SELECT \'100%\' as msg, 100 as i, 100 as f, count(*) as qtde FROM est_produto WHERE ifnull(json_data->>"$.porcent_preench",\'null\') != \'null\' AND json_data->>"$.porcent_preench" = 1');

        $qtdeProdutosComTitulo = $conn->fetchAssoc('SELECT count(*) as qtde FROM est_produto WHERE json_data->>"$.titulo" != \'null\' AND trim(json_data->>"$.titulo") != \'\'');
        $params['qtdeProdutosComTitulo'] = $qtdeProdutosComTitulo['qtde'];

        $qtdeProdutosComTituloESemFoto = $conn->fetchAssoc('SELECT count(*) as qtde FROM est_produto WHERE json_data->>"$.titulo" != \'null\' AND trim(json_data->>"$.titulo") != \'\' AND IFNULL(json_data->>"$.qtde_imagens", 0) = 0');
        $params['qtdeProdutosComTituloESemFoto'] = $qtdeProdutosComTituloESemFoto['qtde'];

        $params['hoje'] = $hoje;

        return $this->doRender('/Estoque/dashboardEstoque.html.twig', $params);
    }


    /**
     * @Route("/est/produto/migrar", name="est_produto_migrar")
     *
     * @param Request $request
     * @return Response
     *
     * @IsGranted("ROLE_ESTOQUE", statusCode=403)
     * @throws \Exception
     */
    public function migrar(Request $request): Response
    {
        // @Route("/est/produto/migrar", name="est_produto_migrar")
        try {
            $this->logger->error('not an error');
            /** @var Connection $conn */
            $conn = $this->getDoctrine()->getConnection();
            $conn->beginTransaction();

            $produtos = $conn->fetchAll('SELECT p.*, u.label as unidade FROM vw_rdp_est_produto p, est_unidade_produto u WHERE p.unidade_produto_id = u.id ORDER BY id ');

            // $json_metadata = $conn->fetchAssoc('SELECT valor FROM cfg_app_config WHERE chave = :chave', ['chave' => 'est_produto_json_metadata']);

            $qryAtributosProduto = $conn->prepare('SELECT a.id, a.label, a.tipo, a.config, a.descricao, pa.valor 
                FROM est_atributo a LEFT JOIN est_produto_atributo pa ON pa.atributo_id = a.id 
                WHERE pa.produto_id = :produto_id ORDER BY pa.ordem');

            $linha = 2;
            $qtdeProdutos = 0;

            $atrs[10] = 'ano';
            $atrs[6] = 'caracteristicas';
            $atrs[13] = 'erp_codigo';
            $atrs[26] = 'cofins';
            $atrs[9] = 'compativel_com';
            $atrs[36] = 'promocao_de';
            $atrs[14] = 'dimensoes';
            $atrs[12] = 'erp_dt_ult_entrada';
            $atrs[11] = 'erp_dt_ult_saida';
            $atrs[7] = 'especif_tec';
            $atrs[33] = 'qtde_estoque_acessorios';
            $atrs[34] = 'qtde_estoque_matriz';
            $atrs[16] = 'qtde_estoque_min';
            $atrs[35] = 'qtde_estoque_total';
            $atrs[23] = 'icms';
            $atrs[21] = 'integr_ecommerce';
            $atrs[24] = 'pis';
            $atrs[8] = 'itens_inclusos';
            $atrs[18] = 'marca';
            $atrs[27] = 'modelos';
            $atrs[29] = 'modelos_2';
            $atrs[31] = 'modelos_3';
            $atrs[19] = 'montadora';
            $atrs[28] = 'montadora_2';
            $atrs[30] = 'montadora_3';
            $atrs[17] = 'peso';
            $atrs[25] = 'pis';
            $atrs[37] = 'promocao_de';
            $atrs[5] = 'preco_acessorios';
            $atrs[4] = 'preco_atacado';
            $atrs[1] = 'preco_custo';
            $atrs[3] = 'preco_site';
            $atrs[2] = 'preco_tabela';
            $atrs[40] = 'promocao_qtde_parcelas';
            $atrs[32] = 'porcent_preench_campos_faltantes';
            $atrs[22] = 'st';
            $atrs[39] = 'promocao_dt_fim';
            $atrs[42] = 'promocao_parcelas_dt_fim';
            $atrs[38] = 'promocao_dt_ini';
            $atrs[41] = 'promocao_parcelas_dt_ini';
            $atrs[20] = 'video';

            $unidades = [
                1 => 'PC',
                3 => 'JG',
                4 => 'KIT',
                5 => 'MT',
                6 => 'PAR'
            ];


            foreach ($produtos as $produto) {
                $qtdeProdutos++;

                $json_data = [];
                $json_data['depto_id'] = $produto['depto_id'];
                $json_data['depto_codigo'] = $produto['depto_codigo'];
                $json_data['depto_nome'] = $produto['depto_nome'];
                $json_data['grupo_id'] = $produto['grupo_id'];
                $json_data['grupo_codigo'] = $produto['grupo_codigo'];
                $json_data['grupo_nome'] = $produto['grupo_nome'];
                $json_data['subgrupo_codigo'] = $produto['subgrupo_codigo'];
                $json_data['subgrupo_nome'] = $produto['subgrupo_nome'];
                $json_data['fornecedor_nome'] = $produto['fornecedor_nome'];
                $json_data['fornecedor_documento'] = $produto['fornecedor_documento'];
                $json_data['titulo'] = $produto['titulo'];
                $json_data['caracteristicas'] = $produto['caracteristicas'];
                $json_data['ean'] = $produto['ean'];
                $json_data['referencia'] = $produto['referencia'];
                $json_data['ncm'] = $produto['ncm'];
                $json_data['composicao'] = $produto['composicao'];
                $json_data['erp_codigo'] = $produto['codigo_from'];
                $json_data['porcent_preench'] = $produto['porcent_preench'];
                $json_data['qtde_estoque_matriz'] = $produto['saldo_estoque_matriz'];
                $json_data['qtde_estoque_acessorios'] = $produto['saldo_estoque_acessorios'];
                $json_data['qtde_estoque_total'] = $produto['saldo_estoque_total'];
                $json_data['qtde_imagens'] = $produto['qtde_imagens'];
                $json_data['imagem1'] = $produto['imagem1'];
                $json_data['unidade'] = $unidades[$produto['unidade_produto_id']];

                $qryAtributosProduto->bindParam('produto_id', $produto['id']);
                $qryAtributosProduto->execute();

                $atributos = $qryAtributosProduto->fetchAll();

                foreach ($atributos as $atributo) {
                    $json_data[$atrs[$atributo['id']]] = $atributo['valor'];
                }

                $nProduto['json_data'] = json_encode($json_data);

                $conn->update('est_produto', $nProduto, ['id' => $produto['id']]);
                $this->logger->info($linha++ . ' escrita(s)');
            }

            $conn->commit();
            return new Response('OK');

        } catch (\Exception | DBALException $e) {
            $conn->rollBack();
            $this->logger->error($e->getTraceAsString());
            throw new \RuntimeException($e->getMessage());
        }

    }


    /**
     *
     * @Route("/est/graficoTotalEstoquePorFilial/", name="est_graficoTotalEstoquePorFilial")
     * @param Request $request
     * @return JsonResponse
     *
     * @IsGranted("ROLE_RELVENDAS", statusCode=403)
     * @throws ViewException
     */
    public function graficoTotalEstoquePorFilial(Request $request): JsonResponse
    {
        /** @var ProdutoRepository $produtoRepository */
        $produtoRepository = $this->getDoctrine()->getRepository(Produto::class);
        $r = $produtoRepository->totalEstoquePorFilial();
        return new JsonResponse($r);
    }


}
