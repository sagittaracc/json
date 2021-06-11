<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\Json;

final class JsonLoadTest extends TestCase
{
    public function testLoadJsonFromString(): void
    {
        Json::load('e1.txt')->saveAs('test.csv', 'content.items');
        $this->assertTrue(true);
    }
}