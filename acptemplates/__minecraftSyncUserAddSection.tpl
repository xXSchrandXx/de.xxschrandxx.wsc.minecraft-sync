{if $action == 'edit' && $__wcf->getSession()->getPermission('admin.minecraftSync.canManage') && MINECRAFT_SYNC_ENABLED && MINECRAFT_SYNC_IDENTITY}


	<section class="section">
		<h2 class="sectionTitle">{lang}wcf.page.userAddSection.minecraftSync.sectionTitle{/lang}</h2>
		{if $minecraftUsers|count > 0}
			<div class="jsObjectActionContainer" data-object-action-class-name="wcf\data\user\minecraft\MinecraftSyncAction">
				{foreach from=$minecraftUsers item=minecraftUser}
					<a class="minecraftSyncButton" data-object-id="{$minecraftUser->minecraftUserID}">
						<span class="icon icon24 fa-wifi"></span>
					</a>
					{event name='rowButtons'}
				{/foreach}
			</div>
		{else}
			<p class="info">{lang}wcf.global.noItems{/lang}</p>
		{/if}
	</section>

	<script data-relocate="true">
		require(["xXSchrandXx/Minecraft/MinecraftSync"], function(MinecraftSync) {
			new MinecraftSync.default();
		});
	</script>
{/if}