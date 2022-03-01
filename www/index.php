<?php

/**
 * Created by PhpStorm.
 * User: matthes
 * Date: 11.06.18
 * Time: 15:22
 */
namespace Demo;

use App\IndexMatcher;
use App\Lexer;
use Phore\Core\Exception\InvalidDataException;
use Phore\MicroApp\App;
use Phore\MicroApp\AppModule;
use Phore\MicroApp\Auth\AclRule;
use Phore\MicroApp\Auth\BasicUserProvider;
use Phore\MicroApp\Auth\HttpBasicAuthMech;
use Phore\MicroApp\Controller\Controller;
use Phore\MicroApp\Handler\JsonExceptionHandler;
use Phore\MicroApp\Handler\JsonResponseHandler;
use Phore\MicroApp\Type\QueryParams;
use Phore\MicroApp\Type\Request;
use Phore\MicroApp\Type\Route;
use Phore\MicroApp\Type\RouteParams;
use Phore\Misc\UniDB\SQLiteDriver;
use Phore\Misc\UniDB\SqlSelectStatement;
use Phore\Theme\CoreUI\CoreUi_Config_PageWithHeader;
use Phore\Theme\CoreUI\CoreUi_PageWithHeader;
use Phore\Theme\CoreUI\CoreUiModule;

require __DIR__ . "/../vendor/autoload.php";

// Configure the App
$app = new App();
$app->activateExceptionErrorHandlers();
$app->setOnExceptionHandler(new JsonExceptionHandler());
$app->setResponseHandler(new JsonResponseHandler());


$app->acl->addRule(\aclRule()->route("/*")->ALLOW());


$app->define("uniDb", function () {
    $i =  new SQLiteDriver(SQLITE_FILE);
    $i->connect();
    return $i;
});

$app->router->onGet("/v1/suggest", function (SQLiteDriver $uniDb, Request $request) {
    $q = $request->GET->get("q", new InvalidDataException("Missing Parameter 'q'"));

    $lexQ = new Lexer($q);
    $indexer = new IndexMatcher();

    $sql = new SqlSelectStatement(["*"], "street");
    $sql->limit(10);

    $lexQ->parseZipFirst();

    if ($lexQ->zip !== null) {
        $sql->where("postcode = ?", [$lexQ->zip]);
    }

    $lexQ->parse(true);

    if ($uniDb->query("select count(*) from city where icity = ?", [$indexer->transformToIndex($lexQ->city)])->firstCell() == 0) {
        // City is not first element
        $lexQ->parse(false);
    }

    if ($lexQ->street !== null)
        $sql->where("istreet_name LIKE ?", [trim($indexer->transformToIndex($lexQ->street)) . "%"]);
    if ($lexQ->city !== null)
        $sql->where("icity LIKE ?", [trim($indexer->transformToIndex($lexQ->city)). "%"]);

    $dbResult = $uniDb->query($sql);
    $ret = [
        "query" => $q,
        "suggestions" => $dbResult->fetchAll(),
        "debug" => $uniDb->lastQueryRaw,
        "debug2" => $lexQ
    ];

    return $ret;
});


// Run the Application
$app->serve();
