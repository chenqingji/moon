{if isset($rows)}
{foreach from=$rows item=row}
<!-- item -->                                
<article class="item" data-href="#" data-log="#" data-gameid="{$row.id}" data-package>
	<div class="licon">
		<!--<a href="#"><button style="display: block; width: 100%; border: none;" class="go_app"><img src="{$row.banner}"  data-target="getkey"></button></a>-->
		<a href="#" style="border: none;background:initial;"><button style="display: block; width: 100%; border: none; background:initial;" class="go_app"><img {if time()>$row['codeOpenTime']}src="{$bigimage}gift-bigimage-{$row['id']}.png"{else}src="{$bigimage}gift-bigimage-open-{$row['id']}.png"{/if}  data-target="getkey"></button></a>
	</div>
</article>                                
{/foreach}
{/if}