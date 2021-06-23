<?php

namespace tests\outputTest;

use cvOutput\output;
use PHPUnit\Framework\TestCase;

class outputTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function test_construct()
    {
        new output("../tests/testData/interface", "./tests/testData");
    }

    /**
     * @doesNotPerformAssertions
     */
    public function _test_csharp()
    {
        (new output("../tests/testData/testdata.yml", "./tests/testData"))->csharp();
    }
}