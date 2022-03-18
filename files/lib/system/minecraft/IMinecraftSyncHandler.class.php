<?php

namespace wcf\system\minecraft;

interface IMinecraftSyncHandler
{
    /**
     * Konstruiert die Klasse
     * @param $minecraftID
     */
    public function __construct(int $minecraftID);

    /**
     * Gibt die Gruppen auf dem Minecraft-Server wieder.
     * @return array
     */
    public function getGroups();

    /**
     * Gibt die Gruppen des Spieler auf dem Minecraft-Server wieder.
     * @param $uuid
     * @return array
     */
    public function getPlayerGroups(string $uuid);

    /**
     * Fügt den Spieler auf dem Minecraft-Server in die Gruppe hinzu.
     * @param $uuid
     * @param $group
     * @return array
     */
    public function addPlayerToGroup(string $uuid, string $group);

    /**
     * Entfernt den Spieler auf dem Minecraft-Server aus der Gruppe.
     * @param $uuid
     * @param $group
     * @return array
     */
    public function removePlayerFromGroup(string $uuid, string $group);
}
