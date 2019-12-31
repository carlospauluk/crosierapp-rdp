<?php


namespace App\Controller\Estoque;


use App\Business\Estoque\ProdutoBusiness;
use App\Entity\Estoque\Produto;
use App\Repository\Estoque\ProdutoRepository;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Twig\FilterInput;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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

    /** @var ProdutoBusiness */
    private $produtoBusiness;

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
     * @return Response
     * @IsGranted("ROLE_ESTOQUE", statusCode=403)
     */
    public function exportarExcel(): Response
    {
        $nomeArquivo = 'produtos.xlsx';
        $outputFile = $_SERVER['PASTA_ESTOQUE_PRODUTOS_EXCEL'] . $nomeArquivo;
        @unlink($outputFile);
        $params = $this->produtoBusiness->gerarExcel();
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
            new FilterData(['codigoFrom'], 'EQ', 'codigoFrom', $params),
            new FilterData(['nome', 'titulo'], 'LIKE', 'nomeTitulo', $params),
            new FilterData(['nomeDepto'], 'LIKE', 'nomeDepto', $params),
            new FilterData(['porcentPreench'], 'BETWEEN_PORCENT', 'porcentPreench', $params),
            new FilterData(['qtdeImagens'], 'EQ', 'qtdeImagens', $params),
            new FilterData(['titulo'], 'IS_NOT_EMPTY', 'tituloIsNotEmpty', $params),
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
            'formUrl' => $_SERVER['CROSIERAPPVENDEST_URL'] . '/est/produto/form',
            'listId' => 'produto_list'
        ];
        $params['filterInputs'] = [
            new FilterInput('Código', 'id'),
            new FilterInput('Código (ERP)', 'codigoFrom'),
            new FilterInput('Nome/Título/Código', 'nomeTitulo'),
            new FilterInput('Depto', 'nomeDepto'),
            new FilterInput('Status Cad', 'porcentPreench', 'BETWEEN_INTEGER', null, ['sufixo' => '%']),
            new FilterInput('Qtde Imagens', 'qtdeImagens', 'INTEGER'),
            new FilterInput('', 'tituloIsNotEmpty', 'HIDDEN'),
        ];
        $params['listAuxDatas'] = json_encode(['crosierAppVendestUrl' => $_SERVER['CROSIERAPPVENDEST_URL']]);
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
     * @return Response
     *
     * @IsGranted("ROLE_ESTOQUE", statusCode=403)
     * @throws \Exception
     */
    public function dashboardEstoque(Request $request): Response
    {
        $hoje = (new \DateTime())->format('d/m/Y');

        /** @var ProdutoRepository $repoProdutos */
        $repoProdutos = $this->getDoctrine()->getRepository(Produto::class);

        $deptos = $repoProdutos->findDeptos();

        foreach ($deptos as $depto) {
            $qtde = $repoProdutos->count(['nomeDepto' => $depto['deptoNome']]);
            $params['deptos'][$depto['deptoNome']] = $qtde;
        }


        $qtde = $repoProdutos->doCountByFiltersSimpl([['porcentPreench', 'EQ', 0]]);
        $params['porcentPreench'][0] = $qtde;
        for ($i = 1; $i < 100; $i += 10) {
            $qtde = $repoProdutos->doCountByFiltersSimpl([['porcentPreench', 'BETWEEN', [$i / 100, ($i + 9) / 100]]]);
            $params['porcentPreench'][$i] = $qtde;
        }


        $qtdeProdutosComTitulo = $repoProdutos->doCountByFiltersSimpl([['titulo', 'IS_NOT_EMPTY']]);
        $params['qtdeProdutosComTitulo'] = $qtdeProdutosComTitulo;


        $qtdeProdutosComTituloESemFoto = $repoProdutos->doCountByFiltersSimpl([['titulo', 'IS_NOT_EMPTY'], ['qtdeImagens', 'EQ', 0]]);
        $params['qtdeProdutosComTituloESemFoto'] = $qtdeProdutosComTituloESemFoto;

        $params['hoje'] = $hoje;

        return $this->doRender('/Estoque/dashboardEstoque.html.twig', $params);

    }


}