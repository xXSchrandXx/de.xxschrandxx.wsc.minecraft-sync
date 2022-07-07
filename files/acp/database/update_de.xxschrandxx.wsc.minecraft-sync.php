<?php

use wcf\system\database\table\column\BlobDatabaseTableColumn;
use wcf\system\database\table\PartialDatabaseTable;

return [
    // wcf1_minecraft
    PartialDatabaseTable::create('wcf1_minecraft')
        ->columns([
            BlobDatabaseTableColumn::create('groups')
        ])
];
