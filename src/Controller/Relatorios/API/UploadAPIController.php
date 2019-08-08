<?php

namespace App\Controller\Relatorios\API;

use App\Business\Relatorios\RelCtsPagRec01Business;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\StringUtils\StringUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @package App\Controller\Utils\API
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
        if (!$tipoRelatorio || !in_array($tipoRelatorio, ['RELCTSPAGREC01', 'RELVENDAS01', 'RELCOMPFOR01', 'RELESTOQUE01', 'RELCOMPRAS01'])) {
            $output['msg'] = 'tipoRelatorio inexistente: "' . $tipoRelatorio . '"';
            return new JsonResponse($output);
        }
        $dir = $_SERVER['PASTA_UPLOAD_' . $tipoRelatorio] . 'fila/';

        if ($request->files->get('file')) {
            $file = $request->files->get('file');
            if ($file->getError() !== 0) {
                $output['msg'] = $file->getErrorMessage();
            } else if ($file instanceof UploadedFile) {
                /** @var UploadedFile $file */
                $uuid = StringUtils::guidv4();
                $extensao = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                $novoNome = $uuid . '.' . $extensao;
                $nomeArquivo = $dir . $novoNome;
                copy($file->getPathname(), $nomeArquivo);
                $output['uploaded'] = true;
                $output['nomeArquivo'] = $novoNome;
            }
        } else {
            $output['msg'] = 'file não informado';
            $this->logger->debug('"file" não informado');
            $this->logger->debug('file: ' . $request->files->get('file'));
        }
        return new JsonResponse($output);
    }


}
