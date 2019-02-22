<?php
/**
 * Created by PhpStorm.
 * User: petr
 * Date: 2/22/2019
 * Time: 11:58 AM
 */

require __DIR__ . '/../vendor/autoload.php';
$configurator = new Nette\Configurator;
$configurator->setDebugMode(true); // enable for your remote IP
$configurator->enableTracy(__DIR__ . '/../log');
$configurator->setTimeZone('Europe/Prague');
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()
    ->addDirectory(__DIR__)
    ->register();
$configurator->addConfig(__DIR__ . '/config/config.neon');
$container = $configurator->createContainer();
return $container;