<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use sagittaracc\Json;

final class JsonLoadTest extends TestCase
{
    public function testLoadJsonFromString(): void
    {
        $json = JSON::load('e1.txt');

        $json->saveAs('receipt.csv', null, ['content']);
        $json->saveAs('content.csv', 'content', ['items']);
        $json->saveAs('items.csv', 'content.items');

        $json->getStructure(['content'])->saveAs('receipt-structure');
        $json->getStructure(['items'], 'content')->saveAs('content-structure');
        $json->getStructure([], 'content.items')->saveAs('items-structure');

        $this->assertTrue(true);
    }
}