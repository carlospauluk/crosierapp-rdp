<?php

namespace App\Controller\Relatorios\API;

use App\Business\Relatorios\RelCtsPagRec01Business;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
class RelCtsPagReg01APIController extends AbstractController
{

    /** @var LoggerInterface */
    private $logger;

    /** @var RelCtsPagRec01Business */
    private $relCtsPagRec01Business;

    /**
     * RelCtsPagReg01APIController constructor.
     * @param LoggerInterface $logger
     * @param RelCtsPagRec01Business $relCtsPagRec01Business
     */
    public function __construct(LoggerInterface $logger, RelCtsPagRec01Business $relCtsPagRec01Business)
    {
        $this->logger = $logger;
        $this->relCtsPagRec01Business = $relCtsPagRec01Business;
    }

    /**
     * @Route("/api/relatorios/relCtsPagRec01/upload", name="api_relatorios_relCtsPagRec01_upload")
     * @param Request $request
     * @return JsonResponse
     * @throws ViewException
     */
    public function upload(Request $request): JsonResponse
    {
        $this->logger->debug('Iniciando o upload...');
        $output = ['uploaded' => false];

        if ($request->files->get('file')) {
            $file = $request->files->get('file');
            if ($file instanceof UploadedFile) {
                /** @var UploadedFile $file */
                $contents = file_get_contents($file->getPathname());
                $output['uploaded'] = true;
            }
        } else {
            $this->logger->debug('"file" não informado');
            $this->logger->debug('file: ' . $request->files->get('file'));
        }
        return new JsonResponse($output);
    }


}
