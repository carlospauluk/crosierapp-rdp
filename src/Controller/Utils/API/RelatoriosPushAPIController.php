<?php

namespace App\Controller\Utils\API;

use App\Entity\Utils\RelatorioPush;
use App\EntityHandler\Utils\RelatorioPushEntityHandler;
use CrosierSource\CrosierLibBaseBundle\APIClient\CrosierEntityIdAPIClient;
use CrosierSource\CrosierLibBaseBundle\Controller\BaseController;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\StringUtils\StringUtils;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RelatoriosPushAPIController
 *
 * @package App\Controller\Utils\API
 */
class RelatoriosPushAPIController extends BaseController
{

    private LoggerInterface $logger;

    private RelatorioPushEntityHandler $relatorioPushEntityHandler;

    private CrosierEntityIdAPIClient $crosierEntityIdAPIClient;


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

        $userDestinatarioId = $request->get('userDestinatarioId');
        if (!$userDestinatarioId) {
            $output['msg'] = 'userDestinatarioId N/D';
            return new JsonResponse($output);
        }

        $filename = $request->get('filename');
        if (!$filename) {
            $output['msg'] = 'filename N/D';
            return new JsonResponse($output);
        }

        $rFile = $request->get('file');
        if (!$rFile) {
            $output['msg'] = 'file N/D';
            return new JsonResponse($output);
        }
        $fileData = gzdecode(base64_decode($rFile));

        $uuid = StringUtils::guidv4();
        $extensao = pathinfo($filename, PATHINFO_EXTENSION);

        $tmpFile = sys_get_temp_dir() . '/' . $uuid . '.' . $extensao;
        file_put_contents($tmpFile, $fileData);

        $mimeType = MimeTypes::getDefault()->guessMimeType($tmpFile);

        $file = new UploadedFile($tmpFile, $filename, $mimeType, null, true);

        /** @var RelatorioPush $relatorioPush */
        $relatorioPush = new RelatorioPush();


        $relatorioPush->setTipoArquivo($file->getMimeType());
        $relatorioPush->setFile($file);
        $relatorioPush->setDtEnvio(new \DateTime());
        $relatorioPush->setUserDestinatarioId($request->get('userDestinatarioId'));
        $relatorioPush->setDescricao($relatorioPush->getFile()->getClientOriginalName());
        $this->relatorioPushEntityHandler->save($relatorioPush);
        $this->push($relatorioPush);
        $output['uploaded'] = true;

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
