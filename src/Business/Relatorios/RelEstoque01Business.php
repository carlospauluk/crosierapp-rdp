<?php

namespace App\Business\Relatorios;


use CrosierSource\CrosierLibBaseBundle\Business\Config\SyslogBusiness;
use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\Config\AppConfigEntityHandler;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\AppConfigRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\StringUtils\StringUtils;
use CrosierSource\CrosierLibRadxBundle\Business\ECommerce\IntegradorWebStorm;
use CrosierSource\CrosierLibRadxBundle\Entity\Estoque\Produto;
use CrosierSource\CrosierLibRadxBundle\EntityHandler\Estoque\ProdutoEntityHandler;
use CrosierSource\CrosierLibRadxBundle\Repository\Estoque\ProdutoRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Carlos Eduardo Pauluk
 */
class RelEstoque01Business
{

    private EntityManagerInterface $doctrine;

    private AppConfigEntityHandler $appConfigEntityHandler;

    private ProdutoEntityHandler $produtoEntityHandler;

    private SyslogBusiness $syslog;

    private IntegradorWebStorm $integradorWebStorm;

    // marca quais produtos foram alterados e envia para o integrador de estoques/preços da webstorm
    private array $produtosIds_reintegrarNaWebStorm = [];

    private static int $QTDE_CAMPOS = 31;

    private ?array $deptoIndefinido = null;
    private ?array $grupoIndefinido = null;
    private ?array $subgrupoIndefinido = null;

    /**
     * @param EntityManagerInterface $doctrine
     * @param AppConfigEntityHandler $appConfigEntityHandler
     * @param ProdutoEntityHandler $produtoEntityHandler
     * @param SyslogBusiness $syslog
     * @param IntegradorWebStorm $integradorWebStorm
     */
    public function __construct(EntityManagerInterface $doctrine,
                                AppConfigEntityHandler $appConfigEntityHandler,
                                ProdutoEntityHandler $produtoEntityHandler,
                                SyslogBusiness $syslog,
                                IntegradorWebStorm $integradorWebStorm)
    {
        $this->doctrine = $doctrine;
        $this->appConfigEntityHandler = $appConfigEntityHandler;
        $this->syslog = $syslog->setApp('rdp')->setComponent(self::class);
        $this->produtoEntityHandler = $produtoEntityHandler;
        $this->integradorWebStorm = $integradorWebStorm;
    }

    /**
     *
     */
    public function prepararCampos()
    {
        if (!$this->deptoIndefinido) {
            try {
                $conn = $this->doctrine->getConnection();
                $this->deptoIndefinido = $conn->fetchAssociative('SELECT id, nome FROM est_depto WHERE codigo = \'00\'');
                $this->grupoIndefinido = $conn->fetchAssociative('SELECT id, nome FROM est_grupo WHERE codigo = \'00\' AND depto_id = :deptoId', ['deptoId' => $this->deptoIndefinido['id']]);
                $this->subgrupoIndefinido = $conn->fetchAssociative('SELECT id, nome FROM est_subgrupo WHERE codigo = \'00\' AND grupo_id = :grupoId', ['grupoId' => $this->grupoIndefinido['id']]);
            } catch (\Exception $e) {
                throw new \RuntimeException('Erro ao prepararCampos()');
            }
        }
    }

    /**
     *
     */
    public function processarArquivosNaFila(): void
    {
        $this->syslog->info('Processando arquivos na fila.');
        $pastaFila = $_SERVER['PASTA_UPLOAD_RELESTOQUE01'] . 'fila/';
        $files = scandir($pastaFila, 0);
        if (count($files) < 3) { // conta sempre mais o "." e o ".."
            $this->syslog->info('Nenhum arquivo para processar. Finalizando.');
            return;
        }
        $this->syslog->info((count($files) - 2) . ' arquivo(s) para processar');
        $this->prepararCampos();
        foreach ($files as $file) {
            if (!in_array($file, array('.', '..')) && !is_dir($pastaFila . $file)) {
                try {
                    $this->processarArquivo($file);

                    $this->corrigirEstoquesProdutosComposicao();
                    $this->marcarDtHrAtualizacao();
                    $this->syslog->info('Arquivo processado com sucesso.');
                    @unlink($_SERVER['PASTA_UPLOAD_RELESTOQUE01'] . 'ok/ultimo.gra');
                    rename($pastaFila . $file, $_SERVER['PASTA_UPLOAD_RELESTOQUE01'] . 'ok/ultimo.gra');
                    $this->syslog->info('Arquivo movido para pasta "ok".');
                } catch (\Exception $e) {
                    @rename($pastaFila . $file, $_SERVER['PASTA_UPLOAD_RELESTOQUE01'] . 'falha/' . $file);
                    $this->syslog->err('Erro processarArquivosNaFila()');
                    $this->syslog->err($e->getTraceAsString());
                    $this->syslog->info('Arquivo movido para pasta "falha".');
                }
            }
        }
        $this->syslog->info('Finalizando com sucesso.');
    }

    /**
     * @param string $arquivo
     * @return int
     */
    private function processarArquivo(string $arquivo): int
    {
        $this->syslog->info('Iniciando processamento do arquivo ' . $arquivo);
        $pastaFila = $_SERVER['PASTA_UPLOAD_RELESTOQUE01'] . 'fila/';
        $conteudo = file_get_contents($pastaFila . $arquivo);
        $linhas = explode(PHP_EOL, $conteudo);
        $totalRegistros = count($linhas) - 2;

        $mudancas = 0;
        $naoAlterados = 0;
        $linha = null;
        try {
            $camposAgrupados = [];

            for ($i = 1; $i < $totalRegistros; $i++) {
                $linha = $linhas[$i];
                if (!trim($linha)) {
                    continue;
                }
                $linha = $linha[-1] === '|' ? substr($linha, 0, -1) : $linha;

                $campos = explode('|', $linha);
                if (count($campos) !== self::$QTDE_CAMPOS) {
                    $this->syslog->err('Qtde de campos difere de ' . self::$QTDE_CAMPOS . ' para a linha "' . $linha . '" (qtde: ' . count($campos) . ')');
                    continue;
                }

                if ($campos[8] ?: false) {
                    $campos[8] = DateTimeUtils::parseDateStr($campos[8])->format('Y-m-d');
                }

                $cMax = count($campos);
                for ($c = 0; $c < $cMax; $c++) {
                    $campos[$c] = trim($campos[$c]) !== '' ? trim(str_replace("'", "''", $campos[$c])) : null;
                }

                $codigo = $campos[0];
                $filial = $campos[4];


                $camposAgrupados[$codigo]['codigoProduto'] = $codigo;
                $camposAgrupados[$codigo]['nome'] = mb_convert_encoding($campos[1], 'ISO-8859-1', 'UTF-8');
                $camposAgrupados[$codigo]['custoMedio'] = $campos[2];
                $camposAgrupados[$codigo]['precoVenda'] = $campos[3];
                $camposAgrupados[$codigo]['filial'] = $filial;
                $camposAgrupados[$codigo]['qtdeMaxima'] = $campos[6];
                $camposAgrupados[$codigo]['codigoFornecedor'] = $campos[9];
                $camposAgrupados[$codigo]['nomeFornecedor'] = mb_convert_encoding($campos[10], 'ISO-8859-1', 'UTF-8');
                $camposAgrupados[$codigo]['recnum'] = $campos[11];
                $camposAgrupados[$codigo]['codedi'] = mb_convert_encoding($campos[12], 'ISO-8859-1', 'UTF-8');

                // $camposAgrupados[$codigo]['EAN'] = $campos[13];
                $camposAgrupados[$codigo]['GENERO'] = $campos[14];
                $camposAgrupados[$codigo]['CFOP_DENTRO'] = $campos[15];
                $camposAgrupados[$codigo]['CFOP_FORA'] = $campos[16];
                $camposAgrupados[$codigo]['UNIDADE'] = $campos[17];
                $camposAgrupados[$codigo]['EAN_TRIB'] = $campos[18];
                $camposAgrupados[$codigo]['ORIGEM'] = $campos[19];
                $camposAgrupados[$codigo]['CST_ICMS'] = $campos[20];
                $camposAgrupados[$codigo]['MODALIDADE_ICMS'] = $campos[21];
                $camposAgrupados[$codigo]['ALIQUOTA_ICMS'] = $campos[22];
                $camposAgrupados[$codigo]['CEST'] = $campos[23];
                $camposAgrupados[$codigo]['CUSTO_MEDIO_ERP'] = $campos[24];
                $camposAgrupados[$codigo]['PRECO_MEDIO_ERP'] = $campos[25];
                $camposAgrupados[$codigo]['MARGEM_LIQUIDA_ERP'] = $campos[26];
                $camposAgrupados[$codigo]['PIS'] = $campos[27];
                $camposAgrupados[$codigo]['COFINS'] = $campos[28];
                $camposAgrupados[$codigo]['NCM'] = $campos[29];
                $camposAgrupados[$codigo]['IPI'] = $campos[30];


                $camposAgrupados[$codigo]['qtde_estoque_min_' . strtolower($filial)] = $campos[5];
                $camposAgrupados[$codigo]['qtde_estoque_' . strtolower($filial)] = $campos[7];
                $camposAgrupados[$codigo]['deficit_estoque_' . strtolower($filial)] = bcsub($campos[7], $campos[5], 3);
                $camposAgrupados[$codigo]['dt_ult_saida_' . strtolower($filial)] = $campos[8];

            }

            $conn = $this->doctrine->getConnection();

            $rProdutos = $conn->fetchAllAssociative('SELECT * FROM est_produto WHERE composicao = \'N\'');
            $produtos = [];
            foreach ($rProdutos as $rProduto) {
                $rProdutoJsonData = json_decode($rProduto['json_data'], true);
                if ($rProdutoJsonData['erp_codigo'] ?? null) {
                    $produtos[$rProdutoJsonData['erp_codigo']] = $rProduto;
                }
            }

            foreach ($camposAgrupados as $erp_codigo => $dadosProduto) {
                if ($this->handleNaEstProduto($dadosProduto, $produtos[$erp_codigo] ?? null)) {
                    $mudancas++;
                } else {
                    $naoAlterados++;
                }
            }

            if (count($this->produtosIds_reintegrarNaWebStorm) > 0) {
                $this->syslog->info('Enviando produtos alterados para integração na webstorm', implode(',', $this->produtosIds_reintegrarNaWebStorm));
                $this->integradorWebStorm->atualizaEstoqueEPrecos($this->produtosIds_reintegrarNaWebStorm);
            }

            $this->syslog->info('Total de mudanças: ' . $mudancas);
            $this->syslog->info('Total não alterados: ' . $naoAlterados);
            return $mudancas;

        } catch (\Throwable $e) {
            $this->syslog->err('processarArquivo() - erro ');
            $this->syslog->info('Erro ao inserir a linha "' . $linha . '"');
            $this->syslog->err($e->getTraceAsString());
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * @param array $campos
     * @param array|null $produto
     * @return bool
     */
    public function handleNaEstProduto(array $campos, ?array $produto = null): bool
    {
        try {
            $this->prepararCampos();
            $agora = (new \DateTime())->format('Y-m-d H:i:s');
            $conn = $this->doctrine->getConnection();

            $updating = true;
            $json_data_ORIG = null;
            if (!$produto) {
                $updating = false;
                $produto = [];
                $json_data = [];
                $produto['uuid'] = StringUtils::guidv4();
                $produto['codigo'] = $campos['codigoProduto'];
                $produto['depto_id'] = $this->deptoIndefinido['id'];
                $json_data['depto_codigo'] = '00';
                $json_data['depto_nome'] = 'INDEFINIDO';
                $produto['grupo_id'] = $this->grupoIndefinido['id'];
                $json_data['grupo_codigo'] = '00';
                $json_data['grupo_nome'] = 'INDEFINIDO';
                $produto['subgrupo_id'] = $this->subgrupoIndefinido['id'];
                $json_data['subgrupo_codigo'] = '00';
                $json_data['subgrupo_nome'] = 'INDEFINIDO';
                $json_data['erp_codigo'] = $campos['codigoProduto'];

                $fornecedor = $conn->fetchAssociative('SELECT * FROM est_fornecedor WHERE json_data->>"$.codigo" = ?', [$campos['codigoFornecedor']]);
                if (!$fornecedor) {
                    unset($dadosFornecedor, $fornecedor);
                    $dadosFornecedor = [];
                    $dadosFornecedor['nome'] = $campos['nomeFornecedor'];
                    $dadosFornecedor['documento'] = $campos['cpfcnpjFornecedor'] ?? null;
                    $dadosFornecedor['inserted'] = $agora;
                    $dadosFornecedor['updated'] = $agora;
                    $dadosFornecedor['version'] = 0;
                    $dadosFornecedor['estabelecimento_id'] = 1;
                    $dadosFornecedor['user_inserted_id'] = 1;
                    $dadosFornecedor['user_updated_id'] = 1;

                    $dadosFornecedor_jsonData['codigo'] = $campos['codigoFornecedor'];
                    $dadosFornecedor['json_data'] = json_encode($dadosFornecedor_jsonData);

                    $conn->insert('est_fornecedor', $dadosFornecedor);
                    $fornecedorId = $conn->lastInsertId();
                    $fornecedor = $conn->fetchAssociative('SELECT * FROM est_fornecedor WHERE id = ?', [$fornecedorId]);
                }

                $produto['fornecedor_id'] = $fornecedor['id'];
                $json_data['fornecedor_nome'] = $fornecedor['nome'];
                $json_data['fornecedor_documento'] = $fornecedor['documento'];

                $produto['nome'] = $campos['nome'];
                $produto['status'] = 'INATIVO';
                $produto['composicao'] = 'N';
                $produto['unidade_padrao_id'] = 1;
                $produto['inserted'] = $agora;

                $produto['version'] = 0;
                $produto['estabelecimento_id'] = 1;
                $produto['user_inserted_id'] = 1;
                $produto['user_updated_id'] = 1;
            } else {
                $json_data = json_decode($produto['json_data'], true);
                $json_data_ORIG = json_decode($produto['json_data'], true);
                ksort($json_data_ORIG);
            }

            $json_data['recnum'] = $campos['recnum'] ?? null;
            if ($campos['codedi'] ?? null) {
                $codedi = str_replace('#@#', ',', utf8_encode($campos['codedi']));
                $json_data['cod_edi'] = $codedi;
            }

            $json_data['qtde_estoque_max'] = $campos['qtdeMaxima'] ?? null;

            $json_data['preco_custo'] = $campos['custoMedio'] ?? null;
            $json_data['preco_tabela'] = $campos['precoVenda'] ?? null;

            // Se já tiver preco_site > 0, mantém. Senão, pega o preco_tabela.
            //if (isset($json_data['preco_site'])) {
            //    $json_data['preco_site'] = ($json_data['preco_site'] > 0.0) ? $json_data['preco_site'] : $json_data['preco_tabela'];
            //} else {
            $json_data['preco_site'] = $json_data['preco_tabela'];
            //}


            $json_data['qtde_imagens'] = $json_data['qtde_imagens'] ?? 0;
            $json_data['imagem1'] = $json_data['imagem1'] ?? '';

            $json_data['qtde_estoque_matriz'] = isset($campos['qtde_estoque_matriz']) ? (float)$campos['qtde_estoque_matriz'] : null;
            $json_data['qtde_estoque_min_matriz'] = isset($campos['qtde_estoque_min_matriz']) ? (float)$campos['qtde_estoque_min_matriz'] : null;
            $json_data['deficit_estoque_matriz'] = isset($campos['deficit_estoque_matriz']) ? (float)$campos['deficit_estoque_matriz'] : null;
            $json_data['dt_ult_saida_matriz'] = $campos['dt_ult_saida_matriz'] ?? null;

            $json_data['qtde_estoque_acessorios'] = isset($campos['qtde_estoque_acessorios']) ? (float)$campos['qtde_estoque_acessorios'] : null;
            $json_data['qtde_estoque_min_acessorios'] = isset($campos['qtde_estoque_min_acessorios']) ? (float)$campos['qtde_estoque_min_acessorios'] : null;
            $json_data['deficit_estoque_acessorios'] = isset($campos['deficit_estoque_acessorios']) ? (float)$campos['deficit_estoque_acessorios'] : null;
            $json_data['dt_ult_saida_acessorios'] = $campos['dt_ult_saida_acessorios'] ?? null;

            $json_data['qtde_estoque_delpozo'] = $campos['qtde_estoque_delpozo-pg'] ?? null;
            $json_data['dt_ult_saida_delpozo'] = $campos['dt_ult_saida_delpozo-pg'] ?? null;

            $json_data['qtde_estoque_telemaco'] = $campos['qtde_estoque_telemaco'] ?? null;
            $json_data['dt_ult_saida_telemaco'] = $campos['dt_ult_saida_telemaco'] ?? null;

            $json_data['qtde_estoque_deposito'] = $campos['qtde_estoque_deposito-mtz'] ?? null;
            $json_data['dt_ult_saida_deposito'] = $campos['dt_ult_saida_deposito-mtz'] ?? null;


            $json_data['qtde_estoque_total'] =
                ((float)($json_data['qtde_estoque_matriz'] ?? 0)) +
                ((float)($json_data['qtde_estoque_deposito'] ?? 0)) +
                ((float)($json_data['qtde_estoque_acessorios'] ?? 0));


            // $json_data['ean'] = $campos['EAN'];
            $json_data['genero'] = $campos['GENERO'] ?? null;
            $json_data['cfop_dentro'] = $campos['CFOP_DENTRO'] ?? null;
            $json_data['cfop_fora'] = $campos['CFOP_FORA'] ?? null;
            $json_data['unidade'] = $campos['UNIDADE'] ?? null;
            $json_data['ean_trib'] = $campos['EAN_TRIB'] ?? null;
            $json_data['origem'] = $campos['ORIGEM'] ?? null;
            $json_data['cst_icms'] = $campos['CST_ICMS'] ?? null;
            $json_data['modalidade_icms'] = $campos['MODALIDADE_ICMS'] ?? null;
            $json_data['aliquota_icms'] = $campos['ALIQUOTA_ICMS'] ?? null;
            $json_data['cest'] = $campos['CEST'] ?? null;
            $json_data['custo_medio_erp'] = $campos['CUSTO_MEDIO_ERP'] ?? null;
            $json_data['preco_medio_erp'] = $campos['PRECO_MEDIO_ERP'] ?? null;
            $json_data['margem_liquida_erp'] = $campos['MARGEM_LIQUIDA_ERP'] ?? null;
            $json_data['pis'] = $campos['PIS'] ?? null;
            $json_data['cofins'] = $campos['COFINS'] ?? null;
            $json_data['ncm'] = $campos['NCM'] ?? null;
            $json_data['ipi'] = $campos['IPI'] ?? null;


            ksort($json_data);
            $produto['json_data'] = json_encode($json_data);

            if (!$produto['json_data']) {
                $this->syslog->err('Erro ao gerar json_data para CODIGO = ' . $campos['codigoProduto'] . '. Continuando...');
                $produto['json_data'] = null;
            }

            $produto['updated'] = $agora;

            $produtoSaldo = [
                'qtde' => $json_data['qtde_estoque_matriz'] ?? 0,
                'inserted' => $agora,
                'updated' => $agora,
                'user_inserted_id' => 1,
                'user_updated_id' => 1,
                'estabelecimento_id' => 1,
                'version' => 0,
                'json_data' => json_encode(['venda_ecommerce' => true]) // necessário para habilitar o faturamento para a venda
            ];

            if (!$updating) {
                $this->syslog->info('handleNaEstProduto - inserindo novo produto');
                $this->syslog->debug('handleNaEstProduto - ' . implode(',', $campos));

                $conn->insert('est_produto', $produto);
                $produto['id'] = $conn->lastInsertId();

                $conn->delete('est_produto_saldo', ['produto_id' => $produto['id']]);
                $produtoSaldo['produto_id'] = $produto['id'];
                $conn->insert('est_produto_saldo', $produtoSaldo);

                $this->syslog->info('handleNaEstProduto - produto inserido (id: ' . $produto['id'] . ')');
                return true;
            } else {
                $id = $produto['id'];

                $produtoSaldo['produto_id'] = $id;
                $conn->delete('est_produto_saldo', ['produto_id' => $id]);
                $conn->insert('est_produto_saldo', $produtoSaldo);

                if (strcmp($produto['json_data'], json_encode($json_data_ORIG)) !== 0) {
                    $this->syslog->info('handleNaEstProduto - produto com alterações no json_data. UPDATE...');
                    // para não sobreescrever outras alterações que possam ter sido feitas no Crosier, somente o campo json_data está sendo atualizado
                    $conn->update('est_produto', ['json_data' => $produto['json_data'], 'updated' => $produto['updated']], ['id' => $id]);

                    $this->produtosIds_reintegrarNaWebStorm[] = $id;
                    $this->syslog->info('handleNaEstProduto - UPDATE OK (id: ' . $produto['id'] . ')');
                    return true;
                } else {
                    $this->syslog->debug('Nada mudou para CODIGO = ' . $campos['codigoProduto'] . '. Continuando...');
                    return false;
                }
            }
        } catch (\Throwable $e) {
            $this->syslog->err('Erro ao handleNaEstProduto');
            $this->syslog->err($e->getTraceAsString());
            return false;
        }
    }

    /**
     * @throws ViewException
     */
    public function corrigirEstoquesProdutosComposicao()
    {
        try {
            $conn = $this->appConfigEntityHandler->getDoctrine()->getConnection();
            $rProdutosComposicao = $conn->fetchAllAssociative('SELECT id FROM est_produto WHERE composicao = \'S\'');
            $this->syslog->info('Corrigindo estoques para ' . count($rProdutosComposicao) . ' produto(s) em composição');
            /** @var ProdutoRepository $repoProduto */
            $repoProduto = $this->appConfigEntityHandler->getDoctrine()->getRepository(Produto::class);
            foreach ($rProdutosComposicao as $rProdutoComposicao) {
                /** @var Produto $produto */
                $produto = $repoProduto->find($rProdutoComposicao['id']);

                $valorTotal = 0.0;
                $menorQtdeDisponivel = null;

                foreach ($produto->composicoes as $itemComposicao) {
                    $itemComposicao->qtdeEmEstoque = $itemComposicao->produtoFilho->jsonData['qtde_estoque_total'] ?? 0.0; // save
                    $valorTotal = bcadd($valorTotal, $itemComposicao->getTotalComposicao(), 2);

                    $qtdeDisponivel = $itemComposicao->qtdeEmEstoque >= $itemComposicao->qtde ? bcdiv($itemComposicao->qtdeEmEstoque, $itemComposicao->qtde, 0) : 0;
                    $menorQtdeDisponivel = ($menorQtdeDisponivel !== null && $menorQtdeDisponivel < $qtdeDisponivel) ? $menorQtdeDisponivel : $qtdeDisponivel;
                }

                $produto->jsonData['preco_tabela'] = $valorTotal;
                $produto->jsonData['preco_site'] = $valorTotal;
                $produto->jsonData['qtde_estoque_total'] = $menorQtdeDisponivel;

                $conn->update('est_produto', ['json_data' => json_encode($produto->jsonData)], ['id' => $produto->getId()]);
            }
        } catch (\Throwable $e) {
            $errMsg = 'Erro ao corrigirEstoquesProdutosComposicao()';
            $this->syslog->err($errMsg, $e->getTraceAsString());
            throw new ViewException('Erro ao corrigirEstoquesProdutosComposicao()');
        }
    }

    /**
     * @throws ViewException
     */
    private function marcarDtHrAtualizacao(): void
    {
        try {
            /** @var AppConfigRepository $repoAppConfig */
            $repoAppConfig = $this->doctrine->getRepository(AppConfig::class);
            /** @var AppConfig $appConfig */
            $appConfig = $repoAppConfig->findOneByFiltersSimpl([['chave', 'EQ', 'relEstoque01.dthrAtualizacao'], ['appUUID', 'EQ', $_SERVER['CROSIERAPP_UUID']]]);
            if (!$appConfig) {
                $appConfig = new AppConfig();
                $appConfig->setChave('relEstoque01.dthrAtualizacao');
                $appConfig->setAppUUID($_SERVER['CROSIERAPP_UUID']);
            }
            $appConfig->setValor((new \DateTime())->format('Y-m-d H:i:s.u'));
            $this->appConfigEntityHandler->save($appConfig);
        } catch (\Exception $e) {
            $this->syslog->err('Erro ao marcar app_config (relEstoque01.dthrAtualizacao)');
            $this->syslog->err($e->getMessage());
            throw new ViewException('Erro ao marcar dt/hr atualização');
        }
    }


}
