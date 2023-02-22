<?php
declare(strict_types=1);
/**
 * User: Fabien Sanchez
 * Date: 08/02/2019
 * Time: 10:32
 */

require_once __DIR__ . "/../vendor/autoload.php";

$parameters = $argv;
$scriptName = array_shift($parameters);
$provider = array_shift($parameters);

if ($provider === null) {
    throw new \ErrorException("Le provider n'a pas été donné.");
}

switch (strtolower($provider)) {
    case 'oci':
        $tnsname = array_shift($parameters);
        $user = array_shift($parameters);
        $password = array_shift($parameters);
        if (null === $tnsname) {
            throw new \ErrorException("Le tnsname n'a pas été donné.");
        }
        if (null === $user) {
            throw new \ErrorException("Le user n'a pas été donné.");
        }
        if (null === $password) {
            throw new \ErrorException("Le password n'a pas été donné.");
        }
        $parser = new \App\Parser\OciParser((string)$tnsname, (string)$user, (string)$password);
        break;
    case 'pgsql':
        $host = array_shift($parameters);
        $dbname = array_shift($parameters);
        $user = array_shift($parameters);
        $password = array_shift($parameters);
        $port = array_shift($parameters) ?? 5432;
        if (null === $host) {
            throw new \ErrorException("Le host n'a pas été donné.");
        }
        if (null === $dbname) {
            throw new \ErrorException("Le dbname n'a pas été donné.");
        }
        if (null === $user) {
            throw new \ErrorException("Le user n'a pas été donné.");
        }
        if (null === $password) {
            throw new \ErrorException("Le password n'a pas été donné.");
        }
        $parser = new \App\Parser\PgSqlParser((string)$host, (string)$dbname, (string)$user, (string)$password, (int)$port);
        break;
    case'mysql':
    case'sqlite':
    default:
        throw new \ErrorException("Le provider n'est pas valide.");
}

$parser->parse();

if (!is_dir('./dist') && !mkdir('./dist') && !is_dir('./dist')) {
    throw new \RuntimeException(sprintf('Directory "%s" was not created', './dist'));
}

/** @var \App\Parser\Parsable $parser */

$content = <<<UML
@startuml

skinparam roundcorner 5
skinparam monochrome true
skinparam defaultfontname roboto
skinparam defaultfontsize 12
UML;
$content .= $parser->getTables()->render();
$content .= $parser->getLiaisons()->render();
$content .= "@enduml" . PHP_EOL;
file_put_contents('./dist/mcd.txt', $content);