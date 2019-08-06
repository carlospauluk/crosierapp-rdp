<?php

namespace App\Controller;


use App\Entity\Relatorios\RelCtsPagRec01;
use App\Entity\Relatorios\RelVendas01;
use CrosierSource\CrosierLibBaseBundle\Controller\BaseController;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package App\Controller
 * @author Carlos Eduardo Pauluk
 */
class DefaultController extends BaseController
{

    /**
     *
     * @Route("/", name="index")
     */
    public function index(SessionInterface $session)
    {
        $hoje = (new \DateTime())->format('d/m/Y');
        $mais60dias = (new \DateTime())->add(new \DateInterval('P60D'))->format('d/m/Y');
        $primeiroDia = DateTimeUtils::getPrimeiroDiaMes()->format('d/m/Y');
        $ultimoDia = DateTimeUtils::getUltimoDiaMes()->format('d/m/Y');

        $primeiroDia_mesPassado = '01/' . (new \DateTime())->sub(new \DateInterval('P1M'))->format('m/Y');
        $ultimoDia_mesPassado = (new \DateTime())->sub(new \DateInterval('P1M'))->format('t/m/Y');


        $filiais = $this->getDoctrine()->getRepository(RelCtsPagRec01::class)->getFiliais();
        array_unshift($filiais, ['id' => '', 'text' => 'TODAS']);
        $params['filiais'] = json_encode($filiais);

        $localizadores = $this->getDoctrine()->getRepository(RelCtsPagRec01::class)->getLocalizadores();
        array_unshift($localizadores, ['id' => '', 'text' => 'TODOS']);
        $params['localizadores'] = json_encode($localizadores);

        $lojas = $this->getDoctrine()->getRepository(RelVendas01::class)->getLojas();
        array_unshift($lojas, ['id' => '', 'text' => 'TODAS']);
        $params['lojas'] = json_encode($lojas);

        $grupos = $this->getDoctrine()->getRepository(RelVendas01::class)->getGrupos();
        array_unshift($grupos, ['id' => '', 'text' => 'TODOS']);
        $params['grupos'] = json_encode($grupos);

        $params['filter']['vendas']['dts'] = $session->get('dashboard.filter.vendas.dts') ?? ($primeiroDia . ' - ' . $ultimoDia);
        $params['filter']['vendas']['loja'] = $session->get('dashboard.filter.vendas.loja') ?? '';
        $params['filter']['vendas']['grupo'] = $session->get('dashboard.filter.vendas.grupo') ?? '';

        $params['filter']['contasPagRec']['dts'] = $session->get('dashboard.filter.contasPagRec.dts') ?? ($hoje . ' - ' . $mais60dias);
        $params['filter']['contasPagRec']['filial'] = urlencode($session->get('dashboard.filter.contasPagRec.filial')) ?? $filiais[0]['id'];
        $params['filter']['contasPagRec']['localizador'] = urlencode($session->get('dashboard.filter.contasPagRec.localizador')) ?? $localizadores[0]['id'];

        $params['filter']['relCompFor01']['dts'] = $session->get('dashboard.filter.relCompFor01.dts') ?? ($primeiroDia_mesPassado . ' - ' . $ultimoDia_mesPassado);


        $response = $this->doRender('dashboard.html.twig', $params);
        $response->headers->addCacheControlDirective('no-cache', true);
        $response->headers->addCacheControlDirective('Cache-control', 'private');
        $response->expire();

        return $response;
    }


}