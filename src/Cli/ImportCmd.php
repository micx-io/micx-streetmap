<?php


namespace App\Cli;


use App\IndexMatcher;
use Phore\CliTools\Cli\CliContext;
use Phore\CliTools\Helper\GetOptResult;
use Phore\CliTools\PhoreAbstractCmd;
use Phore\Misc\Map\PhoreMap;
use Phore\Misc\UniDB\SQLiteDriver;

class ImportCmd extends PhoreAbstractCmd
{

    public function invoke(CliContext $context)
    {
        $opts = $context->getOpts("i:");

        $indexer = new IndexMatcher();

        $unidb = new SQLiteDriver(SQLITE_FILE);
        $unidb->setSchemaFile("/opt/demo/schema.sqlite.sql");
        $unidb->connect();

        $xml = new \XMLReader();
        if ( ! $xml->open($opts->get("i")))
            $context->emergency("cannot read input file");

        $count = 0;
        $cityMap = new PhoreMap();
        $streetMap = new PhoreMap();

        while($xml->read()) {
            $depth = $xml->depth;
            if ($depth < 1)
                continue;
            if ($xml->name !== "way")
                continue;
            //echo "\n$depth: " . str_repeat(" ", $depth) . $xml->name . $xml->getAttribute("id");

            $data = ["tags" => []];
            $xml->read();
            while($xml->depth > 1) {
                $xml->read();
                if ($xml->name !== "tag")
                    continue;
                $data["tags"][$xml->getAttribute("k")] = $xml->getAttribute("v");
            }

            $city = $data["tags"]["addr:city"] ?? null;
            $postcode = $data["tags"]["addr:postcode"] ?? null;
            $streetName = $data["tags"]["addr:street"] ?? null;
            $houseNr = $data["tags"]["addr:housenumber"] ?? null;

            if ($city === null || $postcode === null)
                continue;

            if ( ! $cityMap->has($postcode)) {
                $unidb->insert("city", [
                    "city_id" => $cityMap->get($postcode, $cityMap->count() + 1),
                    "city" => $city,
                    "icity" => $indexer->transformToIndex((string)$city),
                    "postcode" => $postcode
                ]);
            }

            $streetKey = $postcode . $streetName;
            if ( ! $streetMap->has($streetKey)) {
                $unidb->insert("street", [
                    "street_id" => $streetMap->get($streetKey, $streetMap->count() + 1),
                    "street_name" => $streetName,
                    "istreet_name" => $indexer->transformToIndex((string)$streetName),
                    "postcode" => $postcode,
                    "city" => $city,
                    "icity" => $indexer->transformToIndex((string)$city)
                ]);
            }

            if ($houseNr !== null) {
                $unidb->insert("housenr", [
                    "street_id" => $streetMap->get($streetKey),
                    "housenr" => $houseNr
                ]);
            }


            $count++;
            if ($count % 1000 === 0)
                echo "$count..\n";
        }

    }
}
