<?php


namespace App\Controller\Estoque;


use App\Business\Estoque\ProdutoBusiness;
use App\Entity\Estoque\Produto;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
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
     * @return \Symfony\Component\HttpFoundation\Response
     * @IsGranted({"ROLE_ESTOQUE_ADMIN"}, statusCode=403)
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
     * @param int $num
     * @return string
     */
    public function excelCol(int $num): string
    {
        $numeric = ($num - 1) % 26;
        $letter = chr(65 + $numeric);
        $num2 = (int)(($num - 1) / 26);
        if ($num2 > 0) {
            return $this->excelCol($num2) . $letter;
        }
        // else
        return $letter;
    }

    /**
     * @param array $params
     * @return array
     */
    public function getFilterDatas(array $params): array
    {
        return [
            new FilterData(['nome', 'titulo', 'id', 'codigoFrom'], 'LIKE', 'str', $params),
        ];
    }


    /**
     *
     * @Route("/est/produto/list/", name="est_produto_list")
     * @param Request $request
     * @return Response
     * @throws \Exception
     *
     * @IsGranted({"ROLE_ESTOQUE_ADMIN"}, statusCode=403)
     */
    public function list(Request $request): Response
    {
        $params = [
            'listView' => '@CrosierLibBase/list.html.twig',
            'listJS' => 'Estoque/produto_list.js',
            'listRoute' => 'est_produto_list',
            'listRouteAjax' => 'est_produto_datatablesJsList',
            'listPageTitle' => 'Produtos',
            'listId' => 'produto_list'
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
     * @IsGranted({"ROLE_ESTOQUE_ADMIN"}, statusCode=403)
     */
    public function datatablesJsList(Request $request): Response
    {
        return $this->doDatatablesJsList($request);
    }


    /**
     *
     * @Route("/moverImagens", name="moverImagens")
     * @param Request $request
     * @return Response
     * @throws ViewException
     *
     * @IsGranted({"ROLE_ESTOQUE_ADMIN"}, statusCode=403)
     */
    public function moverImagens(Request $request): Response
    {
        $conn = $this->getDoctrine()->getConnection();

        $crosierBaseDir = '/opt/crosier/';

        $qryProdutos = $conn->query('SELECT p.*, img.* FROM est_produto_imagem img JOIN est_produto p ON p.id = img.produto_id');
        while ($produto = $qryProdutos->fetch()) {
            $novoDir = $crosierBaseDir . 'crosierapp-vendest/public/images/produtos/' . $produto['depto_id'] . '/' . $produto['grupo_id'] . '/' . $produto['subgrupo_id'] . '/';
            $this->logger->info($novoDir);
            if (!file_exists($novoDir) && !mkdir($concurrentDirectory = $novoDir, 0777, true) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
            rename($crosierBaseDir . 'crosierapp-vendest/public/images/produtos/5d/' . $produto['image_name'], $concurrentDirectory . $produto['image_name']);

        }

        return new Response('OK');
    }






}