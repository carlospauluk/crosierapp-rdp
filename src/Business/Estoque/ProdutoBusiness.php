<?php


namespace App\Business\Estoque;


use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\AppConfigRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\StringUtils\StringUtils;
use CrosierSource\CrosierLibRadxBundle\Entity\Estoque\Produto;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Psr\Log\LoggerInterface;

/**
 * Controller auxiliar ao ProdutoController
 *
 *
 * @package App\Controller\Estoque
 */
class ProdutoBusiness
{

    private LoggerInterface $logger;

    private EntityManagerInterface $doctrine;

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
     * @param EntityManagerInterface $doctrine
     */
    public function setDoctrine(EntityManagerInterface $doctrine): void
    {
        $this->doctrine = $doctrine;
    }


    /**
     * Gera os dados tanto para Excel quanto para CSV.
     *
     * @param bool $apenasProdutosComTitulo
     * @return array
     * @throws ViewException
     */
    private function gerarDadosParaArquivo(bool $apenasProdutosComTitulo): array
    {
        try {
            /** @var Connection $conn */
            $conn = $this->doctrine->getConnection();

            $sqlTitulo = $apenasProdutosComTitulo ? 'AND IFNULL(p.json_data->>"$.titulo",\'null\') != \'null\'' : '';

            $produtos = $conn->fetchAll('SELECT p.* FROM est_produto p WHERE true ' . $sqlTitulo . ' ORDER BY id');

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
            $titulos[] = 'Qtde Imagens';
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

            $titulos[] = 'Estoque Matriz';
            $titulos[] = 'Estoque Acessórios';
            $titulos[] = 'Estoque Total';


            $linha = 2;
            $qtdeProdutos = 0;


            $dados[] = $titulos;

            /** @var AppConfigRepository $repoAppConfig */
            $repoAppConfig = $this->doctrine->getRepository(AppConfig::class);
            $jsonMetadata = json_decode($repoAppConfig->findOneBy(
                [
                    'appUUID' => $_SERVER['CROSIERAPPRADX_UUID'],
                    'chave' => 'est_produto_json_metadata'
                ]
            )->getValor(), true);


            /** @var Produto $produto */
            foreach ($produtos as $produto) {
                $qtdeProdutos++;
                $atributosProduto = [];

                foreach ($jsonMetadata['campos'] as $nomeDoCampo => $metadata) {
                    $val = json_decode($produto['json_data'], true)[$nomeDoCampo] ?? '';
                    if ($metadata['tipo'] === 'compo') {
                        $subcampos = explode('|', $val);
                        $subCampo_configs = explode('|', $metadata['formato']);
                        foreach ($subcampos as $k => $subcampo) {
                            $cfg = explode(',', $subCampo_configs[$k]);
                            $atributosProduto[$nomeDoCampo . '_' . $cfg[0]] = $subcampo;
                        }
                    } else {
                        $atributosProduto[$nomeDoCampo] = $val;
                    }
                }


                $r = [];

                $r[] = DateTimeUtils::parseDateStr($produto['updated'])->format('d/m/Y H:i:s');
                $r[] = $produto['id'];
                $r[] = $atributosProduto['unidade'];
                $r[] = bcmul((float)$atributosProduto['porcent_preench'], 100, 2);
                $r[] = $produto['nome'];
                $r[] = $atributosProduto['titulo'];
                $r[] = $atributosProduto['depto_codigo'] . ' - ' . $atributosProduto['depto_nome'];
                $r[] = $atributosProduto['grupo_codigo'] . ' - ' . $atributosProduto['grupo_nome'];
                $r[] = $atributosProduto['subgrupo_codigo'] . ' - ' . $atributosProduto['subgrupo_nome'];
                $r[] = $atributosProduto['fornecedor_nome'];
                $r[] = $atributosProduto['preco_tabela'] ?? '';
                $r[] = $atributosProduto['preco_site'] ?? '';
                $r[] = $atributosProduto['preco_atacado'] ?? '';
                $r[] = $atributosProduto['preco_acessorios'] ?? '';
                $r[] = $atributosProduto['caracteristicas'] ? 'Sim' : 'Não';
                $r[] = ($atributosProduto['especif_tec'] ?? null) ? 'Sim' : 'Não';
                $r[] = ($atributosProduto['itens_inclusos'] ?? null) ? 'Sim' : 'Não';
                $r[] = $atributosProduto['ean'];
                $r[] = $atributosProduto['referencia'];
                $r[] = $atributosProduto['marca'] ?? '';
                $r[] = $atributosProduto['video_url'] ?? '';
                $r[] = ($atributosProduto['compativel_com'] ?? null) ? 'Sim' : 'Não';
                $r[] = $atributosProduto['ano'] ?? '';
                $r[] = $atributosProduto['montadora'] ?? '';
                $r[] = $atributosProduto['modelos'] ?? '';
                $r[] = $atributosProduto['montadora_2'] ?? '';
                $r[] = $atributosProduto['modelos_2'] ?? '';
                $r[] = $atributosProduto['montadora_3'] ?? '';
                $r[] = $atributosProduto['modelos_3'] ?? '';
                $r[] = $produto['status'];
                $r[] = $atributosProduto['dimensoes_A'] ?? '';
                $r[] = $atributosProduto['dimensoes_L'] ?? '';
                $r[] = $atributosProduto['dimensoes_C'] ?? '';
                $r[] = $atributosProduto['peso'] ?? '';
                $r[] = $atributosProduto['qtde_imagens'] ?? '';
                $r[] = $atributosProduto['integr_ecommerce'] ?? '';
                $r[] = $atributosProduto['erp_codigo'];
                $r[] = $atributosProduto['ncm'];
                $r[] = $atributosProduto['preco_custo'] ?? '';
                $r[] = $atributosProduto['st'] ?? '';
                $r[] = $atributosProduto['icms'] ?? '';
                $r[] = $atributosProduto['ipi'] ?: '0';
                $r[] = $atributosProduto['pis'] ?? '';
                $r[] = $atributosProduto['cofins'] ?? '';
                $r[] = $atributosProduto['erp_dt_ult_saida'] ?? '';
                $r[] = $atributosProduto['erp_dt_ult_entrada'] ?? '';

                $r[] = $atributosProduto['qtde_estoque_matriz'] ?? '';
                $r[] = $atributosProduto['qtde_estoque_acessorios'] ?? '';
                $r[] = $atributosProduto['qtde_estoque_total'] ?? '';

                $dados[] = $r;
                $this->logger->info($linha++ . ' escrita(s)');
            }

            return $dados;
        } catch (Exception | DBALException $e) {
            $this->logger->error('Erro ao gerar arquivo xlsx');
            $this->logger->error($e->getMessage());
            throw new ViewException('Erro ao gerar arquivo xlsx');
        }
    }

    /**
     * @param bool $apenasProdutosComTitulo
     * @return array
     * @throws ViewException
     */
    public function gerarExcel(bool $apenasProdutosComTitulo): array
    {

        try {
            $dados = $this->gerarDadosParaArquivo($apenasProdutosComTitulo);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->fromArray($dados);
            $writer = new Xlsx($spreadsheet);
            $nomeArquivo = StringUtils::guidv4() . '_produtos.xlsx';
            $outputFile = $_SERVER['PASTA_ESTOQUE_PRODUTOS_EXCEL'] . $nomeArquivo;
            $writer->save($outputFile);
            $params['arquivo'] = $_SERVER['CROSIERAPPRDP_URL'] . $_SERVER['PASTA_ESTOQUE_PRODUTOS_EXCEL_DOWNLOAD'] . $nomeArquivo;
            $params['qtdeProdutos'] = count($dados) - 1;
            return $params;
        } catch (\Exception $e) {
            if ($e instanceof ViewException) {
                throw $e;
            }
            $this->logger->error('Erro ao gerar arquivo xlsx');
            $this->logger->error($e->getMessage());
            throw new ViewException('Erro ao gerar arquivo xlsx');
        }
    }

    /**
     * @param bool $apenasProdutosComTitulo
     * @return array
     * @throws ViewException
     */
    public function gerarCSV(bool $apenasProdutosComTitulo): array
    {
        try {
            $dados = $this->gerarDadosParaArquivo($apenasProdutosComTitulo);

            $nomeArquivo = StringUtils::guidv4() . '_produtos.csv';
            $outputFile = $_SERVER['PASTA_ESTOQUE_PRODUTOS_EXCEL'] . $nomeArquivo;

            $fp = fopen($outputFile, 'w');
            foreach ($dados as $linha) {
                fputcsv($fp, $linha, ';');
            }
            fclose($fp);

            $params['arquivo'] = $_SERVER['CROSIERAPPRDP_URL'] . $_SERVER['PASTA_ESTOQUE_PRODUTOS_EXCEL_DOWNLOAD'] . $nomeArquivo;
            $params['qtdeProdutos'] = count($dados) - 1;
            return $params;
        } catch (\Exception $e) {
            if ($e instanceof ViewException) {
                throw $e;
            }
            $this->logger->error('Erro ao gerar arquivo CSV');
            $this->logger->error($e->getMessage());
            throw new ViewException('Erro ao gerar arquivo CSV');
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
     * Utilizado no gráfico de Total de Estoque por Filial
     *
     * @return mixed
     * @throws ViewException
     */
    public function totalEstoquePorFilial()
    {
        try {
            $sql = 'SELECT 
                        SUM(json_data->>"$.qtde_estoque_matriz") as total_qtde_atual, 
                        SUM(json_data->>"$.qtde_estoque_matriz" * json_data->>"$.preco_tabela") as total_venda, 
                        SUM(json_data->>"$.qtde_estoque_matriz" * json_data->>"$.preco_custo") as total_custo_medio ' .
                'FROM est_produto';
            $conn = $this->doctrine->getConnection();
            $totalMatriz = $conn->fetchAssoc($sql);
            $sql = 'SELECT 
                        SUM(json_data->>"$.qtde_estoque_acessorios") as total_qtde_atual, 
                        SUM(json_data->>"$.qtde_estoque_acessorios" * json_data->>"$.preco_tabela") as total_venda, 
                        SUM(json_data->>"$.qtde_estoque_acessorios" * json_data->>"$.preco_custo") as total_custo_medio ' .
                'FROM est_produto';
            $conn = $this->doctrine->getConnection();
            $totalAcessorios = $conn->fetchAssoc($sql);
            $r = [
                [
                    'desc_filial' => 'MATRIZ',
                    'total_qtde_atual' => bcmul('1.0', $totalMatriz['total_qtde_atual'], 2),
                    'total_venda' => bcmul('1.0', $totalMatriz['total_venda'], 2),
                    'total_custo_medio' => bcmul('1.0', $totalMatriz['total_custo_medio'], 2),
                ],
                [
                    'desc_filial' => 'ACESSORIOS',
                    'total_qtde_atual' => bcmul('1.0', $totalAcessorios['total_qtde_atual'], 2),
                    'total_venda' => bcmul('1.0', $totalAcessorios['total_venda'], 2),
                    'total_custo_medio' => bcmul('1.0', $totalAcessorios['total_custo_medio'], 2),
                ],
            ];
            return $r;
        } catch (DBALException $e) {
            throw new ViewException('Erro ao gerar totalEstoquePorFilial()');
        }
    }


    public function getProdutoByCodigo($codigo)
    {
        $conn = $this->doctrine->getConnection();
        $sql = 'SELECT * FROM est_produto WHERE json_data->>"$.erp_codigo" = :codigo';
        return $conn->fetchAssoc($sql, ['codigo' => $codigo]);
    }


}