<?php

namespace wcf\system\background\job;

use wcf\system\background\job\AbstractBackgroundJob;
use wcf\system\minecraft\MinecraftSyncHandler;

class MinecraftSyncSyncBackgroundJob extends AbstractBackgroundJob
{
    protected ?int $userID;
    protected array $unsetGroups;

    public function __construct(array $unsetGroups = [], ?int $userID = null)
    {
        $this->userID = $userID;
        $this->unsetGroups = $unsetGroups;
    }

    protected $retryAfter = 3 * 10;

    /**
     * @inheritDoc
     */
    public function retryAfter()
    {
        return $this->retryAfter;
    }

    /**
     * @inheritDoc
     */
    public function perform()
    {
        if (MINECRAFT_SYNC_ENABLED) {
            if ($this->userID === null) {
                $responses = MinecraftSyncHandler::getInstance()->syncAll($this->unsetGroups);
                // TODO fail on TooManyConnections in responses
                // Waiting until 5.5 update
            } else {
                $responses = MinecraftSyncHandler::getInstance()->syncUser($this->userID, $this->unsetGroups);
                // TODO fail on TooManyConnections in responses
                // Waiting until 5.5 update
            }
        }
    }
}
