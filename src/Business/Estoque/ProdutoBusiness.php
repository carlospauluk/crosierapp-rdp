<?php


namespace App\Business\Estoque;


use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\AppConfigRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\StringUtils\StringUtils;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Controller auxiliar ao ProdutoController
 *
 * @author Carlos Eduardo Pauluk
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

            $conn = $this->doctrine->getConnection();

            $sqlTitulo = $apenasProdutosComTitulo ? 'AND IFNULL(p.json_data->>"$.titulo",\'null\') != \'null\'' : '';

            $produtos = $conn->fetchAllAssociative('SELECT p.*, f.documento as fornecedor_documento, f.nome as fornecedor_nome FROM est_produto p, est_fornecedor f WHERE p.fornecedor_id = f.id ' . $sqlTitulo . ' ORDER BY id LIMIT 10');

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

            $titulos[] = 'Descrição';
            $titulos[] = 'Características';
            $titulos[] = 'Especificações Técnicas';
            $titulos[] = 'Itens Inclusos';
            $titulos[] = 'Compatível com';

            $titulos[] = 'EAN';
            $titulos[] = 'Referência';
            $titulos[] = 'Marca';
            $titulos[] = 'Vídeo';

            $titulos[] = 'Ano';
            $titulos[] = 'Montadora';
            $titulos[] = 'Modelos';
            $titulos[] = 'Ano (2)';
            $titulos[] = 'Montadora (2)';
            $titulos[] = 'Modelos (2)';
            $titulos[] = 'Ano (3)';
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

            $rUnidades = $conn->fetchAllAssociative('SELECT id, label FROM est_unidade');
            $unidades = [];
            foreach ($rUnidades as $rUnidade) {
                $unidades[$rUnidade['id']] = $rUnidade['label'];
            }


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
                $r[] = $unidades[$produto['unidade_padrao_id']];
                $r[] = bcmul((float)$atributosProduto['porcent_preench'], 100, 2);
                $r[] = $produto['nome'];
                $r[] = $atributosProduto['titulo'];
                $r[] = $atributosProduto['depto_codigo'] . ' - ' . $atributosProduto['depto_nome'];
                $r[] = $atributosProduto['grupo_codigo'] . ' - ' . $atributosProduto['grupo_nome'];
                $r[] = $atributosProduto['subgrupo_codigo'] . ' - ' . $atributosProduto['subgrupo_nome'];
                $r[] = StringUtils::mascararCnpjCpf($produto['fornecedor_documento']) . ' - ' . $produto['fornecedor_nome'];
                $r[] = $atributosProduto['preco_tabela'] ?? '';
                $r[] = $atributosProduto['preco_site'] ?? '';
                $r[] = $atributosProduto['preco_atacado'] ?? '';
                $r[] = $atributosProduto['preco_acessorios'] ?? '';

                $r[] = $atributosProduto['descricao_produto'] ? 'Sim' : 'Não';
                $r[] = $atributosProduto['caracteristicas'] ? 'Sim' : 'Não';
                $r[] = $atributosProduto['especif_tec'] ? 'Sim' : 'Não';
                $r[] = $atributosProduto['itens_inclusos'] ? 'Sim' : 'Não';
                $r[] = $atributosProduto['compativel_com'] ? 'Sim' : 'Não';

                $r[] = $atributosProduto['ean'];
                $r[] = $atributosProduto['referencia'];
                $r[] = $atributosProduto['marca'] ?? '';
                $r[] = $atributosProduto['video_url'] ?? '';

                $r[] = $atributosProduto['ano'] ?? '';
                $r[] = $atributosProduto['montadora'] ?? '';
                $r[] = $atributosProduto['modelos'] ?? '';
                $r[] = $atributosProduto['ano_2'] ?? '';
                $r[] = $atributosProduto['montadora_2'] ?? '';
                $r[] = $atributosProduto['modelos_2'] ?? '';
                $r[] = $atributosProduto['ano_3'] ?? '';
                $r[] = $atributosProduto['montadora_3'] ?? '';
                $r[] = $atributosProduto['modelos_3'] ?? '';
                $r[] = $produto['status'];
                $r[] = $atributosProduto['dimensoes_A'] ?? '';
                $r[] = $atributosProduto['dimensoes_L'] ?? '';
                $r[] = $atributosProduto['dimensoes_C'] ?? '';
                $r[] = $atributosProduto['peso'] ?? '';
                $r[] = $atributosProduto['qtde_imagens'] ?? '';
                if (isset($atributosProduto['ecommerce_dt_integr'])) {
                    try {
                        $ecommerce_dt_integr = DateTimeUtils::parseDateStr($atributosProduto['ecommerce_dt_integr'])->format('d/m/Y H:i:s');
                        $r[] = $ecommerce_dt_integr;
                    } catch (\Throwable $e) {
                        $r[] = '';
                    }
                } else {
                    $r[] = '';
                }
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
        } catch (\Throwable $e) {
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
            $totalMatriz = $conn->fetchAssociative($sql);
            $sql = 'SELECT 
                        SUM(json_data->>"$.qtde_estoque_acessorios") as total_qtde_atual, 
                        SUM(json_data->>"$.qtde_estoque_acessorios" * json_data->>"$.preco_tabela") as total_venda, 
                        SUM(json_data->>"$.qtde_estoque_acessorios" * json_data->>"$.preco_custo") as total_custo_medio ' .
                'FROM est_produto';
            $conn = $this->doctrine->getConnection();
            $totalAcessorios = $conn->fetchAssociative($sql);
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
        } catch (\Throwable $e) {
            throw new ViewException('Erro ao gerar totalEstoquePorFilial()');
        }
    }


    public function getProdutoByCodigo($codigo)
    {
        $conn = $this->doctrine->getConnection();
        $sql = 'SELECT * FROM est_produto WHERE json_data->>"$.erp_codigo" = :codigo';
        return $conn->fetchAssociative($sql, ['codigo' => $codigo]);
    }


    /**
     * @param string|null $montadoraSel
     * @param string|null $anoSel
     * @param string|null $modeloSel
     * @return false|string
     * @throws ViewException
     */
    public function buildMontadorasAnosModelosSelect2(?string $montadoraSel = null, ?string $anoSel = null, ?string $modeloSel = null)
    {
        $sql = 'select distinct(montadora) from ' .
            '(select distinct(upper(json_data->>"$.montadora")) as montadora from est_produto union ' .
            'select distinct(upper(json_data->>"$.montadora_2")) as montadora from est_produto union ' .
            'select distinct(upper(json_data->>"$.montadora_3")) as montadora from est_produto) a ' .
            'where montadora is not null and montadora != \'\' and montadora != \'NULL\' order by montadora';

        $conn = $this->doctrine->getConnection();
        $rDistinctMontadora = $conn->fetchAllAssociative($sql);

        $montadoras = [];
        foreach ($rDistinctMontadora as $rdMontadoras) {
            $expl = explode(',', $rdMontadoras['montadora']);
            foreach ($expl as $v) {
                $montadoras[] = $v;
            }
        }
        $montadoras = array_unique($montadoras);

        $sMontadoras = [];

        $sMontadoras[0] = [
            'id' => '',
            'text' => 'Selecione...'
        ];


        $m = 1;
        foreach ($montadoras as $montadora) {

            if ($montadora === 'TODOS') continue;

            $sMontadoras[$m] = [
                'id' => $montadora,
                'text' => $montadora,
                'selected' => $montadora === $montadoraSel
            ];
            $sMontadoras[$m]['anos'][0] = [
                'id' => '%',
                'text' => 'TODOS'
            ];
            $sMontadoras[$m]['anos'][0]['modelos'][0] = [
                'id' => '%',
                'text' => 'TODOS'
            ];
            $rModelos = $this->getModelosByMontadoraEAno($montadora, '%');
            $modelosAnosMontadora = [];
            foreach ($rModelos as $rModelo) {
                $modelosAnosMontadora = array_unique(array_merge($modelosAnosMontadora, explode(',', $rModelo['modelos'])), SORT_REGULAR);
            }
            foreach ($modelosAnosMontadora as $modelo) {
                if ($modelo === 'TODOS') continue;
                if (!$modelo) continue;
                $sMontadoras[$m]['anos'][0]['modelos'][] = [
                    'id' => $modelo,
                    'text' => $modelo,
                    'selected' => $modelo == $modeloSel
                ];
            }


            $rAnos = $this->getAnosByMontadora($montadora);
            $anosMontadora = [];
            foreach ($rAnos as $rAno) {
                $anosMontadora = array_unique(array_merge($anosMontadora, explode(',', $rAno['ano'])), SORT_REGULAR);
            }
            $a = 1;
            foreach ($anosMontadora as $ano) {
                if ($ano === 'TODOS') continue;
                if (!$ano) continue;
                $sMontadoras[$m]['anos'][$a] = [
                    'id' => $ano,
                    'text' => $ano,
                    'selected' => $ano == $anoSel
                ];
                $sMontadoras[$m]['anos'][$a]['modelos'][] = [
                    'id' => '%',
                    'text' => 'TODOS'
                ];

                $rModelos = $this->getModelosByMontadoraEAno($montadora, $ano);
                $modelosAnosMontadora = [];
                foreach ($rModelos as $rModelo) {
                    $modelosAnosMontadora = array_unique(array_merge($modelosAnosMontadora, explode(',', $rModelo['modelos'])), SORT_REGULAR);
                }
                foreach ($modelosAnosMontadora as $modelo) {
                    if (!$modelo) continue;
                    $sMontadoras[$m]['anos'][$a]['modelos'][] = [
                        'id' => $modelo,
                        'text' => $modelo,
                        'selected' => $modelo == $modeloSel
                    ];
                }
                $a++;
            }
            $m++;
        }

        return json_encode($sMontadoras);

    }

    /**
     * @param string $montadora
     * @return mixed
     * @throws ViewException
     */
    private function getAnosByMontadora(string $montadora)
    {
        try {
            $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.produto.cache', 0, $_SERVER['CROSIER_SESSIONS_FOLDER']);
            $rAnos = $cache->get('produtoBusiness_getAnosByMontadora_' . $montadora, function (ItemInterface $item) use ($montadora) {

                $sql_anos = 'select ano from (
                select distinct(json_data->>"$.ano_3") as ano from est_produto where json_data->>"$.montadora_3" LIKE :montadora
                union 
                select distinct(json_data->>"$.ano_2") as ano from est_produto where json_data->>"$.montadora_2" LIKE :montadora
                union
                select distinct(json_data->>"$.ano") as ano from est_produto where json_data->>"$.montadora" LIKE :montadora
                ) a where ano is not null and ano != \'\' order by ano COLLATE utf8mb4_swedish_ci';

                $conn = $this->doctrine->getConnection();
                $rAnos = $conn->fetchAllAssociative($sql_anos, ['montadora' => '%' . $montadora . '%']);

                return $rAnos;
            });
            return $rAnos;
        } catch (InvalidArgumentException $e) {
            throw new ViewException('Erro ao obter getAnosByMontadora - ' . $montadora);
        }
    }

    /**
     * @param string $montadora
     * @param string $ano
     * @return mixed
     * @throws ViewException
     */
    private function getModelosByMontadoraEAno(string $montadora, string $ano)
    {
        try {
            $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.produto.cache', 0, $_SERVER['CROSIER_SESSIONS_FOLDER']);
            $rModelos = $cache->get('produtoBusiness_getModelosByMontadoraEAno_' . $montadora . '_' . $ano, function (ItemInterface $item) use ($montadora, $ano) {

                $sql_modelos = 'select modelos from (
                    select distinct(json_data->>"$.modelos_3") as modelos from est_produto where json_data->>"$.montadora_3" LIKE :montadora AND json_data->>"$.ano_3" LIKE :ano 
                    union 
                    select distinct(json_data->>"$.modelos_2") as modelos from est_produto where json_data->>"$.montadora_2" LIKE :montadora AND json_data->>"$.ano_2" LIKE :ano
                    union
                    select distinct(json_data->>"$.modelos") as modelos from est_produto where json_data->>"$.montadora" LIKE :montadora AND json_data->>"$.ano" LIKE :ano 
                    ) a where modelos is not null and modelos != \'\' order by modelos COLLATE utf8mb4_swedish_ci;';


                $conn = $this->doctrine->getConnection();
                $rModelos = $conn->fetchAllAssociative($sql_modelos, ['montadora' => '%' . $montadora . '%', 'ano' => '%' . $ano . '%']);

                return $rModelos;
            });
            return $rModelos;
        } catch (InvalidArgumentException $e) {
            throw new ViewException('Erro ao obter getAnosByMontadora - ' . $montadora);
        }
    }

    /**
     *
     */
    public function clearCaches()
    {
        $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.cache', 0, $_SERVER['CROSIER_SESSIONS_FOLDER']);

        $cache->reset();
    }


    /**
     * Gera os dados tanto para Excel quanto para CSV.
     *
     * @param string $arquivo
     * @return int[]
     * @throws ViewException
     */
    public function lerExcelProdutos(string $arquivo): array
    {
        try {
            $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($arquivo);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            $spreadsheet = $reader->load($arquivo);

            $worksheet = $spreadsheet->getActiveSheet();
            $planilha = [];
            foreach ($worksheet->getRowIterator() as $k => $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells,
                $cells = [];
                foreach ($cellIterator as $cell) {
                    $cells[] = $cell->getValue();
                }
                $planilha[] = $cells;
                if ($k > 65000) {
                    throw new ViewException('Erro ao ler planilha (k>65000)');
                }
            }


            $conn = $this->doctrine->getConnection();

            $rUnidades = $conn->fetchAllAssociative('SELECT id, label FROM est_unidade');
            $unidades = [];
            foreach ($rUnidades as $rUnidade) {
                $unidades[$rUnidade['label']] = $rUnidade['id'];
            }

            $rSubgrupos = $conn->fetchAllAssociative('SELECT 
                json_data->>"$.depto_id" as depto_id, 
                json_data->>"$.depto_codigo" as depto_codigo, 
                json_data->>"$.depto_nome" as depto_nome,
                grupo_id,
                json_data->>"$.grupo_codigo" as grupo_codigo, 
                json_data->>"$.grupo_nome" as grupo_nome,
                id as subgrupo_id, 
                codigo as subgrupo_codigo, 
                nome as subgrupo_nome FROM est_subgrupo');
            $subgrupos = [];
            foreach ($rSubgrupos as $rSubgrupo) {
                $chave = $rSubgrupo['depto_codigo'] . ' - ' . $rSubgrupo['depto_nome'] . '---' .
                    $rSubgrupo['grupo_codigo'] . ' - ' . $rSubgrupo['grupo_nome'] . '---' .
                    $rSubgrupo['subgrupo_codigo'] . ' - ' . $rSubgrupo['subgrupo_nome'];
                $subgrupos[md5($chave)] = $rSubgrupo;
            }

            $rFornecedores = $conn->fetchAllAssociative('SELECT id, documento, nome FROM est_fornecedor');
            $fornecedores = [];
            foreach ($rFornecedores as $rFornecedor) {
                $fornecedores[md5(StringUtils::mascararCnpjCpf($rFornecedor['documento']) . ' - ' . $rFornecedor['nome'])] = $rFornecedor['id'];
            }

            $alterados = 0;
            $naoAlterados = 0;

            foreach ($planilha as $k => $linha) {
                if ($k === 0) continue;
                if (!is_int($linha[1])) continue;
                $produto = $conn->fetchAssociative('SELECT * FROM est_produto WHERE id = :produtoId', ['produtoId' => $linha[1]]);
                if (!$produto) {
                    throw new ViewException('Produto não encontrado para o id ' . $linha[1]);
                }

                $produto_orig = $conn->fetchAssociative('SELECT * FROM est_produto WHERE id = :produtoId', ['produtoId' => $linha[1]]);
                $produto_jsonData_orig = json_decode($produto_orig['json_data'], true);
                ksort($produto_jsonData_orig);
                $produto_orig['json_data'] = json_encode($produto_jsonData_orig);

                $produto_jsonData = json_decode($produto['json_data'], true);

                $produto['unidade_padrao_id'] = $unidades[$linha[2]];
                $produto_jsonData['titulo'] = $linha[5];
                $chaveDeptoGrupoSubgrupo = $linha[6] . '---' . $linha[7] . '---' . $linha[8];
                $subgrupo = $subgrupos[md5($chaveDeptoGrupoSubgrupo)];
                $produto['depto_id'] = $subgrupo['depto_id'];
                $produto['grupo_id'] = $subgrupo['grupo_id'];
                $produto['subgrupo_id'] = $subgrupo['subgrupo_id'];
                $produto['fornecedor_id'] = $fornecedores[md5($linha[9])];

                if (!in_array($linha[14], ['Sim', 'Não'], true)) {
                    $produto_jsonData['descricao_produto'] = $linha[14];
                }
                if (!in_array($linha[15], ['Sim', 'Não'], true)) {
                    $produto_jsonData['caracteristicas'] = $linha[15];
                }
                if (!in_array($linha[16], ['Sim', 'Não'], true)) {
                    $produto_jsonData['especif_tec'] = $linha[16];
                }
                if (!in_array($linha[17], ['Sim', 'Não'], true)) {
                    $produto_jsonData['itens_inclusos'] = $linha[17];
                }
                if (!in_array($linha[18], ['Sim', 'Não'], true)) {
                    $produto_jsonData['compativel_com'] = $linha[18];
                }

                $produto_jsonData['ean'] = $linha[19];
                $produto_jsonData['referencia'] = $linha[20];
                $produto_jsonData['marca'] = $linha[21];

                $produto_jsonData['ano'] = $linha[23];
                $produto_jsonData['montadora'] = $linha[24];
                $produto_jsonData['modelos'] = $linha[25];

                $produto_jsonData['ano_2'] = $linha[26];
                $produto_jsonData['montadora_2'] = $linha[27];
                $produto_jsonData['modelos_2'] = $linha[28];

                $produto_jsonData['ano_3'] = $linha[29];
                $produto_jsonData['montadora_3'] = $linha[30];
                $produto_jsonData['modelos_3'] = $linha[31];

                $produto['status'] = $linha[32] === 'ATIVO' ? 'ATIVO' : 'INATIVO';

                $produto_jsonData['dimensoes'] = $linha[33] . '|' . $linha[34] . $linha[35]; // A|L|C
                $produto_jsonData['peso'] = $linha[36];

                ksort($produto_jsonData);

                $produto_jsonData_orig = json_encode($produto_jsonData_orig);

                $produto['json_data'] = json_encode($produto_jsonData);

                // Verifica se houve alterações
                if (strcmp(json_encode($produto), json_encode($produto_orig)) !== 0) {
                    $id = $produto['id'];

                    // unset nos campos "VIRTUAL GENERATED"
                    unset(
                        $produto['id'],
                        $produto['depto_nome'],
                        $produto['titulo'],
                        $produto['qtde_estoque_matriz'],
                        $produto['qtde_estoque_min_matriz'],
                        $produto['deficit_estoque_matriz'],
                        $produto['dt_ult_saida_acessorios'],
                        $produto['qtde_estoque_acessorios'],
                        $produto['qtde_estoque_min_acessorios'],
                        $produto['deficit_estoque_acessorios'],
                        $produto['qtde_estoque_total'],
                        $produto['porcent_preench'],
                        $produto['qtde_imagens'],
                        $produto['dt_ult_saida_matriz'],
                        $produto['ecommerce_dt_integr']);

                    $produto['updated'] = (new \DateTime())->format('Y-m-d H:i:s');
                    $conn->update('est_produto', $produto, ['id' => $id]);
                    $alterados++;
                } else {
                    $naoAlterados++;
                }
            }
            return ['ALTERADOS' => $alterados, 'NAO_ALTERADOS' => $naoAlterados];

        } catch (\Throwable $e) {
            $this->logger->error('Erro ao ler arquivo xlsx');
            $this->logger->error($e->getMessage());
            throw new ViewException('Erro ao ler arquivo xlsx');
        }
    }

}