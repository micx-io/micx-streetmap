<?php


namespace App\Cli;


use Phore\CliTools\Cli\CliContext;
use Phore\CliTools\Helper\GetOptResult;
use Phore\CliTools\PhoreAbstractCmd;
use Phore\Misc\UniDB\SQLiteDriver;

class SearchCmd extends PhoreAbstractCmd
{

    public function invoke(CliContext $context)
    {
        $opts = $context->getOpts();

        $unidb = new SQLiteDriver("/tmp/sqlite");
        $unidb->connect();

        print_r ($unidb->queryRaw("SELECT * FROM street WHERE street_name LIKE '{$opts->argv(0)}%' GROUP BY city, street_name LIMIT 5"));
    }
}
