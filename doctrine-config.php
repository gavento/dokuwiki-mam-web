<?php

require_once "vendor/autoload.php";


use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

/**
 * Bootstrap doctrine, class autoloading, database connection and entity manager.
 * Only instantiated once per process, consequent calls return with cached result.
 */

function getMaMEntityManager() {
    static $entityManager;

    if ($entityManager === null) {

	$paths = array(__DIR__ . "/MaMWeb/Entity/");
	$isDevMode = true;

	$loader = new Doctrine\Common\ClassLoader('MaMWeb', __DIR__);
	$loader->register();

	// the connection configuration
	$dbParams = array(
	    'driver'   => 'pdo_pgsql',
	    'user'     => 'mam',
	    'dbname'   => 'mam',
	);

	$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
	$entityManager = EntityManager::create($dbParams, $config);
    }

    return $entityManager;
}

