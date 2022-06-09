<?php

namespace wcf\system\minecraft;

use GuzzleHttp\Exception\GuzzleException;
use wcf\data\user\group\UserGroupList;
use wcf\data\user\minecraft\MinecraftUser;
use wcf\data\user\minecraft\MinecraftUserEditor;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\User;
use wcf\data\user\UserList;
use wcf\system\benchmark\Benchmark;
use wcf\system\exception\MinecraftException;
use wcf\system\exception\SystemException;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\JSON;
use wcf\util\StringUtil;

class MinecraftSyncHandler extends AbstractMultipleMinecraftHandler implements IMinecraftSyncHandler
{
    /**
     * Baut die Klasse auf
     */
    public function init(): void
    {
        if (MINECRAFT_SYNC_IDENTITY) {
            $this->minecraftIDs = explode("\n", StringUtil::unifyNewlines(MINECRAFT_SYNC_IDENTITY));
        }
        parent::init();
    }

    /**
     * Calls methods on minecrafts and catches exceptions.
     * @see AbstractMultipleMinecraftHandler#call
     */
    protected function callAndCatch(string $httpMethod, string $method = '', array $args = [], ?int $minecraftID = null)
    {
        try {
            /** @var \Psr\Http\Message\ResponseInterface */
            $response = $this->call($httpMethod, $method, $args, $minecraftID);
            if ($response === null) {
                throw new MinecraftException("Unknown server with id " . $minecraftID);
            }
            return JSON::decode($response->getBody());
        } catch (GuzzleException | SystemException | MinecraftException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            return [
                'status' => $e->getMessage(),
                'statusCode' => $e->getCode()
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function status(?int $minecraftID = null)
    {
        if ($minecraftID === null) {
            $stati = [];
            foreach ($this->minecraftIDs as $minecraftID) {
                $stati[$minecraftID] = $this->status($minecraftID);
            }
            return $stati;
        } else {
            return $this->callAndCatch('GET', 'permission/status', [], $minecraftID);
        }
    }

    /**
     * @inheritDoc
     */
    public function groupList(?int $minecraftID = null)
    {
        if ($minecraftID === null) {
            $groups = [];
            foreach ($this->minecraftIDs as $minecraftID) {
                $groups[$minecraftID] = $this->groupList($minecraftID);
            }
            return $groups;
        } else {
            $response = $this->callAndCatch('GET', 'permission/groupList', [], $minecraftID);
            if (!array_key_exists('statusCode', $response)) {
                return [];
            }
            if ($response['statusCode'] !== 200) {
                return [];
            }
            if (!array_key_exists('groups', $response)) {
                return [];
            }
            return $response['groups'];
        }
    }

    /**
     * @inheritDoc
     */
    public function getUserGroups(string $uuid, ?int $minecraftID = null)
    {
        if ($minecraftID === null) {
            $playerGroups = [];
            foreach ($this->minecraftIDs as $minecraftID) {
                $playerGroups[$minecraftID] = $this->getUserGroups($uuid, $minecraftID);
            }
            return $playerGroups;
        } else {
            $response = $this->callAndCatch('POST', 'permission/getUserGroups', [
                'uuid' => $uuid
            ], $minecraftID);
            if (!array_key_exists('statusCode', $response)) {
                return [];
            }
            if ($response['statusCode'] !== 200) {
                return [];
            }
            if (!array_key_exists('groups', $response)) {
                return [];
            }
            return $response['groups'];
        }
    }

    /**
     * @inheritDoc
     */
    public function getUsersGroups(array $map, ?int $minecraftID = null)
    {
        if ($minecraftID === null) {
            $playerGroups = [];
            foreach ($this->minecraftIDs as $minecraftID) {
                $playerGroups[$minecraftID] = $this->getUsersGroups($map, $minecraftID);
            }
            return $playerGroups;
        } else {
            if (count($map) > MINECRAFT_SYNC_ENTRIES_PER_CALL) {
                $chunks = array_chunk($map, MINECRAFT_SYNC_ENTRIES_PER_CALL, true);
                $response = [];
                foreach ($chunks as $chunk) {
                    $response += $this->getUsersGroups($chunk, $minecraftID);
                }
                return $response;
            } else {
                return $this->callAndCatch('POST', 'permission/getUsersGroups', $map, $minecraftID);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function addUserToGroup(string $uuid, string $group, ?int $minecraftID = null)
    {
        if ($minecraftID === null) {
            $responses = [];
            foreach ($this->minecraftIDs as $minecraftID) {
                $response[$minecraftID] = $this->addUserToGroup($uuid, $group, $minecraftID);
            }
            return $responses;
        } else {
            return $this->callAndCatch('POST', 'permission/addUserToGroup', [
                'uuid' => $uuid,
                'group' => $group
            ], $minecraftID);
        }
    }

    /**
     * @inheritDoc
     */
    public function addUsersToGroups(array $map, ?int $minecraftID = null)
    {
        if ($minecraftID === null) {
            $responses = [];
            foreach ($this->minecraftIDs as $minecraftID) {
                $responses[$minecraftID] = $this->removeUsersFromGroups($map, $minecraftID);
            }
            return $responses;
        } else {
            if (count($map) > MINECRAFT_SYNC_ENTRIES_PER_CALL) {
                $chunks = array_chunk($map, MINECRAFT_SYNC_ENTRIES_PER_CALL, true);
                $response = [];
                foreach ($chunks as $chunk) {
                    $response += $this->addUsersToGroups($chunk, $minecraftID);
                }
                return $response;
            } else {
                return $this->callAndCatch('POST', 'permission/addUsersToGroups', $map, $minecraftID);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function removeUserFromGroup(string $uuid, string $group, ?int $minecraftID = null)
    {
        if ($minecraftID === null) {
            $responses = [];
            foreach ($this->minecraftIDs as $minecraftID) {
                $responses[$minecraftID] = $this->removeUserFromGroup($uuid, $group, $minecraftID);
            }
            return $responses;
        } else {
            return $this->callAndCatch('POST', 'permission/removeUserFromGroup', [
                'uuid' => $uuid,
                'group' => $group
            ], $minecraftID);
        }
    }

    /**
     * @inheritDoc
     */
    public function removeUsersFromGroups(array $map, ?int $minecraftID = null)
    {
        if ($minecraftID === null) {
            $responses = [];
            foreach ($this->minecraftIDs as $minecraftID) {
                $responses[$minecraftID] = $this->removeUsersFromGroups($map, $minecraftID);
            }
            return $responses;
        } else {
            if (count($map) > MINECRAFT_SYNC_ENTRIES_PER_CALL) {
                $chunks = array_chunk($map, MINECRAFT_SYNC_ENTRIES_PER_CALL, true);
                $response = [];
                foreach ($chunks as $chunk) {
                    $response += $this->getUsersGroups($chunk, $minecraftID);
                }
                return $response;
            } else {
                return $this->callAndCatch('POST', 'permission/removeUsersFromGroups', $map, $minecraftID);
            }
        }
    }

    private $wscGroups = [];

    /**
     * @inheritDoc
     */
    public function getWSCGroups()
    {
        if (!empty($this->wscGroups)) {
            return $this->wscGroups;
        }

        $wscGroupList = new UserGroupList();
        $wscGroupList->getConditionBuilder()->add('minecraftGroups IS NOT NULL');
        $wscGroupList->readObjects();
        $wscGroups = $wscGroupList->getObjects();

        foreach ($wscGroups as $wscGroup) {
            try {
                $this->wscGroups[$wscGroup->groupID] = JSON::decode($wscGroup->minecraftGroups);
            } catch (SystemException $e) {
            }
        }

        return $this->wscGroups;
    }

    /**
     * @inheritDoc
     */
    public function syncUser(int $userID, array $removeGroups = [])
    {
        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->getConditionBuilder()->add('userID = ?', [$userID]);
        $minecraftUserList->readObjects();
        $minecraftUsers = $minecraftUserList->getObjects();

        $responses = [];
        foreach ($minecraftUsers as $minecraftUserID => $minecraftUser) {
            $responses[$minecraftUserID] = $this->sync($minecraftUser, $removeGroups);
        }

        return $responses;
    }

    /**
     * @inheritDoc
     */
    public function syncMinecraftUUID(string $uuid, array $removeGroups = [])
    {
        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->getConditionBuilder()->add('minecraftUUID = ?', [$uuid]);
        $minecraftUserList->readObjects();
        $minecraftUser = $minecraftUserList->getSingleObject();
        if ($minecraftUser === null) {
            throw new \BadMethodCallException("Cannot get a single object when the list contains no objects.");
        }
        return $this->sync($minecraftUser, $removeGroups);
    }

    /**
     * @inheritDoc
     */
    public function sync(MinecraftUser $minecraftUser, array $removeGroups = [])
    {
        $response = $this->syncMultiple([$minecraftUser], $removeGroups);

        if (array_key_exists('benchmark', $response)) {
            $result = [
                'added' => [],
                'removed' => [],
                'benchmark' => $response['benchmark']
            ];
        } else {
            $result = [
                'added' => [],
                'removed' => [],
            ];
        }

        foreach ($response['added'] as $minecraftID => $uuids) {
            foreach ($uuids['users'] as $uuid => $data) {
                $result['added'][$minecraftID] = $data;
                continue;
            }
        }
        foreach ($response['removed'] as $minecraftID => $uuids) {
            foreach ($uuids['users'] as $uuid => $data) {
                $result['removed'][$minecraftID] = $data;
                continue;
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function delete(MinecraftUser $minecraftUser)
    {
        // 1. UUID & User
        $uuid = $minecraftUser->minecraftUUID;

        // 2. Benutzergruppen vom WSC erhalten
        $wscGroups = $this->getWSCGroups();

        // 3. Benutzergruppen von Minecraft-Servern erhalten
        $minecraftHasGroups = $this->getUserGroups($uuid);

        // 4. Benutzergruppen vom Minecraft-Server filtern.
        $minecraftHasGroupsFiltered = [];
        foreach ($minecraftHasGroups as $minecraftID => $hasGroups) {
            if (!$hasGroups) {
                if (ENABLE_DEBUG_MODE) {
                    \wcf\functions\exception\logThrowable(new MinecraftException("Could not get groups on server with id " . $minecraftID));
                }
                continue;
            }
            foreach ($wscGroups as $groupID => $wscGroupInfo) {
                foreach ($wscGroupInfo as $minecraftID2 => $groups) {
                    if ($minecraftID != $minecraftID2) {
                        continue;
                    }
                    foreach ($groups as $group) {
                        if (in_array($group, $hasGroups)) {
                            if (isset($minecraftHasGroupsFiltered[$minecraftID])) {
                                \array_push($minecraftHasGroupsFiltered[$minecraftID], $group);
                            } else {
                                $minecraftHasGroupsFiltered[$minecraftID] = [$group];
                            }
                        }
                    }
                }
            }
        }

        // 5. Gruppen entfernen
        $response = [];
        foreach ($minecraftHasGroupsFiltered as $minecraftID => $groups) {
            foreach ($groups as $group) {
                if (array_key_exists($minecraftID, $response)) {
                    $response[$minecraftID] += $this->removeUserFromGroup($uuid, $group, $minecraftID);
                } else {
                    $response[$minecraftID] = [$this->removeUserFromGroup($uuid, $group, $minecraftID)];
                }
            }
        }

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function syncLatest(array $removeGroups = [])
    {
        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->sqlOrderBy = 'lastSync ASC';
        $minecraftUserList->sqlLimit = MINECRAFT_SYNC_ENTRIES_PER_CALL;
        $minecraftUserList->getConditionBuilder()->add('lastSync < ?', [strtotime('+1 day')]);
        $minecraftUserList->readObjects();
        $minecraftUsers = $minecraftUserList->getObjects();
        if (!empty($minecraftUsers)) {
            MinecraftSyncHandler::getInstance()->syncMultiple($minecraftUsers, $removeGroups);
        }
    }

    /**
     * @inheritDoc
     */
    public function syncAll(array $removeGroups = [])
    {
        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->readObjects();
        return $this->syncMultiple($minecraftUserList->getObjects());
    }

    /**
     * @inheritDoc
     */
    public function syncMultiple(array $minecraftUsers, array $removeGroups = [])
    {
        $bmIndex = null;
        if (WCF::benchmarkIsEnabled()) {
            $bmIndex = Benchmark::getInstance()->start("BEGIN");
        }

         // 1. UUID & User
        /**
         * Array
         * (
         *     $userID => Array
         *     (
         *         $i => $minecraftUUID
         *     )
         * )
         * @var array
         */
        $userIDs = [];
        foreach ($minecraftUsers as $minecraftUser) {
            if (array_key_exists($minecraftUser->userID, $userIDs)) {
                \array_push($userIDs[$minecraftUser->userID], $minecraftUser->minecraftUUID);
            } else {
                $userIDs[$minecraftUser->userID] = [$minecraftUser->minecraftUUID];
            }
        }

        $userList = new UserList();
        $userList->getConditionBuilder()->add('user_table.userID IN (?)', [array_keys($userIDs)]);
        $userList->readObjects();
        $users = $userList->getObjects();

        // 2. Benutzergruppen vom WSC erhalten
        $wscGroups = $this->getWSCGroups();

        // 3. Liste alle Gruppen der Benutzer auf
        /**
         * Array
         * (
         *     $userID => Array
         *     (
         *         $i => $groupID
         *     )
         * )
         * @var array
         */
        $usersGroupIDs = [];
        foreach ($users as $user) {
            $usersGroupIDs[$user->userID] = $user->getGroupIDs();
        }

        // 4. Auflisten welche Gruppen der Benutzer haben sollte
        /**
         * Array
         * (
         *     $userID => Array
         *     (
         *         $minecraftID => Array
         *         (
         *             $groupID => Array
         *             (
         *                 $i => $groupName
         *             )
         *         )
         *     )
         * )
         * @var array
         */
        $usersShouldHave = [];
        foreach ($userIDs as $userID => $uuids) {
            foreach ($wscGroups as $groupID => $wscGroupInfo) {
                foreach ($wscGroupInfo as $minecraftID => $wscGroup) {
                    if (in_array($groupID, $usersGroupIDs[$userID])) {
                        $usersShouldHave[$userID][$minecraftID][$groupID] = $wscGroup;
                    }
                }
            }
        }

        // 5. Auflisten welche Gruppen der Benutzer nicht haben sollte
        /**
         * Array
         * (
         *     $userID => Array
         *     (
         *         $minecraftID => Array
         *         (
         *             $groupID => Array
         *             (
         *                 $i => $groupName
         *             )
         *         )
         *     )
         * )
         * @var array
         */
        $usersShouldNotHave = [];
        foreach ($userIDs as $userID => $uuids) {
            foreach ($wscGroups as $groupID => $wscGroupInfo) {
                foreach ($wscGroupInfo as $minecraftID => $wscGroup) {
                    if (!in_array($groupID, $usersGroupIDs[$userID])) {
                        $usersShouldNotHave[$userID][$minecraftID][$groupID] = $wscGroup;
                    }
                }
                foreach ($removeGroups as $minecraftID => $shouldNotHaveWSCGroups) {
                    foreach ($shouldNotHaveWSCGroups as $groupID => $shouldNotHaveGroups) {
                        if (isset($usersShouldNotHave[$userID][$minecraftID][$groupID])) {
                            foreach ($shouldNotHaveGroups as $shouldNotHaveGroup) {
                                if (!in_array($shouldNotHaveGroup, $usersShouldNotHave[$userID][$minecraftID][$groupID])) {
                                    array_push($usersShouldNotHave[$userID][$minecraftID][$groupID], $shouldNotHaveGroup);
                                }
                            }
                        } else {
                            $usersShouldNotHave[$userID][$minecraftID][$groupID] = $shouldNotHaveGroups;
                        }
                    }
                }
            }
        }

//        wcfDebug($usersShouldHave, $usersShouldNotHave);

        // 6. Benutzergruppen von Minecraft-Servern erhalten
        $uuidList = [];
        foreach ($userIDs as $userID => $uuids) {
            $uuidList += $uuids;
        }

        /**
         * Array
         * (
         *     $minecraftUUD => Array
         *     (
         *         'groups' => Array
         *         (
         *             $i => $groupName
         *         )
         *         'status' => $status
         *         'statusCode' => $statusCode
         *     )
         * )
         * @var array
         */
        $uuidsMinecraftHasGroups = $this->getUsersGroups($uuidList);

        // 7. Benutzergruppen vom Minecraft-Server filtern.
        /**
         * Array
         * (
         *     $minecraftID => Array
         *     (
         *         $minecraftUUID => Array
         *         (
         *             'groups' => Array
         *             (
         *                 $i => $groupName
         *             )
         *             'status' => $status
         *             'statusCode' => $statusCode
         *         )
         *     )
         * )
         * @var array
         */
        $minecraftHasGroupsFiltered = [];
        foreach ($uuidsMinecraftHasGroups as $minecraftID => $a) {
            if (!is_array($a)) {
                continue;
            }
            if (!array_key_exists('users', $a)) {
                continue;
            }
            $minecraftHasGroups = $a['users'];
            foreach ($minecraftHasGroups as $uuid => $b) {
                if (!is_array($b)) {
                    continue;
                }
                if (!array_key_exists('groups', $b)) {
                    continue;
                }
                $hasGroups = $b['groups'];
                foreach ($wscGroups as $groupID => $wscGroupInfo) {
                    foreach ($wscGroupInfo as $minecraftID2 => $groups) {
                        if ($minecraftID2 != $minecraftID) {
                            continue;
                        }
                        foreach ($groups as $group) {
                            if (in_array($group, $hasGroups)) {
                                if (isset($minecraftHasGroupsFiltered[$minecraftID][$uuid])) {
                                    \array_push($minecraftHasGroupsFiltered[$minecraftID][$uuid], $group);
                                } else {
                                    $minecraftHasGroupsFiltered[$minecraftID][$uuid] = [$group];
                                }
                            }
                        }
                    }
                }
            }
        }

        // 8. Gruppen müssen hinzugefügt werden
        /**
         * Array
         * (
         *     $minecraftID => Array
         *     (
         *         $minecraftUUID => Array
         *         (
         *             $id => $groupName
         *         )
         *     )
         * )
         * @var array
         */
        $needToAdd = [];
        foreach ($usersShouldHave as $userID => $shouldHaveGroupIDs) {
            foreach ($shouldHaveGroupIDs as $minecraftID => $shouldHaveGroupGroupIDs) {
                foreach ($shouldHaveGroupGroupIDs as $groupID => $shouldHaveGroups) {
                    foreach ($shouldHaveGroups as $shouldHaveGroup) {
                        foreach ($userIDs[$userID] as $uuid) {
                            $add = false;
                            if (!array_key_exists($minecraftID, $minecraftHasGroupsFiltered)) {
                                $add = true;
                            } else if (!in_array($shouldHaveGroup, $minecraftHasGroupsFiltered[$minecraftID][$uuid])) {
                                $add = true;
                            }
                            if ($add) {
                                if (isset($needToAdd[$minecraftID][$uuid])) {
                                    \array_push($needToAdd[$minecraftID][$uuid], $shouldHaveGroup);
                                } else {
                                    $needToAdd[$minecraftID][$uuid] = [$shouldHaveGroup];
                                }
                            }
                        }
                    }
                }
            }
        }

        // 9. Gruppen müssen entfernt werden
        /**
         * Array
         * (
         *     $minecraftID => Array
         *     (
         *         $minecraftUUID => Array
         *         (
         *             $id => $groupName
         *         )
         *     )
         * )
         * @var array
         */
        $needToRemove = [];
        foreach ($usersShouldNotHave as $userID => $shouldNotHave) {
            foreach ($shouldNotHave as $minecraftID => $shouldNotHaveWSCGroups) {
                if (!isset($minecraftHasGroupsFiltered[$minecraftID])) {
                    continue;
                }
                foreach ($userIDs[$userID] as $uuid) {
                    if (!isset($minecraftHasGroupsFiltered[$minecraftID][$uuid])) {
                        continue;
                    }
                    foreach ($shouldNotHaveWSCGroups as $groupID => $shouldNotHaveGroups) {
                        foreach ($shouldNotHaveGroups as $shouldNotHaveGroup) {
                            if (in_array($shouldNotHaveGroup, $minecraftHasGroupsFiltered[$minecraftID][$uuid])) {
                                if (isset($needToRemove[$minecraftID][$uuid])) {
                                    \array_push($needToRemove[$minecraftID][$uuid], $shouldNotHaveGroup);
                                } else {
                                    $needToRemove[$minecraftID][$uuid] = [$shouldNotHaveGroup];
                                }
                            }
                        }
                    }
                }
            }
        }

//        wcfDebug($needToAdd, $needToRemove);

        $response = [
            'added' => [],
            'removed' => []
        ];

        // 10 Gruppen hinzufügen
        foreach ($needToAdd as $minecraftID => $map) {
            $response['added'][$minecraftID] = $this->addUsersToGroups($map, $minecraftID);
        }

        // 11 Gruppen entfernen
        foreach ($needToRemove as $minecraftID => $map) {
            $response['removed'][$minecraftID] = $this->removeUsersFromGroups($map, $minecraftID);
        }

        // 12 lastSync setzen
        foreach ($minecraftUsers as $minecraftUser) {
            $editor = new MinecraftUserEditor($minecraftUser);
            $editor->update(['lastSync' => TIME_NOW]);
        }

        if ($bmIndex !== null) {
            $bm = Benchmark::getInstance();
            $bm->stop($bmIndex);
            $response['benchmark'] = [
                'ExecutionTime' => $bm->getExecutionTime(),
                'MemoryUsage' => $bm->getMemoryUsage()
            ];
        }
        return $response;
    }
}
