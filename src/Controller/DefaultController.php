<?php

namespace App\Controller;


use CrosierSource\CrosierLibBaseBundle\Controller\BaseController;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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

        $primeiroDia_mesPassado = '01/' . ((new \DateTime())->sub(new \DateInterval('P1M')))->format('m/Y');
        $ultimoDia_mesPassado = ((new \DateTime())->sub(new \DateInterval('P1M')))->format('t/m/Y');

        $params['filter']['vendas']['dts'] = $session->get('dashboard.filter.vendas.dts') ?? ($primeiroDia . ' - ' . $ultimoDia);
        $params['filter']['contasPagRec']['dts'] = $session->get('dashboard.filter.contasPagRec.dts') ?? ($hoje . ' - ' . $mais60dias);
        $params['filter']['relCompFor01']['dts'] = $session->get('dashboard.filter.relCompFor01.dts') ?? ($primeiroDia_mesPassado . ' - ' . $ultimoDia_mesPassado);
        return $this->doRender('dashboard.html.twig', $params);
    }


}