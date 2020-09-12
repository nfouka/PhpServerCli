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
use Closure;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\Table;

/**
 * Runs a local web server in a background process.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class Server extends Command
{
 
    protected static $defaultName = 'server:start';
    public $addressport ; 
    public $io  ; 
    public $write ; 
    public $logger  ; 
    public $logo ; 
    
    public function __construct(string $addressport = null  )
    {
        $this->addressport = $addressport; 
        $this->logo ="
   ___  __        ________   ____                    
  / _ \/ /  ___  / ___/ (_) / __/__ _____  _____ ____
 / ___/ _ \/ _ \/ /__/ / / _\ \/ -_) __/ |/ / -_) __/
/_/  /_//_/ .__/\___/_/_/ /___/\__/_/  |___/\__/_/   
         /_/                                         
";
        parent::__construct();
    }
    
    
    public function getHelp()
    {
        return parent::getHelp()."CliServer " ; 
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        
        $this
        ->setDescription('Run server with command line')
        
        
        ->addOption(
            'path',
            null,
            InputOption::VALUE_OPTIONAL,
            'path of php installer default make /usr/bin/php',
            '/usr/bin/php'
            )  
        
        ->addOption(
            'addressport',
            null,
            InputOption::VALUE_OPTIONAL,
            'hostport --default = 127.0.0.1:8088',
            '127.0.0.1:8088'
            )
            
            ->addOption(
                'logger',
                null,
                InputOption::VALUE_OPTIONAL ,
                'path of logger file --default=server.log ',
                'server.log'
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
 
        $outputStyle = new OutputFormatterStyle('black', 'green' );
        $output->getFormatter()->setStyle('fire', $outputStyle);
        $this->logger =  $input->getOption('logger')   ;  
        $this->io = $output ; 
        $this->write = $output ; 
        $this->addressport  = $input->getOption('addressport')   ; 
        $io = new SymfonyStyle($input, $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output);
       
        $process = new Process([$input->getOption('path') ,"-S", $input->getOption('addressport') ,"--docroot=".$input->getOption('rootdir') ]);
        $process->setTimeout(3600);   
        
        
        
        $connection = @fsockopen( explode(":" ,$input->getOption('addressport'))[0] , explode(":" ,$input->getOption('addressport'))[1] );
        
        if (!is_resource($connection))
        {
            $build = time() ; 
            $md5 = "(md5)b5757eb0ae31b11dc0545b46e77abc46" ;  
            $io->success("$this->logo\nPHP Server Builder PhpCli\nAuthor : nadir.fouka@gmail.com \nVersion 1.0\nSource : https://github.com/nfouka/PhpServerCli\nLast Build $build \nSHA-1 signature: A19FEF24C9B1BCE670B65DDC347286C4A3DCA6D5\nLicense MIT");
            
            
            $io->warning("Server address : http://".$input->getOption('addressport') ) ; 
            
            
            $process->run( Closure::fromCallable([$this, 'sync']) );
            return Command::SUCCESS  ; 
        }
        else
        {
            $io->error("Adresse or port in used , change port or kill process pid of port :".$input->getOption('addressport'))[1] ; 
            return Command::FAILURE  ; 
        }
        
        
    }
    
    public function sync($type, $buffer ) {
        // echo "\033[32m ====== ServerWeb Cli  $this->addressport ======>  \033[0m $buffer";

        $file = $input->getOption('logger') ; 
        file_put_contents($file, $buffer , FILE_APPEND | LOCK_EX);
        
            if ( preg_match("/404/i", $buffer ) == 1 ) {
                $this->write->write("<fg=red>".$this->addressport.":".$buffer."</>");
            }else{
                $this->write->write("<info>".$this->addressport.":".$buffer."</info>");
            }

    }
}