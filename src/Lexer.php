<?php


namespace App;


class Lexer
{

    public $query;

    public $street;
    public $houseNr;
    public $city;
    public $zip;



    public function parseZipFirst (string $input=null, &$rest=null) : ?string
    {
        if ($input === null)
            $input = $this->query;
        $rest = $input;
        if (preg_match ("/^(([0-9]{3,5})[,-]?)/", $input, $matches)) {
            $rest = trim (substr($input, strlen($matches[1])));
            $this->zip = $matches[2];
            return $matches[2];
        }
        return null;
    }

    private function parseStreet(string $input, &$street, &$houseNo) {
        $street = null; $houseNo = null;

        $input = trim ($input);
        if (preg_match("/(([0-9]+[a-z-_.+]{0,2}){1,3})$/i", $input, $matches)) {
            $houseNo = $matches[1];
            $input = substr($input, 0, - (strlen($houseNo) +1));
        }
        $street = trim ($input);
    }


    private function parseCityZip(string $input, &$city, &$zip)
    {
        $city = null; $zip = null;
        $input = trim ($input);

        if (is_numeric($input) && strlen($input) > 3) {
            $this->zip = $input;
        } else {
            $this->city = $input;
        }
    }

    public function parse($cityFirst=false)
    {
        $this->parseZipFirst($this->query, $rest);
        $exp = explode(",", $rest, 2);


        if ($cityFirst) {
            $this->city = trim($exp[0]);
            if (count($exp) > 1)
                $this->parseStreet($exp[1], $this->street, $this->houseNr);
        } else {
            $this->parseStreet($exp[0], $this->street, $this->houseNr);
            $this->city = null;
            if (count($exp) > 1)
                $this->parseCityZip($exp[1], $this->city, $this->zip);
        }

    }

    public function __construct(string $query)
    {
        $this->query = str_replace(";", ",", $query);
    }
}
