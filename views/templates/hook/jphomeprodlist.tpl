{function descr}
    <div class="jphpl-descr span5">
        <h3>{$p.name|escape:'html':'UTF-8'}</h3>
        <p>{$p.description|escape:'UTF-8'}</p>
        <a class='jphpl-btn' href='{$p.link|escape:'html'}'>{l s='Check in store' mod='jphomeprodlist'}</a>
    </div>
{/function}

<div id='jphomeprodlist'>
    <h2 class="jphpl-title">
        {l s='New' mod='jphomeprodlist'}
        <span class='jphpl-title-strong'>{l s='products' mod='jphomeprodlist'}</span>
    </h2>
    {foreach $products as $p}
        <div class="jphpl-product-row row-fluid">
            {if $p@iteration is even by 1}{call descr}{/if}
{*            <div class="jphpl-images text-{if $p@iteration is even by 1}right{else}left{/if} span7">*}
            <div class="jphpl-images text-center span7">
                <img class='auto-crop' src="{$link->getImageLink($p.link_rewrite, $p.id_image, 'jphomeprodlist')|escape:'html'}" />
                {*                     height="{$homeSize.height}" width="{$homeSize.width}" alt="{$p.name|escape:html:'UTF-8'}" />*}
            </div>
            {if $p@iteration is odd by 1}{call descr}{/if}
        </div>
    {/foreach}
</div>