<?php

namespace wcf\system\background\job;

use wcf\data\user\UserList;
use wcf\system\background\job\AbstractBackgroundJob;
use wcf\system\minecraft\MinecraftSyncHandler;
use wcf\system\WCF;

class MinecraftSyncSyncGroupBackgroundJob extends AbstractBackgroundJob
{
    protected int $groupID;
    protected array $removeGroups;

    public function __construct(int $groupID, array $removeGroups = [])
    {
        $this->groupID = $groupID;
        $this->removeGroups = $removeGroups;
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
            return;
        }

        $sql = "SELECT userID
                FROM wcf".WCF_N."_user_to_group
                WHERE groupID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$this->groupID]);
        $userIDs = $statement->fetchArray();

        $responses = MinecraftSyncHandler::getInstance()->syncMultiple($userIDs, $this->removeGroups);
        // TODO fail on TooManyConnections in responses
        foreach ($responses as $minecraftID => $response) {
            if (array_key_exists('retryAfter', $response)) {
                $this->retryAfter = $response['retryAfter'];
                $this->fail();
            }
        }
    }
}
