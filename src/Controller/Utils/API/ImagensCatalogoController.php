<?php

namespace App\Controller\Utils\API;


use CrosierSource\CrosierLibBaseBundle\Controller\BaseController;
use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\Config\AppConfigEntityHandler;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\AppConfigRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;

/**
 *
 *
 * @package App\Controller\Utils
 * @author Carlos Eduardo Pauluk
 */
class ImagensCatalogoController extends BaseController
{
    /**
     *
     * @Route("/api/utils/setarImagemParaUsuario", name="api_utils_setarImagemParaUsuario")
     */
    public function setarImagemParaUsuario(Request $request, AppConfigEntityHandler $appConfigEntityHandler): JsonResponse
    {
        try {
            $usuario = $request->get('usuario');
            $recnum = $request->get('recnum');
            /** @var AppConfigRepository $repoAppConfig */
            $repoAppConfig = $this->getDoctrine()->getRepository(AppConfig::class);
            $appConfig = $repoAppConfig->findOneBy(['appUUID' => 'b10ef8c0-841f-4688-9ee2-30e39639be8a', 'chave' => 'recnumImagemParaUsuario_' . $usuario]);
            if (!$appConfig) {
                $appConfig = new AppConfig();
                $appConfig->setAppUUID('b10ef8c0-841f-4688-9ee2-30e39639be8a');
                $appConfig->setChave('recnumImagemParaUsuario_' . $usuario);
            }
            $appConfig->setValor($recnum);
            $appConfigEntityHandler->save($appConfig);
            return new JsonResponse(['result' => 'OK']);
        } catch (\Throwable $e) {
            $this->getLogger()->error('Erro ao setarImagemParaUsuario');
            $this->getLogger()->error('usuario: ' . $usuario);
            $this->getLogger()->error('recnum: ' . $recnum);
            return new JsonResponse(['result' => 'ERRO']);
        }
    }

    /**
     *
     * @Route("/api/utils/obterImagemParaUsuario", name="api_utils_obterImagemParaUsuario")
     *
     */
    public function obterImagemParaUsuario(Request $request): RedirectResponse
    {
        $usuario = $request->get('usuario');
        /** @var AppConfigRepository $repoAppConfig */
        $repoAppConfig = $this->getDoctrine()->getRepository(AppConfig::class);
        /** @var AppConfig $appConfig */
        $appConfig = $repoAppConfig->findOneBy(['appUUID' => 'b10ef8c0-841f-4688-9ee2-30e39639be8a', 'chave' => 'recnumImagemParaUsuario_' . $usuario]);
        $recnum = $appConfig->getValor() ?? '99999';

        $client = new Client();
        $urlAPICatalogo = $_SERVER['URL_API_CATALOGO'] . $recnum;
        $response = $client->request('GET', $urlAPICatalogo)->getBody()->getContents();
        $json = json_decode($response, true);

        $urlFoto = $_SERVER['URL_FOTOS_CATALOGO'] . $json['fotos'][0];

        return $this->redirect($urlFoto);
    }


}
