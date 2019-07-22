<?php

namespace App\Controller;


use CrosierSource\CrosierLibBaseBundle\Controller\BaseController;
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
        $params['filter']['vendas']['dts'] = $session->get('dashboard.filter.vendas.dts') ?? null;
        $params['filter']['contasPagRec']['dts'] = $session->get('dashboard.filter.contasPagRec.dts') ?? null;
        return $this->doRender('dashboard.html.twig', $params);
    }


}