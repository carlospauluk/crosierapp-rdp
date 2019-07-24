<?php

namespace App\Controller;


use CrosierSource\CrosierLibBaseBundle\Controller\BaseController;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package App\Controller
 * @author Carlos Eduardo Pauluk
 */
class DefaultController extends BaseController
{

    /**
     *
     * @Route("/", name="index")
     */
    public function index(SessionInterface $session)
    {
        $hoje = (new \DateTime())->format('d/m/Y');
        $mais60dias = ((new \DateTime())->add(new \DateInterval('P60D')))->format('d/m/Y');
        $primeiroDia = (DateTimeUtils::getPrimeiroDiaMes())->format('d/m/Y');
        $ultimoDia = (DateTimeUtils::getUltimoDiaMes())->format('d/m/Y');

        $params['filter']['vendas']['dts'] = $session->get('dashboard.filter.vendas.dts') ?? ($primeiroDia . ' - ' . $ultimoDia);
        $params['filter']['contasPagRec']['dts'] = $session->get('dashboard.filter.contasPagRec.dts') ?? ($hoje . ' - ' . $mais60dias);
        return $this->doRender('dashboard.html.twig', $params);
    }


}