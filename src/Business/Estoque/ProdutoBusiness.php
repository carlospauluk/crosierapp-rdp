<?php


namespace App\Business\Estoque;


use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\AppConfigRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\StringUtils\StringUtils;
use Doctrine\DBAL\DBALException;
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

            $produtos = $conn->fetchAllAssociative('SELECT p.* FROM est_produto p WHERE true ' . $sqlTitulo . ' ORDER BY id');

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
                    $ecommerce_dt_integr = DateTimeUtils::parseDateStr($atributosProduto['ecommerce_dt_integr'])->format('d/m/Y H:i:s');
                    $r[] = $ecommerce_dt_integr;
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
        } catch (DBALException $e) {
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

}