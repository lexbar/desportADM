<?php
namespace Desport\PanelBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Desport\PanelBundle\Entity\InstallQueue;

class InstallQueueCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('cron:installQueue')
            ->setDescription('Run next stage from install queue')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        
        $queue = $em->createQuery(
            "SELECT q
            FROM DesportPanelBundle:InstallQueue q
            WHERE q.done = FALSE AND q.trials <= 3"
        )->getResult();
        
        if($queue)
        {
            //Get only one a time
            $myQueue = $queue[0];
            
            //Set +1 trials
            $myQueue->increaseTrials();
            $em->persist($myQueue);
            $em->flush();
            
            if($this->executeStage($myQueue->getStage(), $myQueue->getSite())) 
            {
                //Mark this stage as DONE
                $myQueue->setDone(true);
                $em->persist($myQueue);
                $output->writeln('Stage ' . $myQueue->getStage() . ' of site ' . $myQueue->getSite()->getName() . ' completed');
                
                //Set next stage
                if($myQueue->getStage() < $myQueue->lastStage)
                {
                    $queue = new InstallQueue();
                    $queue->setSite($myQueue->getSite());
                    $queue->setNextStage($myQueue);
                    
                    $output->writeln('Created new stage ' . $queue->getStage() . ' for site ' . $queue->getSite()->getName());
                    $em->persist($queue);
                }
                
                $em->flush();
            }
            else
            {
                //Mark this stage as DONE
                $output->writeln('Stage ' . $myQueue->getStage() . ' of site ' . $myQueue->getSite()->getName() . ' failed');
                
                //Set next stage
                /*
                $queue = new InstallQueue();
                $queue->setSite($myQueue->getSite());
                $queue->setNextStage($myQueue);
                
                $output->writeln('Created new stage ' . $queue->getStage() . ' for site ' . $queue->getSite()->getName());
                
                $em->persist($queue);
                $em->flush();*/
            }
        }
        
        $output->writeln('DONE');
    }
    
    public function executeStage($stage_id, $site)
    {
        $install = $this->getContainer()->get("desport.install");
        
        switch($stage_id)
        {
            case 0:
                if($install->createSubdomain($site->getName(), $site->getBandwidth(), $site->getQuota()))
                {
                    // Success
                    //$response->setData(array('next_step'=>'1'));
                    return true;
                }
                else
                {
                    // Error, client or site not found
                    //$response->setData(array('error'=>'domain_not_created'));
                    return false;
                }
            break;
            
            case 1:
                sleep(1); //if too fast it may not work
                if($install->createDatabase($site->getName()))
                {
                    // Success
                    //$response->setData(array('next_step'=>'2'));
                    return true;
                }
                else
                {
                    // Error, client or site not found
                    //$response->setData(array('error'=>'database_not_created'));
                    return false;
                }
            break;
            
            case 2:
                sleep(6); //if too fast it may not work
                if($install->cloneRepository($site->getName()))
                {
                    if($install->fillParameters($site))
                    {
                        // Success
                        //$response->setData(array('next_step'=>'3'));
                        return true;
                    }
                    else
                    {
                        // Error, client or site not found
                        //$response->setData(array('error'=>'parameters_not_filled'));
                        return false;
                    }
                }
                else
                {
                    // Error, client or site not found
                    //$response->setData(array('error'=>'repository_not_cloned'));
                    return false;
                }
                
            break;
            
            case 3:
                sleep(10); //if too fast it may not work
                if($install->loadDatabase($site->getName(), $site->getClient()->getEmail(), $site->getClient()->getContactName()))
                {
                    // Success
                    //$response->setData(array('next_step'=>'end'));
                    return true;
                }
                else
                {
                    // Error, client or site not found
                    //$response->setData(array('error'=>'database_not_loaded'));
                    return false;
                }
            break;
            default: 
                //$response->setData(array('error'=>'bad_request'));
                return false;
        }
        
        return false;
    }
}