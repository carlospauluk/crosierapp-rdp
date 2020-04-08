<?php

namespace App\Business\Relatorios;


use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\Config\AppConfigEntityHandler;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\AppConfigRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\StringUtils\StringUtils;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 *
 *
 * @package App\Business\Relatorios
 */
class RelEstoque01Business
{

    private EntityManagerInterface $doctrine;

    private LoggerInterface $logger;

    private AppConfigEntityHandler $appConfigEntityHandler;

    private array $deptoIndefinido;
    private array $grupoIndefinido;
    private array $subgrupoIndefinido;

    /**
     * @param EntityManagerInterface $doctrine
     * @param LoggerInterface $logger
     * @param AppConfigEntityHandler $appConfigEntityHandler
     */
    public function __construct(EntityManagerInterface $doctrine,
                                LoggerInterface $logger,
                                AppConfigEntityHandler $appConfigEntityHandler)
    {
        $this->doctrine = $doctrine;
        $this->appConfigEntityHandler = $appConfigEntityHandler;
        $this->logger = $logger;
        $this->prepararCampos();
    }


    public function prepararCampos()
    {
        /** @var Connection $conn */
        $conn = $this->doctrine->getConnection();

        $this->deptoIndefinido = $conn->fetchAssoc('SELECT id, nome FROM est_depto WHERE codigo = \'00\'');
        $this->grupoIndefinido = $conn->fetchAssoc('SELECT id, nome FROM est_grupo WHERE codigo = \'00\'');
        $this->subgrupoIndefinido = $conn->fetchAssoc('SELECT id, nome FROM est_subgrupo WHERE codigo = \'00\'');

    }

    /**
     *
     */
    public function processarArquivosNaFila(): void
    {
        $this->prepararCampos();
        $pastaFila = $_SERVER['PASTA_UPLOAD_RELESTOQUE01'] . 'fila/';
        $files = scandir($pastaFila, 0);
        foreach ($files as $file) {
            if (!in_array($file, array('.', '..'))) {
                try {
                    $this->processarArquivo($file);
                    $this->marcarDtHrAtualizacao();
                    $this->logger->info('Arquivo processado com sucesso.');
                    rename($pastaFila . $file, $_SERVER['PASTA_UPLOAD_RELESTOQUE01'] . 'ok/' . $file);
                    $this->logger->info('Arquivo movido para pasta "ok".');
                } catch (\Exception $e) {
                    rename($pastaFila . $file, $_SERVER['PASTA_UPLOAD_RELESTOQUE01'] . 'falha/' . $file);
                    $this->logger->info('Arquivo movido para pasta "falha".');
                }
            }
        }
    }

    /**
     * @param string $arquivo
     * @return int
     * @throws ViewException
     */
    public function processarArquivo(string $arquivo): int
    {
        $pastaFila = $_SERVER['PASTA_UPLOAD_RELESTOQUE01'] . 'fila/';
        $conteudo = file_get_contents($pastaFila . $arquivo);
        $linhas = explode(PHP_EOL, $conteudo);
        $totalRegistros = count($linhas) - 2;

        $t = 0;
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
                if (count($campos) !== 13) {
                    throw new ViewException('Qtde de campos difere de 13 para a linha "' . $linha . '" (qtde: ' . count($campos) . ')');
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

                $camposAgrupados[$codigo] = $camposAgrupados[$codigo] ??
                    [
                        'codigoProduto' => $codigo,
                        'nome' => mb_convert_encoding($campos[1], 'ISO-8859-1', 'UTF-8'),
                        'custoMedio' => $campos[2],
                        'precoVenda' => $campos[3],
                        'filial' => $filial,
                        'qtdeMaxima' => $campos[6],
                        'codigoFornecedor' => $campos[9],
                        'nomeFornecedor' => mb_convert_encoding($campos[10], 'ISO-8859-1', 'UTF-8'),
                        'recnum' => $campos[11],
                        'codedi' => mb_convert_encoding($campos[12], 'ISO-8859-1', 'UTF-8')
                    ];

                $camposAgrupados[$codigo]['qtde_estoque_min_' . strtolower($filial)] = $campos[5];
                $camposAgrupados[$codigo]['qtde_estoque_' . strtolower($filial)] = $campos[7];
                $camposAgrupados[$codigo]['deficit_estoque_' . strtolower($filial)] = bcsub($campos[7], $campos[5], 3);
                $camposAgrupados[$codigo]['dt_ult_saida_' . strtolower($filial)] = $campos[8];

                $this->logger->info('camposAgrupados: ' . str_pad($i, 9, '0', STR_PAD_LEFT) . '/' . $totalRegistros);
            }

            /** @var Connection $conn */
            $conn = $this->doctrine->getConnection();

            $rProdutos = $conn->fetchAll('SELECT * FROM est_produto');
            $produtos = [];
            foreach ($rProdutos as $rProduto) {
                $rProdutoJsonData = json_decode($rProduto['json_data'], true);
                if ($rProdutoJsonData['erp_codigo'] ?? null) {
                    $produtos[$rProdutoJsonData['erp_codigo']] = $rProduto;
                }
            }

            $totalCamposAgrupados = count($camposAgrupados);
            $i = 0;
            foreach ($camposAgrupados as $erp_codigo => $dadosProduto) {
                $this->handleNaEstProduto($dadosProduto, $produtos[$erp_codigo] ?? null);
                $this->logger->info('est_produto: ' . ++$i . '/' . $totalCamposAgrupados);
            }

        } catch (\Throwable $e) {
            $this->logger->error('processarArquivo() - erro ');
            $this->logger->info('Erro ao inserir a linha "' . $linha . '"');
            $this->logger->error($e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }

        return $t;


    }

    /**
     * @param array $campos
     * @return string
     */
    public function handleNaEstProduto(array $campos, ?array $produto = null): string
    {
        try {
            /** @var Connection $conn */
            $conn = $this->doctrine->getConnection();

            $updating = true;
            $json_data_ORIG = null;
            if (!$produto) {
                $updating = false;
                $produto = [];
                $json_data = [];
                $produto['uuid'] = StringUtils::guidv4();
                $produto['depto_id'] = $this->deptoIndefinido['id'];
                $json_data['depto_codigo'] = '00';
                $json_data['depto_nome'] = 'INDEFINIDO';
                $produto['grupo_id'] = $this->grupoIndefinido['id'];
                $json_data['grupo_codigo'] = '00';
                $json_data['grupo_nome'] = 'INDEFINIDO';
                $produto['subgrupo_id'] = $this->subgrupoIndefinido['id'];;
                $json_data['subgrupo_codigo'] = '00';
                $json_data['subgrupo_nome'] = 'INDEFINIDO';
                $json_data['erp_codigo'] = $campos['codigoProduto'];

                $fornecedor = $conn->fetchAssoc('SELECT * FROM est_fornecedor WHERE json_data->>"$.codigo" = ?', [$campos['codigoFornecedor']]);
                if (!$fornecedor) {
                    unset($dadosFornecedor, $fornecedor);
                    $dadosFornecedor = [];
                    $dadosFornecedor['nome'] = $campos['nomeFornecedor'];
                    $dadosFornecedor['documento'] = $campos['cpfcnpjFornecedor'] ?? null;
                    $dadosFornecedor['inserted'] = (new \DateTime())->format('Y-m-d H:i:s');
                    $dadosFornecedor['updated'] = (new \DateTime())->format('Y-m-d H:i:s');
                    $dadosFornecedor['version'] = 0;
                    $dadosFornecedor['estabelecimento_id'] = 1;
                    $dadosFornecedor['user_inserted_id'] = 1;
                    $dadosFornecedor['user_updated_id'] = 1;

                    $dadosFornecedor_jsonData['codigo'] = $campos['codigoFornecedor'];
                    $dadosFornecedor['json_data'] = json_encode($dadosFornecedor_jsonData);

                    $conn->insert('est_fornecedor', $dadosFornecedor);
                    $fornecedorId = $conn->lastInsertId();
                    $fornecedor = $conn->fetchAssoc('SELECT * FROM est_fornecedor WHERE id = ?', [$fornecedorId]);
                }

                $produto['fornecedor_id'] = $fornecedor['id'];
                $json_data['fornecedor_nome'] = $fornecedor['nome'];
                $json_data['fornecedor_documento'] = $fornecedor['documento'];

                $produto['nome'] = $campos['nome'];
                $produto['status'] = 'INATIVO';
                $produto['composicao'] = 'N';
                $produto['inserted'] = (new \DateTime())->format('Y-m-d H:i:s');

                $produto['version'] = 0;
                $produto['estabelecimento_id'] = 1;
                $produto['user_inserted_id'] = 1;
                $produto['user_updated_id'] = 1;
            } else {
                $json_data = json_decode($produto['json_data'], true);
                $json_data_ORIG = json_decode($produto['json_data'], true);
            }

            $json_data['recnum'] = $campos['recnum'] ?? null;
            if ($campos['codedi'] ?? null) {
                $codedi = str_replace('#@#', ',', utf8_encode($campos['codedi']));
                $json_data['cod_edi'] = $codedi;
            }


            $json_data['qtde_estoque_max'] = $campos['qtdeMaxima'] ?? null;

            $json_data['preco_custo'] = $campos['custoMedio'] ?? null;
            $json_data['preco_tabela'] = $campos['precoVenda'] ?? null;

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

            $json_data['qtde_estoque_delpozo'] = $campos['qtde_estoque_delpozo'] ?? null;
            $json_data['dt_ult_saida_delpozo'] = $campos['dt_ult_saida_delpozo'] ?? null;


            $json_data['qtde_estoque_telemaco'] = $campos['qtde_estoque_telemaco'] ?? null;
            $json_data['dt_ult_saida_telemaco'] = $campos['dt_ult_saida_telemaco'] ?? null;

            $produto['json_data'] = json_encode($json_data);

            if (!$produto['json_data']) {
                $this->logger->error('Erro ao gerar json_data para CODIGO = ' . $campos['codigoProduto'] . '. Continuando...');
                $produto['json_data'] = null;
            }

            $produto['updated'] = (new \DateTime())->format('Y-m-d H:i:s');

            if (!$updating) {
                $conn->insert('est_produto', $produto);
                $id = $conn->lastInsertId();
            } else {
                $id = $produto['id'];
                if (strcmp($produto['json_data'], json_encode($json_data_ORIG)) !== 0) {
                    // somente o campo json_data está sendo atualizado
                    $conn->update('est_produto', ['json_data' => $produto['json_data'] ], ['id' => $id]);
                } else {
                    $this->logger->info('Nada mudou para CODIGO = ' . $campos['codigoProduto'] . '. Continuando...');
                }

            }
            return $id;
        } catch (\Throwable | DBALException $e) {
            $this->logger->error('Erro ao handleNaEstProduto');
            $this->logger->error($e->getMessage());
            throw new \RuntimeException('Erro ao handleNaEstProduto');
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
            $this->logger->error('Erro ao marcar app_config (relEstoque01.dthrAtualizacao)');
            $this->logger->error($e->getMessage());
            throw new ViewException('Erro ao marcar dt/hr atualização');
        }
    }


}
