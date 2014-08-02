<div id="bskfacebookbox">
    {if $fbpage}
    <div class="fb-like-box" 
         data-href="https://www.facebook.com/{$fbpage}" 
         data-width="{$width}" 
         data-height="{$height}" 
         data-colorscheme="{$colorscheme}"
         data-show-faces="{if $show_faces}true{else}false{/if}" 
         data-stream="{if $show_stream}true{else}false{/if}" 
         data-show-border="{if $show_border}true{else}false{/if}" 
         data-header="{if $show_header}true{else}false{/if}"></div>
    {else}
    <div class="alert alert-warning">There is no page name set for the facebook box!</div>    
    {/if}
</div>