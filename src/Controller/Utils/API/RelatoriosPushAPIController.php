<?php

namespace App\Controller\Utils\API;

use App\Entity\Utils\RelatorioPush;
use App\EntityHandler\Utils\RelatorioPushEntityHandler;
use CrosierSource\CrosierLibBaseBundle\APIClient\Config\PushMessageAPIClient;
use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierEntityIdAPIClient;
use CrosierSource\CrosierLibBaseBundle\Controller\BaseController;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RelatoriosPushAPIController extends BaseController
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /** @var RelatorioPushEntityHandler */
    private $relatorioPushEntityHandler;

    /** @var CrosierEntityIdAPIClient */
    private $crosierEntityIdAPIClient;


    /**
     * @required
     * @param mixed $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @required
     * @param RelatorioPushEntityHandler $relatorioPushEntityHandler
     */
    public function setRelatorioPushEntityHandler(RelatorioPushEntityHandler $relatorioPushEntityHandler): void
    {
        $this->relatorioPushEntityHandler = $relatorioPushEntityHandler;
    }

    /**
     * @required
     * @param CrosierEntityIdAPIClient $crosierEntityIdAPIClient
     */
    public function setCrosierEntityIdAPIClient(CrosierEntityIdAPIClient $crosierEntityIdAPIClient): void
    {
        $this->crosierEntityIdAPIClient = $crosierEntityIdAPIClient;
    }



    /**
     * @Route("/api/utils/relatoriosPush/upload", name="relatorios_push_upload")
     * @param Request $request
     * @return JsonResponse
     * @throws ViewException
     */
    public function upload(Request $request): JsonResponse
    {
        $this->logger->debug('Iniciando o upload...');
        $output = ['uploaded' => false];
        if ($request->files->get('file') && $request->get('userDestinatarioId')) {
            /** @var RelatorioPush $relatorioPush */
            $relatorioPush = new RelatorioPush();
            $relatorioPush->setFile($request->files->get('file'));
            $relatorioPush->setDtEnvio(new \DateTime());
            $relatorioPush->setUserDestinatarioId($request->get('userDestinatarioId'));
            $relatorioPush->setDescricao($relatorioPush->getFile()->getClientOriginalName());
            $this->relatorioPushEntityHandler->save($relatorioPush);
            $this->push($relatorioPush);
            $output['uploaded'] = true;
        } else {
            $this->logger->debug('file ou userDestinatarioId não informados');
            $this->logger->debug('file: ' . $request->files->get('file'));
            $this->logger->debug('userDestinatarioId: ' . $request->get('userDestinatarioId'));

        }
        return new JsonResponse($output);

    }

    public function push(RelatorioPush $relatorioPush): void
    {
        $pushMessage = [
            'userDestinatarioId' => $relatorioPush->getUserDestinatarioId(),
            'url' => $_SERVER['CROSIERAPPRDP_URL'] . '/relatorioPush/abrir/' . $relatorioPush->getId(),
            'mensagem' => 'Você recebeu um novo arquivo...'
        ];
        $this->crosierEntityIdAPIClient->setBaseURI($_SERVER['CROSIERCORE_URL'] . '/api/cfg/pushMessage');
        $this->crosierEntityIdAPIClient->save($pushMessage);
    }


}