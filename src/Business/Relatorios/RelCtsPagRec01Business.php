<?php

namespace App\Business\Relatorios;


use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Doctrine\DBAL\ConnectionException;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 *
 *
 * @package App\Business\Relatorios
 */
class RelCtsPagRec01Business
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
     * @param string $arquivo
     * @return void
     * @throws ViewException
     */
    public function processarArquivo(string $arquivo): void
    {

        $linhas = explode(PHP_EOL, $arquivo);
        $totalRegistros = count($linhas);
        /** @var \Doctrine\DBAL\Connection $conn */
        $conn = $this->doctrine->getEntityManager()->getConnection();

        $conn->beginTransaction();

        try {
            $t = 0;
            for ($i = 1; $i < $totalRegistros; $i++) {
                $linha = $linhas[$i];
                $campos = explode('|', $linha);
                if (count($campos) !== 15) {
                    throw new ViewException('Qtde de campos difere de 15 para a linha "$linha"');
                }

                $campos[2] = DateTimeUtils::parseDateStr($campos[2])->format('Y-m-d');
                $campos[3] = DateTimeUtils::parseDateStr($campos[3])->format('Y-m-d');
                $campos[4] = trim($campos[4]) ? DateTimeUtils::parseDateStr($campos[4])->format('Y-m-d') : '';
                $campos[14] = trim($campos[14]) ? DateTimeUtils::parseDateStr($campos[14])->format('Y-m-d') : '';


                $cMax = count($campos);
                for ($c = 0; $c < $cMax; $c++) {
                    $campos[$c] = $campos[$c] ? "'" . trim(str_replace("'", "''", $campos[$c])) . "'" : 'null';
                }

                $sql = sprintf(
                    'INSERT INTO rdp_rel_ctspagrec01 VALUES(null,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s, 1, now(), now(), 1, 1)',
                    $campos[0],
                    $campos[1],
                    $campos[2],
                    $campos[3],
                    $campos[4],
                    $campos[5],
                    $campos[6],
                    $campos[7],
                    $campos[8],
                    $campos[9],
                    $campos[10],
                    $campos[11],
                    $campos[12],
                    $campos[13],
                    $campos[14]
                );

                $t += $conn->executeUpdate($sql);
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
        }


    }

}
