<?php
namespace AppBundle\Scheduler;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class SchedulerTask
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var \DateTime
     */
    protected $currentExecution;

    /**
     * @var \DateTime
     */
    protected $lastExecution;


    /**
     * SchedulerTask constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, InputInterface $input, OutputInterface $output, \DateTime $current, \DateTime $last)
    {
        $this->container = $container;
        $this->input = $input;
        $this->output = $output;
        $this->lastExecution = $last;
        $this->currentExecution = $current;
    }

    /**
     * Get the task description
     * @return string
     */
    abstract public function getDescription();

    /**
     * Execute the scheduler task
     * @return mixed
     */
    abstract public function run();



}