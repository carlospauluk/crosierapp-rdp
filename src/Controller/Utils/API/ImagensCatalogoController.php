<?php

namespace App\Controller\Utils\API;


use App\Entity\Estoque\Produto;
use App\Entity\Estoque\ProdutoImagem;
use App\EntityHandler\Estoque\ProdutoImagemEntityHandler;
use App\Repository\Estoque\ProdutoRepository;
use CrosierSource\CrosierLibBaseBundle\Controller\BaseController;
use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\EntityHandler\Config\AppConfigEntityHandler;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\AppConfigRepository;
use Doctrine\DBAL\Connection;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Naming\UniqidNamer;

/**
 *
 *
 * @author Carlos Eduardo Pauluk
 */
class ImagensCatalogoController extends BaseController
{
    /**
     *
     * @Route("/api/utils/setarImagemParaUsuario", name="api_utils_setarImagemParaUsuario")
     * @param Request $request
     * @param AppConfigEntityHandler $appConfigEntityHandler
     * @return JsonResponse
     */
    public function setarImagemParaUsuario(Request $request, AppConfigEntityHandler $appConfigEntityHandler): JsonResponse
    {
        try {
            $usuario = $request->get('usuario');
            $recnum = $request->get('recnum');
            /** @var AppConfigRepository $repoAppConfig */
            $repoAppConfig = $this->getDoctrine()->getRepository(AppConfig::class);
            $appConfig = $repoAppConfig->findOneBy(['appUUID' => $_SERVER['CROSIERAPP_UUID'], 'chave' => 'recnumImagemParaUsuario_' . $usuario]);
            if (!$appConfig) {
                $appConfig = new AppConfig();
                $appConfig->setAppUUID($_SERVER['CROSIERAPP_UUID']);
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
     * @param Request $request
     * @return RedirectResponse
     */
    public function obterImagemParaUsuario(Request $request): RedirectResponse
    {
        $urlFoto = $_SERVER['CROSIERCORE_URL'] . '/build/static/images/notfound.png';

        $usuario = $request->get('usuario');

        try {
            /** @var AppConfigRepository $repoAppConfig */
            $repoAppConfig = $this->getDoctrine()->getRepository(AppConfig::class);
            /** @var AppConfig $appConfig */
            $appConfig = $repoAppConfig->findOneBy(['appUUID' => $_SERVER['CROSIERAPP_UUID'], 'chave' => 'recnumImagemParaUsuario_' . $usuario]);
            $recnum = $appConfig->getValor() ?? '99999';
            $client = new Client();
            $urlAPICatalogo = $_SERVER['URL_API_CATALOGO'] . $recnum;
            $response = $client->request('GET', $urlAPICatalogo)->getBody()->getContents();
            $json = json_decode($response, true);
            $foto = $json['fotos'][0] ?? null;
            if ($foto) {
                $urlFoto = $_SERVER['URL_FOTOS_CATALOGO'] . $foto;
            }
        } catch (\Exception $e) {
            $this->getLogger()->error('Erro ao obterImagemParaUsuario');
            $this->getLogger()->error('usuario: ' . $usuario);
            $this->getLogger()->error($e->getMessage());
        }

        return $this->redirect($urlFoto);
    }


    /**
     *
     * @Route("/downloadCatalogo", name="downloadCatalogo")
     * @param ProdutoImagemEntityHandler $produtoImagemEntityHandler
     * @return Response
     */
    public function downloadCatalogo(ProdutoImagemEntityHandler $produtoImagemEntityHandler): Response
    {

        try {
            /** @var ProdutoRepository $repoProduto */
            $repoProduto = $this->getDoctrine()->getRepository(Produto::class);
            /** @var Connection $conn */
            $conn = $this->getDoctrine()->getConnection();
            $todos = $conn->fetchAll('SELECT id, json_data->>"$.erp_codigo" as recnum FROM est_produto');
            $client = new Client();
            foreach ($todos as $r) {
                try {
                    $urlAPICatalogo = $_SERVER['URL_API_CATALOGO'] . $r['recnum'];
                    $response = $client->request('GET', $urlAPICatalogo)->getBody()->getContents();
                    $json = json_decode($response, true);
                    $foto = $json['fotos'][0] ?? null;
                    if ($foto) {
                        /** @var Produto $produto */
                        $produto = $repoProduto->find($r['id']);
                        $urlFoto = $_SERVER['URL_FOTOS_CATALOGO'] . $foto;

                        file_put_contents(sys_get_temp_dir() . '/' . $foto, file_get_contents($urlFoto));

                        $file = new UploadedFile(sys_get_temp_dir() . '/' . $foto, $foto, null, null, true);

                        $produtoImagem = new ProdutoImagem();
                        $produtoImagem->setImageFile($file);
                        // $produtoImagem->setImageName($foto);
                        $produtoImagem->setProduto($produto);
                        $produtoImagemEntityHandler->save($produtoImagem);
                        @unlink(sys_get_temp_dir() . '/' . $foto);
                    }
                } catch (\Exception $e) {
                    $this->getLogger()->error('Erro ao obter imagem em ' . $urlAPICatalogo);
                    $this->getLogger()->error($e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $this->getLogger()->error('Erro ao downloadCatalogo');
            $this->getLogger()->error($e->getMessage());
        }
        return new Response('OK');
    }


}
