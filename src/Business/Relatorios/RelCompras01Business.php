<?php

namespace App\Business\Relatorios;


use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\Config\AppConfigEntityHandler;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\AppConfigRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 *
 *
 * @package App\Business\Relatorios
 */
class RelCompras01Business
{

    private EntityManagerInterface $doctrine;

    private LoggerInterface $logger;

    private AppConfigEntityHandler $appConfigEntityHandler;

    private RelEstoque01Business $relEstoque01Business;

    private array $produtos;

    /**
     * @param EntityManagerInterface $doctrine
     * @param LoggerInterface $logger
     * @param RelEstoque01Business $relEstoque01Business
     * @param AppConfigEntityHandler $appConfigEntityHandler
     */
    public function __construct(EntityManagerInterface $doctrine,
                                LoggerInterface $logger,
                                RelEstoque01Business $relEstoque01Business,
                                AppConfigEntityHandler $appConfigEntityHandler)
    {
        $this->doctrine = $doctrine;
        $this->appConfigEntityHandler = $appConfigEntityHandler;
        $this->relEstoque01Business = $relEstoque01Business;
        $this->logger = $logger;
    }

    /**
     *
     */
    public function processarArquivosNaFila(): void
    {
        $pastaFila = $_SERVER['PASTA_UPLOAD_RELCOMPRAS01'] . 'fila/';
        $files = scandir($pastaFila, 0);
        foreach ($files as $file) {
            if (!in_array($file, array('.', '..'))) {
                try {
                    $this->processarArquivo($file);
                    $this->marcarDtHrAtualizacao();
                    $this->logger->info('Arquivo processado com sucesso.');
                    @unlink($_SERVER['PASTA_UPLOAD_RELCOMPRAS01'] . 'ok/ultimo.gra');
                    rename($pastaFila . $file, $_SERVER['PASTA_UPLOAD_RELCOMPRAS01'] . 'ok/ultimo.gra');
                    $this->logger->info('Arquivo movido para pasta "ok".');
                } catch (\Exception $e) {
                    rename($pastaFila . $file, $_SERVER['PASTA_UPLOAD_RELCOMPRAS01'] . 'falha/' . $file);
                    $this->logger->info('Arquivo movido para pasta "falha".');
                }
            }
        }
    }

    /**
     * @param string $arquivo
     * @return int
     */
    public function processarArquivo(string $arquivo): int
    {
        $pastaFila = $_SERVER['PASTA_UPLOAD_RELCOMPRAS01'] . 'fila/';
        $conteudo = file_get_contents($pastaFila . $arquivo);
        $linhas = explode(PHP_EOL, $conteudo);
        $totalRegistros = count($linhas);
        /** @var Connection $conn */
        $conn = $this->doctrine->getConnection();

        $t = 0;
        $linha = null;
        try {
            $relCompras01 = [];
            for ($i = 1; $i < $totalRegistros; $i++) {
                $linha = $linhas[$i];
                if (!trim($linha)) {
                    continue;
                }
                $camposSplit = explode('|', $linha);
                if (count($camposSplit) !== 23) {
                    throw new ViewException('Qtde de campos difere de 23 para a linha "' . $linha . '"');
                }

                $cMax = count($camposSplit);
                for ($c = 0; $c < $cMax; $c++) {
                    $camposSplit[$c] = trim($camposSplit[$c]) !== '' ? trim(str_replace("'", "''", $camposSplit[$c])) : null;
                }

                $pvCompra = $camposSplit[0];

                $campos = [];
                $campos['PV_COMPRA'] = $pvCompra;
                $campos['ITEM'] = $camposSplit[1];
                $campos['QTDE'] = $camposSplit[2];
                $campos['EMISSAO'] = $camposSplit[3] ? DateTimeUtils::parseDateStr($camposSplit[3])->format('Y-m-d') : null;
                // $campos['ANO'] = $camposSplit[4];
                // $campos['MES'] = $camposSplit[5];
                $campos['CNPJ_FORNEC'] = preg_replace("/[^0-9]/", "", $camposSplit[6]);
                $campos['COD_FORNEC'] = $camposSplit[7];
                $campos['NOME_FORNEC'] = $camposSplit[8];
                $campos['COD_PROD'] = $camposSplit[9];
                $campos['DESC_PROD'] = $camposSplit[10];
                // $campos['TOTAL_PRECO_VENDA'] = $camposSplit[11];
                $campos['TOTAL_PRECO_CUSTO'] = $camposSplit[12];
                // $campos['RENTABILIDADE_ITEM'] = $camposSplit[13];
                $campos['COD_VENDEDOR'] = $camposSplit[14];
                $campos['NOME_VENDEDOR'] = $camposSplit[15];
                $campos['LOJA'] = $camposSplit[16];
                $campos['TOTAL_CUSTO_PV'] = $camposSplit[17];
                // $campos['TOTAL_VENDA_PV'] = $camposSplit[18];
                // $campos['RENTABILIDADE_PV'] = $camposSplit[19];
                // $campos['CLIENTE_PV'] = $camposSplit[20];
                $campos['GRUPO'] = $camposSplit[21];
                $campos['PREVISAO_ENTREGA'] = $camposSplit[22] ? DateTimeUtils::parseDateStr($camposSplit[22])->format('Y-m-d') : null;

                $relCompras01[$pvCompra][] = $campos;
                $this->logger->info('camposAgrupados: ' . str_pad($i, 9, '0', STR_PAD_LEFT) . '/' . $totalRegistros);
            }

            $rPedidosCompra = $conn->fetchAll(
                'select p.id, p.fornecedor_id, p.total, p.json_data, count(*) as qtde_itens from est_pedidocompra p left join est_pedidocompra_item i on p.id = i.pedidocompra_id group by p.id');
            $pedidosCompra = [];
            foreach ($rPedidosCompra as $rPedidoCompra) {
                $jsonData = json_decode($rPedidoCompra['json_data'], true);
                if ($jsonData['pv_compra_ekt'] ?? null) {
                    $pedidosCompra[$jsonData['pv_compra_ekt']] = $rPedidoCompra;
                }
            }

            $totalRelPedidosCompra01 = count($rPedidosCompra);
            $i = 0;

            $this->buildProdutosArray();
            foreach ($relCompras01 as $pv_compra => $relCompra01) {
                if (isset($pedidosCompra[$pv_compra])) {
                    $pedidoCompra = $pedidosCompra[$pv_compra];
                    $pedidoCompraJsonData = json_decode($pedidoCompra['json_data'], true);
                    if ((float)$pedidoCompra['total'] !== (float)$relCompra01[0]['TOTAL_CUSTO_PV'] ||
                        (int)$pedidoCompraJsonData['fornecedor_cnpj'] !== (int)$relCompra01[0]['CNPJ_FORNEC'] ||
                        count($relCompra01) !== (int)$pedidoCompra['qtde_itens']) {
                        $conn->delete('est_pedidocompra', ['id' => $pedidosCompra[$pv_compra]['id']]);
                    } else {
                        $this->logger->info('est_pedidocompra: ' . str_pad(++$i, 9, '0', STR_PAD_LEFT) . '/' . $totalRelPedidosCompra01);
                        continue;
                    }
                }

                $this->handlePedidoCompra($relCompra01);
                $this->logger->info('est_pedidocompra: ' . str_pad(++$i, 9, '0', STR_PAD_LEFT) . '/' . $totalRelPedidosCompra01);
            }
            $this->logger->info('FIM. OK!');


        } catch (\Exception $e) {
            $this->logger->error('processarArquivo() - erro ');
            $this->logger->error($e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }

        return $t;
    }

    /**
     * @param array $dadosEkt
     * @throws \Exception
     */
    private function handlePedidoCompra(array $dadosEkt): void
    {
        /** @var Connection $conn */
        $conn = $this->doctrine->getConnection();

        $cabecalho = $dadosEkt[0];

        $pedidoCompra = [];

        $fornecedor = $conn->fetchAssoc('SELECT id FROM est_fornecedor WHERE documento = ?', [$cabecalho['CNPJ_FORNEC']]);
        if (!$fornecedor) {
            $dadosFornecedor = [];
            $dadosFornecedor['documento'] = $cabecalho['CNPJ_FORNEC'];
            $dadosFornecedor['nome'] = utf8_encode($cabecalho['NOME_FORNEC']);
            $dadosFornecedor['json_data'] = json_encode(['cod_fornecedor_ekt' => $cabecalho['COD_FORNEC']]);
            $dadosFornecedor['inserted'] = (new \DateTime())->format('Y-m-d H:i:s');
            $dadosFornecedor['updated'] = (new \DateTime())->format('Y-m-d H:i:s');
            $dadosFornecedor['version'] = 0;
            $dadosFornecedor['estabelecimento_id'] = 1;
            $dadosFornecedor['user_inserted_id'] = 1;
            $dadosFornecedor['user_updated_id'] = 1;
            $conn->insert('est_fornecedor', $dadosFornecedor);
            $fornecedor['id'] = $conn->lastInsertId();
        }

        $pedidoCompra['fornecedor_id'] = $fornecedor['id'] ?? null;

        $pedidoCompra['dt_emissao'] = $cabecalho['EMISSAO'];
        $pedidoCompra['dt_prev_entrega'] = $cabecalho['PREVISAO_ENTREGA'];
        $pedidoCompra['total'] = $cabecalho['TOTAL_CUSTO_PV'];

        $pedidoCompra['status'] = 'FINALIZADO';

        $jsonData = [];
        $jsonData['pv_compra_ekt'] = $cabecalho['PV_COMPRA'];
        $jsonData['fornecedor_codigo'] = $cabecalho['COD_FORNEC'];
        $jsonData['fornecedor_cnpj'] = $cabecalho['CNPJ_FORNEC'];
        $jsonData['fornecedor_nome'] = utf8_encode($cabecalho['NOME_FORNEC']);
        $jsonData['loja'] = $cabecalho['LOJA'];
        $jsonData['grupo'] = $cabecalho['GRUPO'];

        $pedidoCompra['json_data'] = json_encode($jsonData);

        $pedidoCompra['inserted'] = (new \DateTime())->format('Y-m-d H:i:s');
        $pedidoCompra['updated'] = (new \DateTime())->format('Y-m-d H:i:s');
        $pedidoCompra['version'] = 0;
        $pedidoCompra['estabelecimento_id'] = 1;
        $pedidoCompra['user_inserted_id'] = 1;
        $pedidoCompra['user_updated_id'] = 1;

        $conn->insert('est_pedidocompra', $pedidoCompra);
        $pedidoCompraId = $conn->lastInsertId();

        foreach ($dadosEkt as $i => $item) {

            $pedidoCompraItem = [];
            $pedidoCompraItem['pedidocompra_id'] = $pedidoCompraId;

            $produto = $this->produtos[$item['COD_PROD']];
            if (!$produto) {
                $arrProduto = [
                    'codigoProduto' => $item['COD_PROD'],
                    'codigoFornecedor' => $item['COD_FORNEC'],
                    'cpfcnpjFornecedor' => $item['CNPJFORNEC'],
                    'nomeFornecedor' => $item['NOME_FORNEC'],
                    'nome' => $item['DESC_PROD']
                ];

                $produto['id'] = $this->relEstoque01Business->handleNaEstProduto($arrProduto);
                $this->buildProdutosArray();
            }


            $pedidoCompraItem['ordem'] = $i + 1;
            $pedidoCompraItem['qtde'] = $item['QTDE'];
            $pedidoCompraItem['descricao'] = $produto['nome'];
            $pedidoCompraItem['preco_custo'] = $item['TOTAL_PRECO_CUSTO'];

            $pedidoCompraItem['inserted'] = (new \DateTime())->format('Y-m-d H:i:s');
            $pedidoCompraItem['updated'] = (new \DateTime())->format('Y-m-d H:i:s');
            $pedidoCompraItem['version'] = 0;
            $pedidoCompraItem['estabelecimento_id'] = 1;
            $pedidoCompraItem['user_inserted_id'] = 1;
            $pedidoCompraItem['user_updated_id'] = 1;

            $jsonData = [];
            $jsonData['erp_codigo'] = $item['COD_PROD'];
            $produtoJsonData = json_decode($produto['json_data'], true);
            $jsonData['produto_id'] = $produto['id'];
            $jsonData['produto_nome'] = $produto['nome'] ?? null;
            $jsonData['produto_depto_id'] = $produto['depto_id'] ?? null;
            $jsonData['produto_depto_nome'] = $produtoJsonData['depto_nome'] ?? null;
            $jsonData['produto_grupo_id'] = $produto['grupo_id'] ?? null;
            $jsonData['produto_grupo_nome'] = $produto['grupo_nome'] ?? null;
            $jsonData['produto_subgrupo_id'] = $produto['subgrupo_id'] ?? null;
            $jsonData['produto_subgrupo_nome'] = $produto['subgrupo_nome'] ?? null;
            $jsonData['produto_preco_tabela'] = $produto['subgrupo_nome'] ?? null;
            $jsonData['produto_recnum'] = $produtoJsonData['recnum'] ?? null;
            $jsonData['produto_cod_edi'] = $produtoJsonData['cod_edi'] ?? null;
            $jsonData['produto_erp_codigo'] = $produtoJsonData['erp_codigo'] ?? null;

            $pedidoCompraItem['json_data'] = json_encode($jsonData);
            $conn->insert('est_pedidocompra_item', $pedidoCompraItem);
        }
    }

    /**
     * Constrói o array/cachê dos produtos
     */
    private function buildProdutosArray()
    {
        /** @var Connection $conn */
        $conn = $this->doctrine->getConnection();
        $rProdutos = $conn->fetchAll('SELECT *, json_data->>"$.erp_codigo" as erp_codigo FROM est_produto');

        $this->produtos = [];
        foreach ($rProdutos as $rProduto) {
            $this->produtos[$rProduto['erp_codigo']] = $rProduto;
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
            $appConfig = $repoAppConfig->findOneByFiltersSimpl([['chave', 'EQ', 'relCompras01.dthrAtualizacao'], ['appUUID', 'EQ', $_SERVER['CROSIERAPP_UUID']]]);
            if (!$appConfig) {
                $appConfig = new AppConfig();
                $appConfig->setChave('relCompras01.dthrAtualizacao');
                $appConfig->setAppUUID($_SERVER['CROSIERAPP_UUID']);
            }
            $appConfig->setValor((new \DateTime())->format('Y-m-d H:i:s.u'));
            $this->appConfigEntityHandler->save($appConfig);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao marcar app_config (relCompras01.dthrAtualizacao)');
            $this->logger->error($e->getMessage());
            throw new ViewException('Erro ao marcar dt/hr atualização');
        }
    }

}
