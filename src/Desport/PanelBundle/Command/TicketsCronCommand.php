<?php
namespace Desport\PanelBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Desport\PanelBundle\Entity\EventType\TicketReminder;

class TicketsCronCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('cron:tickets')
            ->setDescription('Update tickets status')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        
        $pending = $em->createQuery(
            "SELECT t
            FROM DesportPanelBundle:Ticket t
            WHERE t.state LIKE 'pending reminder'"
        )->getResult();
        
        $countTicketReminders = 0;
        
        if($pending)
        {
            foreach($pending as $ticket)
            {
                if($ticket->getStateDate() > new \DateTime('23 hour ago'))
                {
                    $event = new TicketReminder();
                    
                    $event->setUser($ticket->getResponsible());
                    $event->setClient($ticket->getClient());
                    $event->setTicket($ticket);
                    
                    $ticket->setStateDate(new \DateTime('now'));
                    
                    $em->persist($event); 
                    $countTicketReminders ++;
                }
            }
        }
        
        $em->flush();
        
        $output->writeln('DONE');
        $output->writeln('TicketReminders: '.$countTicketReminders);
    }
}