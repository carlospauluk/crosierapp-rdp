<?php

namespace App\Business\Relatorios;


use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\Config\AppConfigEntityHandler;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\AppConfigRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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
        $pastaFila = $_SERVER['PASTA_UPLOAD_RELCLIENTES01'] . 'fila/';
        $files = scandir($pastaFila, 0);
        foreach ($files as $file) {
            if (!in_array($file, array('.', '..'))) {

                try {
                    $this->processarArquivo($file);
                    $this->marcarDtHrAtualizacao();
                    $this->logger->info('Arquivo processado com sucesso.');
                    rename($pastaFila . $file, $_SERVER['PASTA_UPLOAD_RELCLIENTES01'] . 'ok/' . $file);
                    $this->logger->info('Arquivo movido para pasta "ok".');
                } catch (\Exception $e) {
                    rename($pastaFila . $file, $_SERVER['PASTA_UPLOAD_RELCLIENTES01'] . 'falha/' . $file);
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
        $pastaFila = $_SERVER['PASTA_UPLOAD_RELCLIENTES01'] . 'fila/';
        $conteudo = file_get_contents($pastaFila . $arquivo);
        $linhas = explode(PHP_EOL, $conteudo);
        $totalRegistros = count($linhas);
        /** @var Connection $conn */
        $conn = $this->doctrine->getConnection();

        $conn->beginTransaction();

        /**
         * CODIGO    codigo
         * NOME    nome
         * DATA_PRI    dt_pri
         * DATA_CADASTRO    dt_cadastro
         * CPF_CNPJ    documento
         * RG_IE    rg
         * ENDER    endereco
         * CIDADE    cidade
         * UF    estado
         * CEP    cep
         * DDD    fone
         * FONE
         * BAIRRO    bairro
         * TIPO    tipo
         * ENDER_TRABALHO    trabalho_endereco
         * CIDADE_TRA    trabalho_cidade
         * UF_TRA    trabalho_estado
         * CEP_TRA    trabalho_cep
         * DDD_FAX    trabalho_fax
         * FONE_FAX
         * DATA_ULT_COMPRA    dt_ult_compra
         * CARGO_TRA    trabalho_cargo
         * MAIOR_COMPRA    vlr_maior_compra
         * CONJUGE    conjuge_nome
         * NASC_CONJUGE    conjuge_dt_nasc
         * TRABALHO_CON    conjuge_trabalho
         * RG_CON    conjuge_rg
         * ENDER_TRA_CON    conjuge_trabalho_endereco
         * CID_TRA_CON    conjuge_trabalho_cidade
         * DIAS_ATRASO    dias_atraso
         * CEP_TRA_CON    conjuge_trabalho_cep
         * DDD_TRA_CON    conjuge_trabalho_fone
         * FONE_TRA_CON
         * ADM_TRA_CON    conjuge_trabalho_adm
         * CLIENTE_BLOQUEADO    cliente_bloqueado
         * VLR_ULTCOMPRA    vlr_ult_compra
         * OBS3    obs3
         * PESSOAS_AUTO2    pessoas_auto2
         * DESBLOQUEIO_TMP    desbloqueio_tmp
         * DATA_PAGTO    dt_pagto
         * OBS1    obs1
         * OBS2    obs2
         * COND_PAGTO    cond_pagto
         * SUSPENSO    suspenso
         * LIMITE_COMPRAS    limite_compras
         * AC_COMPRAS    ac_compras
         * LOCALIZADOR    localizador
         * ENDERCOB    cobranca_endereco
         * CIDADECOB    cobranca_cidade
         * UFCOB    cobranca_estado
         * CEPCOB    cobranca_cep
         * BAIRROCOB    cobranca_bairro
         * OBS4    obs4
         * OBS5    obs5
         * OBS6    obs6
         * NAS_PROP    dt_nas_prop
         * NAS_FUNDA    dt_nas_funda
         * RAMO    ramo
         * BENS01    bens1
         * BENS02    bens2
         * SCANIA    scania
         * VOLVO    volvo
         * MB    mb
         * OUTROS    outros
         * SCANIA01    scania01
         * VOLVO01    volvo01
         * MB01    mb01
         * OUTROS01    outros01
         * FLAG_CASA    flag_casa
         * REF_BANCO    ref_banco
         * REF_BANCO01    ref_banco01
         * REF_COME    ref_come
         * REF_COME01    ref_come01
         * VENDEDOR    vendedor
         * PAI    pai
         * MAE    mae
         * PES_CONHE    conhecido_pes
         * FON_CONHE    conhecido_fone
         * EMAIL    email
         * INTEG_WLE    integ_wle
         * OBS01    obs01
         * OBS02    obs02
         * OBS03    obs03
         * OBS04    obs04
         * OBS05    obs05
         * OBS06    obs06
         * OBS07    obs07
         * OBS08    obs08
         * OBS09    obs09
         * OBS10    obs10
         * OBS11    obs11
         * OBS12    obs12
         * OBS13    obs13
         * OBS14    obs14
         * OBS15    obs15
         * OBS16    obs16
         * OBS17    obs17
         * OBS18    obs18
         * OBS19    obs19
         * OBS20    obs20
         * RG2    rg2
         * NUMERO_ENDER    endereco
         * COMPLEMENTO    complemento
         * COD_MUNIC    cod_munic
         * FLAG_LIB_PRECO    flag_lib_preco
         * SUGERE_CONSULTA    sugere_consulta
         * FLAG_COMISSAO    flag_comissao
         * DIAS_TRV_FAT    dias_trv_fat
         * MARGEM_ESPECIAL    margem_especial
         * TIPO_CLIENTE    tipo_cliente
         * TIPO_FORNEC    tipo_fornec
         * FLAG_SCP    flag_scp
         * FLAG_CHEQUEDEV    flag_chequedev
         * COD_CONSUL    cod_consul
         * FROTISTA    frotista
         * CLASSIFICACAO    classificacao
         */

        $t = 0;
        $linha = null;
        try {
            for ($i = 1; $i < $totalRegistros; $i++) {
                $linha = $linhas[$i];
                if (!trim($linha)) {
                    continue;
                }
                $campos = explode('|', $linha);
                if (count($campos) !== 116) {
                    throw new ViewException('Qtde de campos difere de 116 para a linha "' . $linha . '"');
                }

                $campos[6] .= $campos[101]; # ENDER + NUMERO_ENDER
                $campos[10] .= $campos[11]; # DDD + FONE
                $campos[18] .= $campos[19]; # DDD_FAX + FONE_FAX
                $campos[31] .= $campos[32]; # DDD_TRA_CON + FONE_TRA_CON

                $campos[13] = $campos[13] ? 'S' : 'N';
                $campos[15] = $campos[15] ? 'S' : 'N';
                $campos[16] = $campos[16] ? 'S' : 'N';
                $campos[19] = $campos[19] ? 'S' : 'N';

                $cMax = count($campos);
                for ($c = 0; $c < $cMax; $c++) {
                    $campos[$c] = $campos[$c] ? "'" . utf8_encode(trim(str_replace("'", "''", $campos[$c]))) . "'" : 'null';
                }

                $sql = sprintf(
                    'INSERT INTO rdp_rel_cliente01 (
                            
                            estabelecimento_id,inserted,updated,user_inserted_id,user_updated_id
                        )
                    VALUES(null,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s, 1, now(), now(), 1, 1)',
                    $campos[0], // codigo,
                    $campos[1], // nome,
                    $campos[2], // documento,
                    $campos[3], // rg,
                    $campos[4], // endereco,
                    $campos[5], // cidade,
                    $campos[6], // estado,
                    $campos[7], // cep,
                    $campos[8], // fone,
                    $campos[10], // bairro,
                    $campos[11],// localizador,
                    $campos[12],// cond_pagto,
                    $campos[13],// desbloqueio_tmp,
                    $campos[14],// ac_compras,
                    $campos[15],// flag_lib_preco,
                    $campos[16],// sugere_consulta,
                    $campos[17],// margem_especial,
                    $campos[18],// limite_compras,
                    $campos[19] // cliente_bloqueado
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



