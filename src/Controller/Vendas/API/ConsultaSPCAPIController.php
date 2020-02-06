<?php

namespace App\Controller\Vendas\API;

use CrosierSource\CrosierLibBaseBundle\Repository\Config\AppConfigRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\StringUtils\StringUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 *
 * @author Carlos Eduardo Pauluk
 */
class ConsultaSPCAPIController extends AbstractController
{


    protected \SoapClient $soapClient;

    /**
     * ConsultaSPCAPIController constructor.
     * @throws \SoapFault
     */
    public function __construct(AppConfigRepository $repoAppConfig)
    {
        $spcEndpoint = $repoAppConfig->findOneBy(['appUUID' => $_SERVER['CROSIERAPP_UUID'], 'chave' => 'spc_endpoint']);

        $spcUser = $repoAppConfig->findOneBy(['appUUID' => $_SERVER['CROSIERAPP_UUID'], 'chave' => 'spc_user']);
        $spcPw = $repoAppConfig->findOneBy(['appUUID' => $_SERVER['CROSIERAPP_UUID'], 'chave' => 'spc_pw']);
        $token = base64_encode($spcUser->getValor() . ':' . $spcPw->getValor());

        //Create the client object
        $this->soapclient = new \SoapClient($spcEndpoint->getValor(), ['login' => $spcUser->getValor(), 'password' => $spcPw->getValor()]);
    }

    /**
     * @Route("/api/vendas/consultarProdutos", name="api_ven_consultarProdutos")
     * @param Request $request
     * @return Response
     */
    public function consultarProdutos(Request $request): Response
    {
        $r = [];
        $produtos = $this->soapclient->__call('listarProdutos', []);
        foreach ($produtos->produto as $produto) {
            $r[] = $produto->codigo . ' - ' . $produto->nome;
        }
        return new JsonResponse(['r' => $r]);
    }


    /**
     * @Route("/api/vendas/consultaSpc", name="api_ven_consultaSpc")
     * @param Request $request
     * @return Response
     */
    public function consultaSpc(Request $request): Response
    {

        $produtoSpc = $request->get('produtoSpc') ?? 12;
        $documento = $request->get('documento');
        if (!$documento) {
            throw new \LogicException('documento não informado');
        }
        if (strlen($documento) !== 11 && strlen($documento) !== 14) {
            throw new \LogicException('documento com tamanho diferente de 11 ou 14');
        }

        $tipo = strlen($documento) === 11 ? 'F' : 'J';


        $result = $this->soapclient->__call('consultar', [
            'web:filtro' => [
                'codigo-produto' => $produtoSpc,
                'tipo-consumidor' => $tipo,
                'documento-consumidor' => $documento,
            ]
        ]);

        $r = [];

        $pj = $result->consumidor->{'consumidor-pessoa-juridica'} ?? false;
        $cadastroNaoLocalizado = false;
        if ($pj) {
            if ($result->consumidor->{'consumidor-pessoa-juridica'}->{'razao-social'} === 'CADASTRO NAO LOCALIZADO') {
                $cadastroNaoLocalizado = true;
            } else {
                $r[] = 'Consulta para ' . $result->consumidor->{'consumidor-pessoa-juridica'}->{'nome-comercial'} . ' (' . $result->consumidor->{'consumidor-pessoa-juridica'}->{'razao-social'} . ')';
            }
        } else {
            if ($result->consumidor->{'consumidor-pessoa-fisica'}->{'nome'} === 'CADASTRO NAO LOCALIZADO') {
                $cadastroNaoLocalizado = true;
            } else {
                $r[] = 'Consulta para ' . $result->consumidor->{'consumidor-pessoa-fisica'}->{'nome'} ?? '';
            }
        }
        if ($cadastroNaoLocalizado) {
            $r[] = 'Cadastro não localizado para o documento ' . StringUtils::mascararCnpjCpf($documento);
            return new Response(implode('<br>', $r));
        }


        $r[] = '';

        if ($result->{'alerta-documento'}->resumo->{'quantidade-total'} ?? false) {

            $r[] = '>>> CONSULTA DE DOCUMENTOS: alerta(s) para ' . $result->{'alerta-documento'}->resumo->{'quantidade-total'} . ' documento(s):';
            $r[] = '';

            if ($result->{'alerta-documento'}->resumo->{'quantidade-total'} === 1) {
                $detAlertaDocumento = $result->{'alerta-documento'}->{'detalhe-alerta-documento'};
                $documentos = '';
                if (is_array($detAlertaDocumento->{'tipo-documento-alerta'})) {
                    foreach ($detAlertaDocumento->{'tipo-documento-alerta'} as $tipoDocumento) {
                        $documentos .= $tipoDocumento->nome . ',';
                    }
                } else {
                    $documentos .= $detAlertaDocumento->{'tipo-documento-alerta'}->nome;
                }
                $documentos = substr($documentos, 0, -1);

                $r[] = '1) ' . ($detAlertaDocumento->{'motivo'} ?? '') . ' de ' . $documentos .
                    ' (Dt Ocorrência: ' . DateTimeUtils::parseDateStr(($detAlertaDocumento->{'data-ocorrencia'} ?? ''))->format('d/m/Y') . ') ' .
                    ' (Incluído em ' . DateTimeUtils::parseDateStr(($detAlertaDocumento->{'data-inclusao'} ?? ''))->format('d/m/Y') . ') ';
            } else {
                $i = 1;
                foreach ($result->{'alerta-documento'}->{'detalhe-alerta-documento'} as $detAlertaDocumento) {
                    $documentos = '';
                    if (is_array($detAlertaDocumento->{'tipo-documento-alerta'})) {
                        foreach ($detAlertaDocumento->{'tipo-documento-alerta'} as $tipoDocumento) {
                            $documentos .= $tipoDocumento->nome . ',';
                        }
                    } else {
                        $documentos .= $detAlertaDocumento->{'tipo-documento-alerta'}->nome;
                    }
                    $documentos = substr($documentos, 0, -1);

                    $r[] = $i++ . ') ' . ($detAlertaDocumento->{'motivo'} ?? '') . ' de ' . $documentos .
                        ' (Dt Ocorrência: ' . DateTimeUtils::parseDateStr(($detAlertaDocumento->{'data-ocorrencia'} ?? ''))->format('d/m/Y') . ') ' .
                        ' (Incluído em ' . DateTimeUtils::parseDateStr(($detAlertaDocumento->{'data-inclusao'} ?? ''))->format('d/m/Y') . ') ';
                }
            }
        } else {
            $r[] = '>>> CONSULTA DE DOCUMENTOS: Nenhum alerta de ocorrências.';
        }

        $r[] = '';
        $r[] = '';

        $totalOcorrenciasSPC = ($result->{'spc'}->resumo->{'quantidade-total'} ?? 0);
        if ($totalOcorrenciasSPC) {
            $valorTotalOcorrencias = $result->{'spc'}->resumo->{'valor-total'} ?? 0;
            $r[] = '>>> CONSULTA SPC: ' . $totalOcorrenciasSPC . ' ocorrência(s) encontrada(s) no valor total de R$ ' . number_format($valorTotalOcorrencias, 2, ',', '.');
            $r[] = '';

            if ($totalOcorrenciasSPC === 1) {
                $detSpc = $result->{'spc'}->{'detalhe-spc'};
                $r[] =
                    '1) Incluído por ' . ($detSpc->{'nome-entidade'} ?? '') .
                    ' em ' . DateTimeUtils::parseDateStr($detSpc->{'data-inclusao'})->format('d/m/Y') .
                    ' (como ' . $detSpc->{'comprador-fiador-avalista'} . '). ' .
                    'Contrato: ' . ($detSpc->{'contrato'} ?? '') .
                    'Vencto: ' . DateTimeUtils::parseDateStr($detSpc->{'data-vencimento'})->format('d/m/Y') .
                    'Valor: ' . number_format($detSpc->{'valor'}, 2, ',', '.');

            } else {
                $i = 1;
                foreach ($result->{'spc'}->{'detalhe-spc'} as $detSpc) {
                    $r[] =
                        $i++ . ') Incluído por ' . ($detSpc->{'nome-entidade'} ?? '') .
                        ' em ' . DateTimeUtils::parseDateStr($detSpc->{'data-inclusao'})->format('d/m/Y') .
                        ' (como ' . $detSpc->{'comprador-fiador-avalista'} . '). ' .
                        'Contrato: ' . ($detSpc->{'contrato'} ?? '') . '. ' .
                        'Vencto: ' . DateTimeUtils::parseDateStr($detSpc->{'data-vencimento'})->format('d/m/Y') . '. ' .
                        'Valor: ' . number_format($detSpc->{'valor'}, 2, ',', '.');
                }
            }
        } else {
            $r[] = '>>> CONSULTA SPC: Nenhuma ocorrência.';
        }

        $r[] = '';
        $r[] = '';

        $totalOcorrenciasPROTESTOS = ($result->{'protesto'}->resumo->{'quantidade-total'} ?? 0);
        if ($totalOcorrenciasPROTESTOS) {
            $valorTotalOcorrencias = $result->{'protesto'}->resumo->{'valor-total'} ?? 0;
            $r[] = '>>> CONSULTA PROTESTOS: ' . $totalOcorrenciasPROTESTOS . ' ocorrência(s) encontrada(s) no valor total de R$ ' . number_format($valorTotalOcorrencias, 2, ',', '.');
            $r[] = '';

            if ($totalOcorrenciasPROTESTOS === 1) {
                $detProtesto = $result->{'protesto'}->{'detalhe-protesto'};
                $r[] =
                    '1) Cartório: ' . ($detProtesto->cartorio->nome) . ' em ' . $detProtesto->cartorio->cidade->nome . '-' . $detProtesto->cartorio->cidade->estado->{'sigla-uf'} .
                    ' em ' . DateTimeUtils::parseDateStr($detProtesto->{'data-protesto'})->format('d/m/Y') .
                    'Valor: ' . number_format($detProtesto->{'valor'}, 2, ',', '.');

            } else {
                $i = 1;
                foreach ($result->{'protesto'}->{'detalhe-protesto'} as $detProtesto) {
                    $r[] =
                        $i++ . ') Cartório: ' . ($detProtesto->cartorio->nome) . ' em ' . $detProtesto->cartorio->cidade->nome . '-' . $detProtesto->cartorio->cidade->estado->{'sigla-uf'} .
                        ' em ' . DateTimeUtils::parseDateStr($detProtesto->{'data-protesto'})->format('d/m/Y') . '. ' . 
                        'Valor: ' . number_format($detProtesto->{'valor'}, 2, ',', '.');
                }
            }
        } else {
            $r[] = '>>> CONSULTA PROTESTOS: Nenhuma ocorrência.';
        }

        $r[] = '';
        $r[] = '';

        $totalOcorrenciasChequeLojista = ($result->{'cheque-lojista'}->resumo->{'quantidade-total'} ?? 0);
        if ($totalOcorrenciasChequeLojista) {
            $valorTotalOcorrencias = $result->{'cheque-lojista'}->resumo->{'valor-total'} ?? 0;
            $r[] = '>>> CONSULTA DE CHEQUES: ' . $totalOcorrenciasChequeLojista . ' ocorrência(s) encontrada(s) no valor total de R$ ' . number_format($valorTotalOcorrencias, 2, ',', '.');
            $r[] = '';

            if ($totalOcorrenciasChequeLojista === 1) {
                $detChequeLojista = $result->{'cheque-lojista'}->{'detalhe-chequeLojista'};
                $r[] =
                    '1) Cartório: ' . ($detChequeLojista->cartorio->nome) . ' em ' . $detChequeLojista->cartorio->cidade->nome . '-' . $detChequeLojista->cartorio->cidade->estado->{'sigla-uf'} .
                    ' em ' . DateTimeUtils::parseDateStr($detChequeLojista->{'data-'})->format('d/m/Y') .
                    'Valor: ' . number_format($detChequeLojista->{'valor'}, 2, ',', '.');

            } else {
                $i = 1;
                foreach ($result->{'cheque-lojista'}->{'detalhe-cheque-lojista'} as $detChequeLojista) {
                    $r[] =
                        $i++ . ') Alinea ' . $detChequeLojista->alinea->descricao .
                        ' (' . str_pad($detChequeLojista->{'cheque-inicial'}->{'dados-bancarios'}->banco->codigo, 3, '0', STR_PAD_LEFT) . ' - ' . $detChequeLojista->{'cheque-inicial'}->{'dados-bancarios'}->banco->nome . '. ' .
                        'Agência ' . $detChequeLojista->{'cheque-inicial'}->{'dados-bancarios'}->{'numero-agencia'} . '. ' .
                        'Número: ' . $detChequeLojista->{'cheque-inicial'}->{'numero'} . '. ' .
                        'Emissão: ' . DateTimeUtils::parseDateStr($detChequeLojista->{'cheque-inicial'}->{'data-emissao'})->format('d/m/Y') . '. ' .
                        'Valor: ' . number_format($detChequeLojista->{'cheque-inicial'}->{'valor'}, 2, ',', '.');
                }
            }
        } else {
            $r[] = '>>> CONSULTA DE CHEQUES: Nenhuma ocorrềncia.';
        }

        return new Response(implode(chr(13), $r));
    }

}
