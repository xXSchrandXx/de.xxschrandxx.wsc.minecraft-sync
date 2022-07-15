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
    * Liste der alten 'minecraftGroups'
    * @var array
    */
    protected $oldMinecraftGroups = [];

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
        $minecrafts = $handler->getMinecrafts();

        foreach ($minecrafts as /** @var \wcf\data\minecraft\Minecraft **/ $minecraft) {
            try {
                if (!array_key_exists($minecraft->minecraftID, $minecrafts)) {
                    throw new UserInputException('minecraftGroupNames-' . $minecraft->minecraftID, 'unknownMinecraftID', ['minecraftID' => $minecraft->minecraftID]);
                }
            } catch (UserInputException $e) {
                $eventObj->errorField = $e->getField();
                $eventObj->errorType[$e->getField()] = $e->getType();
                throw $e;
            }
        }
    }

    /**
     * Sets oldMinecraftGroups and adds minecraftGroups additionalFields.
     * @see \wcf\form\AbstractForm::save()
     */
    public function save(/** @var UserGroupAddForm */$eventObj)
    {
        // Check weather this is the first sync
        if ($eventObj instanceof UserGroupEditForm) {
            try {
                $this->oldMinecraftGroups = JSON::decode($eventObj->group->minecraftGroups);
            } catch (SystemException $e) {
            }

            // Setting new 'minecraftGroups'
            $eventObj->additionalFields = array_merge($eventObj->additionalFields, [
                'minecraftGroups' => JSON::encode($this->minecraftGroups)
            ]);
        }
    }

    /**
     * Synchronises new groups on minecraftserver.
     * @see \wcf\form\AbstractForm::saved()
     */
    public function saved(/** @var UserGroupAddForm */$eventObj)
    {
        /**
        * List removed 'minecraftGroups'
        * @var array
        */
        $diff = [];
        foreach ($this->oldMinecraftGroups as $minecraftID => $groupNames) {
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
//        $job->perform();
        BackgroundQueueHandler::getInstance()->enqueueIn($job);

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
            }
        }

        /** @var MinecraftSyncHandler */
        $handler = MinecraftSyncHandler::getInstance();
        $groupList = [];

        foreach ($handler->getMinecrafts() as $minecraft) {
            $groupList[$minecraft->minecraftID] = \unserialize($minecraft->groups);
        }

        // assign variables
        WCF::getTPL()->assign(
            [
                'minecrafts' => $handler->getMinecrafts(),
                'minecraftGroups' => $this->minecraftGroups,
                'minecraftGroupNames' => $groupList
            ]
        );
    }
}
