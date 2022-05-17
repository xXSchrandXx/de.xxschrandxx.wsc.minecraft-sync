<?php

namespace wcf\system\minecraft;

use wcf\data\user\minecraft\MinecraftUser;

interface IMinecraftSyncHandler
{
    /**
     * Gibt den status des Minecraft-Servers wieder.
     * @param $minecraftID
     * @return array
     */
    public function status(?int $minecraftID = null);

    /**
     * Gibt die Gruppen auf den Minecraft-Server(n) wieder.
     * @return array
     */
    public function groupList(?int $minecraftID = null);

    /**
     * Gibt die Gruppen des Spieler auf den Minecraft-Server(n) wieder.
     * @param $uuid
     * @param $minecraftID
     * @return array
     */
    public function getUserGroups(string $uuid, ?int $minecraftID = null);

    /**
     * Gibt alle Gruppen aller Spieler auf den Minecraft-Server(n) wieder.
     * @param $map strucutre:
     * Array
     * (
     *     $uuid
     * )
     * @param $minecraftID
     * @return array structure:
     * Array
     * (
     *     $minecraftID => Array
     *     (
     *          $i => $groupName
     *     )
     * )
     */
    public function getUsersGroups(array $map, ?int $minecraftID = null);

    /**
     * Fügt den Spieler auf den Minecraft-Server(n) in die Gruppe hinzu.
     * @param $uuid
     * @param $group
     * @param $minecraftID
     * @return array
     */
    public function addUserToGroup(string $uuid, string $group, ?int $minecraftID = null);

    /**
     * Fügt alle Spieler auf den Minecraft-Server(n) in die Gruppen hinzu.
     * @param $map structure:
     * Array
     * (
     *     $minecraftUUID => Array
     *     (
     *         $id => $groupName
     *     )
     * )
     * @param $minecraftID
     * @return array
     */
    public function addUsersToGroups(array $map, ?int $minecraftID = null);

    /**
     * Entfernt den Spieler auf den Minecraft-Server(n) aus der Gruppe.
     * @param $uuid
     * @param $group
     * @param $minecraftID
     * @return array
     */
    public function removeUserFromGroup(string $uuid, string $group, ?int $minecraftID = null);

    /**
     * Entfernt alle Spieler auf den Minecraft-Server(n) aus den Gruppen.
     * @param $map structure:
     * Array
     * (
     *     $minecraftUUID => Array
     *     (
     *         $id => $groupName
     *     )
     * )
     * @param $minecraftID
     * @return array
     */
    public function removeUsersFromGroups(array $map, ?int $minecraftID = null);

    /**
     * Gibt Gruppen an, welche von Minecraft-Sync synchronisiert werden sollen.
     * @return array structure:
     * Array
     * (
     *     $groupID => Array
     *     (
     *         $minecraftID => Array
     *         (
     *             $i => $groupName
     *         )
     *     )
     * )
     */
    public function getWSCGroups();

    /**
     * Synchronisiert die uuid und userID
     * @param $uuid
     * @param $userID
     * @param $removeGroups
     * @return array structure:
     * Array
     * (
     *     'status' => $status
     *     'statusCode' => $statusCode
     * {If run successfully also:}
     *     'added' => Array
     *     (
     *         $minecraftID => Array
     *         (
     *             $i => Array
     *             (
     *                 'status' => $status
     *                 'statusCode' => $statusCode
     *             )
     *         )
     *     )
     *     'removed' => Array
     *     (
     *         $minecraftID => Array
     *         (
     *             $i => Array
     *             (
     *                 'status' => $statusMessage
     *                 'statusCode' => $statusCode
     *             )
     *         )
     *     )
     * )
     */
    public function sync(string $uuid, int $userID, array $removeGroups = []);

    /**
     * Synchronisiert einen MinecraftUser
     * @param $minecraftUser
     * @param $removeGroups
     * @return array structure:
     * Array
     * (
     *     'status' => $status
     *     'statusCode' => $statusCode
     * {If run successfully also:}
     *     'added' => Array
     *     (
     *         $minecraftID => Array
     *         (
     *             $i => Array
     *             (
     *                 'status' => $status
     *                 'statusCode' => $statusCode
     *             )
     *         )
     *     )
     *     'removed' => Array
     *     (
     *         $minecraftID => Array
     *         (
     *             $i => Array
     *             (
     *                 'status' => $statusMessage
     *                 'statusCode' => $statusCode
     *             )
     *         )
     *     )
     * )
     */
    public function syncMinecraftUser(MinecraftUser $minecraftUser, array $removeGroups = []);

    /**
     * Syncronisiert alle MinecraftUser des User mit der gegebenen ID.
     * @param $userID
     * @param $removeGroups
     * @return array structure:
     * Array
     * (
     *     $minecraftUserID => Array
     *     (
     *         'status' => $status
     *         'statusCode' => $statusCode
     * {If run successfully also:}
     *         'added' => Array
     *         (
     *             $minecraftID => Array
     *             (
     *                 $i => Array
     *                 (
     *                     'status' => $status
     *                     'statusCode' => $status
     *                 )
     *             )
     *         )
     *         'removed' => Array
     *         (
     *             $minecraftID => Array
     *             (
     *                 $i => Array
     *                 (
     *                     'status' => $status
     *                     'statusCode' => $statusCode
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function syncUser(int $userID, array $removeGroups = []);

    /**
     * Entfernt einen MinecraftUser.
     * @param $minecraftUser
     */
    public function delete(MinecraftUser $minecraftUser);

    /**
     * Synchronisiert alle MinecraftUser
     * @param $removeGroups
     */
    public function syncAll(array $removeGroups = []);
}
