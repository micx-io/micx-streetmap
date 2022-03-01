<?php


namespace App\Cli;


use Phore\CliTools\Cli\CliContext;
use Phore\CliTools\Helper\GetOptResult;
use Phore\CliTools\PhoreAbstractMainCmd;

class MainCmd extends PhoreAbstractMainCmd
{

    public function invoke(CliContext $context)
    {
        $opts = $context->getOpts("i:");

        $context->dispatchMap([
            "import" => new ImportCmd(),
            "search" => new SearchCmd()
        ], $opts);
    }
}
