<?php

namespace wcf\system\cronjob;

use wcf\data\cronjob\Cronjob;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\system\minecraft\MinecraftSyncHandler;

class MinecraftSyncCronjob extends AbstractCronjob
{
    /**
     * @inheritDoc
     */
    public function execute(Cronjob $cronjob)
    {
        parent::execute($cronjob);

        if (!MINECRAFT_SYNC_ENABLED) {
            return;
        }
        $minecraftUserList = new MinecraftUserList();
        $lastDay = TIME_NOW - 24 * 60 * 60 * 1000;
        $minecraftUserList->sqlOrderBy = 'lastSync ASC';
        $minecraftUserList->sqlLimit = 100;
        $minecraftUserList->getConditionBuilder()->add('lastSync < ?', [$lastDay]);
        $minecraftUserList->readObjects();
        $minecraftUsers = $minecraftUserList->getObjects();
        if (!empty($minecraftUsers)) {
            MinecraftSyncHandler::getInstance()->syncMultiple($minecraftUsers);
        }
    }
}
