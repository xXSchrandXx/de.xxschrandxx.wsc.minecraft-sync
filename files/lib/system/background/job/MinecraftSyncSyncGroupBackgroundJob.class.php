<?php

namespace wcf\system\background\job;

use wcf\system\background\job\AbstractBackgroundJob;
use wcf\system\database\exception\DatabaseQueryException;
use wcf\system\database\exception\DatabaseQueryExecutionException;
use wcf\system\exception\MinecraftException;
use wcf\system\minecraft\MinecraftSyncHandler;
use wcf\system\WCF;

class MinecraftSyncSyncGroupBackgroundJob extends AbstractBackgroundJob
{
    protected $groupID;

    protected array $userIDChunks;
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
        if (!MINECRAFT_SYNC_ENABLED) {
            return;
        }

        if (!isset($this->userIDChunks)) {
            $sql = "SELECT userID
                    FROM wcf" . WCF_N . "_user_to_group
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

        $fail = false;
        foreach ($this->userIDChunks as $chunkID => $userIDs) {
            if (empty($userIDs)) {
                continue;
            }
            $response = MinecraftSyncHandler::getInstance()->syncMultipleUser($userIDs, $this->removeGroups);

            // TODO Check if statusCode is between 199 > x < 300
            $retryAfter = $this->getRetryAfter($response);
            if ($retryAfter === false) {
                $this->userIDChunks[$chunkID] = [];
            } else {
                $this->retryAfter = $retryAfter;
                $fail = true;
            }
        }

        if ($fail) {
            throw new MinecraftException('Synchronisation of "' . $this->groupID . '" failed.');
        }
    }

    /**
     * Überprüft ob ein Chunk erneut synchronisiert werden muss.
     * @param array responses
     * @return bool|false
     */
    private function getRetryAfter($responses)
    {
        $retryAfters = [];
        foreach ($responses['added'] as $minecraftID => $response) {
            if (array_key_exists('retryAfter', $response)) {
                array_push($retryAfters, $response['retryAfter']);
            }
        }
        foreach ($responses['removed'] as $minecraftID => $response) {
            if (array_key_exists('retryAfter', $response)) {
                array_push($retryAfters, $response['retryAfter']);
            }
        }
        if (empty($retryAfters)) {
            return false;
        }
        return max($retryAfters);
    }
}
