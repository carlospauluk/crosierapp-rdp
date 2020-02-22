<?php


namespace App\Business\Estoque;


use App\Entity\Estoque\Produto;
use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\AppConfigRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\StringUtils\StringUtils;
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
     * @param bool $apenasProdutosComTitulo
     * @return array
     * @throws ViewException
     */
    public function gerarExcel(bool $apenasProdutosComTitulo): array
    {

        try {
            /** @var Connection $conn */
            $conn = $this->doctrine->getConnection();

            $sqlTitulo = $apenasProdutosComTitulo ? 'AND p.json_data->>"$.titulo" IS NOT NULL AND trim(p.json_data->>"$.titulo") != \'\'' : '';

            $qryProdutos = $conn->query('SELECT p.* FROM est_produto p WHERE true ' . $sqlTitulo . ' ORDER BY id');

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

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
            while ($produto = $qryProdutos->fetch()) {
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

            $sheet->fromArray($dados);
            $writer = new Xlsx($spreadsheet);
            $nomeArquivo = StringUtils::guidv4() . '_produtos.xlsx';
            $outputFile = $_SERVER['PASTA_ESTOQUE_PRODUTOS_EXCEL'] . $nomeArquivo;
            $writer->save($outputFile);
            $params['arquivo'] = $_SERVER['CROSIERAPPRDP_URL'] . $_SERVER['PASTA_ESTOQUE_PRODUTOS_EXCEL_DOWNLOAD'] . $nomeArquivo;
            $params['qtdeProdutos'] = $qtdeProdutos;
            return $params;
        } catch (Exception | DBALException $e) {
            $this->logger->error('Erro ao gerar arquivo xlsx');
            $this->logger->error($e->getMessage());
            throw new ViewException('Erro ao gerar arquivo xlsx');
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
        $this->logger->debug('Iniciando atualizarCamposEstoqueProdutoPelaRelEstoque01()');

        /** @var Connection $conn */
        $conn = $this->doctrine->getConnection();
        $conn->beginTransaction();


        try {
            $qryProdutos = $conn->query('SELECT * FROM est_produto');

            $qryAtributo = $conn->prepare('SELECT id FROM est_atributo WHERE uuid = :uuid');

            // Estoque "Acessórios"
            $qryAtributo->bindValue('uuid', 'c37e9985-53f2-47f4-833a-52ace1f84e60');
            $qryAtributo->execute();
            $atrEstoqueAcessorios = $qryAtributo->fetch();

            // Estoque "Matriz"
            $qryAtributo->bindValue('uuid', '3edb71db-375d-4d37-b36d-8287f291606b');
            $qryAtributo->execute();
            $atrEstoqueMatriz = $qryAtributo->fetch();

            // Estoque Total
            $qryAtributo->bindValue('uuid', '8f25a3e6-cf93-4111-be2b-a46dedc30107');
            $qryAtributo->execute();
            $atrEstoqueTotal = $qryAtributo->fetch();

            // Atualização do atributo Dt Últ Saída (o campo se refere a MATRIZ)
            // Dt Últ Saída
            $qryAtributo->bindValue('uuid', '4cad1e55-a08c-4550-adfd-f8baca8f44a7');
            $qryAtributo->execute();
            $atrDtUltSaida = $qryAtributo->fetch();

            // Atualização do atributo Dt Últ Entrada (o campo se refere a MATRIZ)
            // Dt Últ Entrada
            $qryAtributo->bindValue('uuid', 'f6673979-149f-4813-83b8-5722c0aa35ee');
            $qryAtributo->execute();
            $atrDtUltEntrada = $qryAtributo->fetch();


            // Atualização do atributo Preço Custo (o campo se refere a MATRIZ)
            // Preço Custo
            $qryAtributo->bindValue('uuid', '84ec35ff-22c1-4479-8368-baf766702e5e');
            $qryAtributo->execute();
            $atrPrecoCusto = $qryAtributo->fetch();

            // Atualização do atributo Preço Acessórios (o campo se refere a loja ACESSÓRIOS)
            // Preço Acessórios
            $qryAtributo->bindValue('uuid', '2445ba8a-cb2d-4de5-b519-21c71248a727');
            $qryAtributo->execute();
            $atrPrecoAcessorios = $qryAtributo->fetch();


            // Atualização do atributo 'Código ERP' (apenas copia do est_produto.codigo_from)
            // Serve apenas para não complicar a exibição deste campo na outra aba (ERP)
            $qryAtributo->bindValue('uuid', '88bb5c49-1147-4a47-8e46-6d85ddd559bc');
            $qryAtributo->execute();
            $atrCodigoERP = $qryAtributo->fetch();


            // ????
//            $qryAtributo->bindValue('uuid', '1809d058-337c-46b2-a540-fd5315467d1a'); // Preço Atacado
//            $qryAtributo->execute();
//            $atrPrecoAtacado = $qryAtributo->fetch();

            // Atualização do atributo Preço Tabela (o campo se refere a loja MATRIZ)
            $qryAtributo->bindValue('uuid', 'c22e79c5-4dfd-4506-b3f5-53473f88bf2f'); // Preço Tabela
            $qryAtributo->execute();
            $atrPrecoTabela = $qryAtributo->fetch();


            $rRelEstoque01 = $conn->fetchAll('SELECT * FROM rdp_rel_estoque01 ORDER BY cod_prod');

            // Preenche um array de "cachê" para não precisar fazer N querys no foreach
            $relEstoque01 = [];
            foreach ($rRelEstoque01 as $rowRelEstoque01) {
                $codProd = $rowRelEstoque01['cod_prod'];
                if (!isset($relEstoque01[$codProd])) {
                    $relEstoque01[$codProd] = [];
                }

                if ($rowRelEstoque01['desc_filial'] === 'MATRIZ') {
                    $relEstoque01[$codProd]['estoqueMatriz'] = $rowRelEstoque01['qtde_atual'];
                    $relEstoque01[$codProd]['dtUltSaida'] = $rowRelEstoque01['dt_ult_saida'];
                    $relEstoque01[$codProd]['precoCusto'] = $rowRelEstoque01['custo_medio'];
                    $relEstoque01[$codProd]['precoTabela'] = $rowRelEstoque01['preco_venda'];
                } else if ($rowRelEstoque01['desc_filial'] === 'ACESSORIOS') {
                    $relEstoque01[$codProd]['estoqueAcessorios'] = $rowRelEstoque01['qtde_atual'];
                }
            }

            // Atributo Dt Últ Entrada vem da tabela rdp_rel_compras01
            $rRelCompras01 = $conn->fetchAll('SELECT cod_prod, MAX(dt_emissao) as dt FROM rdp_rel_compras01 WHERE loja = \'MATRIZ\' GROUP BY cod_prod');
            foreach ($rRelCompras01 as $rowRelCompras01) {
                $codProd = $rowRelCompras01['cod_prod'];
                if (isset($relEstoque01[$codProd])) {
                    $relEstoque01[$codProd]['dtUltEntrada'] = $rowRelCompras01['dt'];
                }
            }

            $produtos = [];

            while ($produto = $qryProdutos->fetch()) {
                $produtos[] = ['id' => $produto['id'], 'codigo_from' => $produto['codigo_from']];
            }

            $i = 0;
            $this->logger->debug('Atualizando ' . count($produtos) . ' produtos');
            foreach ($produtos as $produto) {

                // Atualização do atributo 'Código ERP' (apenas copia do est_produto.codigo_from)
                // Serve apenas para não complicar a exibição deste campo na outra aba (ERP)
                $this->insereAtributoSeProdutoAindaNaoTem($produto, $atrCodigoERP['id'], 1, 'ERP');

                $conn->update('est_produto_atributo', ['valor' => $produto['codigo_from']],
                    [
                        'produto_id' => $produto['id'],
                        'atributo_id' => $atrCodigoERP['id']
                    ]);

                $this->insereAtributoSeProdutoAindaNaoTem($produto, $atrEstoqueMatriz['id'], 1, 'Estoques');
                $this->insereAtributoSeProdutoAindaNaoTem($produto, $atrEstoqueAcessorios['id'], 2, 'Estoques');
                $this->insereAtributoSeProdutoAindaNaoTem($produto, $atrEstoqueTotal['id'], 3, 'Estoques');

                $total = 0.0;

                $estoqueMatriz = $relEstoque01[$produto['codigo_from']]['estoqueMatriz'] ?? 0.0;
                if ($estoqueMatriz) {
                    $total += (float)$estoqueMatriz;
                    $conn->update('est_produto_atributo', ['valor' => $estoqueMatriz],
                        [
                            'produto_id' => $produto['id'],
                            'atributo_id' => $atrEstoqueMatriz['id']
                        ]);
                }

                $estoqueAcessorios = $relEstoque01[$produto['codigo_from']]['estoqueAcessorios'] ?? 0.0;
                if ($estoqueAcessorios) {
                    $total += (float)$estoqueAcessorios;
                    $conn->update('est_produto_atributo', ['valor' => $estoqueAcessorios],
                        [
                            'produto_id' => $produto['id'],
                            'atributo_id' => $atrEstoqueAcessorios['id']
                        ]);
                }

                $conn->update('est_produto_atributo', ['valor' => $total],
                    [
                        'produto_id' => $produto['id'],
                        'atributo_id' => $atrEstoqueTotal['id']
                    ]);


                // Atualização do campo Dt Últ Saída
                $dtUltSaidaMatriz = $relEstoque01[$produto['codigo_from']]['dtUltSaida'] ?? null;
                if ($dtUltSaidaMatriz) {
                    $dtUltSaidaMatriz = DateTimeUtils::parseDateStr($dtUltSaidaMatriz)->format('d/m/Y');
                    $conn->update('est_produto_atributo',
                        ['valor' => $dtUltSaidaMatriz],
                        [
                            'produto_id' => $produto['id'],
                            'atributo_id' => $atrDtUltSaida['id']
                        ]);
                }


                // Atualização do campo Dt Últ Entrada
                $dtUltEntradaMatriz = $relEstoque01[$produto['codigo_from']]['dtUltEntrada'] ?? null;
                if ($dtUltEntradaMatriz) {
                    $dtUltEntradaMatriz = DateTimeUtils::parseDateStr($dtUltEntradaMatriz)->format('d/m/Y');
                    $conn->update('est_produto_atributo',
                        ['valor' => $dtUltEntradaMatriz],
                        [
                            'produto_id' => $produto['id'],
                            'atributo_id' => $atrDtUltEntrada['id']
                        ]);
                }

                // Atualização do campo Preço Custo
                $precoCusto = $relEstoque01[$produto['codigo_from']]['precoCusto'] ?? null;
                if ($precoCusto) {
                    $conn->update('est_produto_atributo',
                        ['valor' => $precoCusto],
                        [
                            'produto_id' => $produto['id'],
                            'atributo_id' => $atrPrecoCusto['id']
                        ]);
                }

                // Atualização do campo Preço Acessórios
                $precoAcessorios = $relEstoque01[$produto['codigo_from']]['precoAcessorios'] ?? null;
                if ($precoAcessorios) {
                    $conn->update('est_produto_atributo',
                        ['valor' => $precoAcessorios],
                        [
                            'produto_id' => $produto['id'],
                            'atributo_id' => $atrPrecoAcessorios['id']
                        ]);
                }


                // Atualização do campo Preço Tabela
                $precoTabela = $relEstoque01[$produto['codigo_from']]['precoTabela'] ?? null;
                if ($precoTabela) {
                    $conn->update('est_produto_atributo',
                        ['valor' => $precoTabela],
                        [
                            'produto_id' => $produto['id'],
                            'atributo_id' => $atrPrecoTabela['id']
                        ]);
                }

                $this->logger->debug(++$i . ' atualizado(s)');

            }

            $this->logger->debug('COMMIT');
            $conn->commit();
            $this->logger->debug('OK');
        } catch (DBALException $e) {
            $this->logger->debug('ROLLBACK');
            $conn->rollBack();
        }

    }


    /**
     * Verifica se o produto possui os atributos de 'estoques'. Se não tiver, insere.
     *
     * @param array $produto
     * @param int $atributoId
     * @param int $ordem
     * @param string $aba
     * @return bool
     * @throws DBALException
     */
    private function insereAtributoSeProdutoAindaNaoTem(array $produto, int $atributoId, int $ordem, string $aba): bool
    {
        $conn = $this->doctrine->getConnection();
        $qryAtributoProduto = $conn->prepare('SELECT * FROM est_produto_atributo WHERE atributo_id = :atributo_id AND produto_id = :produto_id');

        $qryAtributoProduto->bindValue('atributo_id', $atributoId);
        $qryAtributoProduto->bindValue('produto_id', $produto['id']);
        $qryAtributoProduto->execute();
        if (!$qryAtributoProduto->fetch()) {
            try {
                $estProdutoAtributo = [
                    'produto_id' => $produto['id'],
                    'atributo_id' => $atributoId,
                    'aba' => $aba,
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
                $conn->insert('est_produto_atributo', $estProdutoAtributo);
                return true;
            } catch (DBALException $e) {
                throw new \RuntimeException('Erro ao insereAtributoSeProdutoAindaNaoTem');
            }
        }
        return false;
    }


}