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
        new output(
            __DIR__ . DIRECTORY_SEPARATOR .'./testData/input',
            __DIR__ . DIRECTORY_SEPARATOR .'./testData/output'
        );
    }
}