<?php

use wcf\system\database\table\column\BlobDatabaseTableColumn;
use wcf\system\database\table\PartialDatabaseTable;

return [
    // wcf_user_group
    PartialDatabaseTable::create('wcf1_user_group')
        ->columns([
            BlobDatabaseTableColumn::create('minecraftGroups')
        ]),
];
