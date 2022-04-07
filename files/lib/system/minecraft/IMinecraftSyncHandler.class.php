<?php

namespace wcf\system\minecraft;

use wcf\data\user\minecraft\MinecraftUser;

interface IMinecraftSyncHandler
{
    /**
     * Gibt den status des Minecraft-Servers wieder.
     * If a {@link GuzzleException} happens or a {@link JSON#decode($json, $asArray = true)} throws a {@link SystemException} the response will be false.
     * @param $minecraftID
     * @return array
     */
    public function status(?int $minecraftID = null);

    /**
     * Gibt die Gruppen auf den Minecraft-Server(n) wieder.
     * If a {@link GuzzleException} happens or a {@link JSON#decode($json, $asArray = true)} throws a {@link SystemException} the response will be false.
     * @return array
     */
    public function groupList(?int $minecraftID = null);

    /**
     * Gibt die Gruppen des Spieler auf den Minecraft-Server(n) wieder.
     * If a {@link GuzzleException} happens or a {@link JSON#decode($json, $asArray = true)} throws a {@link SystemException} the response will be false.
     * @param $uuid
     * @return array|false
     */
    public function getUserGroups(string $uuid, ?int $minecraftID = null);

    /**
     * TODO
     */
    public function getUsersGroups(array $map, ?int $minecraftID = null);

    /**
     * Fügt den Spieler auf den Minecraft-Server(n) in die Gruppe hinzu..
     * @param $uuid
     * @param $group
     * @return array
     */
    public function addUserToGroup(string $uuid, string $group, ?int $minecraftID = null);

    /**
     * TODO
     */
    public function addUsersToGroups(array $map, ?int $minecraftID = null);

    /**
     * Entfernt den Spieler auf den Minecraft-Server(n) aus der Gruppe.
     * @param $uuid
     * @param $group
     * @return array
     */
    public function removeUserFromGroup(string $uuid, string $group, ?int $minecraftID = null);

    /**
     * TODO
     */
    public function removeUsersFromGroups(array $map, ?int $minecraftID = null);

    /**
     * Gibt Gruppen an, welche von Minecraft-Sync synchronisiert werden sollen.
     * @return array
     */
    public function getWSCGroups();

    /**
     * Synchronisiert einen MinecraftUser
     * @param $minecraftUser
     */
    public function sync(MinecraftUser $minecraftUser);

    /**
     * TODO
     */
    public function syncAll();
}
