<?php

namespace App\Controller\Vendas\API;

use CrosierSource\CrosierLibBaseBundle\Repository\Config\AppConfigRepository;
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

        /**
         * DADOS
         * nome-comercial|@|razao-social
         *
         * ALERTA-DOCUMENTO
         * QUANTIDADE-TOTAL:n
         * 1|@|tipos-documentos-alerta|data-inclusao|@|data-ocorrencia|@|entidade-origem|@|motivo|@|observacao
         * 2|@|tipos-documentos-alerta|data-inclusao|@|data-ocorrencia|@|entidade-origem|@|motivo|@|observacao
         * 3|@|tipos-documentos-alerta|data-inclusao|@|data-ocorrencia|@|entidade-origem|@|motivo|@|observacao
         * ...
         *
         * SPC
         * QUANTIDADE-TOTAL:n
         * 1|data-inclusao|@|data-vencimento|@|nome-entidade|@|contrato|@|comprador-fiador-avalista|@|valor
         * 2|data-inclusao|@|data-vencimento|@|nome-entidade|@|contrato|@|comprador-fiador-avalista|@|valor
         * 3|data-inclusao|@|data-vencimento|@|nome-entidade|@|contrato|@|comprador-fiador-avalista|@|valor
         * ...
         *
         */

        $sep = '|@|';

        $r = [];
        $r[] = 'DADOS';
        $r[] = $result->consumidor->{'consumidor-pessoa-juridica'}->{'nome-comercial'} . $sep . $result->consumidor->{'consumidor-pessoa-juridica'}->{'razao-social'};
        $r[] = '';

        $r[] = 'ALERTA-DOCUMENTO';
        $r[] = 'QUANTIDADE-TOTAL:' . $result->{'alerta-documento'}->resumo->{'quantidade-total'} ?? 0;
        if ($result->{'alerta-documento'}->resumo->{'quantidade-total'} ?? false) {
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
                $r[] =
                    '1' . $sep .
                    $documentos . $sep .
                    ($detAlertaDocumento->{'data-inclusao'} ?? '') . $sep .
                    ($detAlertaDocumento->{'data-ocorrencia'} ?? '') . $sep .
                    ($detAlertaDocumento->{'entidade-origem'} ?? '') . $sep .
                    ($detAlertaDocumento->{'motivo'} ?? '') . $sep .
                    ($detAlertaDocumento->{'observacao'} ?? '');
            } else {
                $i = 1;
                foreach ($result->{'alerta-documento'}->{'detalhe-alerta-documento'} as $detAlertaDocumento) {
                    $r[] =
                        $i++ . $sep .
                        ($detAlertaDocumento->{'data-inclusao'} ?? '') . $sep .
                        ($detAlertaDocumento->{'data-ocorrencia'} ?? '') . $sep .
                        ($detAlertaDocumento->{'entidade-origem'} ?? '') . $sep .
                        ($detAlertaDocumento->{'motivo'} ?? '') . $sep .
                        ($detAlertaDocumento->{'observacao'} ?? '');
                }
            }
        }

        $r[] = 'SPC';
        $r[] = 'QUANTIDADE-TOTAL:' . $result->{'spc'}->resumo->{'quantidade-total'} ?? 0;
        $r[] = 'VALOR-TOTAL:' . $result->{'spc'}->resumo->{'valor-total'} ?? 0;
        if ($result->{'spc'}->resumo->{'quantidade-total'} ?? false) {
            if ($result->{'spc'}->resumo->{'quantidade-total'} === 1) {
                $detSpc = $result->{'spc'}->{'detalhe-spc'};
                $r[] =
                    '1' . $sep .
                    ($detSpc->{'data-inclusao'} ?? '') . $sep .
                    ($detSpc->{'data-vencimento'} ?? '') . $sep .
                    ($detSpc->{'nome-entidade'} ?? '') . $sep .
                    ($detSpc->{'contrato'} ?? '') . $sep .
                    ($detSpc->{'comprador-fiador-avalista'} ?? '') . $sep .
                    ($detSpc->{'valor'} ?? 0);
            } else {
                $i = 1;
                foreach ($result->{'spc'}->{'detalhe-spc'} as $detSpc) {
                    $r[] =
                        $i++ . $sep .
                        ($detSpc->{'data-inclusao'} ?? '') . $sep .
                        ($detSpc->{'data-vencimento'} ?? '') . $sep .
                        ($detSpc->{'nome-entidade'} ?? '') . $sep .
                        ($detSpc->{'contrato'} ?? '') . $sep .
                        ($detSpc->{'comprador-fiador-avalista'} ?? '') . $sep .
                        ($detSpc->{'valor'} ?? 0);
                }
            }
        }


        return new Response(implode('<br>',$r));
    }

}
