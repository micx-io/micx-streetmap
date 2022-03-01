<?php

namespace Test;

use App\IndexMatcher;
use PHPUnit\Framework\TestCase;

class IndexGeneratorTest extends TestCase
{

    public function testReplacement()
    {
        $r = new IndexMatcher();

        $this->assertEquals("abe", $r->transformToIndex("a-B-é"));
        $this->assertEquals("abc", $r->transformToIndex("abbc"));
        $this->assertEquals("hab", $r->transformToIndex("hab"));
        $this->assertEquals("ab", $r->transformToIndex("ahb"));
    }

}
