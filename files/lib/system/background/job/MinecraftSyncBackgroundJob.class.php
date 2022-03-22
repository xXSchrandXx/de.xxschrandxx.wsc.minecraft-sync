<?php

namespace wcf\system\background\job;

use wcf\system\background\job\AbstractBackgroundJob;
use wcf\system\minecraft\MinecraftSyncHandler;

class MinecraftSyncBackgroundJob extends AbstractBackgroundJob
{
    /**
     * @inheritDoc
     */
    public function retryAfter()
    {
        switch ($this->getFailures()) {
            case 1:
                return 5 * 60;
            case 2:
                return 30 * 60;
            case 3:
                return 2 * 60 * 60;
        }
    }

    /**
     * @inheritDoc
     */
    public function perform()
    {
        if (MINECRAFT_SYNC_ENABLED) {
// TODO            MinecraftSyncHandler::getInstance()->syncAll();
        }
    }
}