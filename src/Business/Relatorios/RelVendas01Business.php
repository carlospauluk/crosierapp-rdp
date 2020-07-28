<?php

namespace App\Business\Relatorios;


use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\Config\AppConfigEntityHandler;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\AppConfigRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibRadxBundle\EntityHandler\Vendas\VendaEntityHandler;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 *
 *
 * @package App\Business\Relatorios
 */
class RelVendas01Business
{

    private EntityManagerInterface $doctrine;

    private LoggerInterface $logger;

    private AppConfigEntityHandler $appConfigEntityHandler;

    private VendaEntityHandler $vendaEntityHandler;

    private RelEstoque01Business $relEstoque01Business;

    private array $produtos;

    /**
     * @param EntityManagerInterface $doctrine
     * @param LoggerInterface $logger
     * @param AppConfigEntityHandler $appConfigEntityHandler
     * @param VendaEntityHandler $vendaEntityHandler
     * @param RelEstoque01Business $relEstoque01Business
     */
    public function __construct(EntityManagerInterface $doctrine,
                                LoggerInterface $logger,
                                AppConfigEntityHandler $appConfigEntityHandler,
                                VendaEntityHandler $vendaEntityHandler,
                                RelEstoque01Business $relEstoque01Business)
    {
        $this->doctrine = $doctrine;
        $this->appConfigEntityHandler = $appConfigEntityHandler;
        $this->vendaEntityHandler = $vendaEntityHandler;
        $this->logger = $logger;
        $this->relEstoque01Business = $relEstoque01Business;
    }

    /**
     *
     */
    public function processarArquivosNaFila(): void
    {
        $pastaFila = $_SERVER['PASTA_UPLOAD_RELVENDAS01'] . 'fila/';
        $files = scandir($pastaFila, 0);
        foreach ($files as $file) {
            if (!in_array($file, array('.', '..'))) {

                try {
                    $this->processarArquivo($file);
                    $this->marcarDtHrAtualizacao();
                    $this->logger->info('Arquivo de vendas processado com sucesso.');
                    rename($pastaFila . $file, $_SERVER['PASTA_UPLOAD_RELVENDAS01'] . 'ok/' . $file);
                    $this->logger->info('Arquivo movido para pasta "ok".');
                } catch (\Exception $e) {
                    rename($pastaFila . $file, $_SERVER['PASTA_UPLOAD_RELVENDAS01'] . 'falha/' . $file);
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
        $pastaFila = $_SERVER['PASTA_UPLOAD_RELVENDAS01'] . 'fila/';
        $conteudo = file_get_contents($pastaFila . $arquivo);
        $linhas = explode(PHP_EOL, $conteudo);
        $totalRegistros = count($linhas) - 2;
        /** @var Connection $conn */
        $conn = $this->doctrine->getConnection();

        $t = 0;
        $linha = null;

        $relVendas01 = [];

        try {
            for ($i = 1; $i < $totalRegistros; $i++) {
                $linha = $linhas[$i];
                if (!trim($linha)) {
                    continue;
                }

                $camposSplit = explode('|', $linha);
                if (count($camposSplit) !== 28) {
                    throw new ViewException('Qtde de campos difere de 28 para a linha "' . $linha . '"');
                }

                $cMax = count($camposSplit);
                for ($c = 0; $c < $cMax; $c++) {
                    $camposSplit[$c] = trim($camposSplit[$c]) !== '' ? trim(str_replace("'", "''", $camposSplit[$c])) : null;
                }

                $prevenda = $camposSplit[0];

                $campos = [];
                $campos['PREVENDA'] = $prevenda;
                $campos['ITEM'] = $camposSplit[1];
                $campos['QTDE'] = $camposSplit[2];
                $campos['EMISSAO'] = $camposSplit[3] ? DateTimeUtils::parseDateStr($camposSplit[3])->format('Y-m-d') : null;
                $campos['ANO'] = $camposSplit[4];
                $campos['MES'] = $camposSplit[5];
                $campos['COD_FORNEC'] = $camposSplit[6];
                $campos['CNPJFORNEC'] = preg_replace("/[^0-9]/", "", $camposSplit[7]);
                $campos['NOME_FORNEC'] = $camposSplit[8];
                $campos['COD_PROD'] = $camposSplit[9];
                $campos['DESC_PROD'] = $camposSplit[10];
                $campos['TOTAL_PRECO_VENDA'] = $camposSplit[11];
                $campos['TOTAL_PRECO_CUSTO'] = $camposSplit[12];
                $campos['RENTABILIDADE_ITEM'] = $camposSplit[13];
                $campos['COD_VENDEDOR'] = $camposSplit[14];
                $campos['NOME_VENDEDOR'] = $camposSplit[15];
                $campos['LOJA'] = $camposSplit[16];
                $campos['TOTAL_CUSTO_PV'] = $camposSplit[17];
                $campos['TOTAL_VENDA_PV'] = $camposSplit[18];
                $campos['RENTABILIDADE_PV'] = $camposSplit[19];
                $campos['CODCLI'] = $camposSplit[20];
                $campos['CNPJCLI'] = preg_replace("/[^0-9]/", "", $camposSplit[21]);
                $campos['CLIENTE_PV'] = $camposSplit[22];
                $campos['GRUPO'] = $camposSplit[23];
                $campos['NOTA'] = $camposSplit[24];
                $campos['DATA_NOTA'] = $camposSplit[25] ? DateTimeUtils::parseDateStr($camposSplit[25])->format('Y-m-d') : $campos['EMISSAO'];
                $campos['MARGEM_LIQUIDA'] = $camposSplit[26];
                $campos['NOVA_COMISSAO'] = $camposSplit[27];

                if ((float)$campos['RENTABILIDADE_PV'] === -100.0) {
                    $this->logger->info('NÃO IMPORTAR. Regra "RENTABILIDADE PV" = -100.00');
                    continue;
                }


                $relVendas01[$prevenda][] = $campos;
                $this->logger->info('camposAgrupados: ' . str_pad($i, 9, '0', STR_PAD_LEFT) . '/' . $totalRegistros);
            }

            $rVenVendas = $conn->fetchAll('select v.id, v.cliente_id, v.valor_total, v.json_data, count(*) as qtde_itens from ven_venda v left join ven_venda_item i on v.id = i.venda_id group by v.id');
            $venVendas = [];
            foreach ($rVenVendas as $rVenVenda) {
                $jsonData = json_decode($rVenVenda['json_data'], true);
                if ($jsonData['prevenda_ekt'] ?? null) {
                    $venVendas[$jsonData['prevenda_ekt']] = $rVenVenda;
                }
            }

            $totalRelVendas01 = count($relVendas01);
            $i = 0;

            $this->buildProdutosArray();
            foreach ($relVendas01 as $num_pv_ekt => $relVenda01) {
                if (isset($venVendas[$num_pv_ekt])) {
                    $venVenda = $venVendas[$num_pv_ekt];
                    // se mudou o sub_total ou o cliente ou a qtde de itens, apaga do ven_venda e deixa reinserir
                    $venVendaJsonData = json_decode($venVenda['json_data'], true);
                    if ((float)$venVenda['valor_total'] !== (float)$relVenda01[0]['TOTAL_VENDA_PV'] ||
                        (int)$venVendaJsonData['cod_cliente_ekt'] !== (int)$relVenda01[0]['CODCLI'] ||
                        count($relVenda01) !== (int)$venVenda['qtde_itens']) {
                        $conn->delete('ven_venda', ['id' => $venVendas[$num_pv_ekt]['id']]);
                    } else {
                        $this->logger->info('ven_venda: ' . str_pad(++$i, 9, '0', STR_PAD_LEFT) . '/' . $totalRelVendas01);
                        continue;
                    }
                }

                $this->handleVenda($relVenda01);
                $this->logger->info('ven_venda: ' . str_pad(++$i, 9, '0', STR_PAD_LEFT) . '/' . $totalRelVendas01);
            }
            $this->logger->info('FIM. OK!');
        } catch (\Throwable $e) {
            $this->logger->error('processarArquivo() - erro ');
            $this->logger->error($e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }

        return $t;
    }

    /**
     * @param array $dadosVendaEkt
     * @throws \Doctrine\DBAL\DBALException
     */
    private function handleVenda(array $dadosVendaEkt): void
    {
        /** @var Connection $conn */
        $conn = $this->doctrine->getConnection();

        $cabecalho = $dadosVendaEkt[0];

        $venda = [];

        $cliente = $conn->fetchAssoc('SELECT id FROM crm_cliente WHERE documento = ?', [$cabecalho['CNPJCLI']]);
        if (!$cliente) {

            try {
                $dadosCliente = [];
                $dadosCliente['documento'] = $cabecalho['CNPJCLI'];
                $dadosCliente['nome'] = utf8_encode($cabecalho['CLIENTE_PV']);
                $dadosCliente['json_data'] = json_encode(['cod_cliente_ekt' => $cabecalho['CODCLI']]);
                $dadosCliente['inserted'] = (new \DateTime())->format('Y-m-d H:i:s');
                $dadosCliente['updated'] = (new \DateTime())->format('Y-m-d H:i:s');
                $dadosCliente['version'] = 0;
                $dadosCliente['estabelecimento_id'] = 1;
                $dadosCliente['user_inserted_id'] = 1;
                $dadosCliente['user_updated_id'] = 1;
                $conn->insert('crm_cliente', $dadosCliente);
                $cliente['id'] = $conn->lastInsertId();
            } catch (DBALException $e) {
                $this->logger->error('Erro ao salvar o cliente');
            }
        }

        $venda['cliente_id'] = $cliente['id'] ?? null;

        $venda['dt_venda'] = $cabecalho['EMISSAO'];

        $venda['vendedor_id'] = 1; // 'NÃO IDENTIFICADO'
        $venda['status'] = 'PV Finalizado';
        $venda['subtotal'] = $cabecalho['TOTAL_VENDA_PV'];
        $venda['desconto'] = 0.0;
        $venda['valor_total'] = $cabecalho['TOTAL_VENDA_PV'];

        $jsonData = [];
        $jsonData['prevenda_ekt'] = $cabecalho['PREVENDA'];
        $jsonData['cod_cliente_ekt'] = $cabecalho['CODCLI'];
        $jsonData['cliente_cnpj'] = $cabecalho['CNPJCLI'];
        $jsonData['cliente_nome'] = utf8_encode($cabecalho['CLIENTE_PV']);
        $jsonData['vendedor_codigo'] = $cabecalho['COD_VENDEDOR'];
        $jsonData['vendedor_nome'] = $cabecalho['NOME_VENDEDOR'];
        $jsonData['loja'] = $cabecalho['LOJA'];
        $jsonData['grupo'] = $cabecalho['GRUPO'];
        $jsonData['nota'] = $cabecalho['NOTA'];
        $jsonData['dt_nota'] = $cabecalho['DATA_NOTA'];
        $jsonData['margem_liquida'] = $cabecalho['MARGEM_LIQUIDA'];
        $jsonData['nova_comissao'] = $cabecalho['NOVA_COMISSAO'];
        $jsonData['total_custo_pv'] = $cabecalho['TOTAL_CUSTO_PV'];
        $jsonData['rentabilidade_pv'] = $cabecalho['RENTABILIDADE_PV'];
        $jsonData['canal'] = 'LOJA FÍSICA';

        $venda['json_data'] = json_encode($jsonData);

        $venda['inserted'] = (new \DateTime())->format('Y-m-d H:i:s');
        $venda['updated'] = (new \DateTime())->format('Y-m-d H:i:s');
        $venda['version'] = 0;
        $venda['estabelecimento_id'] = 1;
        $venda['user_inserted_id'] = 1;
        $venda['user_updated_id'] = 1;
        $venda['plano_pagto_id'] = 1;

        $conn->insert('ven_venda', $venda);
        $vendaId = $conn->lastInsertId();

        foreach ($dadosVendaEkt as $i => $item) {

            $vendaItem = [];
            $vendaItem['venda_id'] = $vendaId;

            $produto = $this->produtos[$item['COD_PROD']] ?? null;
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

            $vendaItem['produto_id'] = $produto['id'];
            $vendaItem['ordem'] = $i + 1;
            $vendaItem['qtde'] = $item['QTDE'];
            $vendaItem['descricao'] = $produto['nome'] ?? '<<< PRODUTO SEM NOME >>>';
            $vendaItem['preco_venda'] = bcdiv($item['TOTAL_PRECO_VENDA'], ($item['QTDE'] > 0) ? $item['QTDE'] :  1, 2);
            $vendaItem['subtotal'] = bcmul($vendaItem['qtde'], $vendaItem['preco_venda'], 2);
            $vendaItem['desconto'] = 0.0;
            $vendaItem['total'] = $vendaItem['subtotal'];

            $vendaItem['inserted'] = (new \DateTime())->format('Y-m-d H:i:s');
            $vendaItem['updated'] = (new \DateTime())->format('Y-m-d H:i:s');
            $vendaItem['version'] = 0;
            $vendaItem['estabelecimento_id'] = 1;
            $vendaItem['user_inserted_id'] = 1;
            $vendaItem['user_updated_id'] = 1;

            $jsonData = [];
            $jsonData['erp_codigo'] = $item['COD_PROD'];
            $jsonData['total_preco_custo'] = $item['TOTAL_PRECO_CUSTO'];
            $jsonData['rentabilidade_item'] = $item['RENTABILIDADE_ITEM'];

            $produtoJsonData = json_decode($produto['json_data'] ?? '{}', true);
            $jsonData['fornecedor_id'] = $produto['fornecedor_id'] ?? null;
            $jsonData['fornecedor_documento'] = $produtoJsonData['fornecedor_documento'] ?? null;
            $jsonData['fornecedor_nome'] = $produtoJsonData['fornecedor_nome'] ?? null;
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


            $vendaItem['json_data'] = json_encode($jsonData);

            $conn->insert('ven_venda_item', $vendaItem);
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
            $appConfig = $repoAppConfig->findOneByFiltersSimpl([['chave', 'EQ', 'relVendas01.dthrAtualizacao'], ['appUUID', 'EQ', $_SERVER['CROSIERAPP_UUID']]]);
            if (!$appConfig) {
                $appConfig = new AppConfig();
                $appConfig->setChave('relVendas01.dthrAtualizacao');
                $appConfig->setAppUUID($_SERVER['CROSIERAPP_UUID']);
            }
            $appConfig->setValor((new \DateTime())->format('Y-m-d H:i:s.u'));
            $this->appConfigEntityHandler->save($appConfig);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao marcar app_config (relVendas01.dthrAtualizacao)');
            $this->logger->error($e->getMessage());
            throw new ViewException('Erro ao marcar dt/hr atualização');
        }
    }

}
