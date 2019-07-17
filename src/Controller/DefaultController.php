<?php

namespace App\Controller;


use CrosierSource\CrosierLibBaseBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
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
    public function index()
    {
        return $this->doRender('dashboard.html.twig');
    }


    /**
     *
     * @Route("/testMercure", name="testMercure")
     */
    public function testMercure()
    {
        return $this->doRender('push.html.twig');

    }


    public function processarArquivo() {
        // ANO|MES|COD_FORNEC|NOME_FORNEC|COD_PROD|DESC_PROD|TOTAL_PRECO_VENDA|TOTAL_PRECO_CUSTO|RENTABILIDADE|COD_VENDEDOR|NOME_VENDEDOR
    }




}