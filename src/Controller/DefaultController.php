<?php

namespace App\Controller;


use App\Entity\Relatorios\RelCtsPagRec01;
use App\Entity\Vendas\Venda;
use CrosierSource\CrosierLibBaseBundle\Business\Config\StoredViewInfoBusiness;
use CrosierSource\CrosierLibBaseBundle\Controller\BaseController;
use CrosierSource\CrosierLibBaseBundle\Entity\Config\AppConfig;
use CrosierSource\CrosierLibBaseBundle\Repository\Config\AppConfigRepository;
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

    protected StoredViewInfoBusiness $storedViewInfoBusiness;

    /**
     * @required
     * @param StoredViewInfoBusiness $storedViewInfoBusiness
     */
    public function setStoredViewInfoBusiness(StoredViewInfoBusiness $storedViewInfoBusiness): void
    {
        $this->storedViewInfoBusiness = $storedViewInfoBusiness;
    }


    /**
     *
     * @Route("/", name="index")
     * @param SessionInterface $session
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
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

        $lojas = $this->getDoctrine()->getRepository(Venda::class)->getLojas();
        array_unshift($lojas, ['id' => '', 'text' => 'TODAS']);
        $params['lojas'] = json_encode($lojas);

        $grupos = $this->getDoctrine()->getRepository(Venda::class)->getGrupos();
        array_unshift($grupos, ['id' => '', 'text' => 'TODOS']);
        $params['grupos'] = json_encode($grupos);

        $params['filter']['chartVendasTotalPorFornecedor']['dts'] = $session->get('dashboard.filter.chartVendasTotalPorFornecedor.dts') ?? ($primeiroDia . ' - ' . $ultimoDia);
        $params['filter']['chartVendasTotalPorFornecedor']['lojas'] = $session->get('dashboard.filter.chartVendasTotalPorFornecedor.lojas') ?? '';
        $params['filter']['chartVendasTotalPorFornecedor']['grupos'] = $session->get('dashboard.filter.chartVendasTotalPorFornecedor.grupos') ?? '';

        $params['filter']['chartVendasTotalPorVendedor']['dts'] = $session->get('dashboard.filter.chartVendasTotalPorVendedor.dts') ?? ($primeiroDia . ' - ' . $ultimoDia);
        $params['filter']['chartVendasTotalPorVendedor']['lojas'] = $session->get('dashboard.filter.chartVendasTotalPorVendedor.lojas') ?? '';
        $params['filter']['chartVendasTotalPorVendedor']['grupos'] = $session->get('dashboard.filter.chartVendasTotalPorVendedor.grupos') ?? '';

        $params['filter']['contasPagRec']['dts'] = $session->get('dashboard.filter.contasPagRec.dts') ?? ($hoje . ' - ' . $mais60dias);
        $params['filter']['contasPagRec']['filial'] = urlencode($session->get('dashboard.filter.contasPagRec.filial')) ?? $filiais[0]['id'];
        $params['filter']['contasPagRec']['localizador'] = urlencode($session->get('dashboard.filter.contasPagRec.localizador')) ?? $localizadores[0]['id'];

        $params['filter']['relCompFor01']['dts'] = $session->get('dashboard.filter.relCompFor01.dts') ?? ($primeiroDia_mesPassado . ' - ' . $ultimoDia_mesPassado);


//        /** @var ProdutoRepository $repoEstoque01 */
//        $repoEstoque01 =  $this->getDoctrine()->getRepository(Produto::class);
        $params['reposicaoEstoqueTotais']['filiais'] = [['desc_filial' => 'ACESSORIOS'], ['desc_filial' => 'MATRIZ']];
//        $svi = $this->storedViewInfoBusiness->retrieve('est_pedidoCompra_listReposicao');
        $params['reposicaoEstoqueTotais']['filter'] = $svi['formPesquisar']['filter'] ?? null;


        /** @var AppConfigRepository $repoAppConfig */
        $repoAppConfig = $this->getDoctrine()->getRepository(AppConfig::class);
        /** @var AppConfig $appConfig */
        $appConfig = $repoAppConfig->findOneByFiltersSimpl([['chave', 'EQ', 'relCompFor01.dthrAtualizacao'], ['appUUID', 'EQ', $_SERVER['CROSIERAPP_UUID']]]);
        $params['relCompFor01_dthrAtualizacao'] = $appConfig ? DateTimeUtils::parseDateStr($appConfig->getValor()) : '';
        $appConfig = $repoAppConfig->findOneByFiltersSimpl([['chave', 'EQ', 'relCompras01.dthrAtualizacao'], ['appUUID', 'EQ', $_SERVER['CROSIERAPP_UUID']]]);
        $params['relCompras01_dthrAtualizacao'] = $appConfig ? DateTimeUtils::parseDateStr($appConfig->getValor()) : '';
        $appConfig = $repoAppConfig->findOneByFiltersSimpl([['chave', 'EQ', 'relCtsPagRec01.dthrAtualizacao'], ['appUUID', 'EQ', $_SERVER['CROSIERAPP_UUID']]]);
        $params['relCtsPagRec01_dthrAtualizacao'] = $appConfig ? DateTimeUtils::parseDateStr($appConfig->getValor()) : '';
        $appConfig = $repoAppConfig->findOneByFiltersSimpl([['chave', 'EQ', 'relEstoque01.dthrAtualizacao'], ['appUUID', 'EQ', $_SERVER['CROSIERAPP_UUID']]]);
        $params['relEstoque01_dthrAtualizacao'] = $appConfig ? DateTimeUtils::parseDateStr($appConfig->getValor()) : '';
        $appConfig = $repoAppConfig->findOneByFiltersSimpl([['chave', 'EQ', 'relVendas01.dthrAtualizacao'], ['appUUID', 'EQ', $_SERVER['CROSIERAPP_UUID']]]);
        $params['relVendas01_dthrAtualizacao'] = $appConfig ? DateTimeUtils::parseDateStr($appConfig->getValor()) : '';

        return $this->doRender('dashboard.html.twig', $params);
    }


}