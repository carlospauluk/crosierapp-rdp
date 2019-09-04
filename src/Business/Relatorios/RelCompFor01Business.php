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
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 *
 * @package App\Business\Relatorios
 */
class RelCompFor01Business
{

    /** @var RegistryInterface */
    private $doctrine;

    /** @var LoggerInterface */
    private $logger;

    /** @var AppConfigEntityHandler */
    private $appConfigEntityHandler;

    /**
     * @param RegistryInterface $doctrine
     * @param LoggerInterface $logger
     * @param AppConfigEntityHandler $appConfigEntityHandler
     */
    public function __construct(RegistryInterface $doctrine,
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
        $pastaFila = $_SERVER['PASTA_UPLOAD_RELCOMPFOR01'] . 'fila/';
        $files = scandir($pastaFila, 0);
        foreach ($files as $file) {
            if (!in_array($file, array('.', '..'))) {

                try {
                    $this->processarArquivo($file);
                    $this->marcarDtHrAtualizacao();
                    $this->logger->info('Arquivo processado com sucesso.');
                    rename($pastaFila . $file, $_SERVER['PASTA_UPLOAD_RELCOMPFOR01'] . 'ok/' . $file);
                    $this->logger->info('Arquivo movido para pasta "ok".');
                } catch (\Exception $e) {
                    rename($pastaFila . $file, $_SERVER['PASTA_UPLOAD_RELCOMPFOR01'] . 'falha/' . $file);
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
        $pastaFila = $_SERVER['PASTA_UPLOAD_RELCOMPFOR01'] . 'fila/';
        $conteudo = file_get_contents($pastaFila . $arquivo);
        $linhas = explode(PHP_EOL, $conteudo);
        $totalRegistros = count($linhas);
        /** @var Connection $conn */
        $conn = $this->doctrine->getEntityManager()->getConnection();

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
                if (count($campos) !== 11) {
                    throw new ViewException('Qtde de campos difere de 11 para a linha "' . $linha . '"');
                }

                $campos[2] = DateTimeUtils::parseDateStr($campos[2])->format('Y-m-d');

                $cMax = count($campos);
                for ($c = 0; $c < $cMax; $c++) {
                    $campos[$c] = $campos[$c] ? "'" . trim(str_replace("'", "''", $campos[$c])) . "'" : 'null';
                }

                $sql = sprintf(
                    'INSERT INTO rdp_rel_compfor01 (
                            id,
                            lancto,
                            docto,
                            dt_movto,
                            cod_prod,
                            desc_prod,
                            qtde,
                            preco_custo,
                            total,
                            cod_fornec,
                            nome_fornec,
                            obs,
                            estabelecimento_id,inserted,updated,user_inserted_id,user_updated_id
                        )
                    VALUES(null,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s, 1, now(), now(), 1, 1)',
                    $campos[0], // lancto,
                    $campos[1], // docto,
                    $campos[2], // dt_movto,
                    $campos[3], // produto_cod,
                    $campos[4], // produto_desc,
                    $campos[5], // qtde,
                    $campos[6], // preco_custo,
                    $campos[7], // total,
                    $campos[8], // cod_fornec,
                    $campos[9], // nome_fornec,
                    $campos[10]// obs
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
            $appConfig = $repoAppConfig->findOneByFiltersSimpl([['chave', 'EQ', 'relCompFor01.dthrAtualizacao'], ['appUUID', 'EQ', $_SERVER['CROSIERAPP_UUID']]]);
            $appConfig->setValor((new \DateTime())->format('Y-m-d H:i:s.u'));
            $this->appConfigEntityHandler->save($appConfig);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao marcar app_config (relCompFor01.dthrAtualizacao)');
            $this->logger->error($e->getMessage());
            throw new ViewException('Erro ao marcar dt/hr atualização');
        }
    }

}
