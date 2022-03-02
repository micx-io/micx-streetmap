<?php
namespace App;



use App\Config\Config;
use App\Controller\SuggestCtrl;
use Brace\Core\AppLoader;
use Brace\Core\BraceApp;
use Psr\Http\Message\ServerRequestInterface;

AppLoader::extend(function (BraceApp $app) {


    $app->router->on("GET@/v1/streetmap/streetmap.js", function (BraceApp $app, string $subscriptionId, Config $config, ServerRequestInterface $request) {

        $origin = $request->getHeader("referer")[0] ?? "";
        if ( ! origin_match($origin, $config->allow_origins)) {
            $origin = addslashes($origin);
            $subscriptionId = addslashes($subscriptionId);
            return $app->responseFactory->createResponseWithBody(
                "console.log('Webanalytics: Invalid origin $origin for subscriptionId $subscriptionId')",
                403, ["content-type"=>"text/javascript"]
            );
        }


        $jsText = file_get_contents(__DIR__ . "/../src/micxtools-v1.js");
        $jsText .= file_get_contents(__DIR__ . "/../src/micxtools-debounce-v1.js");
        $jsText .= file_get_contents(__DIR__ . "/../src/streetmap.js");

        $jsText = str_replace(
            ["%%ENDPOINT_URL%%", "%%SUBSCRIPTION_ID%%"],
            [
                "//" . $app->request->getUri()->getHost() . "/v1/streetmap/",
                $subscriptionId,
            ],
            $jsText
        );

        $response = $app->responseFactory->createResponseWithBody($jsText, 200, ["Content-Type" => "application/javascript"]);
        return $response;
    });


    $app->router->on("GET@/v1/streetmap/query", SuggestCtrl::class);



    $app->router->on("GET@/v1/streetmap/", function() {
        return ["system" => "micx webanalytics", "status" => "ok"];
    });

});
