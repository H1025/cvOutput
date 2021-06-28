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
            __DIR__ . DIRECTORY_SEPARATOR . './testData/input',
            __DIR__ . DIRECTORY_SEPARATOR . './testData/output'
        );
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_csharp()
    {
        (new output(__DIR__ . DIRECTORY_SEPARATOR . './testData/input'))
            ->csharp(__DIR__ . DIRECTORY_SEPARATOR . './testData/output');
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_apiListMD()
    {
        (new output(__DIR__ . DIRECTORY_SEPARATOR . './testData/input'))
            ->apiListMD(__DIR__ . DIRECTORY_SEPARATOR . './testData/output');
    }
}
