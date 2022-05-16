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
* [xXSchrandXx/de.xxschrandxx.wsc.minecraft-api](https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-api)
* [xXSchrandXx/de.xxschrandxx.wsc.minecraft-linker](https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-linker)
* [xXSchrandXx/de.xxschrandxx.wsc.minecraft-sync](https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-sync)
* [xXSchrandXx/de.xxschrandxx.wsc.minecraft-profile](https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-profile)
* [xXSchrandXx/WSC-Minecraft-Bridge](https://github.com/xXSchrandXx/WSC-Minecraft-Bridge)
* [xXSchrandXx/WSC-Minecraft-Authenticator](https://github.com/xXSchrandXx/WSC-Minecraft-Authenticator)

## WoltLab
* [Plugin-Store/Minecraft-API](https://www.woltlab.com/pluginstore/file/7077-minecraft-api/)
* [Plugin-Store/Minecraft-Linker](https://www.woltlab.com/pluginstore/file/7093-minecraft-linker/)
## Spigot
* [Resources/WSC-Minecraft-Bridge](https://www.spigotmc.org/resources/wsc-minecraft-bridge.100716/)
* [Resources/WSC-Minecraft-Authenticator](https://www.spigotmc.org/resources/wsc-minecraft-authenticator.101169/)
## Donate
* [PayPal](https://www.paypal.com/donate/?hosted_button_id=RFYYT7QSAU7YJ)
## JavaDocs
* [Docs/wscbridge](https://maven.gamestrike.de/docs/wscbridge/)
* [Docs/wscauthenticator](https://maven.gamestrike.de/docs/wscauthenticator/)
## Maven
```XML
<repository>
	<id>schrand-repo</id>
	<url>https://maven.gamestrike.de/mvn/</url>
</repository>
```