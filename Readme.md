Languages: [German](#----german) | [English](#----english)
<h1>
    English
</h1>
<h2>
    General
</h2>
<h3>
    Plugin description
</h3>
<p>This plugin is an interface between other plugins and the <a href="https://pluginstore.woltlab.com/file/7077-minecraft-api/">Minecraft-API</a>.</p>
<p>Minecraft Linker lets users link their Minecraft UUID (s) to their WSC account.</p>
<p>It does not matter whether the Minecraft server is online or offline. The user will be sent a confirmation code on the Minecraft server with which they can unlock themselves in the WSC.</p>
<h3>
    Requirements
</h3>
<p>This plugin requires <a href="https://pluginstore.woltlab.com/file/7077-minecraft-api/">Minecraft-API</a> (Included in the package).</p>
<h3>
    Links
</h3>
<p>Github: <a href="https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-linker">xXSchrandXx/de.xxschrandxx.wsc.minecraft-linker</a></p>
<p>Minecraft-API: <a href="https://pluginstore.woltlab.com/file/7077-minecraft-api/">Minecraft-API</a></p>
<p>Minecraft-Linker Spigot: <a href="https://www.spigotmc.org/resources/minecraft-linker.97871/">Minecraft-Linker Spigot</a></p>
<h2>
    API-Usage
</h2>

```XML
<eventlistener name="MyEventExample">
    <eventclassname>wcf\data\user\minecraft\MinecraftAction</eventclassname>
    <eventname>finalizeAction</eventname>
    <listenerclassname>wcf\system\event\listener\MyEventListenerExample</listenerclassname>
</eventlistener>
```

```PHP
<?php
namespace wcf\system\event\listener;

class MyEventListenerExample implements IParameterizedEventListener {
    public function execute($eventObj, $className, $eventName, array &$parameters) {
        $action = $eventObj->action;
        if ($action = 'create') {
            $minecraft = $eventObj->parameters['data'];
            // Do stuff
        } else if ($action = 'delete') {
            foreach ($eventObj->getObjects() as $minecraft) {
                // Do stuff
            }
        }
    }
}
```
<h1>
    German
</h1>
<h2>
    Allgemeines
</h2>
<h3>
    Plugin-Beschreibung
</h3>
<p>Dieses Plugin ist eine Schnittstelle zwischen anderen Plugins und der <a href="https://pluginstore.woltlab.com/file/7077-minecraft-api/">Minecraft-API</a>.</p>
<p>Minecraft-Linker lässt Benutzer Ihre Minecraft-UUID(s) mit Ihrem WSC-Account verknüpfen.</p>
<p>Dabei ist es egal, ob online oder offline Minecraft-Server. Dem Benutzer wird ein Bestätigungscode auf dem Minecraft-Server zugeschickt mit welchem er sich im WSC freischaltet.</p>
<h3>
    Voraussetzungen
</h3>
<p>Dieses Plugin benötigt <a href="https://pluginstore.woltlab.com/file/7077-minecraft-api/">Minecraft-API</a> (Im Packet mit enthalten).</p>
<p>Um BungeeCord oder Spigot Features nutzen zu können, benötigt ihr das Spigot-Plugin: Minecraft-Linker.</p>
<h3>
    Links
</h3>
<p>Minecraft-API: <a href="https://pluginstore.woltlab.com/file/7077-minecraft-api/">Minecraft-API</a></p>
<p>Minecraft-Linker Spigot: <a href="https://www.spigotmc.org/resources/minecraft-linker.97871/">Minecraft-Linker Spigot</a></p>
<h2>
    API-Gebrauch
</h2>

```XML
<eventlistener name="MyEventExample">
    <eventclassname>wcf\data\user\minecraft\MinecraftAction</eventclassname>
    <eventname>finalizeAction</eventname>
    <listenerclassname>wcf\system\event\listener\MyEventListenerExample</listenerclassname>
</eventlistener>
```

```PHP
<?php
namespace wcf\system\event\listener;

class MyEventListenerExample implements IParameterizedEventListener {
    public function execute($eventObj, $className, $eventName, array &$parameters) {
        $action = $eventObj->action;
        if ($action = 'create') {
            $minecraft = $eventObj->parameters['data'];
            // Sachen machen
        } else if ($action = 'delete') {
            foreach ($eventObj->getObjects() as $minecraft) {
                // Sachen machen
            }
        }
    }
}
```