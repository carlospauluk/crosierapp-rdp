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
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @param Request $request
     * @param ProdutoImagemEntityHandler $produtoImagemEntityHandler
     * @param AppConfigEntityHandler $appConfigEntityHandler
     * @return Response
     */
    public function downloadCatalogo(Request $request, ProdutoImagemEntityHandler $produtoImagemEntityHandler, AppConfigEntityHandler $appConfigEntityHandler): Response
    {
        try {
            $html = 'NADA';
            /** @var AppConfigRepository $repoAppConfig */
            $repoAppConfig = $this->getDoctrine()->getRepository(AppConfig::class);
            $rImagensNoCatalogo = $repoAppConfig->findAppConfigByChave('imagensNoCatalogo') ?? new AppConfig('imagensNoCatalogo', $_SERVER['CROSIERAPP_UUID']);
            $imagensNoCatalogo = json_decode($rImagensNoCatalogo->getValor(), true);
            $imagensNoCatalogo['baixadas'] = $imagensNoCatalogo['baixadas'] ?? [];
            $imagensNoCatalogo['baixadas2'] = $imagensNoCatalogo['baixadas2'] ?? [];
            $imagensNoCatalogo['naoConsta'] = $imagensNoCatalogo['naoConsta'] ?? [];
            $imagensNoCatalogo['erros'] = $imagensNoCatalogo['erros'] ?? [];

            $ids = array_merge([-1], $imagensNoCatalogo['baixadas2'], $imagensNoCatalogo['erros'], $imagensNoCatalogo['naoConsta']);

            /** @var ProdutoRepository $repoProduto */
            $repoProduto = $this->getDoctrine()->getRepository(Produto::class);
            /** @var Connection $conn */
            $conn = $this->getDoctrine()->getConnection();
            $limit = (int)($request->get('limit') ?? 500);
            $todos = $conn->fetchAll('SELECT id, json_data->>"$.recnum" as recnum FROM est_produto WHERE json_data->>"$.recnum" IS NOT NULL AND id NOT IN (' . implode(',', $ids) . ') ORDER BY id LIMIT ' . $limit);
            $client = new Client();
            $this->getLogger()->error('SOH ATIVANDO O LOG');
            foreach ($todos as $r) {

                $urlAPICatalogo = $_SERVER['URL_API_CATALOGO'] . $r['recnum'];
                try {
                    $this->getLogger()->info('Tentando baixar para o recnum ' . $r['recnum']);
                    $response = $client->request('GET', $urlAPICatalogo)->getBody()->getContents();
                    $json = json_decode($response, true);

                    if (isset($json['fotos']) && count($json['fotos']) > 0) {
                        /** @var Produto $produto */
                        $produto = $repoProduto->find($r['id']);

                        if (count($json['fotos']) < 1) {
                            if (!in_array($r['id'], $imagensNoCatalogo['naoConsta'])) {
                                $imagensNoCatalogo['naoConsta'][] = $r['id'];
                            }
                            continue;
                        }

                        if (count($json['fotos']) <= $produto->imagens->count()) {
                            $this->getLogger()->info('id = ' . $r['id'] . '( recnum = ' . $r['recnum'] . ') já foi baixada');
                            $imagensNoCatalogo['baixadas2'][] = $r['id'];
                            continue;
                        }

                        $this->getLogger()->info('Baixando foto para produtoId = ' . $r['id']);

                        $i = 0;
                        foreach ($json['fotos'] as $foto) {
                            $i++;
                            if (in_array($r['id'], $imagensNoCatalogo['baixadas']) && $i === 1) {
                                continue;
                            }
                            $urlFoto = $_SERVER['URL_FOTOS_CATALOGO'] . $foto;
                            $this->getLogger()->info('url = ' . $urlFoto);
                            file_put_contents(sys_get_temp_dir() . '/' . $foto, file_get_contents($urlFoto));

                            $file = new UploadedFile(sys_get_temp_dir() . '/' . $foto, $foto, null, null, true);

                            $produtoImagem = new ProdutoImagem();
                            $produtoImagem->setImageFile($file);
                            // $produtoImagem->setImageName($foto);
                            $produtoImagem->setProduto($produto);
                            /** @var ProdutoImagem $produtoImagem */
                            $produtoImagemEntityHandler->save($produtoImagem);
                            $produto->imagens->add($produtoImagem);
                        }

                        $json_data = json_encode([
                            'qtde_imagens' => $produto->imagens->count(),
                            'imagem1' => $produto->imagens->get(0)->getImageName()
                        ]);

                        $json_data = 'JSON_MERGE_PATCH(json_data,\'' . $json_data . '\')';

                        $conn->exec('UPDATE est_produto SET json_data = ' . $json_data . ' WHERE id = ' . $produto->getId());

                        $imagensNoCatalogo['baixadas2'][] = $r['id'];

                        $this->getLogger()->info('OK >>> ' . $i . ' imagens baixadas');
                    } else {
                        $this->getLogger()->info('NÃO CONSTA ' . $r['recnum']);
                        $imagensNoCatalogo['naoConsta'][] = $r['id'];
                    }
                } catch (\Exception $e) {
                    if ($e instanceof ClientException) {
                        if ($e->getCode() === 404) {
                            $this->getLogger()->info('NÃO CONSTA ' . $r['recnum']);
                            $imagensNoCatalogo['naoConsta'][] = $r['id'];
                        } else {
                            $imagensNoCatalogo['erros'][] = $r['id'];
                        }
                    }
                    $this->getLogger()->error('Erro ao obter imagem em ' . $urlAPICatalogo);
                    $this->getLogger()->error($e->getMessage());

                }
            }
            $rImagensNoCatalogo->setValor(json_encode($imagensNoCatalogo));
            $appConfigEntityHandler->save($rImagensNoCatalogo);

            $html = '';
            $html .= '<a href="#baixadas">Baixadas</a><br>';
            $html .= '<a href="#naoConsta">Não constam</a><br>';
            $html .= '<a href="#erros">Erros</a><br>';

            $t = 0;

            $html .= '<h1 id="baixadas">Imagens baixadas do catálogo</h1>';
            foreach ($imagensNoCatalogo['baixadas2'] as $k => $v) {
                $html .= ($k + 1) . ') <a href="' . $_SERVER['CROSIERAPPRADX_URL'] . '/est/produto/form/' . $v . '">' . $_SERVER['CROSIERAPPRADX_URL'] . '/est/produto/form/' . $v . '</a><br>';
                $t++;
            }
            $html .= '<hr>';

            $html .= '<h1 id="naoConsta">Imagens que não constam no catálogo</h1>';
            foreach ($imagensNoCatalogo['naoConsta'] as $k => $v) {
                $html .= ($k + 1) . ') <a href="' . $_SERVER['CROSIERAPPRADX_URL'] . '/est/produto/form/' . $v . '">' . $_SERVER['CROSIERAPPRADX_URL'] . '/est/produto/form/' . $v . '</a><br>';
                $t++;
            }
            $html .= '<hr>';

            $html .= '<h1 id="erros">Erros</h1>';
            foreach ($imagensNoCatalogo['erros'] as $k => $v) {
                $html .= ($k + 1) . ') <a href="' . $_SERVER['CROSIERAPPRADX_URL'] . '/est/produto/form/' . $v . '">' . $_SERVER['CROSIERAPPRADX_URL'] . '/est/produto/form/' . $v . '</a><br>';
                $t++;
            }
            $html .= '<hr>';
            $html .= $t . ' processadas';

        } catch (\Exception $e) {
            $this->getLogger()->error('Erro ao downloadCatalogo');
            $this->getLogger()->error($e->getMessage());
        }


        return new Response($html);
    }


}
