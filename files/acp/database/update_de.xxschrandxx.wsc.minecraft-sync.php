<?php

use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\PartialDatabaseTable;

return [
    // wcf1_user_minecraft
    PartialDatabaseTable::create('wcf1_user_minecraft')
        ->columns([
            NotNullInt10DatabaseTableColumn::create('lastSync')
                ->defaultValue(0)
        ]),
];
