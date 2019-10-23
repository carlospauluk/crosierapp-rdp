<?php


namespace App\Controller\Estoque;


use CrosierSource\CrosierLibBaseBundle\Controller\BaseController;
use CrosierSource\CrosierLibBaseBundle\Utils\StringUtils\StringUtils;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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

    /** @var RegistryInterface */
    private $doctrine;

    /**
     * @required
     * @param RegistryInterface $doctrine
     */
    public function setDoctrine(RegistryInterface $doctrine): void
    {
        $this->doctrine = $doctrine;
    }


    /**
     *
     * @Route("/est/produto/exportarExcel/", name="est_produto_exportarExcel")
     * @return BinaryFileResponse
     * @IsGranted({"ROLE_ESTOQUE_ADMIN"}, statusCode=403)
     */
    public function exportarExcel(): BinaryFileResponse
    {

        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $col = 1;
            $sheet->setCellValue($this->excelCol($col++) . '1', 'Código');
            $sheet->setCellValue($this->excelCol($col++) . '1', 'Depto');
            $sheet->setCellValue($this->excelCol($col++) . '1', 'Grupo');
            $sheet->setCellValue($this->excelCol($col++) . '1', 'Subgrupo');
            $sheet->setCellValue($this->excelCol($col++) . '1', 'Fornecedor');
            $sheet->setCellValue($this->excelCol($col++) . '1', 'Nome');
            $sheet->setCellValue($this->excelCol($col++) . '1', 'Título');
            $sheet->setCellValue($this->excelCol($col++) . '1', 'Características');
            $sheet->setCellValue($this->excelCol($col++) . '1', 'EAN');
            $sheet->setCellValue($this->excelCol($col++) . '1', 'Referência');
            $sheet->setCellValue($this->excelCol($col++) . '1', 'NCM');
            $sheet->setCellValue($this->excelCol($col++) . '1', 'Status');
            $sheet->setCellValue($this->excelCol($col++) . '1', 'Unidade');
            $sheet->setCellValue($this->excelCol($col++) . '1', 'Código From');
            $sheet->setCellValue($this->excelCol($col++) . '1', 'Status Cad');
            $sheet->setCellValue($this->excelCol($col++) . '1', 'Atualizado');

            $conn = $this->doctrine->getEntityManager()->getConnection();

            $qryProdutos = $conn->query('SELECT * FROM est_produto ORDER BY id');

            $linha = 2;
            while ($produto = $qryProdutos->fetch()) {
                $col = 1;
                $sheet->setCellValue($this->excelCol($col++) . $linha, $produto['id']);
                $sheet->setCellValue($this->excelCol($col++) . $linha, $produto['depto_codigo'] . ' - ' . $produto['depto_nome']);
                $sheet->setCellValue($this->excelCol($col++) . $linha, $produto['grupo_codigo'] . ' - ' . $produto['grupo_nome']);
                $sheet->setCellValue($this->excelCol($col++) . $linha, $produto['subgrupo_codigo'] . ' - ' . $produto['subgrupo_nome']);
                $sheet->setCellValue($this->excelCol($col++) . $linha, $produto['fornecedor_nome']);
                $sheet->setCellValue($this->excelCol($col++) . $linha, $produto['nome']);
                $sheet->setCellValue($this->excelCol($col++) . $linha, $produto['titulo']);
                $sheet->setCellValue($this->excelCol($col++) . $linha, $produto['caracteristicas']);
                $sheet->setCellValue($this->excelCol($col++) . $linha, $produto['ean']);
                $sheet->setCellValue($this->excelCol($col++) . $linha, $produto['referencia']);
                $sheet->setCellValue($this->excelCol($col++) . $linha, $produto['ncm']);
                $sheet->setCellValue($this->excelCol($col++) . $linha, $produto['status']);
                $sheet->setCellValue($this->excelCol($col++) . $linha, $produto['unidade']);
                $sheet->setCellValue($this->excelCol($col++) . $linha, $produto['codigo_from']);
                $sheet->setCellValue($this->excelCol($col++) . $linha, (float)$produto['porcent_preench'] * 100);
                $sheet->setCellValue($this->excelCol($col++) . $linha, $produto['updated']);
                $linha++;
            }


            //        foreach ($todos as $produto) {
            //
            //        }
            $writer = new Xlsx($spreadsheet);
            $outputFile = $_SERVER['PASTA_ESTOQUE_PRODUTOS_EXCEL'] . StringUtils::guidv4() . '.xlsx';
            $writer->save($outputFile);
            return $this->file($outputFile, 'produtos.xlsx', ResponseHeaderBag::DISPOSITION_INLINE);
        } catch (Exception $e) {
            throw new \RuntimeException('Erro ao gerar arquivo xlsx');
        }
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