<?php

namespace KTU\ShopBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ClearPurchasesCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('cron:clearPurchases')
            ->setDescription('Clears purchases that are more then 20min in progress.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $purchasesRep = $em->getRepository('KTUShopBundle:Purchases');

        // Find purchases that are in progress
        $purchInProg = $purchasesRep->findPurchasesByStatus(3);

        $compDate =date('Y-m-d H:i:s', strtotime('-20 minutes'));

        foreach($purchInProg as $p){
            $date = $p->getDate();

            if($date > $compDate)
               $em->remove($p);
        }

        $em->flush();

    }


}