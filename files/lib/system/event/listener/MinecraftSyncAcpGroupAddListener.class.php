<?php

namespace wcf\system\event\listener;

use wcf\acp\form\UserGroupEditForm;
use wcf\system\background\BackgroundQueueHandler;
use wcf\system\background\job\MinecraftSyncSyncGroupBackgroundJob;
use wcf\system\exception\SystemException;
use wcf\system\exception\UserInputException;
use wcf\system\minecraft\MinecraftSyncHandler;
use wcf\system\WCF;
use wcf\util\JSON;

class MinecraftSyncAcpGroupAddListener implements IParameterizedEventListener
{
    /**
     * Liste der Minecraft Server Gruppen
     * @var array
     */
    protected $minecraftGroups = [];

    /**
     * @inheritDoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!(MINECRAFT_SYNC_ENABLED && MINECRAFT_LINKER_ENABLED && MINECRAFT_SYNC_IDENTITY)) {
            return;
        }
        if (!WCF::getSession()->getPermission('admin.minecraftSync.canManage')) {
            return;
        }

        $this->$eventName($eventObj);
    }

    /**
     * @see \wcf\form\AbstractForm::readFormparameters()
     */
    public function readFormParameters()
    {
        if (isset($_POST['minecraftGroupNames'])) {
            $this->minecraftGroups = $_POST['minecraftGroupNames'];
        }
    }

    /**
     * @see \wcf\form\AbstractForm::validate()
     */
    public function validate(\wcf\acp\form\UserGroupAddForm $eventObj)
    {
        /** @var MinecraftSyncHandler */
        $handler = MinecraftSyncHandler::getInstance();
        $groups = $handler->groupList();
        foreach ($this->minecraftGroups as $minecraftID => $groupNames) {
            try {
                if (!array_key_exists($minecraftID, $groups)) {
                    throw new UserInputException('minecraftGroupNames-' . $minecraftID, 'unknownMinecraftID', ['minecraftID' => $minecraftID]);
                }
                foreach ($groupNames as $groupName) {
                    if (!in_array($groupName, $groups[$minecraftID])) {
                        throw new UserInputException('minecraftGroupNames-' . $minecraftID, 'unknownGroupName', ['minecraftID' => $minecraftID, 'minecraftGroupName' => $groupName]);
                    }
                }
            } catch (UserInputException $e) {
                $eventObj->errorField = $e->getField();
                $eventObj->errorType[$e->getField()] = $e->getType();
                throw $e;
            }
        }
    }

    /**
     * Removes old (now unmanaged) groups from minecraftserver.
     * @see \wcf\form\AbstractForm::save()
     */
    public function save(/** @var UserGroupAddForm */$eventObj)
    {
        if (MINECRAFT_SYNC_ENABLED) {
            // Check weather this is the first sync
            if ($eventObj instanceof UserGroupEditForm) {
                /**
                 * List of old data for 'minecraftGroups'
                 * @var array
                 */
                $oldMinecraftGroups = [];
                try {
                    $oldMinecraftGroups = JSON::decode($eventObj->group->minecraftGroups);
                } catch (SystemException $e) {
                }

                // Setting new 'minecraftGroups'
                $eventObj->additionalFields = array_merge($eventObj->additionalFields, [
                    'minecraftGroups' => JSON::encode($this->minecraftGroups)
                ]);

                /**
                 * List removed 'minecraftGroups'
                 * @var array
                 */
                $diff = [];
                foreach ($oldMinecraftGroups as $minecraftID => $groupNames) {
                    if (array_key_exists($minecraftID, $this->minecraftGroups)) {
                        foreach ($groupNames as $groupName) {
                            if (!in_array($groupName, $this->minecraftGroups[$minecraftID])) {
                                if (isset($diff[$minecraftID][$eventObj->groupID])) {
                                    array_push($diff[$minecraftID][$eventObj->groupID], $groupName);
                                } else {
                                    $diff[$minecraftID][$eventObj->groupID] = [$groupName];
                                }
                            }
                        }
                    } else {
                        $diff[$minecraftID][$eventObj->groupID] = $groupNames;
                    }
                }
                $job = new MinecraftSyncSyncGroupBackgroundJob($eventObj->groupID, $diff);
//                $job->perform();
                BackgroundQueueHandler::getInstance()->enqueueIn($job);
            } else {
                $job = new MinecraftSyncSyncGroupBackgroundJob($eventObj->groupID);
//                $job->perform();
                BackgroundQueueHandler::getInstance()->enqueueIn($job);
            }
        }

        // reset values
        if (!($eventObj instanceof UserGroupEditForm)) {
            $this->minecraftGroups = [];
        }
    }

    /**
     * @see \wcf\form\AbstractForm::assignVariables()
     */
    public function assignVariables(/** @var UserGroupAddForm */$eventObj)
    {
        if (empty($_POST) && $eventObj instanceof UserGroupEditForm) {
            try {
                $this->minecraftGroups = JSON::decode($eventObj->group->minecraftGroups);
            } catch (SystemException $e) {
                // do nothing
            }
        }

        /** @var MinecraftSyncHandler */
        $handler = MinecraftSyncHandler::getInstance();

        // assign variables
        WCF::getTPL()->assign(
            [
                'minecrafts' => $handler->getMinecrafts(),
                'minecraftGroups' => $this->minecraftGroups,
                'minecraftGroupNames' => $handler->groupList()
            ]
        );
    }
}
