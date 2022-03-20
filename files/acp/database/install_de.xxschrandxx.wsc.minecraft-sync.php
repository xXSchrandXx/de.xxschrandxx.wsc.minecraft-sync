<?php

use wcf\system\database\table\column\BlobDatabaseTableColumn;
use wcf\system\database\table\PartialDatabaseTable;

return [
    // _user_group
    PartialDatabaseTable::create('wcf' . WCF_N . '_user_group')
        ->columns([
            BlobDatabaseTableColumn::create('minecraftGroups')
        ]),
];
