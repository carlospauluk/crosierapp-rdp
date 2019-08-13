<?php

namespace App\Business\Relatorios;


use App\Entity\Relatorios\RelEstoque01;
use App\Repository\Relatorios\RelEstoque01Repository;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 *
 * @package App\Business\Relatorios
 */
class RelEstoque01Business
{

    /** @var RegistryInterface */
    private $doctrine;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param RegistryInterface $doctrine
     * @param LoggerInterface $logger
     */
    public function __construct(RegistryInterface $doctrine,
                                LoggerInterface $logger)
    {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
    }

    /**
     *
     */
    public function processarArquivosNaFila(): void
    {
        $pastaFila = $_SERVER['PASTA_UPLOAD_RELESTOQUE01'] . 'fila/';
        $files = scandir($pastaFila, 0);
        foreach ($files as $file) {
            if (!in_array($file, array('.', '..'))) {

                try {
                    $this->processarArquivo($file);
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
        $conn = $this->doctrine->getEntityManager()->getConnection();

        $conn->beginTransaction();


        $t = 0;
        $linha = null;
        try {
            $conn->executeUpdate('DELETE FROM rdp_rel_estoque01');
            for ($i = 1; $i < $totalRegistros; $i++) {
                $linha = $linhas[$i];
                if (!trim($linha)) {
                    continue;
                }
                $linha = $linha[-1] === '|' ? substr($linha, 0, -1) : $linha;

                $campos = explode('|', $linha);
                if (count($campos) !== 11) {
                    throw new ViewException('Qtde de campos difere de 11 para a linha "' . $linha . '" (qtde: ' . count($campos) . ')');
                }

                if ($campos[8] ?: false) {
                    $campos[8] = DateTimeUtils::parseDateStr($campos[8])->format('Y-m-d');
                }

                $cMax = count($campos);
                for ($c = 0; $c < $cMax; $c++) {
                    $campos[$c] = trim($campos[$c]) !== '' ? "'" . trim(str_replace("'", "''", $campos[$c])) . "'" : 'null';
                }

                // CODIGO|DESCRICAO|CUSTO_MEDIO|PRECO_VENDA|FILIAL|QTDE_MINIMA|QTDE_MAXIMA|QTDE_ATUAL|DATA_ULT_SAIDA|CODIGO_FORNECEDOR|FORNECEDOR

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
                            estabelecimento_id,inserted,updated,user_inserted_id,user_updated_id
                        )
                    VALUES(null,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s, 1, now(), now(), 1, 1)',
                    $campos[0],
                    $campos[1],
                    $campos[2],
                    $campos[3],
                    $campos[4],
                    $campos[5],
                    $campos[6],
                    $campos[7],
                    'DEFAULT',
                    $campos[8],
                    $campos[9],
                    $campos[10]
                );

                try {
                    $t += $conn->executeUpdate($sql);
                    $this->logger->info($t . ' inseridos');
                } catch (\Exception $e) {
                    $this->logger->info('Erro ao inserir a linha "' . $linha . '"');
                    $this->logger->info('Continuando.');
                }
            }
            $this->logger->info($t . ' registros inseridos');
            $conn->commit();
            $this->logger->info('commit');
        } catch (\Exception $e) {
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


    public function gerarPedidoCompra(array $carrinhoDeCompra): string
    {
        /** @var RelEstoque01Repository $repoEstoque */
        $repoEstoque = $this->doctrine->getRepository(RelEstoque01::class);

        $linhas = [
            'COD_PRODUTO|DESC_PRODUTO|FILIAL|COD_FORNEC|NOME_FORNEC|CUSTO_MEDIO|PRECO_VENDA|QTDE|TOTAL_CUSTO_MEDIO|COD_COMPRADOR|NOME_COMPRADOR'
        ];

        $nomeFornecedor = $repoEstoque->getNomeFornecedorByCodigo((int)$carrinhoDeCompra['fornecedor']);

        $comprador = explode(' - ', $carrinhoDeCompra['comprador']);


        foreach ($carrinhoDeCompra['itens'] as $item) {

            $regs = [
                $item['codProduto'],
                $item['descProduto'],
                $item['descFilial'],
                $carrinhoDeCompra['fornecedor'],
                $nomeFornecedor,
                $item['custoMedio'],
                $item['precoVenda'],
                $item['qtde'],
                $item['totalCustoMedio'],
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
