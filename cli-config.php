<?php

require_once 'doctrine-config.php';

// console
return Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet(getMaMEntityManager());

