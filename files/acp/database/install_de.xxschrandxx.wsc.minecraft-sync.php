<?php

use wcf\system\database\table\column\BlobDatabaseTableColumn;
use wcf\system\database\table\column\IntDatabaseTableColumn;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\VarcharDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\DatabaseTableChangeProcessor;
use wcf\system\database\table\index\DatabaseTableForeignKey;
use wcf\system\WCF;

$tables = [
    // _user_group
    DatabaseTable::create('wcf' . WCF_N . '_user_group')
        ->columns([
            BlobDatabaseTableColumn::create('minecraftGroups')
        ]),
];

(new DatabaseTableChangeProcessor(
    $this->installation->getPackage(),
    $tables,
    WCF::getDB()->getEditor()
))->process();
