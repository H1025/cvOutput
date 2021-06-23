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
        new output("../tests/testData/input", "./tests/testData/output");
    }
}