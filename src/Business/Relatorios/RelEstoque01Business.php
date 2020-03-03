<?php

namespace App\Business\Relatorios;


use App\Entity\Relatorios\RelEstoque01;
use App\Repository\Relatorios\RelEstoque01Repository;
use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\Config\AppConfigEntityHandler;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\AppConfigRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\StringUtils\StringUtils;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

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

    private array $ids;

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
    }


    public function prepararCampos()
    {
        /** @var Connection $conn */
        $conn = $this->doctrine->getConnection();
        $this->ids = [];
        // Aqui é pego o depto, grupo e subgrupo 'INDEFINIDO', pois não temos vinculação a princípio.
        $this->ids['depto'] = $conn->fetchAssoc('SELECT id FROM est_depto WHERE uuid = \'54d9b263-1ac1-11ea-aa1a-02f5eec21cc2\'')['id'];
        $this->ids['grupo'] = $conn->fetchAssoc('SELECT id FROM est_grupo WHERE uuid = \'b111cb42-1ac1-11ea-aa1a-02f5eec21cc2\'')['id'];
        $this->ids['subgrupo'] = $conn->fetchAssoc('SELECT id FROM est_subgrupo WHERE uuid = \'ee784eec-1ac1-11ea-aa1a-02f5eec21cc2\'')['id'];
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
        $totalRegistros = count($linhas);
        /** @var Connection $conn */
        $conn = $this->doctrine->getConnection();

        $conn->beginTransaction();

        $t = 0;
        $linha = null;
        try {
            $camposAgrupados = [];
            $conn->executeUpdate('SET FOREIGN_KEY_CHECKS=0;TRUNCATE TABLE rdp_rel_estoque01;');
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
                    $campos[$c] = trim($campos[$c]) !== '' ? "'" . trim(str_replace("'", "''", $campos[$c])) . "'" : 'null';
                }

                // CODIGO|DESCRICAO|CUSTO_MEDIO|PRECO_VENDA|FILIAL|QTDE_MINIMA|QTDE_MAXIMA|QTDE_ATUAL|DATA_ULT_SAIDA|CODIGO_FORNECEDOR|FORNECEDOR|RECNUM|COD_EDI

                $sql = sprintf(
                    'INSERT INTO rdp_rel_estoque01 (
                            id,                            
                            cod_prod,      
                            desc_prod,     
                            custo_medio,   
                            preco_venda,   
                            desc_filial,   
                            qtde_minima,   
                            qtde_maxima,   
                            qtde_atual,  
                            deficit,  
                            dt_ult_saida,  
                            cod_fornec,   
                            nome_fornec,
                               recnum,
                               cod_edi,
                            estabelecimento_id,inserted,updated,user_inserted_id,user_updated_id
                        )
                    VALUES(null,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s, 1, now(), now(), 1, 1)',
                    $campos[0],
                    mb_convert_encoding($campos[1], 'ISO-8859-1', 'UTF-8'),
                    $campos[2],
                    $campos[3],
                    $campos[4],
                    $campos[5],
                    $campos[6],
                    $campos[7],
                    'DEFAULT',
                    $campos[8],
                    $campos[9],
                    mb_convert_encoding($campos[10], 'ISO-8859-1', 'UTF-8'),
                    $campos[11],
                    mb_convert_encoding($campos[12], 'ISO-8859-1', 'UTF-8'),
                );

                try {
                    $t += $conn->executeUpdate($sql);
                } catch (\Exception $e) {
                    $this->logger->info('Erro ao inserir a linha "' . $linha . '". Continuando...');
                }


                try {
                    $dtUltSaida = $campos[8] ? DateTimeUtils::parseDateStr($campos[8])->format('Y-m-d') : null;
                } catch (\Exception $e) {
                    $dtUltSaida = null;
                }
                $linhaExploded = explode('|', $linha);
                $codigo = trim($linhaExploded[0]);
                $camposAgrupados[$codigo] = isset($camposAgrupados[$codigo]) ? array_replace($camposAgrupados[$codigo], $linhaExploded) : $linhaExploded;
                $filial = trim($camposAgrupados[$codigo][4]);
                switch ($filial) {
                    case 'MATRIZ':
                        $camposAgrupados[$codigo]['qtde_estoque_matriz'] = $linhaExploded[7];
                        $camposAgrupados[$codigo]['dt_ult_saida_matriz'] = $dtUltSaida;
                        $camposAgrupados[$codigo]['erp_dt_ult_saida'] = $dtUltSaida; // está duplicado mesmo
                        $camposAgrupados[$codigo]['preco_custo'] = $linhaExploded[2];
                        $camposAgrupados[$codigo]['preco_tabela'] = $linhaExploded[3];
                        break;
                    case 'DELPOZO-PG':
                        $camposAgrupados[$codigo]['qtde_estoque_delpozo'] = $linhaExploded[7];
                        $camposAgrupados[$codigo]['dt_ult_saida_delpozo'] = $dtUltSaida;
                        break;
                    case 'ACESSORIOS':
                        $camposAgrupados[$codigo]['qtde_estoque_acessorios'] = $linhaExploded[7];
                        $camposAgrupados[$codigo]['dt_ult_saida_acessorios'] = $dtUltSaida;
                        break;
                    case 'TELEMACO':
                        $camposAgrupados[$codigo]['qtde_estoque_telemaco'] = $linhaExploded[7];
                        $camposAgrupados[$codigo]['dt_ult_saida_telemaco'] = $dtUltSaida;
                        break;
                    default:
                        $this->logger->warning('FILIAL não reconhecida para atualização de qtde_estoque: ' . $linhaExploded[4]);
                }
                $this->logger->info('rdp_rel_estoque01: ' . $i . '/' . $totalRegistros);
            }

            $totalCamposAgrupados = count($camposAgrupados);
            $i = 0;
            foreach ($camposAgrupados as $campoAgrupado) {
                $this->handleNaEstProduto($campoAgrupado);
                $this->logger->info('est_produto: ' . ++$i . '/' . $totalCamposAgrupados);
            }

            $conn->commit();
            $this->logger->info('commit');
        } catch (\Throwable $e) {
            $this->logger->error('processarArquivo() - erro ');
            $this->logger->info('Erro ao inserir a linha "' . $linha . '"');
            $this->logger->error($e->getMessage());
            try {
                $conn->rollBack();
            } catch (ConnectionException $e) {
                throw new ViewException($e->getMessage());
            }
            throw new \RuntimeException($e->getMessage());
        }

        return $t;


    }

    /**
     * @param array $campos
     * @return string
     */
    private function handleNaEstProduto(array $campos): string
    {
        try {

            foreach ($campos as $k => $v) {
                $campos[$k] = trim(str_replace("'", '', $v));
            }

            /** @var Connection $conn */
            $conn = $this->doctrine->getConnection();

            $produto = $conn->fetchAssoc('SELECT * FROM est_produto WHERE json_data->>"$.erp_codigo" = :cod', ['cod' => $campos[0]]);
            $updating = true;
            $json_data_ORIG = null;
            if (!$produto) {
                $updating = false;
                $produto = [];
                $json_data = [];
                $produto['uuid'] = StringUtils::guidv4();
                $produto['depto_id'] = $this->ids['depto'];
                $json_data['depto_codigo'] = '00';
                $json_data['depto_nome'] = 'INDEFINIDO';
                $produto['grupo_id'] = $this->ids['grupo'];
                $json_data['grupo_codigo'] = '00';
                $json_data['grupo_nome'] = 'INDEFINIDO';
                $produto['subgrupo_id'] = $this->ids['subgrupo'];
                $json_data['subgrupo_codigo'] = '00';
                $json_data['subgrupo_nome'] = 'INDEFINIDO';
                $json_data['erp_codigo'] = $campos[0];

                $fornecedor = $conn->fetchAssoc('SELECT * FROM est_fornecedor WHERE codigo = ?', [$campos[9]]);
                if (!$fornecedor) {
                    unset($dadosFornecedor, $fornecedor);
                    $dadosFornecedor = [];
                    $dadosFornecedor['codigo'] = $campos[9];
                    $dadosFornecedor['nome'] = $campos[10];
                    $dadosFornecedor['inserted'] = (new \DateTime())->format('Y-m-d H:i:s');
                    $dadosFornecedor['updated'] = (new \DateTime())->format('Y-m-d H:i:s');
                    $dadosFornecedor['version'] = 0;
                    $dadosFornecedor['estabelecimento_id'] = 1;
                    $dadosFornecedor['user_inserted_id'] = 1;
                    $dadosFornecedor['user_updated_id'] = 1;
                    $conn->insert('est_fornecedor', $dadosFornecedor);
                    $fornecedorId = $conn->lastInsertId();
                    $fornecedor = $conn->fetchAssoc('SELECT * FROM est_fornecedor WHERE id = ?', [$fornecedorId]);
                }

                $produto['fornecedor_id'] = $fornecedor['id'];
                $json_data['fornecedor_nome'] = $fornecedor['nome'];
                $json_data['fornecedor_documento'] = $fornecedor['documento'];

                $produto['nome'] = $campos[1];
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

            $json_data['recnum'] = $campos[11];
            $json_data['cod_edi'] = utf8_encode($campos[12]);

            $json_data['preco_custo'] = $campos[2];
            $json_data['preco_tabela'] = $campos[3];

            $json_data['qtde_imagens'] = $json_data['qtde_imagens'] ?? 0;
            $json_data['imagem1'] = $json_data['imagem1'] ?? '';

            $json_data['qtde_estoque_matriz'] = $campos['qtde_estoque_matriz'] ?? null;
            $json_data['dt_ult_saida_matriz'] = $campos['dt_ult_saida_matriz'] ?? null;
            $json_data['erp_dt_ult_saida'] = $campos['erp_dt_ult_saida'] ?? null; // está duplicado mesmo
            $json_data['qtde_estoque_delpozo'] = $campos['qtde_estoque_delpozo'] ?? null;
            $json_data['dt_ult_saida_delpozo'] = $campos['dt_ult_saida_delpozo'] ?? null;
            $json_data['qtde_estoque_acessorios'] = $campos['qtde_estoque_acessorios'] ?? null;
            $json_data['dt_ult_saida_acessorios'] = $campos['dt_ult_saida_acessorios'] ?? null;
            $json_data['qtde_estoque_telemaco'] = $campos['qtde_estoque_telemaco'] ?? null;
            $json_data['dt_ult_saida_telemaco'] = $campos['dt_ult_saida_telemaco'] ?? null;

            $produto['json_data'] = json_encode($json_data);

            if (!$produto['json_data']) {
                $this->logger->error('Erro ao gerar json_data para CODIGO = ' . $campos[0] . '. Continuando...');
                $produto['json_data'] = null;
            }

            $produto['updated'] = (new \DateTime())->format('Y-m-d H:i:s');

            if (!$updating) {
                $conn->insert('est_produto', $produto);
                $id = $conn->lastInsertId();
            } else {
                $id = $produto['id'];
                if (strcmp($produto['json_data'], json_encode($json_data_ORIG)) !== 0) {
                    $conn->update('est_produto', $produto, ['id' => $id]);
                } else {
                    $this->logger->info('Nada mudou para CODIGO = ' . $campos[0] . '. Continuando...');
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
     * Copiado do crosierapp-radx (App\EntityHandler\Estoque\ProdutoAtributoEntityHandler)
     *
     * @param int $produtoToId
     */
    public function colarConfigs(int $produtoToId): void
    {


        try {
            $cache = new FilesystemAdapter($_SERVER['CROSIERAPP_ID'] . '.cache', 0, $_SERVER['CROSIER_SESSIONS_FOLDER']);
            $produtoFromId = $cache->get('produtoBusiness.colarConfigs.produtoFromId', function (ItemInterface $item) {
                /** @var AppConfigRepository $repoAppConfig */
                $repoAppConfig = $this->doctrine->getRepository(AppConfig::class);
                /** @var AppConfig $appConfig */
                $appConfig = $repoAppConfig->findOneByFiltersSimpl([['chave', 'EQ', 'produtoBusiness.colarConfigs.produtoFromId'], ['appUUID', 'EQ', $_SERVER['CROSIERAPP_UUID']]]);

                return $appConfig->getValor();
            });
            /** @var Connection $conn */
            $conn = $this->doctrine->getConnection();
            $produtoFromAtributos = $conn->fetchAll('SELECT * FROM est_produto_atributo WHERE produto_id = :produto_id', ['produto_id' => $produtoFromId]);
            foreach ($produtoFromAtributos as $produtoAtributoFrom) {

                $produtoAtributoTo = null;
                $produtoToAtributos = $conn->fetchAll('SELECT * FROM est_produto_atributo WHERE produto_id = :produto_id', ['produto_id' => $produtoToId]);

                if ($produtoToAtributos) {
                    foreach ($produtoToAtributos as $prodAtribTo_) {
                        if ((int)$produtoAtributoFrom['atributo_id'] === (int)$prodAtribTo_['atributo_id']) {
                            $produtoAtributoTo = $prodAtribTo_;
                            break;
                        }
                    }
                }

                if (!$produtoAtributoTo) {
                    $produtoAtributoTo = [];
                    $produtoAtributoTo['produto_id'] = $produtoToId;
                    $produtoAtributoTo['atributo_id'] = $produtoAtributoFrom['atributo_id'];
                } else if ($produtoAtributoTo['precif'] === $produtoAtributoFrom['precif'] &&
                    $produtoAtributoTo['quantif'] === $produtoAtributoFrom['quantif'] &&
                    $produtoAtributoTo['soma_preench'] === $produtoAtributoFrom['soma_preench'] &&
                    $produtoAtributoTo['aba'] === $produtoAtributoFrom['aba'] &&
                    $produtoAtributoTo['grupo'] === $produtoAtributoFrom['grupo'] &&
                    (int)$produtoAtributoTo['ordem'] === (int)$produtoAtributoFrom['ordem']) {
                    continue;
                }

                $produtoAtributoTo['precif'] = $produtoAtributoFrom['precif'];
                $produtoAtributoTo['quantif'] = $produtoAtributoFrom['quantif'];
                $produtoAtributoTo['soma_preench'] = $produtoAtributoFrom['soma_preench'];
                $produtoAtributoTo['aba'] = $produtoAtributoFrom['aba'];
                $produtoAtributoTo['grupo'] = $produtoAtributoFrom['grupo'];
                $produtoAtributoTo['ordem'] = $produtoAtributoFrom['ordem'];

                if ($produtoAtributoTo['id'] ?? null) {
                    $produtoAtributoTo['updated'] = (new \DateTime())->format('Y-m-d H:i:s');
                    $conn->update('est_produto_atributo', $produtoAtributoTo, ['id' => $produtoAtributoTo['id']]);
                } else {
                    $produtoAtributoTo['inserted'] = (new \DateTime())->format('Y-m-d H:i:s');
                    $produtoAtributoTo['updated'] = (new \DateTime())->format('Y-m-d H:i:s');
                    $produtoAtributoTo['user_inserted_id'] = 1;
                    $produtoAtributoTo['user_updated_id'] = 1;
                    $produtoAtributoTo['estabelecimento_id'] = 1;
                    $conn->insert('est_produto_atributo', $produtoAtributoTo);
                }
            }

        } catch (\Throwable | InvalidArgumentException $e) {
            $this->logger->error('Erro ao colarConfigs($produtoToId=' . $produtoToId . ')');
            $this->logger->error($e->getMessage());
            throw new \RuntimeException('Erro ao colarConfigs($produtoToId=' . $produtoToId . ')');
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

    /**
     * @param array $carrinhoDeCompra
     * @return string
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function gerarPedidoCompra(array $carrinhoDeCompra): string
    {
        /** @var RelEstoque01Repository $repoEstoque */
        $repoEstoque = $this->doctrine->getRepository(RelEstoque01::class);

        $linhas = [
            'COD_PRODUTO|DESC_PRODUTO|FILIAL|COD_FORNEC|NOME_FORNEC|CUSTO_MEDIO|PRECO_VENDA|QTDE|TOTAL_CUSTO_MEDIO|COD_COMPRADOR|NOME_COMPRADOR'
        ];

        $nomeFornecedor = $repoEstoque->getNomeFornecedorByCodigo((int)$carrinhoDeCompra['fornecedor']);

        $comprador = explode(' - ', $carrinhoDeCompra['comprador']);


        /** @var RelEstoque01 $item */
        foreach ($carrinhoDeCompra['itens'] as $item) {
            $regs = [
                $item->getCodProduto(),
                $item->getDescProduto(),
                $item->getDescFilial(),
                $item->getCodFornecedor(),
                $item->getNomeFornecedor(),
                $item->getCustoMedio(),
                $item->getPrecoVenda(),
                $item->getDeficit(),
                $item->getTotalCustoMedio(),
                $comprador[0],
                $comprador[1]
            ];
            $linhas[] = implode('|', $regs);
        }
        $nomeArquivo = (new \DateTime('now'))->format('Y-m-d_H-i-s-U') . '.txt';
        $pasta = $_SERVER['PASTA_PEDIDOSCOMPRA'];

        file_put_contents($pasta . $nomeArquivo, implode(PHP_EOL, $linhas));
        return $nomeArquivo;
    }


}
