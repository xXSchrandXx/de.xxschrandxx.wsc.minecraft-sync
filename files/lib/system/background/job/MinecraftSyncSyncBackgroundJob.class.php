<?php

namespace wcf\system\background\job;

use wcf\system\background\job\AbstractBackgroundJob;
use wcf\system\minecraft\MinecraftSyncHandler;

class MinecraftSyncSyncBackgroundJob extends AbstractBackgroundJob
{
    protected int $userID;

    public function __construct(?int $userID = null)
    {
        $this->userID = $userID;
    }

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
            if ($this->userID === null) {
                $responses = MinecraftSyncHandler::getInstance()->syncAll();
                // TODO fail on TooManyConnections in responses
            } else {
                $responses = MinecraftSyncHandler::getInstance()->syncUser($this->userID);
                // TODO fail on TooManyConnections in responses
            }
        }
    }
}
