<?php

namespace App\Command\Relatorios;

use App\Business\Relatorios\RelCompFor01Business;
use App\Business\Relatorios\RelCtsPagRec01Business;
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

    /** @var RelCtsPagRec01Business */
    private $relCtsPagRec01Business;

    /** @var RelVendas01Business */
    private $relVendas01Business;

    /** @var RelCompFor01Business */
    private $relCompFor01Business;

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

    protected function configure()
    {
        $this->setName('crosierapprdp:processarRelatorios');
        $this->addArgument('tipoRelatorio', InputArgument::REQUIRED, 'Tipo de relatório [\'RELCTSPAGREC01\', \'RELVENDAS01\', \'RELCOMPFOR01\']');
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
            default:
                throw new \RuntimeException('tipoRelatorio desconhecido');
        }
    }

}