<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of compile
 *
 * @author nadir
 */


require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Application as BaseApplication;
use Cli\Server ; 

$application = new BaseApplication() ; 
$application->add( new Server() );
$application->run();

