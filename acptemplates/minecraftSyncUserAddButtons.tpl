<a href="#" title="{lang}wcf.page.minecraftSyncUserAdd.button.status{/lang}" class="minecraftSyncButton jsToolTips">
	<span class="icon icon16 fa-refresh"></span>
</a>

<script data-relocate="true">
	require(["xXSchrandXx/Minecraft/MinecraftUserSync", "Language"], function(MinecraftUserSync, Language) {
		Language.addObject({
			'wcf.page.minecraftSyncUserAdd.button.status.result': '{lang}wcf.page.minecraftSyncUserAdd.button.status.result{/lang}'
		});
		new MinecraftUserSync.default();
	});
</script>