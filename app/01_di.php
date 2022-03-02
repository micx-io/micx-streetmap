<?php
namespace App;

use App\Config\Config;
use Brace\Command\CommandModule;
use Brace\Core\AppLoader;
use Brace\Core\BraceApp;
use Brace\Dbg\BraceDbg;
use Brace\Mod\Request\Zend\BraceRequestLaminasModule;
use Brace\Router\RouterModule;
use Phore\Di\Container\Producer\DiService;
use Phore\Di\Container\Producer\DiValue;
use Phore\Misc\UniDB\SQLiteDriver;
use Psr\Http\Message\ServerRequestInterface;


BraceDbg::SetupEnvironment(true, ["192.168.178.20", "localhost", "localhost:8080"]);



AppLoader::extend(function () {
    $app = new BraceApp();
    $app->addModule(new BraceRequestLaminasModule());
    $app->addModule(new RouterModule());
    $app->addModule(new CommandModule());

    $app->define("app", new DiValue($app));

    $app->define("subscriptionId", new DiService(function (ServerRequestInterface $request) {
        $subscriptionId = $request->getQueryParams()["subscription_id"] ?? throw new \InvalidArgumentException("Paramter 'subscription_id' is missing");
        if ( ! preg_match("/^[a-z0-9]+$/", $subscriptionId))
            throw new \InvalidArgumentException("Invalid subscription_id paramter value");
        return $subscriptionId;
    }));

    $app->define("uniDb", new DiService(function() {
        $d = new SQLiteDriver("/opt/osm_data.sqlite");
        $d->connect();
        return $d;
    }));


    $app->define("config", new DiService(function (string $subscriptionId) {
        return phore_hydrate(phore_file(CONFIG_PATH . "/$subscriptionId.yml", )->get_yaml(), Config::class);
    }));

    return $app;
});