<?php

namespace App\Controller\Estoque;


use App\Business\Estoque\PedidoCompraBusiness;
use CrosierSource\CrosierLibBaseBundle\Controller\BaseController;
use CrosierSource\CrosierLibBaseBundle\Utils\DateTimeUtils\DateTimeUtils;
use CrosierSource\CrosierLibBaseBundle\Utils\ViewUtils\Select2JsUtils;
use CrosierSource\CrosierLibRadxBundle\Entity\Estoque\Fornecedor;
use CrosierSource\CrosierLibRadxBundle\Repository\Estoque\FornecedorRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @author Carlos Eduardo Pauluk
 */
class PedidoCompraAuxController extends BaseController
{

    private PedidoCompraBusiness $pedidoCompraBusiness;

    /**
     * @required
     * @param PedidoCompraBusiness $pedidoCompraBusiness
     */
    public function setPedidoCompraBusiness(PedidoCompraBusiness $pedidoCompraBusiness): void
    {
        $this->pedidoCompraBusiness = $pedidoCompraBusiness;
    }


    /**
     *
     * @Route("/est/pedidoCompra/listReposicao/", name="est_pedidoCompra_listReposicao")
     * @param Request $request
     * @return Response
     * @throws \Exception
     *
     * @IsGranted("ROLE_RELVENDAS", statusCode=403)
     */
    public function listReposicao(Request $request): Response
    {
        $queryParams = $request->query->all();
        if (!array_key_exists('filter', $queryParams)) {
            // inicializa para evitar o erro
            $queryParams['filter'] = null;

            if ($queryParams['r'] ?? false) {
                $this->storedViewInfoBusiness->clear('est_pedidoCompra_listReposicao');
            } else {
                if ($svi = $this->storedViewInfoBusiness->retrieve('est_pedidoCompra_listReposicao')) {
                    $queryParams['filter'] = $svi['filter'] ?? null;
                    return $this->redirectToRoute('est_pedidoCompra_listReposicao', $queryParams);
                }
            }
        }

        $filiais = [
            ['id' => '', 'text' => 'TODAS'],
            ['id' => 'MATRIZ', 'text' => 'MATRIZ'],
            ['id' => 'ACESSORIOS', 'text' => 'ACESSORIOS'],
        ];
        $params['filiais'] = json_encode($filiais);

        if (!isset($queryParams['filter']['filial'])) {
            $queryParams['filter']['filial'] = 'MATRIZ';
            return $this->redirect($this->generateUrl('est_pedidoCompra_listReposicao', $queryParams));
        }

        /** @var FornecedorRepository $repoFornecedor */
        $repoFornecedor = $this->getDoctrine()->getRepository(Fornecedor::class);

        $fornecedores = $repoFornecedor->findAll(['nome' => 'ASC']);
        $fornecedoresSelect2js = Select2JsUtils::toSelect2DataFn($fornecedores, function ($fornecedor) {
            /** @var Fornecedor $fornecedor */
            return $fornecedor->nome . ' (' . str_pad($fornecedor->getId(), 7, '0', STR_PAD_LEFT) . ')';
        });
        array_unshift($fornecedoresSelect2js, ['id' => '', 'text' => 'TODOS']);
        $params['fornecedores'] = json_encode($fornecedoresSelect2js);


        $filial = $queryParams['filter']['filial'] ?? null;
        $fornecedorId = $queryParams['filter']['fornecedor'] ?? null;
        $fornecedor = null;
        if ($fornecedorId) {
            /** @var Fornecedor $fornecedor */
            $fornecedor = $repoFornecedor->find($fornecedorId);
        }

        $dtUltSaidaApartirDe = null;
        if ($queryParams['filter']['dtUltSaidaApartirDe'] ?? null) {
            $dtUltSaidaApartirDe = DateTimeUtils::parseDateStr($request->get('filter')['dtUltSaidaApartirDe']);
        } else {
            $dtUltSaidaApartirDe = (new \DateTime())->sub(new \DateInterval('P1Y')); // 1 ano atrás
        }
        $queryParams['filter']['dtUltSaidaApartirDe'] = $dtUltSaidaApartirDe->format('d/m/Y');

        $params['filter'] = $queryParams['filter'];
        $params['filter']['apenasARepor'] = filter_var($params['filter']['apenasARepor'] ?? true, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        $params['produtos'] = $this->pedidoCompraBusiness->findReposicoesEstoque($filial, $fornecedor, $dtUltSaidaApartirDe, $params['filter']['apenasARepor']);

        if (count($params['produtos']) >= 500) {
            $this->addFlash('warn', 'Atenção: exibindo apenas um máximo de 500 registros. Necessário filtrar!');
        }

        $this->storedViewInfoBusiness->set('est_pedidoCompra_listReposicao', $queryParams);

        return $this->doRender('Estoque/pedidoCompra_listReposicao.html.twig', $params);
    }


    /**
     *
     * @Route("/est/pedidoCompra/imprimirListReposicao/", name="est_pedidoCompra_imprimirListReposicao")
     *
     * @return void
     *
     * @throws \Exception
     * @IsGranted("ROLE_RELVENDAS", statusCode=403)
     */
    public function imprimirListaReposicao(): void
    {
        $svi = $this->storedViewInfoBusiness->retrieve('est_pedidoCompra_listReposicao');
        $queryParams['filter'] = $svi['filter'] ?? null;

        $filial = $queryParams['filter']['filial'] ?? null;
        $fornecedorId = $queryParams['filter']['fornecedor'] ?? null;
        /** @var FornecedorRepository $repoFornecedor */
        $repoFornecedor = $this->getDoctrine()->getRepository(Fornecedor::class);
        $fornecedor = null;
        if ($fornecedorId) {
            /** @var Fornecedor $fornecedor */
            $fornecedor = $repoFornecedor->find($fornecedorId);
        }

        $dtUltSaidaApartirDe = null;
        if ($queryParams['filter']['dtUltSaidaApartirDe'] ?? null) {
            $dtUltSaidaApartirDe = DateTimeUtils::parseDateStr($queryParams['filter']['dtUltSaidaApartirDe']);
        } else {
            $dtUltSaidaApartirDe = (new \DateTime())->sub(new \DateInterval('P1Y')); // 1 ano atrás
        }
        $queryParams['filter']['dtUltSaidaApartirDe'] = $dtUltSaidaApartirDe->format('d/m/Y');

        $queryParams['filter']['apenasARepor'] = filter_var($queryParams['filter']['apenasARepor'] ?? true, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        $dados = $this->pedidoCompraBusiness->findReposicoesEstoque($filial, $fornecedor, $dtUltSaidaApartirDe, $queryParams['filter']['apenasARepor']);

        $parameters['fornecedor'] = $fornecedor;
        $parameters['dados'] = $dados;
        $parameters['filial'] = $svi['filter']['filial'];
        $parameters['hoje'] = (new \DateTime())->format('d/m/Y H:i');

        gc_collect_cycles();
        gc_disable();

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('enable_remote', true);
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $pdfOptions->setIsRemoteEnabled(true);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('Estoque/pedidoCompra_listReposicao_PDF.html.twig', $parameters);
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);


        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream('aPagarReceber_rel.pdf', [
            'Attachment' => false
        ]);

        gc_collect_cycles();
        gc_enable();

    }


}
