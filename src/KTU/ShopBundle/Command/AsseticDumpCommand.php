<?php


namespace KTU\ShopBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Process\Process;
class AsseticDumpCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ad')
            ->setDescription('Dumps assets. Options: --n no debug mode, --p prod environment')
            ->addOption('n', null, InputOption::VALUE_NONE, 'If set, assets will be dumped with no debug mode option.')
            ->addOption('p', null, InputOption::VALUE_NONE, 'If set, assets will be dumped with prod environment option.')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandTxt = "php app/console assetic:dump";

        if ($input->getOption('n')) {
            $commandTxt = $commandTxt . ' --no-debug';
        }

        if ($input->getOption('p')) {
            $commandTxt = $commandTxt . ' --env=prod';
        }

        $process = new Process( $commandTxt );
        $process->run();
        $outputTxt = $process->getOutput();
        $output->writeln( $outputTxt );
    }
}