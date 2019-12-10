<?php


namespace App\Business\Estoque;


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

    /** @var LoggerInterface */
    private $logger;

    /** @var EntityManagerInterface */
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
     * @param EntityManagerInterface $doctrine
     */
    public function setDoctrine(EntityManagerInterface $doctrine): void
    {
        $this->doctrine = $doctrine;
    }


    public function gerarExcel(): array
    {

        try {
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

            /** @var Connection $conn */
            $conn = $this->doctrine->getConnection();

            $qryProdutos = $conn->query('SELECT p.*, u.label as unidade FROM vw_rdp_est_produto p, est_unidade_produto u WHERE p.unidade_produto_id = u.id AND p.titulo IS NOT NULL AND trim(p.titulo) != \'\' ORDER BY id ');


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
                $r[] = $produto['qtde_imagens'] ?? '';
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

                $r[] = $atributosProduto['Estoque "Matriz"'] ?? '';
                $r[] = $atributosProduto['Estoque "Acessórios"'] ?? '';
                $r[] = $atributosProduto['Estoque Total'] ?? '';

                $dados[] = $r;
                $this->logger->info($linha++ . ' escrita(s)');
            }

            $sheet->fromArray($dados);
            //        foreach ($todos as $produto) {
            //
            //        }

            // array_map('unlink', glob($_SERVER['PASTA_ESTOQUE_PRODUTOS_EXCEL'] . '*.xlsx'));
            $writer = new Xlsx($spreadsheet);
            $nomeArquivo = StringUtils::guidv4() . '_produtos.xlsx';
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
        $this->logger->debug('Iniciando atualizarCamposEstoqueProdutoPelaRelEstoque01()');
        $conn = $this->doctrine->getConnection();
        $conn->beginTransaction();


        try {
            $qryProdutos = $conn->query('SELECT * FROM est_produto');

            $qryAtributo = $conn->prepare('SELECT id FROM est_atributo WHERE uuid = :uuid');

            $qryAtributo->bindValue('uuid', 'c37e9985-53f2-47f4-833a-52ace1f84e60'); // Estoque "Acessórios"
            $qryAtributo->execute();
            $atrEstoqueAcessorios = $qryAtributo->fetch();

            $qryAtributo->bindValue('uuid', '3edb71db-375d-4d37-b36d-8287f291606b'); // Estoque "Matriz"
            $qryAtributo->execute();
            $atrEstoqueMatriz = $qryAtributo->fetch();

            $qryAtributo->bindValue('uuid', '8f25a3e6-cf93-4111-be2b-a46dedc30107'); // Estoque Total
            $qryAtributo->execute();
            $atrEstoqueTotal = $qryAtributo->fetch();

            // Atualização do atributo Dt Últ Saída (o campo se refere a MATRIZ)
            $qryAtributo->bindValue('uuid', '4cad1e55-a08c-4550-adfd-f8baca8f44a7'); // Dt Últ Saída
            $qryAtributo->execute();
            $atrDtUltSaida = $qryAtributo->fetch();
            $qryDtUltSaida = $conn->prepare('SELECT max(dt_ult_saida) as dt FROM rdp_rel_estoque01 WHERE cod_prod = :cod_prod AND desc_filial = \'MATRIZ\'');

            // Atualização do atributo Dt Últ Entrada (o campo se refere a MATRIZ)
            $qryAtributo->bindValue('uuid', 'f6673979-149f-4813-83b8-5722c0aa35ee'); // Dt Últ Entrada
            $qryAtributo->execute();
            $atrDtUltEntrada = $qryAtributo->fetch();
            $qryDtUltEntrada = $conn->prepare('SELECT MAX(dt_emissao) as dt FROM rdp_rel_compras01 WHERE cod_prod = :cod_prod AND loja = \'MATRIZ\'');

            // Atualização do atributo Preço Custo (o campo se refere a MATRIZ)
            $qryAtributo->bindValue('uuid', '84ec35ff-22c1-4479-8368-baf766702e5e'); // Preço Custo
            $qryAtributo->execute();
            $atrPrecoCusto = $qryAtributo->fetch();
            $qryPrecoCusto = $conn->prepare('SELECT custo_medio FROM rdp_rel_estoque01 WHERE cod_prod = :cod_prod AND desc_filial = \'MATRIZ\'');

            // Atualização do atributo Preço Acessórios (o campo se refere a loja ACESSÓRIOS)
            $qryAtributo->bindValue('uuid', '2445ba8a-cb2d-4de5-b519-21c71248a727'); // Preço Acessórios
            $qryAtributo->execute();
            $atrPrecoAcessorios = $qryAtributo->fetch();
            $qryPrecoAcessorios = $conn->prepare('SELECT preco_venda FROM rdp_rel_estoque01 WHERE cod_prod = :cod_prod AND desc_filial = \'ACESSORIOS\'');

            // ????
//            $qryAtributo->bindValue('uuid', '1809d058-337c-46b2-a540-fd5315467d1a'); // Preço Atacado
//            $qryAtributo->execute();
//            $atrPrecoAtacado = $qryAtributo->fetch();

            // Atualização do atributo Preço Tabela (o campo se refere a loja MATRIZ)
            $qryAtributo->bindValue('uuid', 'c22e79c5-4dfd-4506-b3f5-53473f88bf2f'); // Preço Tabela
            $qryAtributo->execute();
            $atrPrecoTabela = $qryAtributo->fetch();
            $qryPrecoTabela = $conn->prepare('SELECT preco_venda FROM rdp_rel_estoque01 WHERE cod_prod = :cod_prod AND desc_filial = \'MATRIZ\'');


            $qryRelEstoque01 = $conn->prepare('SELECT qtde_atual FROM rdp_rel_estoque01 WHERE cod_prod = :cod_prod AND desc_filial = :desc_filial');


            $produtos = [];

            while ($produto = $qryProdutos->fetch()) {
                $produtos[] = ['id' => $produto['id'], 'codigo_from' => $produto['codigo_from']];
            }

            $i = 0;
            $this->logger->debug('Atualizando ' . count($produtos) . ' produtos');
            foreach ($produtos as $produto) {

                $this->insereAtributoSeProdutoAindaNaoTem($produto, $atrEstoqueMatriz['id'], 1, 'Estoques');
                $this->insereAtributoSeProdutoAindaNaoTem($produto, $atrEstoqueAcessorios['id'], 2, 'Estoques');
                $this->insereAtributoSeProdutoAindaNaoTem($produto, $atrEstoqueTotal['id'], 3, 'Estoques');

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

                $conn->update('est_produto_atributo', ['valor' => $total],
                    [
                        'produto_id' => $produto['id'],
                        'atributo_id' => $atrEstoqueTotal['id']
                    ]);


                // Atualização do campo Dt Últ Saída
                $qryDtUltSaida->bindValue('cod_prod', $produto['codigo_from']);
                $qryDtUltSaida->execute();
                $dtUltSaidaMatriz = $qryDtUltSaida->fetch()['dt'];
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
                $qryDtUltEntrada->bindValue('cod_prod', $produto['codigo_from']);
                $qryDtUltEntrada->execute();
                $dtUltEntradaMatriz = $qryDtUltEntrada->fetch()['dt'];
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
                $qryPrecoCusto->bindValue('cod_prod', $produto['codigo_from']);
                $qryPrecoCusto->execute();
                $precoCusto = $qryPrecoCusto->fetch()['custo_medio'];
                if ($precoCusto) {
                    $conn->update('est_produto_atributo',
                        ['valor' => $precoCusto],
                        [
                            'produto_id' => $produto['id'],
                            'atributo_id' => $atrPrecoCusto['id']
                        ]);
                }

                // Atualização do campo Preço Acessórios
                $qryPrecoAcessorios->bindValue('cod_prod', $produto['codigo_from']);
                $qryPrecoAcessorios->execute();
                $precoAcessorios = $qryPrecoAcessorios->fetch()['preco_venda'];
                if ($precoAcessorios) {
                    $conn->update('est_produto_atributo',
                        ['valor' => $precoAcessorios],
                        [
                            'produto_id' => $produto['id'],
                            'atributo_id' => $atrPrecoAcessorios['id']
                        ]);
                }


                // Atualização do campo Preço Tabela
                $qryPrecoTabela->bindValue('cod_prod', $produto['codigo_from']);
                $qryPrecoTabela->execute();
                $precoTabela = $qryPrecoTabela->fetch()['preco_venda'];
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
     * @throws \Doctrine\DBAL\DBALException
     */
    private function insereAtributoSeProdutoAindaNaoTem(array $produto, int $atributoId, int $ordem, string $aba): void
    {
        $conn = $this->doctrine->getConnection();
        $qryAtributoProduto = $conn->prepare('SELECT * FROM est_produto_atributo WHERE atributo_id = :atributo_id AND produto_id = :produto_id');

        $qryAtributoProduto->bindValue('atributo_id', $atributoId);
        $qryAtributoProduto->bindValue('produto_id', $produto['id']);
        $qryAtributoProduto->execute();
        if (!$qryAtributoProduto->fetch()) {
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
        }
    }


}