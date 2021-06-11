# json

This is me helper for uploading data from json files into a database

### Use case
```php

use sagittaracc\Json;

require 'vendor/autoload.php';

for ($i = 1; $i <= 2; $i++) {
    $json = Json::load("e$i.txt")->setHeader($i === 1);

    $json->saveAs('data/receipt.csv', null, ['content']);
    $json->saveAs('data/content.csv', 'content', ['items']);
    $json->saveAs('data/items.csv', 'content.items');
}

$json->getStructure(['content'])->saveAs('data/receipt-structure');
$json->getStructure(['items'], 'content')->saveAs('data/content-structure');
$json->getStructure([], 'content.items')->saveAs('data/items-structure');
```