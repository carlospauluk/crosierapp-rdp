<?php

namespace App\Command\Relatorios;

use App\Business\Estoque\ProdutoBusiness;
use App\Business\Relatorios\RelClientes01Business;
use App\Business\Relatorios\RelCompFor01Business;
use App\Business\Relatorios\RelCompras01Business;
use App\Business\Relatorios\RelCtsPagRec01Business;
use App\Business\Relatorios\RelEstoque01Business;
use App\Business\Relatorios\RelQtdesEstoque01Business;
use App\Business\Relatorios\RelVendas01Business;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 * @author Carlos Eduardo Pauluk
 */
class ProcessarRelatoriosCommand extends Command
{

    private RelCtsPagRec01Business $relCtsPagRec01Business;

    private RelVendas01Business $relVendas01Business;

    private RelCompFor01Business $relCompFor01Business;

    private RelEstoque01Business $relEstoque01Business;

    private ProdutoBusiness $produtoBusiness;

    private RelCompras01Business $relCompras01Business;

    private RelClientes01Business $relCliente01Business;


    protected function configure()
    {
        $this->setName('crosierapprdp:processarRelatorios');
        $this->addArgument('tipoRelatorio', InputArgument::REQUIRED,
            'Tipo de relatório [\'RELCTSPAGREC01\', \'RELVENDAS01\', \'RELCOMPFOR01\', \'RELESTOQUE01\', \'RELCOMPRAS01\', \'RELCLIENTES01\']');
    }

    /**
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tipoRelatorio = $input->getArgument('tipoRelatorio');
        switch ($tipoRelatorio) {
            case 'RELCTSPAGREC01':
                $this->relCtsPagRec01Business->processarArquivosNaFila();
                break;
            case 'RELVENDAS01':
                $this->relVendas01Business->processarArquivosNaFila();
                break;
            case 'RELCOMPFOR01':
                $this->relCompFor01Business->processarArquivosNaFila();
                break;
            case 'RELESTOQUE01':
                $this->relEstoque01Business->processarArquivosNaFila();
                break;
            case 'RELCOMPRAS01':
                $this->relCompras01Business->processarArquivosNaFila();
                break;
            case 'RELCLIENTES01':
                $this->relCliente01Business->processarArquivosNaFila();
                break;
            default:
                throw new \RuntimeException('tipoRelatorio desconhecido: ' . $tipoRelatorio);
        }
        return 1;
    }


    /**
     * @required
     * @param RelCtsPagRec01Business $relCtsPagRec01Business
     */
    public function setRelCtsPagRec01Business(RelCtsPagRec01Business $relCtsPagRec01Business): void
    {
        $this->relCtsPagRec01Business = $relCtsPagRec01Business;
    }

    /**
     * @required
     * @param RelVendas01Business $relVendas01Business
     */
    public function setRelVendas01Business(RelVendas01Business $relVendas01Business): void
    {
        $this->relVendas01Business = $relVendas01Business;
    }

    /**
     * @required
     * @param RelCompFor01Business $relCompFor01Business
     */
    public function setRelCompFor01Business(RelCompFor01Business $relCompFor01Business): void
    {
        $this->relCompFor01Business = $relCompFor01Business;
    }

    /**
     * @required
     * @param RelEstoque01Business $relEstoque01Business
     */
    public function setRelEstoque01Business(RelEstoque01Business $relEstoque01Business): void
    {
        $this->relEstoque01Business = $relEstoque01Business;
    }

    /**
     * @required
     * @param ProdutoBusiness $produtoBusiness
     */
    public function setProdutoBusiness(ProdutoBusiness $produtoBusiness): void
    {
        $this->produtoBusiness = $produtoBusiness;
    }

    /**
     * @required
     * @param RelCompras01Business $relCompras01Business
     */
    public function setRelCompras01Business(RelCompras01Business $relCompras01Business): void
    {
        $this->relCompras01Business = $relCompras01Business;
    }

    /**
     * @required
     * @param RelClientes01Business $relCliente01Business
     */
    public function setRelCliente01Business(RelClientes01Business $relCliente01Business): void
    {
        $this->relCliente01Business = $relCliente01Business;
    }

}