<?php

namespace wcf\system\background\job;

use wcf\data\user\minecraft\MinecraftUserList;
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
                // TODO fail on TooManyConnections in responses
                // Waiting until 5.5 update
            } else {
                MinecraftSyncHandler::getInstance()->syncUser($this->userID, $this->unsetGroups);
                // TODO fail on TooManyConnections in responses
                // Waiting until 5.5 update
            }
        }
    }
}
