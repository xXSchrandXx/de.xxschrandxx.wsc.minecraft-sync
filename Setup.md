# Setting up Minecraft-Sync
[German](#German) | [English](#English)

## English
1. Install and configure [Minecraft-Linker](https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-linker/blob/main/Setup.md) and all required packages and plugins.
2. Install [Minecraft-Sync](https://www.woltlab.com/pluginstore/file/7199-minecraft-sync/) via the store code, via the package search or as a file upload.
3. Configure Minecraft Sync in the ACP under `Configuration > Options > Minecraft > Minecraft Sync`. (A server must be activated in the selection for Minecraft Sync to work.)
5. Remember the URL for step 9.
6. Download [WSC-Sync](https://www.spigotmc.org/resources/wsc-minecraft-sync.105308/) and put it in your `plugins` folder.
7. Restart your server.
8. Open the `config.yml` in the `wscsync-{bungee/bukkit}` folder.
9. Set `url` to the URL from step 5.
10. Change `plugin` to your permission plugin.
List of supported [permission plugins](https://github.com/xXSchrandXx/WSC-Minecraft-Sync/blob/main/src/main/java/de/xxschrandxx/wsc/wscsync/core/api/permission/PermissionPlugin.java).
11. Restart your server.
12. Configure your user groups under `Users > User groups > User groups > {user group entry}`. Modify the list under the `Minecraft Sync` section.

## German
1. Installieren und konfigurieren Sie [Minecraft-Linker](https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-linker/blob/main/Setup.md) und alle erforderlichen Pakete und Plugins.
2. Installieren Sie [Minecraft-Sync](https://www.woltlab.com/pluginstore/file/7199-minecraft-sync/) über den Store-Code, über die Paketsuche oder als Datei-Upload.
3. Konfigurieren Sie Minecraft Sync im ACP unter `Konfiguration > Optionen > Minecraft > Minecraft Sync`. (Damit Minecraft Sync funktioniert, muss ein Server in der Auswahl aktiviert sein.)
5. Merken Sie sich die URL für Schritt 9.
6. Laden Sie [WSC-Sync](https://www.spigotmc.org/resources/wsc-minecraft-sync.105308/) herunter und legen Sie es in Ihrem Ordner `plugins` ab.
7. Starten Sie Ihren Server neu.
8. Öffnen Sie die Datei `config.yml` im Ordner `wscsync-{bungee/bukkit}`.
9. Setzen Sie `url` auf die URL aus Schritt 5.
10. Ändern Sie `plugin` in Ihr Berechtigungs-Plugin.
Liste der unterstützten [Berechtigungs-Plugins](https://github.com/xXSchrandXx/WSC-Minecraft-Sync/blob/main/src/main/java/de/xxschrandxx/wsc/wscsync/core/api/permission/PermissionPlugin.java).
11. Starten Sie Ihren Server neu.
12. Konfigurieren Sie Ihre Benutzergruppen unter `Benutzer > Benutzergruppen > Benutzergruppen > {Benutzergruppeneintrag}`. Ändern Sie die Liste im Abschnitt `Minecraft Sync`.