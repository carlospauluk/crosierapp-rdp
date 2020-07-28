<?php

namespace App\Business\Relatorios;


use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\Config\AppConfigEntityHandler;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\AppConfigRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 *
 * @package App\Business\Relatorios
 */
class RelCtsPagRec01Business
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
        $pastaFila = $_SERVER['PASTA_UPLOAD_RELCTSPAGREC01'] . 'fila/';
        $files = scandir($pastaFila, 0);
        $q = 0;
        foreach ($files as $file) {
            if (!in_array($file, array('.', '..'))) {
                try {
                    $this->processarArquivo($file);
                    $this->marcarDtHrAtualizacao();
                    $this->logger->info('Arquivo processado com sucesso.');
                    rename($pastaFila . $file, $_SERVER['PASTA_UPLOAD_RELCTSPAGREC01'] . 'ok/' . $file);
                    $this->logger->info('Arquivo movido para pasta "ok".');
                    $q++;
                } catch (\Exception $e) {
                    rename($pastaFila . $file, $_SERVER['PASTA_UPLOAD_RELCTSPAGREC01'] . 'falha/' . $file);
                    $this->logger->info('Arquivo movido para pasta "falha".');
                }
            }
        }
        $this->logger->info($q . ' arquivo(s) processado(s).');
    }

    /**
     * @param string $arquivo
     * @return int
     * @throws ViewException
     */
    public function processarArquivo(string $arquivo): int
    {
        $pastaFila = $_SERVER['PASTA_UPLOAD_RELCTSPAGREC01'] . 'fila/';
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
                if (count($campos) !== 17) {
                    throw new ViewException('Qtde de campos difere de 17 para a linha "' . $linha . '"');
                }

                $campos[2] = DateTimeUtils::parseDateStr($campos[2])->format('Y-m-d');
                $campos[3] = DateTimeUtils::parseDateStr($campos[3])->format('Y-m-d');
                $campos[4] = trim($campos[4]) ? DateTimeUtils::parseDateStr($campos[4])->format('Y-m-d') : '';
                $campos[16] = trim($campos[16]) ? DateTimeUtils::parseDateStr($campos[16])->format('Y-m-d') : '';

                if (!$campos[11]) {
                    $campos[11] = 0.0; // valor_titulo
                }

                $cMax = count($campos);
                for ($c = 0; $c < $cMax; $c++) {
                    $campos[$c] = $campos[$c] ? "'" . trim(str_replace("'", "''", $campos[$c])) . "'" : 'null';
                }



                $sql = sprintf(
                    'INSERT INTO rdp_rel_ctspagrec01 (
                            id,
                            lancto,
                            docto,
                            dt_movto,
                            dt_vencto,
                            dt_pagto,
                            cod_cliente,
                            nome_cli_for,
                            localizador,
                            localizador_desc,
                            filial,
                            desc_filial,
                            valor_titulo,
                            valor_baixa,
                            situacao,
                            tipo_pag_rec,
                            numero_nf,
                            dt_emissao_nf,
                            estabelecimento_id,inserted,updated,user_inserted_id,user_updated_id
                        )
                    VALUES(null,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s, 1, now(), now(), 1, 1)',
                    $campos[0], // `lancto`
                    $campos[1], //  `docto`
                    $campos[2], //  `dt_movto`
                    $campos[3], //  `dt_vencto`
                    $campos[4], //  `dt_pagto`
                    $campos[5], //  `cod_cliente`
                    $campos[6], //  `nome_cli_for`
                    $campos[7], //  `localizador`
                    $campos[8], //  `localizador_desc`
                    $campos[9], //  `filial`
                    $campos[10],// `desc_filial`
                    $campos[11],//  `valor_titulo`
                    $campos[12], // `valor_baixa`
                    $campos[13], // `situacao`
                    $campos[14], // `tipo_pag_rec`
                    $campos[15], // `numero_nf`
                    $campos[16]  // `dt_emissao_nf`
                );

                try {
                    $t += $conn->executeUpdate($sql);
                    $this->logger->info($t . ' inseridos');
                } catch (UniqueConstraintViolationException $e) {
                    $this->logger->info('Registro já existente para a linha "' . $linha . '"');
                    $this->logger->info('Continuando.');
                }
            }
            $this->logger->info($t . ' registros inseridos');
            $conn->commit();
            $this->logger->info('commit');
        } catch (\Exception $e) {
            $this->logger->error('processarArquivo() - erro ');
            $this->logger->error('Linha: |' . $linha . '|');
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
            $appConfig = $repoAppConfig->findOneByFiltersSimpl([['chave', 'EQ', 'relCtsPagRec01.dthrAtualizacao'], ['appUUID', 'EQ', $_SERVER['CROSIERAPP_UUID']]]);
            if (!$appConfig) {
                $appConfig = new AppConfig();
                $appConfig->setChave('relCtsPagRec01.dthrAtualizacao');
                $appConfig->setAppUUID($_SERVER['CROSIERAPP_UUID']);
            }
            $appConfig->setValor((new \DateTime())->format('Y-m-d H:i:s.u'));
            $this->appConfigEntityHandler->save($appConfig);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao marcar app_config (relCtsPagRec01.dthrAtualizacao)');
            $this->logger->error($e->getMessage());
            throw new ViewException('Erro ao marcar dt/hr atualização');
        }
    }

}
