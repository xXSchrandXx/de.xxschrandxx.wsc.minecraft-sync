Quicklinks: [General](#general) | [Links](#links) | [License](https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-sync/blob/main/LICENSE)

"Minecraft"™ is a trademark of Mojang Synergies AB. This Resource ist not affiliate with Mojang.

# General
## Description
This plugin syncs groups between WoltLab and Minecraft.
## Requirements
* [Minecraft-API](#links) installed and configured on WoltLab.
* [Supported Permission Plugin](#supported-permission-plugins) installed on [WSC-Minecraft-Bridge](#links)

# Supported permission plugins
You can find a list of supported plugins in the [PermissionPlugin enum](https://github.com/xXSchrandXx/WSC-Minecraft-Bridge/blob/main/src/main/java/de/xxschrandxx/wsc/core/permission/PermissionPlugin.java).
Create an [Issue](https://github.com/xXSchrandXx/WSC-Minecraft-Bridge/issues/new) to request new permission plugins.

# Installation
1. Install and configure [Minecraft-Linker](#links).
2. Install and configure [WSC-Minecraft-Bridge](#links). Enable the permission module.
3. Select the servers to sync groups on.
4. Set groups to set on your minecraft servers.

# Links
## GitHub
* [xXSchrandXx/de.xxschrarndxx.wsc.minecraft-api](https://github.com/xXSchrandXx/de.xxschrarndxx.wsc.minecraft-api)
* [xXSchrandXx/WSC-Minecraft-Bridge](https://github.com/xXSchrandXx/WSC-Minecraft-Bridge)
* [xXSchrandXx/de.xxschrandxx.wsc.minecraft-linker](https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-linker)
* [xXSchrandXx/WSC-Minecraft-Linker](https://github.com/xXSchrandXx/WSC-Minecraft-Linker)
* [xXSchrandXx/de.xxschrandxx.wsc.minecraft-sync](https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-sync)
* [xXSchrandXx/WSC-Minecraft-Sync](https://github.com/xXSchrandXx/WSC-Minecraft-Sync)
* [xXSchrandXx/de.xxschrandxx.wsc.minecraft-authenticator](https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-authenticator)
* [xXSchrandXx/WSC-Minecraft-Authenticator](https://github.com/xXSchrandXx/WSC-Minecraft-Authenticator)
* ([xXSchrandXx/de.xxschrandxx.wsc.minecraft-profile](https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-profile))
## WoltLab
* [Plugin-Store/Minecraft-API](https://www.woltlab.com/pluginstore/file/7077-minecraft-api/)
* [Plugin-Store/Minecraft-Linker](https://www.woltlab.com/pluginstore/file/7093-minecraft-linker/)
* [Plugin-Store/Minecraft-Sync](https://www.woltlab.com/pluginstore/file/7199-minecraft-sync/)
* [Plugin-Store/Minecraft-Authenticator](https://www.woltlab.com/pluginstore/file/7245-minecraft-authenticator/)
* [Plugin-Store/Minecraft-JCoins](https://www.woltlab.com/pluginstore/file/7261-minecraft-jcoins/)
## Spigot
* [Resources/WSC-Minecraft-Bridge](https://www.spigotmc.org/resources/wsc-minecraft-bridge.100716/)
* [Resources/WSC-Minecraft-Linker](https://www.spigotmc.org/resources/wsc-minecraft-linker.105307/)
* [Resources/WSC-Minecraft-Sync](https://www.spigotmc.org/resources/wsc-minecraft-sync.105308/)
* [Resources/WSC-Minecraft-Authenticator](https://www.spigotmc.org/resources/wsc-minecraft-authenticator.101169/)
* [Resources/WSC-Minecraft-JCoins](https://www.spigotmc.org/resources/wsc-minecraft-jcoins.104632/)
## Donate
* [PayPal](https://www.paypal.com/donate/?hosted_button_id=RFYYT7QSAU7YJ)
## JavaDocs
* [Docs/wscbridge](https://maven.gamestrike.de/docs/wscbridge/)
* [Docs/wsclinker](https://maven.gamestrike.de/docs/wsclinker/)
* [Docs/wscsync](https://maven.gamestrike.de/docs/wscsync/)
* [Docs/wscauthenticator](https://maven.gamestrike.de/docs/wscauthenticator/)
* [Docs/wscjcoins](https://maven.gamestrike.de/docs/wscjcoins/)
## Maven
```XML
<repository>
	<id>schrand-repo</id>
	<url>https://maven.gamestrike.de/mvn/</url>
</repository>
```