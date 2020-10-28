<?php

namespace App\Controller\Vendas\API;

use CrosierSource\CrosierLibBaseBundle\Business\Config\SyslogBusiness;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use CrosierSource\CrosierLibRadxBundle\Entity\Estoque\ProdutoComposicao;
use CrosierSource\CrosierLibRadxBundle\Entity\Vendas\Venda;
use CrosierSource\CrosierLibRadxBundle\Entity\Vendas\VendaItem;
use CrosierSource\CrosierLibRadxBundle\Entity\Vendas\VendaPagto;
use CrosierSource\CrosierLibRadxBundle\EntityHandler\Vendas\VendaEntityHandler;
use CrosierSource\CrosierLibRadxBundle\Repository\Vendas\VendaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
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
class VendaAPIController extends AbstractController
{

    private EntityManagerInterface $doctrine;

    private SyslogBusiness $syslog;

    private VendaEntityHandler $vendaEntityHandler;

    /**
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $doctrine
     * @param VendaEntityHandler $vendaEntityHandler
     */
    public function __construct(SyslogBusiness $syslog,
                                EntityManagerInterface $doctrine,
                                VendaEntityHandler $vendaEntityHandler)
    {
        $this->syslog = $syslog->setApp('rdp')->setComponent(self::class);
        $this->doctrine = $doctrine;
        $this->vendaEntityHandler = $vendaEntityHandler;
    }

    /**
     * @Route("/api/ven/venda/consultarECommerces/{data}", name="api_ven_venda_consultarEcommerces")
     * @param string|null $data
     * @return JsonResponse
     * @throws ViewException
     */
    public function consultarECommerces(Request $request, string $data = null): Response
    {
        /** @var VendaRepository $repoVenda */
        $repoVenda = $this->doctrine->getRepository(Venda::class);

        $filterData_canal = new FilterData('canal', 'EQ', 'ECOMMERCE', null, null, true);
        $filterData_canal->setVal('ECOMMERCE');

        $filters = [];
        $filters[] = $filterData_canal;

        $status = $request->get('status');
        if ($status) {
            $filterData_status = new FilterData('ecommerce_status_descricao', 'EQ', 'ecommerce_status_descricao', null, null, true);
            $filterData_status->setVal($status);
            $filters[] = $filterData_status;
        }


        if (!$data) {
            $filterData_data_hoje = new FilterData('dtVenda', 'EQ', 'dtVenda', null, 'date');
            $filterData_data_hoje->setVal(new \DateTime());
            $filterData_data_ontem_e_hoje = new FilterData('dtVenda', 'EQ', 'dtVenda', null, 'date', false, $filterData_data_hoje);
            $ontem = (new \DateTime())->sub((new \DateInterval('P1D')));
            $filterData_data_ontem_e_hoje->setVal($ontem);
            $filters[] = $filterData_data_ontem_e_hoje;
        } else {
            $filterData_data = new FilterData('dtVenda', 'EQ', 'dtVenda', null, 'date');
            $dtData = DateTimeUtils::parseDateStr($data);
            $filterData_data->setVal($dtData);
            $filters[] = $filterData_data;
        }

        $vendas = $repoVenda->findByFiltersSimpl($filters);

        $rs = [];

        if ($vendas) {
            /** @var Venda $venda */
            foreach ($vendas as $venda) {
                $r = [];
                $r[] = '<venda>';
                $r[] = '<id>' . $venda->getId() . '</id>';
                $r[] = '<ecommerce_idPedido>' . ($venda->jsonData['ecommerce_idPedido'] ?? '') . '</ecommerce_idPedido>';
                $r[] = '<ecommerce_status_descricao>' . ($venda->jsonData['ecommerce_status_descricao'] ?? '') . '</ecommerce_status_descricao>';
                $r[] = '<dtVenda>' . $venda->dtVenda->format('Y-m-d H:i:s') . '</dtVenda>';
                $r[] = '<cliente>';
                $r[] = '    <documento>' . $venda->cliente->documento . '</documento>';
                $r[] = '    <nome>' . mb_strtoupper($venda->cliente->nome) . '</nome>';
                if (($venda->cliente->jsonData['tipo_pessoa'] ?? 'PF') === 'PF') {
                    $r[] = '    <rg_ie>' . ($venda->cliente->jsonData['rg'] ?? '') . '</rg_ie>';
                } else {
                    $r[] = '    <rg_ie>' . ($venda->cliente->jsonData['inscricao_estadual'] ?? '') . '</rg_ie>';
                }
                $r[] = '    <email>' . mb_strtolower($venda->cliente->jsonData['email'] ?? '') . '</email>';
                $r[] = '    <fone1>' . ($venda->cliente->jsonData['fone1'] ?? '') . '</fone1>';
                $r[] = '    <fone2>' . ($venda->cliente->jsonData['fone2'] ?? '') . '</fone2>';
                $r[] = '    <endereco>';
                $r[] = '        <logradouro>' . mb_strtoupper($venda->jsonData['ecommerce_entrega_logradouro'] ?? '') . '</logradouro>';
                $r[] = '        <numero>' . mb_strtoupper($venda->jsonData['ecommerce_entrega_numero'] ?? '') . '</numero>';
                $r[] = '        <complemento>' . mb_strtoupper($venda->jsonData['ecommerce_entrega_complemento'] ?? '') . '</complemento>';
                $r[] = '        <bairro>' . mb_strtoupper($venda->jsonData['ecommerce_entrega_bairro'] ?? '') . '</bairro>';
                $r[] = '        <cep>' . mb_strtoupper($venda->jsonData['ecommerce_entrega_cep'] ?? '') . '</cep>';
                $r[] = '        <cidade>' . mb_strtoupper($venda->jsonData['ecommerce_entrega_cidade'] ?? '') . '</cidade>';
                $r[] = '        <estado>' . mb_strtoupper($venda->jsonData['ecommerce_entrega_uf'] ?? '') . '</estado>';
                $r[] = '    </endereco>';
                $r[] = '</cliente>';
                $r[] = '<pagamento>';
                $r[] = '    <subtotal>' . $venda->subtotal . '</subtotal>';
                $r[] = '    <desconto>' . $venda->desconto . '</desconto>';
                $r[] = '    <valor_total>' . $venda->valorTotal . '</valor_total>';

                /** @var VendaPagto $pagto */
                $pagto = $venda->pagtos->get(0);
                if ($pagto->jsonData['nsu'] ?? false) {
                    $r[] = '    <nsu>' . $pagto->jsonData['nsu'] . '</nsu>';
                }
                if ($pagto->jsonData['tid'] ?? false) {
                    $r[] = '    <tid>' . $pagto->jsonData['tid'] . '</tid>';
                }
                if ($pagto->jsonData['tipoFormaPagamento'] ?? false) {
                    $r[] = '    <tipoFormaPagamento>' . $pagto->jsonData['tipoFormaPagamento'] . '</tipoFormaPagamento>';
                }
                if ($pagto->jsonData['nomeFormaPagamento'] ?? false) {
                    $r[] = '    <nomeFormaPagamento>' . $pagto->jsonData['nomeFormaPagamento'] . '</nomeFormaPagamento>';
                }

                $r[] = '</pagamento>';
                $r[] = '<obs>' . ($venda->jsonData['obs'] ?? '') . '</obs>';

                $r[] = '<itens>';


                $itensNaNota = [];
                /** @var VendaItem $vendaItem */
                foreach ($venda->itens as $vendaItem) {
                    if ($vendaItem->produto->composicao === 'S') {
                        /** @var ProdutoComposicao $produtoComposicao */
                        foreach ($vendaItem->produto->composicoes as $produtoComposicao) {
                            $mockItem = new VendaItem();
                            $mockItem->produto = $produtoComposicao->produtoFilho;
                            $mockItem->qtde = bcmul($vendaItem->qtde, $produtoComposicao->qtde, 3);
                            $mockItem->precoVenda = $produtoComposicao->precoComposicao;
                            $mockItem->total = bcmul($mockItem->qtde, $mockItem->precoVenda, 2);
                            $itensNaNota[] = $mockItem;
                        }
                    } else {
                        $itensNaNota[] = $vendaItem;
                    }
                }


                /** @var VendaItem $vendaItem */
                foreach ($itensNaNota as $item) {
                    $r[] = '  <item>';
                    $r[] = '    <erp_codigo>' . ($item->produto->jsonData['erp_codigo'] ?? '') . '</erp_codigo>';
                    $r[] = '    <produto_nome>' . mb_strtoupper($item->produto->nome ?? $item->descricao) . '</produto_nome>';
                    $r[] = '    <fornecedor_documento>' . mb_strtoupper($item->produto->fornecedor->documento ?? '') . '</fornecedor_documento>';
                    $r[] = '    <fornecedor_nome>' . mb_strtoupper($item->produto->fornecedor->nome ?? '') . '</fornecedor_nome>';
                    $r[] = '    <qtde>' . $item->qtde . '</qtde>';
                    $r[] = '    <preco_venda>' . $item->precoVenda . '</preco_venda>';
                    $r[] = '    <desconto>' . $item->desconto . '</desconto>';
                    $r[] = '    <total>' . $item->total . '</total>';
                    $r[] = '  </item>';
                }
                $r[] = '</itens>';

                $r[] = '</venda>';
                $r[] = '';

                file_put_contents($_SERVER['PASTA_VENDAS'] . $venda->getId() . '.txt', implode(PHP_EOL, $r));

                $rs = array_merge($rs, $r);
            }
            return new Response(implode(PHP_EOL, $rs));
        }

        return new Response('Nenhum resultado');

    }


    /**
     * @Route("/api/ven/venda/setarStatus", name="api_ven_venda_setarStatus")
     * @param Request $request
     * @return Response
     */
    public function setarStatus(Request $request): Response
    {
        try {
            $uuid = $request->get('uuid');
            $codEkt = $request->get('codEkt');
            $status = $request->get('status');
            if (!in_array($status, ['INTEGRADO', 'FATURADO', 'FINALIZADO', 'CANCELADO'], true)) {
                return new Response('ERRO: status diferente de \'INTEGRADO\', \'FATURADO\', \'FINALIZADO\', \'CANCELADO\'');
            }
            /** @var VendaRepository $repoVenda */
            $repoVenda = $this->doctrine->getRepository(Venda::class);
            /** @var Venda $venda */
            $venda = $repoVenda->findOneBy(['uuid' => $uuid]);
            if (!$venda) {
                return new Response('ERRO: PV não encontrado para o uuid: ' . $uuid);
            }
            $venda->status = $status;
            $venda->jsonData['codEkt'] = $codEkt;
            $this->vendaEntityHandler->save($venda);
            return new Response('OK');
        } catch (ViewException $e) {
            return new Response('ERRO: ' . $e->getMessage());
        } catch (\Throwable $e) {
            $this->logger->error('Erro em setarIntegrado');
            $this->logger->error($e->getMessage());
            return new Response('ERRO (verificar log)');
        }
    }

}
