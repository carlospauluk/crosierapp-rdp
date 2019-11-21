<?php

namespace App\Controller\Relatorios;


use App\Entity\Relatorios\RelCliente01;
use App\EntityHandler\Relatorios\RelCliente01EntityHandler;
use App\Form\Relatorios\RelCliente01Type;
use App\Repository\Relatorios\RelCliente01Repository;
use CrosierSource\CrosierLibBaseBundle\Controller\FormListController;
use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\AppConfigRepository;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\RepositoryUtils\FilterData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @author Carlos Eduardo Pauluk
 */
class RelCliente01Controller extends FormListController
{

    /** @var SessionInterface */
    private $session;

    /**
     * @required
     * @param RelCliente01EntityHandler $entityHandler
     */
    public function setEntityHandler(RelCliente01EntityHandler $entityHandler): void
    {
        $this->entityHandler = $entityHandler;
    }

    /**
     * @required
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session): void
    {
        $this->session = $session;
    }


    public function getFilterDatas(array $params): array
    {
        return [
            new FilterData(['codigo'], 'EQ', 'codigo', $params),
            new FilterData(['nome'], 'LIKE', 'nome', $params)
        ];
    }

    /**
     *
     * @Route("/relCliente01/list/", name="relCliente01_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     *
     * @IsGranted({"ROLE_RELVENDAS"}, statusCode=403)
     */
    public function list(Request $request): Response
    {

        /** @var AppConfigRepository $repoAppConfig */
        $repoAppConfig = $this->getDoctrine()->getRepository(AppConfig::class);
        /** @var AppConfig $appConfig */
        $appConfig = $repoAppConfig->findOneByFiltersSimpl([['chave', 'EQ', 'relCliente01.dthrAtualizacao'], ['appUUID', 'EQ', $_SERVER['CROSIERAPP_UUID']]]);
        $dthrAtualizacao = $appConfig ? $appConfig->getValor() : null;
        $dthrAtualizacao = $dthrAtualizacao ? DateTimeUtils::parseDateStr($dthrAtualizacao) : '';

        $params = [
            'formRoute' => 'relCliente01_form',
            'listView' => 'Relatorios/relCliente01_list.html.twig',
            'listRoute' => 'relCliente01_list',
            'listRouteAjax' => 'relCliente01_datatablesJsList',
            'listPageTitle' => 'Clientes',
            'page_subTitle' => 'Atualizado em: ' . $dthrAtualizacao->format('d/m/Y H:i:s'),
            'listId' => 'relCliente01_list'
        ];
        return $this->doList($request, $params);
    }

    /**
     *
     * @Route("/relCliente01/datatablesJsList/", name="relCliente01_datatablesJsList")
     * @param Request $request
     * @return Response
     * @throws \CrosierSource\CrosierLibBaseBundle\Exception\ViewException
     *
     * @IsGranted({"ROLE_RELVENDAS"}, statusCode=403)
     */
    public function datatablesJsList(Request $request): Response
    {
        return $this->doDatatablesJsList($request);
    }

    /**
     *
     * @Route("/relCliente01/form/{id}", name="relCliente01_form", defaults={"id"=null}, requirements={"id"="\d+"})
     * @param Request $request
     * @param RelCliente01|null $cliente
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     *
     * @IsGranted({"ROLE_RELVENDAS"}, statusCode=403)
     */
    public function form(Request $request, RelCliente01 $cliente = null)
    {
        if (!$cliente) {
            $cliente = new RelCliente01();
            $cliente->setClienteBloqueado('N');
        }
        $params = [
            'typeClass' => RelCliente01Type::class,
            'formView' => 'Relatorios/relCliente01_form.html.twig',
            'formRoute' => 'relCliente01_form',
            'formPageTitle' => 'Cliente'
        ];
        return $this->doForm($request, $cliente, $params);
    }


}
