<?php

namespace App\Controller\Vendas\API;

use App\Entity\Vendas\PV;
use App\Repository\Vendas\PVRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * RelCtsPagReg01APIController constructor.
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $doctrine
     */
    public function __construct(LoggerInterface $logger, EntityManagerInterface $doctrine)
    {
        $this->logger = $logger;
        $this->doctrine = $doctrine;
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
        $abertos = $repoPV->findBy(['status' => 'ABERTO']);

        $r = [];

        $separador = '|@|';

        /** @var PV $pv */
        foreach ($abertos as $pv) {
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


}
