<?php

namespace App\Business\Relatorios;


use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\Config\AppConfigEntityHandler;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\AppConfigRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 *
 * @package App\Business\Relatorios
 */
class RelVendas01Business
{

    /** @var EntityManagerInterface */
    private $doctrine;

    /** @var LoggerInterface */
    private $logger;

    /** @var AppConfigEntityHandler */
    private $appConfigEntityHandler;

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
                    $this->logger->info('Arquivo processado com sucesso.');
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
     * @throws ViewException
     */
    public function processarArquivo(string $arquivo): int
    {
        $pastaFila = $_SERVER['PASTA_UPLOAD_RELVENDAS01'] . 'fila/';
        $conteudo = file_get_contents($pastaFila . $arquivo);
        $linhas = explode(PHP_EOL, $conteudo);
        $totalRegistros = count($linhas);
        /** @var Connection $conn */
        $conn = $this->doctrine->getConnection();

        $conn->beginTransaction();

        $t = 0;
        $linha = null;
        try {
            for ($i = 1; $i < $totalRegistros; $i++) {
                $linha = $linhas[$i];
                if (!trim($linha)) {
                    continue;
                }
                $campos = explode('|', $linha);
                if (count($campos) !== 21) {
                    throw new ViewException('Qtde de campos difere de 21 para a linha "' . $linha . '"');
                }

                $campos[3] = DateTimeUtils::parseDateStr($campos[3])->format('Y-m-d');

                $cMax = count($campos);
                for ($c = 0; $c < $cMax; $c++) {
                    $campos[$c] = trim($campos[$c]) !== '' ? "'" . trim(str_replace("'", "''", $campos[$c])) . "'" : 'null';
                }

                $sql = sprintf(
                    'INSERT INTO rdp_rel_vendas01 (
                            id,                            
                            prevenda,
                            num_item,
                            qtde,
                            dt_emissao,
                            ano,
                            mes,
                            cod_fornec,
                            nome_fornec,
                            cod_prod,
                            desc_prod,
                            total_preco_venda,
                            total_preco_custo,
                            rentabilidade,
                            cod_vendedor,
                            nome_vendedor,
                            loja,            
                            total_custo_pv,
                            total_venda_pv,
                            rentabilidade_pv,
                            cliente_pv,
                            grupo,
                            estabelecimento_id,inserted,updated,user_inserted_id,user_updated_id
                        )
                    VALUES(null,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s, 1, now(), now(), 1, 1)',
                    $campos[0], // `prevenda`,
                    $campos[1], // `num_item`,
                    $campos[2], // `qtde`,
                    $campos[3], // `dt_emissao`,
                    $campos[4], // `ano`,
                    $campos[5], // `mes`
                    $campos[6], // `cod_fornec`
                    $campos[7], // `nome_fornec`
                    $campos[8], // `cod_prod`
                    $campos[9], // `desc_prod`
                    $campos[10],// `total_preco_venda`
                    $campos[11],// `total_preco_custo`
                    $campos[12],// `rentabilidade`
                    $campos[13],// `cod_vendedor`
                    $campos[14],// `nome_vendedor`
                    $campos[15],// `loja`
                    $campos[16],// `total_custo_pv`
                    $campos[17],// `total_venda_pv`
                    $campos[18],// `rentabilidade_pv`
                    $campos[19],// `cliente_pv`
                    $campos[20] // `grupo`
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
