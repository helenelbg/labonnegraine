{**
 * Scalapay
 *
 * @author Scalapay Plugin Integration Team
 * @copyright 2022 Scalapay
 * @license LICENCE.md for license details.
 *}

{if $scalapayMessage}

    {if !empty($scalapayMessage["success"])}
        <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {$scalapayMessage["success"]}
        </div>
    {/if}
    {if !empty($scalapayMessage["error"])}
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {$scalapayMessage["error"]}
        </div>
    {/if}
{/if}
