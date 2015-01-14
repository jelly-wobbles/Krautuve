<?php


namespace KTU\ShopBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Process\Process;
class ClearCacheCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('cc')
            ->setDescription('Clear cache. Options: --n no debug mode, --p prod environment')
            ->addOption('n', null, InputOption::VALUE_NONE, 'If set, cache will be cleared with no debug mode option.')
            ->addOption('p', null, InputOption::VALUE_NONE, 'If set, cache will be cleared with prod environment option.')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandTxt = "php app/console cache:clear";

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