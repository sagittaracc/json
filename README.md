# json

This is me helper for uploading data from json files into a database

### Use case
```php

use sagittaracc\Json;

require 'vendor/autoload.php';

for ($i = 1; $i <= 2; $i++) {
    $json = Json::load("e$i.txt")->setHeader($i === 1);

    $json->except(['content'])->saveAs('data/receipt.csv');
    $json->read('content')->except(['items'])->saveAs('data/content.csv');
    $json->read('content.items')->saveAs('data/items.csv');
}

$json->except(['content'])->getStructure()->saveAs('data/receipt-structure');
$json->read('content')->except(['items'])->getStructure()->saveAs('data/content-structure');
$json->read('content.items')->getStructure()->saveAs('data/items-structure');
```