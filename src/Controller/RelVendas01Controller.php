<?php

namespace App\Controller;


use App\Entity\RelVendas01;
use App\Entity\Utils\RelatorioPush;
use App\EntityHandler\RelVendas01EntityHandler;
use App\EntityHandler\Utils\RelatorioPushEntityHandler;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Exception\ViewException;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * CRUD Controller para Push.
 *
 * @package App\Controller\Utils
 * @author Carlos Eduardo Pauluk
 */
class RelVendas01Controller extends FormListController
{

    protected $crudParams =
        [
            'typeClass' => null,

            'formView' => null,
//            'formRoute' => null,
            'formPageTitle' => null,
            'form_PROGRAM_UUID' => null,

            'listView' => 'relVendas01_list.html.twig',
            'listRoute' => 'relVendas01_list',
            'listRouteAjax' => 'relVendas01_datatablesJsList',
            'listPageTitle' => 'Vendas',
            'listId' => 'relVendas01List',
            'list_PROGRAM_UUID' => null,
            'listJS' => '',

            'role_access' => 'ROLE_RELVENDAS01',
            'role_delete' => 'ROLE_ADMIN',

        ];

    /**
     * @required
     * @param RelVendas01EntityHandler $entityHandler
     */
    public function setEntityHandler(RelVendas01EntityHandler $entityHandler): void
    {
        $this->entityHandler = $entityHandler;
    }

    public function getFilterDatas(array $params): array
    {
        return [
            new FilterData(['descProduto'], 'LIKE', 'descProduto', $params),
            new FilterData(['nomeFornecedor'], 'LIKE', 'nomeFornecedor', $params)
        ];
    }

    /**
     *
     * @Route("/relVendas01/list/", name="relVendas01_list")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function list(Request $request): Response
    {
        return $this->doList($request);
    }

    /**
     *
     * @Route("/relVendas01/datatablesJsList/", name="relVendas01_datatablesJsList")
     * @param Request $request
     * @return Response
     * @throws ViewException
     */
    public function datatablesJsList(Request $request): Response
    {
        return $this->doDatatablesJsList($request);
    }

    /**
     *
     * @Route("/relVendas01/import/", name="relVendas01_list")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function import(Request $request): Response
    {
        $this->entityHandler->getDoctrine()->getEntityManager()->beginTransaction();


        try {
            $linhas = file(getenv('kernel.project_dir') . '/sql/vendasfor.gra.txt');
            $this->entityHandler->getDoctrine()->getEntityManager()->createNativeQuery('TRUNCATE TABLE rdp_rel_vendas01')->execute();
            foreach ($linhas as $linha) {
                $campos = explode('|', $linha);
                if (count($campos) !== 10) {
                    throw new ViewException('Qtde de campos difere de 10 para a linha "$linha"');
                }

                $e = new RelVendas01();
                $e->setAno($campos[0]);
                $e->setMes($campos[1]);
                $e->setCodFornecedor($campos[2]);
                $e->setNomeFornecedor($campos[3]);
                $e->setCodProduto($campos[4]);
                $e->setDescProduto($campos[5]);
                $e->setNomeVendedor($campos[6]);
                $e->setRentabilidade($campos[7]);
                $e->setTotalPrecoCusto($campos[8]);
                $e->setTotalPrecoVenda($campos[9]);
                $this->entityHandler->save($e);
            }
            $this->entityHandler->getDoctrine()->getEntityManager()->commit();
        } catch (ViewException $e) {
            $this->addFlash('error', 'Erro ao importar');
            $this->addFlash('error', $e->getMessage());
        }

        return new Response('Fim');
    }

}
