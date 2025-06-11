<?php

$errors = false;
?>
<style type="text/css">
    .btn {
        background: linear-gradient(#e2efff, #d3e7ff) repeat scroll 0 0 rgba(0, 0, 0, 0);
        border: 1px solid #a4bed4;
        color: #34404b;
        font-size: 11px;
        height: 27px;
        overflow: hidden;
        position: relative;
        font-weight: bold;
        cursor: pointer;
        float: right;
        margin-top: 6px;
    }

    div {
        font-family: 'Arial,sans-serif' : 'Tahoma';
        font-size: 14px !important;
    }


    #body {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-template-rows: repeat(2, 1fr);
        grid-column-gap: 30px;
        grid-row-gap: 30px;
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
    }

    span.title{
        display:block;
        min-height: 40px;
    }

    #synch {
        grid-area: 1 / 1 / 2 / 2;
    }

    #shop {
        grid-area: 1 / 2 / 2 / 3;
    }

    #footer {
        grid-area: 3 /2 / 3 / 3;
        vertical-align: bottom;
        text-align: right;
    }
</style>

<form id="body" action=" index.php?ajax=1&act=cat_win-categorysynch_confirm&id_lang=<?php echo $sc_agent->id_lang; ?>"
      method="post">

    <?php
    $id_shop_selected = SCI::getSelectedShop();

    $checked_shops = SCI::getSelectedShopActionList();

    $shops = Shop::getShops(false);

    ?>
    <div id="synch">
        <span class="title"><strong><?php echo _l('You want to synchronize the categories positions on the Shop')._l(':'); ?></strong></span>
        <?php
        foreach ($shops as $shop)
        {
            $checked = '';
            if ($shop['id_shop'] == $id_shop_selected)
            {
                $checked = 'checked';
            }

            echo '<input type="radio" name="selected_shop" value="'.$shop['id_shop'].'" '.$checked.' /> '.$shop['name'].'<br/>';
        }
        ?>
    </div>
    <div id="shop">
        <span class="title"><strong><?php echo _l('With Shop(s)')._l(':'); ?></strong></span>
        <?php
        foreach ($shops as $shop)
        {
            $checked = '';
            if (sc_in_array($shop['id_shop'], $checked_shops, 'catWinCategSynchChoice_shops'))
            {
                $checked = 'checked';
            }

            echo '<input type="checkbox" name="checked_shops[]" value="'.$shop['id_shop'].'" '.$checked.' /> '.$shop['name'].'<br/>';
        }
        ?>
    </div>

    <div id="footer">
        <?php if (!$errors) { ?>
            <button class="btn" type="submit"><?php echo _l('Next step'); ?></button>
        <?php } ?>
    </div>

</form>