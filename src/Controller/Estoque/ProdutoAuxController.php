<?php


namespace App\Controller\Estoque;


use App\Business\Estoque\ProdutoBusiness;
use CrosierSource\CrosierLibBaseBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller auxiliar ao ProdutoController
 *
 * Class ProdutoAuxController
 *
 * @package App\Controller\Estoque
 */
class ProdutoAuxController extends BaseController
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


}