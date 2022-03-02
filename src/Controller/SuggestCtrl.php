<?php

namespace App\Controller;

use App\IndexMatcher;
use App\Lexer;
use Laminas\Diactoros\ServerRequest;
use Phore\Core\Exception\InvalidDataException;
use Phore\Misc\UniDB\SQLiteDriver;
use Phore\Misc\UniDB\SqlSelectStatement;

class SuggestCtrl
{


    public function __invoke(SQLiteDriver $uniDb, ServerRequest $request) {
        $q = $request->getQueryParams()["q"] ?? throw new InvalidDataException("Missing Parameter 'q'");

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
    }

}
