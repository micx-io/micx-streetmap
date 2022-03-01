<?php


namespace Test;


use App\Lexer;
use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase
{

    public function testStandard()
    {
        $r = new Lexer("A-b-c 9, Bonn");
        $r->parse();
        $this->assertEquals("A-b-c", $r->street);
        $this->assertEquals("9", $r->houseNr);
        $this->assertEquals("Bonn", $r->city);
    }

    public function testCombined()
    {
        $r = new Lexer("A-b-c 9-11, Bonn");
        $r->parse();
        $this->assertEquals("A-b-c", $r->street);
        $this->assertEquals("9-11", $r->houseNr);
        $this->assertEquals("Bonn", $r->city);
    }

    public function testHouseNrWhithAlpha()
    {
        $r = new Lexer("A-b-c 9b, Bonn");
        $r->parse();
        $this->assertEquals("A-b-c", $r->street);
        $this->assertEquals("9b", $r->houseNr);
        $this->assertEquals("Bonn", $r->city);
    }

    public function testZipFirst()
    {
        $r = new Lexer("47754 A-b-c 9b");
        $this->assertEquals("47754", $r->parseZipFirst());
        $r->parse();
        $this->assertEquals("A-b-c", $r->street);
        $this->assertEquals("9b", $r->houseNr);
        $this->assertEquals("47754", $r->zip);
    }
}
