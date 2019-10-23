<?php

namespace App\Command\Estoque;

use App\Business\Estoque\ProdutoBusiness;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 * @author Carlos Eduardo Pauluk
 */
class GerarExcelProdutosCommand extends Command
{

    /** @var ProdutoBusiness */
    private $produtoBusiness;

    /**
     * @required
     * @param ProdutoBusiness $produtoBusiness
     */
    public function setProdutoBusiness(ProdutoBusiness $produtoBusiness): void
    {
        $this->produtoBusiness = $produtoBusiness;
    }


    protected function configure()
    {
        $this->setName('crosierapprdp:gerarExcelProdutos');
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
        $params = $this->produtoBusiness->gerarExcel();
        $output->writeln('Arquivo: ' . $params['arquivo']);
        $output->writeln('Qtde Produtos: ' . $params['qtdeProdutos']);
    }

}