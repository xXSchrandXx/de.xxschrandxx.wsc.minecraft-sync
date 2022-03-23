<?php

namespace wcf\system\minecraft;

use GuzzleHttp\Exception\GuzzleException;
use wcf\data\user\group\UserGroupList;
use wcf\data\user\minecraft\MinecraftUser;
use wcf\data\user\User;
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
                $response = $this->call($minecraftID, 'GET', 'permission/status');
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
                $response = $this->call($minecraftID, 'GET', 'permission/groupList');
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
                $response = $this->call($minecraftID, 'POST', 'permission/getUserGroups', [
                    'uuid' => $uuid
                ]);
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
                $response = $this->call($minecraftID, 'POST', 'permission/addUserToGroup', [
                    'uuid' => $uuid,
                    'group' => $group
                ]);
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
                $response = $this->call($minecraftID, 'POST', 'permission/removeUserFromGroup', [
                    'uuid' => $uuid,
                    'group' => $group
                ]);
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

        foreach ($wscGroupList as $userGroup) {
            try {
                $this->wscGroups[$userGroup->groupID] = JSON::decode($userGroup->minecraftGroups)[1];
            } catch (SystemException $e) {
            }
        }

        return $this->wscGroups;
    }

    /**
     * @inheritDoc
     */
    public function sync(MinecraftUser $minecraftUser)
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
        foreach ($wscGroups as $groupID => $wscGroup) {
            if (in_array($groupID, $userGroupIDs)) {
                $shouldHave[$groupID] = $wscGroup;
            }
        }

        // 5. Auflisten welche Gruppen der Benutzer nicht haben sollte
        $shouldNotHave = [];
        foreach ($wscGroups as $groupID => $wscGroup) {
            if (!in_array($groupID, $userGroupIDs)) {
                $shouldNotHave[$groupID] = $wscGroup;
            }
        }

//        wcfDebug($shouldHave, $shouldNotHave);

        // 6. Benutzergruppen von Minecraft-Servern erhalten
        $minecraftHasGroups = $this->getUserGroups($uuid);

        // 7. Benutzergruppen vom Minecraft-Server filtern.
        $minecraftHasGroupsFiltered = [];
        foreach ($minecraftHasGroups as $minecraftID => $hasGroups) {
            if (!$hasGroups) {
                if (ENABLE_DEBUG_MODE) {
                    throw new MinecraftException("Could not get groups on server with id " . $minecraftID);
                }
                continue;
            }
            foreach ($wscGroups as $groupID => $groups) {
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

//        wcfDebug($minecraftHasGroups, $minecraftHasGroupsFiltered);

        // 8. Gruppen müssen hinzugefügt werden
        $needToAdd = [];
        foreach ($shouldHave as $shouldHaveGroups) {
            foreach ($shouldHaveGroups as $shouldHaveGroup) {
                foreach ($this->minecraftIDs as $minecraftID) {
                    $add = false;
                    if (!array_key_exists($minecraftID, $minecraftHasGroupsFiltered)) {
                        $add = true;
                    }
                    else if (!in_array($shouldHaveGroup, $minecraftHasGroupsFiltered[$minecraftID])) {
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

//        wcfDebug($needToAdd, $needToRemove);

        $response = [
            'added' => [],
            'removed' => []
        ];

        // TODO Add Groups
        foreach ($needToAdd as $minecraftID => $groups) {
            foreach ($groups as $group) {
                if (array_key_exists($minecraftID, $response['added'])) {
                    $response['added'][$minecraftID] += $this->addUserToGroup($uuid, $group, $minecraftID);
                } else {
                    $response['added'][$minecraftID] = [$this->addUserToGroup($uuid, $group, $minecraftID)];
                }
            }
        }

        // TODO Remove Groups
        foreach ($needToRemove as $minecraftID => $groups) {
            foreach ($groups as $group) {
                if (array_key_exists($minecraftID, $response['removed'])) {
                    $response['removed'][$minecraftID] += $this->removeUserFromGroup($uuid, $group, $minecraftID);
                } else {
                    $response['removed'][$minecraftID] = [$this->removeUserFromGroup($uuid, $group, $minecraftID)];
                }
            }
        }

//        wcfDebug($response);

        return $response;
    }
}
