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

    /**
     *
     * @Route("/push", name="push")
     */
    public function __invoke(Publisher $publisher): Response
    {
        $update = new Update(
            'http://example.com/books/1',
            json_encode(['status' => 'OutOfStock'])
        );

        // The Publisher service is an invokable object
        $publisher($update);

        return new Response('published!');
    }

}