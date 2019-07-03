<?php

namespace App\Controller\Utils\API;

use App\Entity\Utils\RelatorioPush;
use App\EntityHandler\Utils\RelatorioPushEntityHandler;
use CrosierSource\CrosierLibBaseBundle\Controller\BaseController;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class RelatoriosPushAPIController extends BaseController
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /** @var RelatorioPushEntityHandler */
    private $relatorioPushEntityHandler;

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
        }
        return new JsonResponse($output);

    }

    /** @var Publisher */
    private $publisher;

    /**
     * @required
     * @param Publisher $publisher
     */
    public function setPublisher(Publisher $publisher): void
    {
        $this->publisher = $publisher;
    }


    public function push(RelatorioPush $relatorioPush): void
    {
        $topic = 'https://mercure.crosier/topics/user/' . $relatorioPush->getUserDestinatarioId();

        $params = [
            'subject' => 'relatorioPush',
            'arquivo' => $relatorioPush->getArquivo()
        ];

        $update = new Update($topic, json_encode($params));
        $this->publisher->__invoke($update);
    }


}