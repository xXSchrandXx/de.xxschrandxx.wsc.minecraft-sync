<?php

use wcf\system\database\table\column\DefaultTrueBooleanDatabaseTableColumn;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\NotNullVarchar255DatabaseTableColumn;
use wcf\system\database\table\column\ObjectIdDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\index\DatabaseTableForeignKey;

return [
    // wcf1_minecraft_group
    DatabaseTable::create('wcf1_minecraft_group')
        ->columns([
            ObjectIdDatabaseTableColumn::create('minecraftGroupID'),
            NotNullInt10DatabaseTableColumn::create('groupID'),
            NotNullInt10DatabaseTableColumn::create('minecraftID'),
            NotNullVarchar255DatabaseTableColumn::create('minecraftName'),
            DefaultTrueBooleanDatabaseTableColumn::create('shouldHave')
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['groupID'])
                ->onDelete('CASCADE')
                ->referencedColumns(['groupID'])
                ->referencedTable('wcf1_user_group'),
            DatabaseTableForeignKey::create()
                ->columns(['minecraftID'])
                ->onDelete('CASCADE')
                ->referencedColumns(['minecraftID'])
                ->referencedTable('wcf1_minecraft')
        ])
];
