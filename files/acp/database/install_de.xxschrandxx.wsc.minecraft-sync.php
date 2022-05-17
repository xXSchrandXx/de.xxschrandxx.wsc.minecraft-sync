<?php

use wcf\system\database\table\column\BlobDatabaseTableColumn;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\PartialDatabaseTable;

return [
    // wcf1_user_minecraft
    PartialDatabaseTable::create('wcf1_user_minecraft')
        ->columns([
            NotNullInt10DatabaseTableColumn::create('lastSync')
        ]),
    // wcf1_user_group
    PartialDatabaseTable::create('wcf1_user_group')
        ->columns([
            BlobDatabaseTableColumn::create('minecraftGroups')
        ]),
];
