<?php

namespace App\Controller\Utils\API;

use CrosierSource\CrosierLibBaseBundle\Controller\BaseController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RelatoriosPushController extends BaseController
{

    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * @Route("/api/utils/relatoriosPush/upload", name="relatorios_push_upload")
     * @param Request $request
     * @return JsonResponse
     */
    public function upload(Request $request): JsonResponse
    {
        // verificar o IP
        $file = $request->files->get('file');
        $r = print_r($file, true);
        return new JsonResponse($r);
    }

    /**
     * @required
     * @param mixed $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}