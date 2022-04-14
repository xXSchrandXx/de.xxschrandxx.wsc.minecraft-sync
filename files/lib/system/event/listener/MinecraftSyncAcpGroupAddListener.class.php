<?php

namespace wcf\system\event\listener;

use wcf\acp\form\UserGroupEditForm;
use wcf\system\background\BackgroundQueueHandler;
use wcf\system\background\job\MinecraftSyncSyncBackgroundJob;
use wcf\system\exception\SystemException;
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
    public function validate(/** @var UserGroupAddForm */$eventObj)
    {
        // TODO validate minecraftGroupNames
    }

    /**
     * @see \wcf\form\AbstractForm::save()
     */
    public function save(/** @var UserGroupAddForm */$eventObj)
    {
        if (MINECRAFT_SYNC_ENABLED) {
            if ($eventObj instanceof UserGroupEditForm) {
                $oldMinecraftGroups = [];
                try {
                    $oldMinecraftGroups = JSON::decode($eventObj->group->minecraftGroups);
                } catch (SystemException $e) {
                }
    
                $eventObj->additionalFields = array_merge($eventObj->additionalFields, [
                    'minecraftGroups' => JSON::encode($this->minecraftGroups)
                ]);

                // TODO add difference between old and new
                wcfDebug($oldMinecraftGroups, $this->minecraftGroups);
                wcfDebug(
                    array_diff_assoc(
                        $oldMinecraftGroups,
                        $this->minecraftGroups
                    )
                );
//                BackgroundQueueHandler::getInstance()->enqueueIn(new MinecraftSyncSyncBackgroundJob(array_diff_assoc($this->oldMinecraftGroups, $this->minecraftGroups)));
            } else {
                BackgroundQueueHandler::getInstance()->enqueueIn(new MinecraftSyncSyncBackgroundJob());
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

        $minecraft = MinecraftSyncHandler::getInstance();

        // assign variables
        WCF::getTPL()->assign(
            [
                'minecrafts' => $minecraft->getMinecrafts(),
                'minecraftGroups' => $this->minecraftGroups,
                'minecraftGroupNames' => $minecraft->groupList()
            ]
        );
    }
}
