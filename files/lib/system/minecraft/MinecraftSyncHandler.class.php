<?php

namespace wcf\system\minecraft;

use GuzzleHttp\Exception\GuzzleException;
use wcf\data\user\group\UserGroupList;
use wcf\data\user\minecraft\MinecraftUser;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\User;
use wcf\data\user\UserList;
use wcf\system\exception\MinecraftException;
use wcf\system\exception\SystemException;
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
            try {
                /** @var \Psr\Http\Message\ResponseInterface */
                $response = $this->call('GET', 'permission/status', [], $minecraftID);
                if ($response === null) {
                    throw new MinecraftException("Could not get status of server with id " . $minecraftID);
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
            try {
                /** @var \Psr\Http\Message\ResponseInterface */
                $response = $this->call('GET', 'permission/groupList', [], $minecraftID);
                if ($response === null) {
                    throw new MinecraftException("Could not get groups of server with id " . $minecraftID);
                }
                $responseBody = JSON::decode($response->getBody());
                return $responseBody['groups'];
            } catch (GuzzleException | SystemException | MinecraftException $e) {
                if (ENABLE_DEBUG_MODE) {
                    \wcf\functions\exception\logThrowable($e);
                }
                return [];
            }
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
            try {
                /** @var \Psr\Http\Message\ResponseInterface */
                $response = $this->call('POST', 'permission/getUserGroups', [
                    'uuid' => $uuid
                ], $minecraftID);
                if ($response === null) {
                    throw new MinecraftException("Could not get user groups of " . $uuid . " on server with id " . $minecraftID);
                }
                $responseBody = JSON::decode($response->getBody());
                return $responseBody['groups'];
            } catch (GuzzleException | SystemException | MinecraftException $e) {
                if (ENABLE_DEBUG_MODE) {
                    \wcf\functions\exception\logThrowable($e);
                }
                return false;
            }
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
            try {
                // TODO add post_max_size
                $size = $this->getLaterJsonSize($map);
                if ($size > ini_get('post_max_size')) {
                    $length = $size / ini_get('post_max_size');
                    $chunks = array_chunk($map, $length, true);
                    $response = [];
                    foreach ($chunks as $chunk) {
                        $response += $this->getUsersGroups($chunk, $minecraftID);
                    }
                    return $response;
                } else {
                    /** @var \Psr\Http\Message\ResponseInterface */
                    $response = $this->call('POST', 'permission/getUsersGroups', $map, $minecraftID);
                    if ($response === null) {
                        throw new MinecraftException("Could not get users groups on server with id " . $minecraftID);
                    }
                    // TODO
                    return JSON::decode($response->getBody());
                }
            } catch (GuzzleException | SystemException | MinecraftException $e) {
                if (ENABLE_DEBUG_MODE) {
                    \wcf\functions\exception\logThrowable($e);
                }
                return false;
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
            try {
                /** @var \Psr\Http\Message\ResponseInterface */
                $response = $this->call('POST', 'permission/addUserToGroup', [
                    'uuid' => $uuid,
                    'group' => $group
                ], $minecraftID);
                if ($response === null) {
                    throw new MinecraftException("Could not add user " . $uuid . " to group " . $group . " on server with id " . $minecraftID);
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
            try {
                // TODO add post_max_size
                $size = $this->getLaterJsonSize($map);
                if ($size > ini_get('post_max_size')) {
                    $length = $size / ini_get('post_max_size');
                    $chunks = array_chunk($map, $length, true);
                    $response = [];
                    foreach ($chunks as $chunk) {
                        $response += $this->addUsersToGroups($chunk, $minecraftID);
                    }
                    return $response;
                } else {
                    /** @var \Psr\Http\Message\ResponseInterface */
                    $response = $this->call('POST', 'permission/addUsersToGroups', $map, $minecraftID);
                    if ($response === null) {
                        throw new MinecraftException("Could not add users to groups on server with id " . $minecraftID);
                    }
                        return JSON::decode($response->getBody());
                }
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
            try {
                /** @var \Psr\Http\Message\ResponseInterface */
                $response = $this->call('POST', 'permission/removeUserFromGroup', [
                    'uuid' => $uuid,
                    'group' => $group
                ], $minecraftID);
                if ($response === null) {
                    throw new MinecraftException("Could not remove user " . $uuid . " from group " . $group . " on server with id " . $minecraftID);
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
            try {
                // TODO add post_max_size
                $size = $this->getLaterJsonSize($map);
                if ($size > ini_get('post_max_size')) {
                    $length = $size / ini_get('post_max_size');
                    $chunks = array_chunk($map, $length, true);
                    $response = [];
                    foreach ($chunks as $chunk) {
                        $response += $this->getUsersGroups($chunk, $minecraftID);
                    }
                    return $response;
                } else {
                    /** @var \Psr\Http\Message\ResponseInterface */
                    $response = $this->call('POST', 'permission/removeUsersFromGroups', $map, $minecraftID);
                    if ($response === null) {
                        throw new MinecraftException("Could not remove users from groups on server with id " . $minecraftID);
                    }
                    return JSON::decode($response->getBody());
                }
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
    public function sync(MinecraftUser $minecraftUser, array $removeGroups = [])
    {
        // 1. UUID & User
        $uuid = $minecraftUser->minecraftUUID;
        $user = new User($minecraftUser->userID);

        // 2. Benutzergruppen vom WSC erhalten
        $wscGroups = $this->getWSCGroups();

        // 3. Liste alle Gruppen des Benutzers auf
        $userGroupIDs = $user->getGroupIDs();

        // 4. Auflisten welche Gruppen der Benutzer haben sollte
        $shouldHave = [];
        foreach ($wscGroups as $groupID => $wscGroupInfo) {
            foreach ($wscGroupInfo as $minecraftID => $wscGroup) {
                if (in_array($groupID, $userGroupIDs)) {
                    $shouldHave[$groupID] = $wscGroup;
                }
            }
        }

        // 5. Auflisten welche Gruppen der Benutzer nicht haben sollte
        $shouldNotHave = $removeGroups;
        foreach ($wscGroups as $groupID => $wscGroupInfo) {
            foreach ($wscGroupInfo as $minecraftID => $wscGroup) {
                if (!in_array($groupID, $userGroupIDs)) {
                    $shouldNotHave[$groupID] = $wscGroup;
                }
            }
        }
        wcfDebug($shouldNotHave);

        // 6. Benutzergruppen von Minecraft-Servern erhalten
        $minecraftHasGroups = $this->getUserGroups($uuid);

        // 7. Benutzergruppen vom Minecraft-Server filtern.
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

        // 8. Gruppen müssen hinzugefügt werden
        $needToAdd = [];
        foreach ($shouldHave as $shouldHaveGroups) {
            foreach ($shouldHaveGroups as $shouldHaveGroup) {
                foreach ($this->minecraftIDs as $minecraftID) {
                    $add = false;
                    if (!array_key_exists($minecraftID, $minecraftHasGroupsFiltered)) {
                        $add = true;
                    } else if (!in_array($shouldHaveGroup, $minecraftHasGroupsFiltered[$minecraftID])) {
                        $add = true;
                    }
                    if ($add) {
                        if (isset($needToAdd[$minecraftID])) {
                            \array_push($needToAdd[$minecraftID], $shouldHaveGroup);
                        } else {
                            $needToAdd[$minecraftID] = [$shouldHaveGroup];
                        }
                    }
                }
            }
        }

        // 9. Gruppen müssen entfernt werden
        $needToRemove = [];
        foreach ($minecraftHasGroupsFiltered as $minecraftID => $hasGroups) {
            foreach ($hasGroups as $hasGroup) {
                foreach ($shouldNotHave as $shouldNotHaveGroups) {
                    if (in_array($hasGroup, $shouldNotHaveGroups)) {
                        if (isset($needToRemove[$minecraftID])) {
                            \array_push($needToRemove[$minecraftID], $hasGroup);
                        } else {
                            $needToRemove[$minecraftID] = [$hasGroup];
                        }
                    }
                }
            }
        }

        $response = [
            'added' => [],
            'removed' => []
        ];

        // 10 Gruppen hinzufügen
        foreach ($needToAdd as $minecraftID => $groups) {
            foreach ($groups as $group) {
                if (array_key_exists($minecraftID, $response['added'])) {
                    $response['added'][$minecraftID] += $this->addUserToGroup($uuid, $group, $minecraftID);
                } else {
                    $response['added'][$minecraftID] = [$this->addUserToGroup($uuid, $group, $minecraftID)];
                }
            }
        }

        // 11 Gruppen entfernen
        foreach ($needToRemove as $minecraftID => $groups) {
            foreach ($groups as $group) {
                if (array_key_exists($minecraftID, $response['removed'])) {
                    $response['removed'][$minecraftID] += $this->removeUserFromGroup($uuid, $group, $minecraftID);
                } else {
                    $response['removed'][$minecraftID] = [$this->removeUserFromGroup($uuid, $group, $minecraftID)];
                }
            }
        }

        return $response;
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
    public function syncAll(array $removeGroups = [])
    {

        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->readObjects();
        $minecraftUsers = $minecraftUserList->getObjects();

        // 1. UUID & User
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
        $usersGroupIDs = [];
        foreach ($users as $user) {
            $usersGroupIDs[$user->userID] = $user->getGroupIDs();
        }

        // 4. Auflisten welche Gruppen der Benutzer haben sollte
        $usersShouldHave = [];
        foreach ($userIDs as $userID => $uuids) {
            foreach ($wscGroups as $groupID => $wscGroupInfo) {
                foreach ($wscGroupInfo as $minecraftID => $wscGroup) {
                    if (in_array($groupID, $usersGroupIDs[$userID])) {
                        $usersShouldHave[$userID][$groupID] = $wscGroup;
                    }
                }
            }
        }

        // 5. Auflisten welche Gruppen der Benutzer nicht haben sollte
        $usersShouldNotHave = $removeGroups;
        foreach ($userIDs as $userID => $uuids) {
            foreach ($wscGroups as $groupID => $wscGroupInfo) {
                foreach ($wscGroupInfo as $minecraftID => $wscGroup) {
                    if (!in_array($groupID, $usersGroupIDs[$userID])) {
                        $usersShouldNotHave[$userID][$groupID] = $wscGroup;
                    }
                }
            }
        }

        // 6. Benutzergruppen von Minecraft-Servern erhalten
        $uuidList = [];
        foreach ($userIDs as $userID => $uuids) {
            $uuidList += $uuids;
        }
        $uuidsMinecraftHasGroups = $this->getUsersGroups($uuidList);

        // 7. Benutzergruppen vom Minecraft-Server filtern.
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
        $needToAdd = [];
        foreach ($usersShouldHave as $userID => $shouldHave) {
            foreach ($shouldHave as $shouldHaveGroups) {
                foreach ($shouldHaveGroups as $shouldHaveGroup) {
                    foreach ($this->minecraftIDs as $minecraftID) {
                        if (!array_key_exists($minecraftID, $minecraftHasGroupsFiltered)) {
                            continue;
                        }
                        foreach ($minecraftHasGroupsFiltered[$minecraftID] as $uuid => $groups) {
                            if (in_array($shouldHaveGroup, $minecraftHasGroupsFiltered[$minecraftID][$uuid])) {
                                continue;
                            }
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

        // 9. Gruppen müssen entfernt werden
        $needToRemove = [];
        foreach ($minecraftHasGroupsFiltered as $minecraftID => $uuids) {
            foreach ($uuids as $uuid => $hasGroups) {
                foreach ($hasGroups as $hasGroup) {
                    foreach ($usersShouldNotHave as $userID => $shouldNotHave) {
                        foreach ($shouldNotHave as $shouldNotHaveGroups) {
                            if (in_array($hasGroup, $shouldNotHaveGroups)) {
                                if (isset($needToRemove[$minecraftID][$uuid])) {
                                    \array_push($needToRemove[$minecraftID][$uuid], $hasGroup);
                                } else {
                                    $needToRemove[$minecraftID][$uuid] = [$hasGroup];
                                }
                            }
                        }
                    }
                }
            }
        }

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

        return $response;
    }

    private function getLaterJsonSize(array $array)
    {
        return strlen(JSON::encode($array));
    }
}
