<?php

namespace App\Business\Relatorios;


use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\Config\AppConfigEntityHandler;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\AppConfigRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 *
 *
 * @package App\Business\Relatorios
 */
class RelCliente01Business
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
        $pastaFila = $_SERVER['PASTA_UPLOAD_RELCLIENTE01'] . 'fila/';
        $files = scandir($pastaFila, 0);

        foreach ($files as $file) {
            if (!in_array($file, array('.', '..'))) {

                try {
                    $this->processarArquivo($file);
                    $this->marcarDtHrAtualizacao();
                    $this->logger->info('Arquivo processado com sucesso.');
                    rename($pastaFila . $file, $_SERVER['PASTA_UPLOAD_RELCLIENTE01'] . 'ok/' . $file);
                    $this->logger->info('Arquivo movido para pasta "ok".');
                } catch (\Exception $e) {
                    rename($pastaFila . $file, $_SERVER['PASTA_UPLOAD_RELCLIENTE01'] . 'falha/' . $file);
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
        $pastaFila = $_SERVER['PASTA_UPLOAD_RELCLIENTE01'] . 'fila/';
        $conteudo = file_get_contents($pastaFila . $arquivo);
        $linhas = explode(PHP_EOL, $conteudo);
        $totalRegistros = count($linhas);
        /** @var Connection $conn */
        $conn = $this->doctrine->getConnection();

        $conn->beginTransaction();

        /**
         * 0    CODIGO            NUM    6.0
         * 1    NOME                ASC    70
         * 2    DATA_PRI            DAT    3
         * 3    CPF                ASC    20
         * 4    RG                ASC    20
         * 5    ENDER                ASC    40
         * 6    CIDADE            ASC    30
         * 7    UF                ASC    2
         * 8    CEP                ASC    9
         * 9    DDD                ASC    4
         * 10    FONE                ASC    12
         * 11    BAIRRO            ASC    30
         * 12    TIPO                ASC    1
         * 13    ENDER_TRA            ASC    40
         * 14    CIDADE_TRA        ASC    30
         * 15    UF_TRA            ASC    2
         * 16    CEP_TRA            ASC    9
         * 17    DDD_FAX            ASC    4
         * 18    FONE_FAX            ASC    12
         * 19    DATA_ULT            DAT    3
         * 20    CARGO_TRA            ASC    15
         * 21    MAIOR_COMPRA        NUM    10.2
         * 22    CONJUGE            ASC    30
         * 23    NASC_CON            DAT    3
         * 24    TRABALHO_CON        ASC    30
         * 25    RG_CON            ASC    20
         * 26    ENDER_TRA_CON        ASC    30
         * 27    CID_TRA_CON        ASC    30
         * 28    DIAS_ATRASO        NUM    4.0
         * 29    CEP_TRA_CON        ASC    9
         * 30    DDD_TRA_CON        ASC    4
         * 31    FONE_TRA_CON        ASC    12
         * 32    ADM_TRA_CON        DAT    3
         * 33    CARGO_TRA_CON        ASC    15
         * 34    VLR_ULTCOMPRA        NUM    10.2
         * 35    OBS3                ASC    60
         * 36    PESSOAS_AUTO2        ASC    60
         * 37    DATA_PAGTO        DAT    3
         * 38    OBS1                ASC    60
         * 39    OBS2                ASC    60
         * 40    COND_PAGTO        NUM    4.0
         * 41    SUSPENSO            ASC    1
         * 42    LIM_COMPRAS        NUM    6.2
         * 43    COMPRAS            NUM    6.2
         * 44    LOCALIZADOR        NUM    2.0
         * 45    ENDERCOB            ASC    40
         * 46    CIDADECOB            ASC    30
         * 47    UFCOB                ASC    2
         * 48    CEPCOB            ASC    9
         * 49    BAIRROCOB            ASC    30
         * 50    OBS4                ASC    60
         * 51    OBS5                ASC    60
         * 52    OBS6                ASC    60
         * 53    NAS_PROP            DAT    3
         * 54    NAS_FUNDA            DAT    3
         * 55    RAMO                ASC    30
         * 56    BENS01            ASC    50
         * 57    BENS02            ASC    50
         * 58    SCANIA            NUM    4.0
         * 59    VOLVO                NUM    4.0
         * 60    MB                NUM    4.0
         * 61    OUTROS            NUM    4.0
         * 62    SCANIA01            NUM    4.0
         * 63    VOLVO01            NUM    4.0
         * 64    MB01                NUM    4.0
         * 65    OUTROS01            NUM    4.0
         * 66    FLAG_CASA            ASC    1
         * 67    REF_BANCO            ASC    50
         * 68    REF_BANCO01        ASC    50
         * 69    REF_COME            ASC    50
         * 70    REF_COME01        ASC    50
         * 71    VENDEDOR            NUM    4.0
         * 72    PAI                ASC    30
         * 73    MAE                ASC    30
         * 74    PES_CONHE            ASC    50
         * 75    FON_CONHE            ASC    20
         * 76    EMAIL                ASC    40
         * 77    INTEG_WLE            ASC    1
         * 78    OBS01                ASC    75
         * 79    OBS02                ASC    75
         * 80    OBS03                ASC    75
         * 81    OBS04                ASC    75
         * 82    OBS05                ASC    75
         * 83    OBS06                ASC    75
         * 84    OBS07                ASC    75
         * 85    OBS08                ASC    75
         * 86    OBS09                ASC    75
         * 87    OBS10                ASC    75
         * 88    OBS11                ASC    75
         * 89    OBS12                ASC    75
         * 90    OBS13                ASC    75
         * 91    OBS14                ASC    75
         * 92    OBS15                ASC    75
         * 93    OBS16                ASC    75
         * 94    OBS17                ASC    75
         * 95    OBS18                ASC    75
         * 96    OBS19                ASC    75
         * 97    OBS20                ASC    75
         * 98    RG2                ASC    20
         * 99    NUMERO            ASC    20
         * 100    COMPLEMENTO        ASC    60
         * 101    COD_MUNIC            NUM    8.0
         * 102    FLAG_LIB_PRECO    ASC    1
         * 103    FLAG_BLOQUEIO        ASC    1
         * 104    FLAG_COMISSAO        ASC    1
         * 105    DIAS_TRV_FAT        NUM    4.0
         * 106    PERC_MARGEM        NUM    4.2
         * 107    TIPO_CLIENTE        ASC    1
         * 108    TIPO_FORNEC        ASC    1
         * 109    FLAG_SCP            ASC    1
         * 110    FLAG_CHEQUEDEV    ASC    1
         * 111    COD_CONSUL        NUM    4.0
         * 112    FROTISTA            ASC    1
         * 113    CLASSIFICACAO        ASC    1
         */

        $camposMySQL = [
            0 => 'codigo',
            1 => 'nome',
            3 => 'documento',
            4 => 'rg',
            5 => 'endereco',
            100 => 'complemento',
            6 => 'cidade',
            7 => 'estado',
            8 => 'cep',
            9 => 'fone',
            11 => 'bairro',
            44 => 'localizador',
            40 => 'cond_pagto',
            36 => 'desbloqueio_tmp',
            43 => 'ac_compras',
            102 => 'flag_lib_preco',
            103 => 'sugere_consulta',
            106 => 'margem_especial',
            42 => 'limite_compras',
            33 => 'cliente_bloqueado',
            12 => 'tipo',
            13 => 'trabalho_endereco',
            14 => 'trabalho_cidade',
            15 => 'trabalho_estado',
            20 => 'trabalho_cargo',
            16 => 'trabalho_cep',
            17 => 'trabalho_fax',
            2 => 'dt_pri',
            19 => 'dt_ult_compra',
            37 => 'dt_pagto',
            21 => 'vlr_maior_compra',
            34 => 'vlr_ult_compra',
            22 => 'conjuge_nome',
            23 => 'conjuge_dt_nasc',
            25 => 'conjuge_rg',
            24 => 'conjuge_trabalho',
            26 => 'conjuge_trabalho_endereco',
            27 => 'conjuge_trabalho_cidade',
            29 => 'conjuge_trabalho_cep',
            30 => 'conjuge_trabalho_fone',
            32 => 'conjuge_trabalho_adm',
            38 => 'obs1',
            39 => 'obs2',
            35 => 'obs3',
            50 => 'obs4',
            51 => 'obs5',
            52 => 'obs6',
            78 => 'obs01',
            79 => 'obs02',
            80 => 'obs03',
            81 => 'obs04',
            82 => 'obs05',
            83 => 'obs06',
            84 => 'obs07',
            85 => 'obs08',
            86 => 'obs09',
            87 => 'obs10',
            88 => 'obs11',
            89 => 'obs12',
            90 => 'obs13',
            91 => 'obs14',
            92 => 'obs15',
            93 => 'obs16',
            94 => 'obs17',
            95 => 'obs18',
            96 => 'obs19',
            97 => 'obs20',
            28 => 'dias_atraso',
            41 => 'suspenso',
            45 => 'cobranca_endereco',
            49 => 'cobranca_bairro',
            46 => 'cobranca_cidade',
            47 => 'cobranca_estado',
            48 => 'cobranca_cep',
            53 => 'dt_nas_prop',
            54 => 'dt_nas_funda',
            55 => 'ramo',
            56 => 'bens1',
            57 => 'bens2',
            58 => 'scania',
            59 => 'volvo',
            60 => 'mb',
            61 => 'outros',
            62 => 'scania01',
            63 => 'volvo01',
            64 => 'mb01',
            65 => 'outros01',
            66 => 'flag_casa',
            67 => 'ref_banco',
            68 => 'ref_banco01',
            69 => 'ref_come',
            70 => 'ref_come01',
            71 => 'vendedor',
            72 => 'pai',
            73 => 'mae',
            74 => 'conhecido_pes',
            75 => 'conhecido_fone',
            76 => 'email',
            77 => 'integ_wle',
            98 => 'rg2',
            101 => 'cod_munic',
            104 => 'flag_comissao',
            105 => 'dias_trv_fat',
            107 => 'tipo_cliente',
            108 => 'tipo_fornec',
            109 => 'flag_scp',
            110 => 'flag_chequedev',
            111 => 'cod_consul',
            112 => 'frotista',
            113 => 'classificacao'
        ];

        ksort($camposMySQL);


        $t = 0;
        $linha = null;
        try {
            for ($i = 1; $i < $totalRegistros; $i++) {
                $linha = $linhas[$i];
                if (!trim($linha)) {
                    continue;
                }
                $campos = explode('|@|', $linha);
                if (count($campos) !== 114) {
                    throw new ViewException('Qtde de campos difere de 114 para a linha "' . $linha . '"');
                }

                $existeCodigo = $conn->fetchAssoc('SELECT * FROM rdp_rel_cliente01 WHERE codigo = :codigo', ['codigo' => $campos[0]]);
                if ($existeCodigo) {
                    $this->logger->info('Cliente com código ' . $campos[0] . ' já existe na base. Continuando...');
                    continue;
                }

                try {
                    $campos[2] = DateTimeUtils::parseDateStr($campos[2])->format('Y-m-d');
                } catch (\Throwable $e) {
                    $campos[2] = null;
                }
                try {
                    $campos[19] = DateTimeUtils::parseDateStr($campos[19])->format('Y-m-d');
                } catch (\Throwable $e) {
                    $campos[19] = null;
                }
                try {
                    $campos[23] = DateTimeUtils::parseDateStr($campos[23])->format('Y-m-d');
                } catch (\Throwable $e) {
                    $campos[23] = null;
                }
                try {
                    $campos[37] = DateTimeUtils::parseDateStr($campos[37])->format('Y-m-d');
                } catch (\Throwable $e) {
                    $campos[37] = null;
                }
                try {
                    $campos[53] = DateTimeUtils::parseDateStr($campos[53])->format('Y-m-d');
                } catch (\Throwable $e) {
                    $campos[53] = null;
                }
                try {
                    $campos[54] = DateTimeUtils::parseDateStr($campos[54])->format('Y-m-d');
                } catch (\Throwable $e) {
                    $campos[54] = null;
                }

                $campos[5] = trim($campos[5]) . ($campos[99] ? ',' . trim($campos[99]) : ''); # ENDER + NUMERO_ENDER
                $campos[9] .= trim($campos[9]) . ($campos[10] ? ',' . trim($campos[10]) : ''); # DDD + FONE
                $campos[17] .= trim($campos[17]) . ($campos[18] ? ',' . trim($campos[18]) : ''); # DDD_FAX + FONE_FAX
                $campos[30] .= trim($campos[30]) . ($campos[31] ? ',' . trim($campos[31]) : ''); # DDD_TRA_CON + FONE_TRA_CON

                // Reduz para 110 campos
                unset($campos[99], $campos[10], $campos[18], $campos[31]);


                // Flags
                $campos[41] = $campos[41] ? 'S' : 'N';
                $campos[66] = $campos[66] ? 'S' : 'N';
                $campos[77] = $campos[77] ? 'S' : 'N';
                $campos[102] = $campos[102] ? 'S' : 'N';
                $campos[103] = $campos[103] ? 'S' : 'N';
                $campos[104] = $campos[104] ? 'S' : 'N';
                $campos[109] = $campos[109] ? 'S' : 'N';
                $campos[110] = $campos[110] ? 'S' : 'N';
                $campos[112] = $campos[112] ? 'S' : 'N';

                $cMax = count($campos);
                foreach ($campos as $k => $v) {
                    $campos[$k] = $v ? "'" . utf8_encode(trim(str_replace("'", "''", $v))) . "'" : 'null';
                }

                $sql = vsprintf(
                    'INSERT INTO rdp_rel_cliente01 (id, ' . implode(', ', $camposMySQL) . ', inserted,updated,estabelecimento_id,user_inserted_id,user_updated_id)
                    VALUES(null,' . str_repeat('%s,', 110) . ' now(), now(),1, 1, 1)', $campos
                );

                $t += $conn->executeUpdate($sql);
                $this->logger->info($t . ' inseridos');
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
            $appConfig = $repoAppConfig->findOneByFiltersSimpl([['chave', 'EQ', 'relCliente01.dthrAtualizacao'], ['appUUID', 'EQ', $_SERVER['CROSIERAPP_UUID']]]);
            if (!$appConfig) {
                $appConfig = new AppConfig();
                $appConfig->setChave('relCliente01.dthrAtualizacao');
                $appConfig->setAppUUID($_SERVER['CROSIERAPP_UUID']);
            }
            $appConfig->setValor((new \DateTime())->format('Y-m-d H:i:s.u'));
            $this->appConfigEntityHandler->save($appConfig);
        } catch (\Exception $e) {
            $this->logger->error('Erro ao marcar app_config (relCliente01.dthrAtualizacao)');
            $this->logger->error($e->getMessage());
            throw new ViewException('Erro ao marcar dt/hr atualização');
        }
    }

}






