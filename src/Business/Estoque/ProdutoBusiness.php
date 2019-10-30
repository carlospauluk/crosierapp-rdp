<?php


namespace App\Business\Estoque;


use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Controller auxiliar ao ProdutoController
 *
 *
 * @package App\Controller\Estoque
 */
class ProdutoBusiness
{

    /** @var LoggerInterface */
    private $logger;

    /** @var RegistryInterface */
    private $doctrine;

    /**
     * @required
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @required
     * @param RegistryInterface $doctrine
     */
    public function setDoctrine(RegistryInterface $doctrine): void
    {
        $this->doctrine = $doctrine;
    }


    public function gerarExcel(): array
    {

        try {
//            $pool = new \Cache\Adapter\Apcu\ApcuCachePool();
//            $simpleCache = new \Cache\Bridge\SimpleCache\SimpleCacheBridge($pool);
//
//            \PhpOffice\PhpSpreadsheet\Settings::setCache($simpleCache);


            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();


//            $col = 1;
//            $sheet->setCellValue($this->excelCol($col++) . '1', 'Código');
//            $sheet->setCellValue($this->excelCol($col++) . '1', 'Depto');
//            $sheet->setCellValue($this->excelCol($col++) . '1', 'Grupo');
//            $sheet->setCellValue($this->excelCol($col++) . '1', 'Subgrupo');
//            $sheet->setCellValue($this->excelCol($col++) . '1', 'Fornecedor');
//            $sheet->setCellValue($this->excelCol($col++) . '1', 'Nome');
//            $sheet->setCellValue($this->excelCol($col++) . '1', 'Título');
//            $sheet->setCellValue($this->excelCol($col++) . '1', 'Características');
//            $sheet->setCellValue($this->excelCol($col++) . '1', 'EAN');
//            $sheet->setCellValue($this->excelCol($col++) . '1', 'Referência');
//            $sheet->setCellValue($this->excelCol($col++) . '1', 'NCM');
//            $sheet->setCellValue($this->excelCol($col++) . '1', 'Status');
//            $sheet->setCellValue($this->excelCol($col++) . '1', 'Unidade');
//            $sheet->setCellValue($this->excelCol($col++) . '1', 'Código From');
//            $sheet->setCellValue($this->excelCol($col++) . '1', 'Status Cad');
//            $sheet->setCellValue($this->excelCol($col++) . '1', 'Atualizado');
//
//
//
//
//            $qryAtributos = $conn->query('SELECT a.id, a.label, a.tipo, a.config FROM est_atributo a, est_produto_atributo pa WHERE pa.atributo_id = a.id GROUP BY a.id, a.label, a.tipo');
//            $ordemAtributos = [];
//            while ($atributo = $qryAtributos->fetch()) {
//                $ordemAtributos[$atributo['id']] = $col;
//                if ($atributo['tipo'] === 'COMPO') {
//                    $subcampos = explode('|', $atributo['config']);
//                    foreach ($subcampos as $subcampo) {
//                        $subCampo_configs = explode(',', $subcampo);
//                        $sheet->setCellValue($this->excelCol($col++) . '1', $atributo['label'] . ' (' . $subCampo_configs[0] . ') ' . $subCampo_configs[2]);
//                    }
//                } else {
//                    $sheet->setCellValue($this->excelCol($col) . '1', $atributo['label']);
//                    $col++;
//                }
//            }


            $titulos[] = 'Atualizado';
            $titulos[] = 'Código';
            $titulos[] = 'Unidade';
            $titulos[] = 'Status Cad';
            $titulos[] = 'Nome';
            $titulos[] = 'Título';
            $titulos[] = 'Depto';
            $titulos[] = 'Grupo';
            $titulos[] = 'Subgrupo';
            $titulos[] = 'Fornecedor';
            $titulos[] = 'Estoque Deposito';
            $titulos[] = 'Estoque Matriz';
            $titulos[] = 'Estoque Acessorios';
            $titulos[] = 'Total Estoque ';
            $titulos[] = 'Preço Tabela';
            $titulos[] = 'Preço Site';
            $titulos[] = 'Preço Atacado';
            $titulos[] = 'Preço Acessórios ';
            $titulos[] = 'Características';
            $titulos[] = 'Especificações Técnicas';
            $titulos[] = 'Itens Inclusos';
            $titulos[] = 'EAN';
            $titulos[] = 'Referência';
            $titulos[] = 'Marca';
            $titulos[] = 'Vídeo';
            $titulos[] = 'Compatível com';
            $titulos[] = 'Ano';
            $titulos[] = 'Montadora';
            $titulos[] = 'Modelos';
            $titulos[] = 'Montadora (2)';
            $titulos[] = 'Modelos (2)';
            $titulos[] = 'Montadora (3)';
            $titulos[] = 'Modelos (3)';
            $titulos[] = 'Status';
            $titulos[] = 'Altura';
            $titulos[] = 'Largura';
            $titulos[] = 'Profundidade';
            $titulos[] = 'Peso';
            $titulos[] = 'Integr E-commerce';
            $titulos[] = 'Código ERP';
            $titulos[] = 'NCM';
            $titulos[] = 'Preço Custo';
            $titulos[] = 'ST';
            $titulos[] = 'ICMS';
            $titulos[] = 'IPI';
            $titulos[] = 'PIS';
            $titulos[] = 'COFINS';
            $titulos[] = 'Dt Últ Saída';
            $titulos[] = 'Dt Últ Entrada';


            $conn = $this->doctrine->getEntityManager()->getConnection();

            $qryProdutos = $conn->query('SELECT p.*, u.label as unidade FROM est_produto p, est_unidade_produto u WHERE p.unidade_produto_id = u.id ORDER BY id ');


            $qryAtributosProduto = $conn->prepare('SELECT a.id, a.label, a.tipo, a.config, a.descricao, pa.valor FROM est_atributo a, est_produto_atributo pa WHERE pa.atributo_id = a.id AND pa.produto_id = :produto_id ORDER BY pa.ordem');

            $linha = 2;
            $qtdeProdutos = 0;


            $dados[] = $titulos;

            while ($produto = $qryProdutos->fetch()) {
                $qtdeProdutos++;

                $atributosProduto = [];
                $qryAtributosProduto->bindValue('produto_id', $produto['id']);
                $qryAtributosProduto->execute();
                while ($atributo = $qryAtributosProduto->fetch()) {
                    if ($atributo['tipo'] === 'COMPO') {
                        $subcampos = explode('|', $atributo['valor']);
                        $subCampo_configs = explode('|', $atributo['config']);
                        foreach ($subcampos as $key => $subcampo) {
                            $labelSubCampoConfigs = explode(',', $subCampo_configs[$key]);
                            $atributosProduto[$atributo['label'] . '_' . $labelSubCampoConfigs[0]] = $subcampo;
                        }
                    } else {
                        $atributosProduto[$atributo['label']] = $atributo['valor'];
                    }
                }


                $r = [];

                $r[] = DateTimeUtils::parseDateStr($produto['updated'])->format('d/m/Y H:i:s');
                $r[] = $produto['id'];
                $r[] = $produto['unidade'];
                $r[] = bcmul((float)$produto['porcent_preench'], 100, 2);
                $r[] = $produto['nome'];
                $r[] = $produto['titulo'];
                $r[] = $produto['depto_codigo'] . ' - ' . $produto['depto_nome'];
                $r[] = $produto['grupo_codigo'] . ' - ' . $produto['grupo_nome'];
                $r[] = $produto['subgrupo_codigo'] . ' - ' . $produto['subgrupo_nome'];
                $r[] = $produto['fornecedor_nome'];
                $r[] = 0; // $produto['estoque_deposito'];
                $r[] = 0; // $produto['estoque_matriz'];
                $r[] = 0; // $produto['estoque_acessorios'];
                $r[] = 0; // $produto['total_estoque'];
                $r[] = $atributosProduto['Preço Tabela'] ?? '';
                $r[] = $atributosProduto['Preço Site'] ?? '';
                $r[] = $atributosProduto['Preço Atacado'] ?? '';
                $r[] = $atributosProduto['Preço Acessórios'] ?? '';
                $r[] = $produto['caracteristicas'] ? 'Sim' : 'Não';
                $r[] = ($atributosProduto['Especificações Técnicas'] ?? null) ? 'Sim' : 'Não';
                $r[] = ($atributosProduto['Itens Inclusos'] ?? null) ? 'Sim' : 'Não';
                $r[] = $produto['ean'];
                $r[] = $produto['referencia'];
                $r[] = $atributosProduto['Marca'] ?? '';
                $r[] = $atributosProduto['Vídeo'] ?? '';
                $r[] = ($atributosProduto['Compatível com'] ?? null) ? 'Sim' : 'Não';
                $r[] = $atributosProduto['Ano'] ?? '';
                $r[] = $atributosProduto['Montadora'] ?? '';
                $r[] = $atributosProduto['Modelos'] ?? '';
                $r[] = $atributosProduto['Montadora (2)'] ?? '';
                $r[] = $atributosProduto['Modelos (2)'] ?? '';
                $r[] = $atributosProduto['Montadora (3)'] ?? '';
                $r[] = $atributosProduto['Modelos (3)'] ?? '';
                $r[] = $produto['status'];
                $r[] = $atributosProduto['Dimensões_A'] ?? '';
                $r[] = $atributosProduto['Dimensões_L'] ?? '';
                $r[] = $atributosProduto['Dimensões_C'] ?? '';
                $r[] = $atributosProduto['Peso'] ?? '';
                $r[] = $atributosProduto['Integr E-commerce'] ?? '';
                $r[] = $produto['codigo_from'];
                $r[] = $produto['ncm'];
                $r[] = $atributosProduto['Preço Custo'] ?? '';
                $r[] = $atributosProduto['ST'] ?? '';
                $r[] = $atributosProduto['ICMS'] ?? '';
                $r[] = $atributosProduto['IPI'] ?? '';
                $r[] = $atributosProduto['PIS'] ?? '';
                $r[] = $atributosProduto['COFINS'] ?? '';
                $r[] = $atributosProduto['Dt Últ Saída'] ?? '';
                $r[] = $atributosProduto['Dt Últ Entrada'] ?? '';

                $dados[] = $r;
                $this->logger->info($linha++ . ' escrita(s)');
            }

            $sheet->fromArray($dados);
            //        foreach ($todos as $produto) {
            //
            //        }
            $writer = new Xlsx($spreadsheet);
            $nomeArquivo = 'produtos.xlsx';
            $outputFile = $_SERVER['PASTA_ESTOQUE_PRODUTOS_EXCEL'] . $nomeArquivo;
            $writer->save($outputFile);
            $params['arquivo'] = $_SERVER['CROSIERAPPRDP_URL'] . $_SERVER['PASTA_ESTOQUE_PRODUTOS_EXCEL_DOWNLOAD'] . $nomeArquivo;
            $params['qtdeProdutos'] = $qtdeProdutos;
            return $params;
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

    /**
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function atualizarCamposEstoqueProdutoPelaRelEstoque01(): void
    {
        $conn = $this->doctrine->getEntityManager()->getConnection();

        $qryProdutos = $conn->query('SELECT * FROM est_produto');

        $qryRelEstoque01 = $conn->prepare('SELECT qtde_atual FROM rdp_rel_estoque01 WHERE cod_prod = :cod_prod AND desc_filial = :desc_filial');


        $qryAtributo = $conn->prepare('SELECT id FROM est_atributo WHERE uuid = :uuid');

        $qryAtributo->bindValue('uuid', 'c37e9985-53f2-47f4-833a-52ace1f84e60');
        $qryAtributo->execute();
        $atrEstoqueAcessorios = $qryAtributo->fetch();

        $qryAtributo->bindValue('uuid', '3edb71db-375d-4d37-b36d-8287f291606b');
        $qryAtributo->execute();
        $atrEstoqueMatriz = $qryAtributo->fetch();

        $qryAtributo->bindValue('uuid', '8f25a3e6-cf93-4111-be2b-a46dedc30107');
        $qryAtributo->execute();
        $atrEstoqueTotal = $qryAtributo->fetch();

        while ($produto = $qryProdutos->fetch()) {

            $this->verificaEInsereProdutoPossuiAtributosEstoques($produto, $atrEstoqueMatriz['id'], 1);
            $this->verificaEInsereProdutoPossuiAtributosEstoques($produto, $atrEstoqueAcessorios['id'], 2);
            $this->verificaEInsereProdutoPossuiAtributosEstoques($produto, $atrEstoqueTotal['id'], 3);

            $qryRelEstoque01->bindValue('cod_prod', $produto['codigo_from']);
            $qryRelEstoque01->bindValue('desc_filial', 'MATRIZ');
            $qryRelEstoque01->execute();
            $matriz = $qryRelEstoque01->fetch();

            $total = 0;

            if ($matriz) {
                $estAtributoProduto['valor'] = $matriz['qtde_atual'];
                $total += (float)$matriz['qtde_atual'];
                $conn->update('est_produto_atributo', $estAtributoProduto,
                    [
                        'produto_id' => $produto['id'],
                        'atributo_id' => $atrEstoqueMatriz['id']
                    ]);
            }

            $qryRelEstoque01->bindValue('cod_prod', $produto['codigo_from']);
            $qryRelEstoque01->bindValue('desc_filial', 'ACESSÓRIOS');
            $qryRelEstoque01->execute();
            $acessorios = $qryRelEstoque01->fetch();

            if ($acessorios) {
                $estAtributoProduto['valor'] = $acessorios['qtde_atual'];
                $total += (float)$acessorios['qtde_atual'];
                $conn->update('est_produto_atributo', $estAtributoProduto,
                    [
                        'produto_id' => $produto['id'],
                        'atributo_id' => $atrEstoqueAcessorios['id']
                    ]);
            }

            if ($total) {
                $estAtributoProduto['valor'] = $total;
                $conn->update('est_produto_atributo', $estAtributoProduto,
                    [
                        'produto_id' => $produto['id'],
                        'atributo_id' => $atrEstoqueTotal['id']
                    ]);
            }


        }

    }

    /**
     * @param array $produto
     * @param int $atributoId
     * @param int $ordem
     * @throws \Doctrine\DBAL\DBALException
     */
    private function verificaEInsereProdutoPossuiAtributosEstoques(array $produto, int $atributoId, int $ordem): void
    {
        $conn = $this->doctrine->getEntityManager()->getConnection();
        $qryAtributoProduto = $conn->prepare('SELECT * FROM est_produto_atributo WHERE atributo_id = :atributo_id AND produto_id = :produto_id');

        $qryAtributoProduto->bindValue('atributo_id', $atributoId);
        $qryAtributoProduto->bindValue('produto_id', $produto['id']);
        $qryAtributoProduto->execute();
        if (!$qryAtributoProduto->fetch()) {
            $atributoEstoqueMatriz = [
                'produto_id' => $produto['id'],
                'atributo_id' => $atributoId,
                'aba' => 'Estoques',
                'grupo' => '',
                'ordem' => $ordem,
                'soma_preench' => 0,
                'quantif' => 'N',
                'precif' => 'N',
                'valor' => '',
                'inserted' => '1900-01-01 00:00:00',
                'updated' => '1900-01-01 00:00:00',
                'version' => '0',
                'estabelecimento_id' => '1',
                'user_inserted_id' => '1',
                'user_updated_id' => '1',
            ];
            $conn->insert('est_produto_atributo', $atributoEstoqueMatriz);
        }
    }


}