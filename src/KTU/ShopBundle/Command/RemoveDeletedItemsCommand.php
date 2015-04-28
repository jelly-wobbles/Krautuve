<?php

namespace KTU\ShopBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveDeletedItemsCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('cron:removeDeletedItems')
            ->setDescription('Removes deleted items that are no longer associated with any purchases.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $itemDet = $em->getRepository('KTUShopBundle:ItemsDetails');

    }


}