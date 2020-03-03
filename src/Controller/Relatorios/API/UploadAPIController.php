<?php

namespace App\Controller\Relatorios\API;

use CrosierSource\CrosierLibBaseBundle\Utils\StringUtils\StringUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 *
 * @author Carlos Eduardo Pauluk
 */
class UploadAPIController extends AbstractController
{

    /** @var LoggerInterface */
    private $logger;

    /**
     * RelCtsPagReg01APIController constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/api/relatorios/upload", name="api_relatorios_upload")
     * @param Request $request
     * @return JsonResponse
     */
    public function upload(Request $request): JsonResponse
    {
        $this->logger->debug('Iniciando o upload...');
        $output = ['uploaded' => false];

        $tipoRelatorio = $request->get('tipoRelatorio');
        if (!$tipoRelatorio || !in_array($tipoRelatorio, ['RELCTSPAGREC01', 'RELVENDAS01', 'RELCOMPFOR01', 'RELESTOQUE01', 'RELCOMPRAS01', 'RELCLIENTES01'])) {
            $output['msg'] = 'tipoRelatorio inexistente: "' . $tipoRelatorio . '"';
            return new JsonResponse($output);
        }
        $dir = $_SERVER['PASTA_UPLOAD_' . $tipoRelatorio] . 'fila/';

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

        /** @var UploadedFile $fileData */
        $uuid = StringUtils::guidv4();
        $extensao = pathinfo($filename, PATHINFO_EXTENSION);
        $novoNome = $uuid . '.' . $extensao;
        $nomeArquivo = $dir . $novoNome;
        file_put_contents($nomeArquivo, $fileData);
        $output['uploaded'] = true;
        $output['nomeArquivo'] = $novoNome;

        return new JsonResponse($output);
    }


}
