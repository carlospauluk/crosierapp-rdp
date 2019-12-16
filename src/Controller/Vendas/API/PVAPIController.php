<?php

namespace App\Controller\Vendas\API;

use App\Entity\Vendas\PV;
use App\EntityHandler\Vendas\PVEntityHandler;
use App\Repository\Vendas\PVRepository;
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
class PVAPIController extends AbstractController
{

    /** @var EntityManagerInterface */
    private $doctrine;

    /** @var LoggerInterface */
    private $logger;
    /**
     * @var PVEntityHandler
     */
    private $pvEntityHandler;

    /**
     * RelCtsPagReg01APIController constructor.
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $doctrine
     * @param PVEntityHandler $pvEntityHandler
     */
    public function __construct(LoggerInterface $logger,
                                EntityManagerInterface $doctrine,
                                PVEntityHandler $pvEntityHandler)
    {
        $this->logger = $logger;
        $this->doctrine = $doctrine;
        $this->pvEntityHandler = $pvEntityHandler;
    }

    /**
     * @Route("/api/ven/pv/consultarNovos", name="api_ven_pv_consultarNovos")
     * @return JsonResponse
     */
    public function consultarNovos(): Response
    {
        $this->logger->debug('api_ven_pv_consultarNovos');

        /** @var PVRepository $repoPV */
        $repoPV = $this->doctrine->getRepository(PV::class);
        $enviados = $repoPV->findBy(['status' => 'ENVIADO']);

        $r = [];

        $separador = '|@|';

        if ($enviados) {
            /** @var PV $pv */
            foreach ($enviados as $pv) {
                $r[] = $pv->getUuid() . $separador .
                    $pv->getClienteCod() . $separador .
                    $pv->getCliente()->getDocumento() . $separador .
                    $pv->getCliente()->getNome() . $separador .
                    $pv->getVendedor() . $separador .
                    $pv->getCondPagto() . $separador .
                    $pv->getDeposito() . $separador .
                    $pv->getFilial() . $separador .
                    $pv->getLocalizador() . $separador .
                    $pv->getStatus() . $separador .
                    $pv->getVenctos() . $separador .
                    $pv->getSubtotal() . $separador .
                    $pv->getDescontos() . $separador .
                    $pv->getTotal();

                foreach ($pv->getItens() as $item) {
                    $r[] = $item->getProduto()->codigoFrom . $separador .
                        $item->getProduto()->nome . $separador .
                        $item->getCodFornecedor() . $separador .
                        $item->getNomeFornecedor() . $separador .
                        $item->getQtde() . $separador .
                        $item->getPrecoCusto() . $separador .
                        $item->getPrecoOrc() . $separador .
                        $item->getPrecoVenda();
                }

                $r[] = '...';

            }
            return new Response(implode(PHP_EOL, $r));
        }

        return new Response('Nenhum resultado');

    }


    /**
     * @Route("/api/ven/pv/setarStatus", name="api_ven_pv_setarStatus")
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
            /** @var PVRepository $repoPV */
            $repoPV = $this->doctrine->getRepository(PV::class);
            /** @var PV $pv */
            $pv = $repoPV->findOneBy(['uuid' => $uuid]);
            if (!$pv) {
                return new Response('ERRO: PV não encontrado para o uuid: ' . $uuid);
            }
            $pv->setStatus($status);
            $pv->setPvEkt($codEkt);
            $this->pvEntityHandler->save($pv);
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
