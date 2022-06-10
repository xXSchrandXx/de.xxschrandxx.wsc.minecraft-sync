<?php

namespace wcf\system\background\job;

use wcf\system\background\job\AbstractBackgroundJob;
use wcf\system\database\exception\DatabaseQueryException;
use wcf\system\database\exception\DatabaseQueryExecutionException;
use wcf\system\minecraft\MinecraftSyncHandler;
use wcf\system\WCF;

class MinecraftSyncSyncGroupBackgroundJob extends AbstractBackgroundJob
{
    protected array $userIDChunks = [];
    protected array $removeGroups;

    public function __construct(int $groupID, array $removeGroups = [])
    {
        $this->removeGroups = $removeGroups;

        $sql = "SELECT userID
                FROM wcf".WCF_N."_user_to_group
                WHERE groupID = ?";
        try {
            /** @var \wcf\system\database\statement\PreparedStatement */
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([$this->groupID]);
            $userIDs = $statement->fetchAll(\PDO::FETCH_COLUMN);
            $this->userIDChunks = array_chunk($userIDs, MINECRAFT_SYNC_ENTRIES_PER_CALL, true);
        } catch (DatabaseQueryException | DatabaseQueryExecutionException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
        }
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
        if (!MINECRAFT_SYNC_ENABLED) {
            return;
        }

        $responses = [];
        foreach ($this->userIDChunks as $chunkID => $userIDs) {
            if (!empty($userIDs)) {
                $responses =+ MinecraftSyncHandler::getInstance()->syncMultipleUser($userIDs, $this->removeGroups);
            }
        }
        // TODO fail on TooManyConnections in responses
        $fail = false;
        foreach ($responses as $minecraftID => $response) {
            if (array_key_exists('retryAfter', $response)) {
                $this->retryAfter = $response['retryAfter'];
                $fail = true;
            } else {
                
            }
        }
        if ($fail) {
            $this->fail();
        }
    }
}
