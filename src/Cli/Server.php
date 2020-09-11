<?php

namespace Cli;

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


use Symfony\Bundle\WebServerBundle\WebServer;
use Symfony\Bundle\WebServerBundle\WebServerConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

/**
 * Runs a local web server in a background process.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class Server extends Command
{


    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        
        $this
        ->setName('server:start')
        ->setDescription('Example command requiring an argument to be passed')
        
        
        ->addOption(
            'path',
            null,
            InputOption::VALUE_REQUIRED,
            'path of php ? default /usr/bin/php',
            '/usr/bin/php'
            )  
        
        ->addOption(
            'addressport',
            null,
            InputOption::VALUE_REQUIRED,
            'hostport',
            '127.0.0.1:8088'
            )
            
          ->addOption(
                'rootdir',
                null,
                InputOption::VALUE_REQUIRED,
                'root dir project ',
                '.'
                )
                ; 
            
         ; 
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
 
        $io = new SymfonyStyle($input, $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output);
       
        $process = new Process([$input->getOption('path') ,"-S", $input->getOption('addressport') ,"--docroot=".$input->getOption('rootdir') ]);
        $process->setTimeout(3600);   
        $io->success("Server run on : ".$input->getOption('addressport') ) ; 
        $process->run( function ($type, $buffer ) {

            if (Process::ERR === $type) {
                
                echo "TYPE PROCESS: \033[01;31m".$type.".".$buffer."\033[0m";
            } else {
                echo 'OUT > '.$buffer;
            }
        } );
        
        return Command::SUCCESS  ; 
        

    }
}