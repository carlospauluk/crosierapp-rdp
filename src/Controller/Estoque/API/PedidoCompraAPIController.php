<?php

namespace App\Controller\Estoque\API;

use App\Entity\Estoque\PedidoCompra;
use App\EntityHandler\Estoque\PedidoCompraEntityHandler;
use App\Repository\Estoque\PedidoCompraRepository;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
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
class PedidoCompraAPIController extends AbstractController
{

    private EntityManagerInterface $doctrine;

    private LoggerInterface $logger;

    private PedidoCompraEntityHandler $pedidoCompraEntityHandler;

    /**
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $doctrine
     * @param PedidoCompraEntityHandler $pedidoCompraEntityHandler
     */
    public function __construct(LoggerInterface $logger,
                                EntityManagerInterface $doctrine,
                                PedidoCompraEntityHandler $pedidoCompraEntityHandler)
    {
        $this->logger = $logger;
        $this->doctrine = $doctrine;
        $this->pedidoCompraEntityHandler = $pedidoCompraEntityHandler;
    }

    /**
     * @Route("/api/est/pedidoCompra/consultarNovos", name="api_est_pedidoCompra_consultarNovos")
     * @return JsonResponse
     */
    public function consultarNovos(): Response
    {
        $this->logger->debug('api_est_pedidoCompra_consultarNovos');

        /** @var PedidoCompraRepository $repoPedidoCompra */
        $repoPedidoCompra = $this->doctrine->getRepository(PedidoCompra::class);
        $enviados = $repoPedidoCompra->findBy(['status' => 'ENVIADO']);

        $r = [];

        if ($enviados) {
            /** @var PedidoCompra $pedidoCompra */
            foreach ($enviados as $pedidoCompra) {
                $r[] = '<<Cabeçalho>>';
                $r[] = $pedidoCompra->fornecedor->nome;
                $r[] = $pedidoCompra->dtEmissao->format('d/m/Y');
                $r[] = $pedidoCompra->responsavel;
                $r[] = $pedidoCompra->prazosPagto;
                $r[] = $pedidoCompra->dtPrevEntrega->format('d/m/Y');
                $r[] = $pedidoCompra->subtotalgetCondPagto();
                $r[] = $pedidoCompra->getDeposito();
                $r[] = $pedidoCompra->getFilial();
                $r[] = $pedidoCompra->getLocalizador();
                $r[] = $pedidoCompra->getStatus();
                $r[] = $pedidoCompra->getVenctos();
                $r[] = $pedidoCompra->getSubtotal();
                $r[] = $pedidoCompra->getDescontos();
                $r[] = $pedidoCompra->getTotal();

                foreach ($pedidoCompra->getItens() as $item) {
                    $r[] = '<<Itens>>';
                    $r[] = $item->getProduto()->codigoFrom;
                    $r[] = $item->getProduto()->nome;
                    $r[] = $item->getCodFornecedor();
                    $r[] = $item->getNomeFornecedor();
                    $r[] = $item->getQtde();
                    $r[] = $item->getPrecoCusto();
                    $r[] = $item->getPrecoOrc();
                    $r[] = $item->getPrecoVenda();
                }
            }
            return new Response(implode(PHP_EOL, $r));
        }

        return new Response('Nenhum resultado');

    }


    /**
     * @Route("/api/est/pedidoCompra/setarStatus", name="api_est_pedidoCompra_setarStatus")
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
            /** @var PedidoCompraRepository $repoPedidoCompra */
            $repoPedidoCompra = $this->doctrine->getRepository(PedidoCompra::class);
            /** @var PedidoCompra $pedidoCompra */
            $pedidoCompra = $repoPedidoCompra->findOneBy(['uuid' => $uuid]);
            if (!$pedidoCompra) {
                return new Response('ERRO: PV não encontrado para o uuid: ' . $uuid);
            }
            $pedidoCompra->status = $status;
            $pedidoCompra->jsonData['codEkt'] = $codEkt;
            $this->pedidoCompraEntityHandler->save($pedidoCompra);
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
