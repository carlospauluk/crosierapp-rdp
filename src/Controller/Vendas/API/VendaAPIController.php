<?php

namespace App\Controller\Vendas\API;

use CrosierSource\CrosierLibBaseBundle\Business\Config\SyslogBusiness;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use CrosierSource\CrosierLibRadxBundle\Entity\Estoque\Produto;
use CrosierSource\CrosierLibRadxBundle\Entity\Vendas\Venda;
use CrosierSource\CrosierLibRadxBundle\Entity\Vendas\VendaItem;
use CrosierSource\CrosierLibRadxBundle\EntityHandler\Vendas\VendaEntityHandler;
use CrosierSource\CrosierLibRadxBundle\Repository\Estoque\ProdutoRepository;
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
     * @throws \Exception
     */
    public function consultarECommerces(string $data = null): Response
    {
        /** @var VendaRepository $repoVenda */
        $repoVenda = $this->doctrine->getRepository(Venda::class);

        $filterData_canal = new FilterData('canal', 'EQ', 'ECOMMERCE', null, null, true);
        $filterData_canal->setVal('ECOMMERCE');


        if (!$data) {
            $filterData_data_hoje = new FilterData('dtVenda', 'EQ', 'dtVenda', null, 'date');
            $filterData_data_hoje->setVal(new \DateTime());
            $filterData_data_ontem_e_hoje = new FilterData('dtVenda', 'EQ', 'dtVenda', null, 'date', false, $filterData_data_hoje);
            $ontem = (new \DateTime())->sub((new \DateInterval('P1D')));
            $filterData_data_ontem_e_hoje->setVal($ontem);
            $vendas = $repoVenda->findByFiltersSimpl([$filterData_canal, $filterData_data_ontem_e_hoje]);
        } else {
            $filterData_data = new FilterData('dtVenda', 'EQ', 'dtVenda', null, 'date');
            $dtData = DateTimeUtils::parseDateStr($data);
            $filterData_data->setVal($dtData);
            $vendas = $repoVenda->findByFiltersSimpl([$filterData_canal, $filterData_data]);
        }

        /** @var ProdutoRepository $repoProduto */
        $repoProduto = $this->doctrine->getRepository(Produto::class);

        $r = [];

        if ($vendas) {
            /** @var Venda $venda */
            foreach ($vendas as $venda) {
                $r[] = '<<Cabeçalho>>';
                $r[] = $venda->dtVenda->format('Y-m-d H:i:s');
                $r[] = $venda->cliente->documento;
                $r[] = $venda->cliente->nome;
                $r[] = $venda->subtotal;
                $r[] = $venda->desconto;
                $r[] = $venda->valorTotal;

                /** @var VendaItem $item */
                foreach ($venda->itens as $item) {
                    $r[] = '<<Itens>>';

                    $r[] = $item->produto->jsonData['erp_codigo'] ?? '';
                    $r[] = $item->produto->nome ?? $item->descricao;
                    $r[] = $item->produto->fornecedor->documento ?? '';
                    $r[] = $item->produto->fornecedor->nome ?? '';
                    $r[] = $item->qtde;
                    $r[] = $item->precoVenda;
                    $r[] = $item->desconto;
                    $r[] = $item->total;
                }
            }
            return new Response(implode(PHP_EOL, $r));
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
