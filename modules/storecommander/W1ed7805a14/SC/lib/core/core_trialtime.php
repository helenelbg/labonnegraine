<?php

?>
<script>
    var wTrialWindow = null;

    if (!dhxWins.isWindow("wTrialWindow"))
    {
        wTrialWindow = dhxWins.createWindow("wTrialWindow", 50, 50, 670, 550);
        wTrialWindow.setText('<?php echo _l('Your Trial period information', 1); ?>');
    }


    wTrialWindow.attachURL("index.php?ajax=1&act=all_gettrialtime&id_lang="+SC_ID_LANG+"&"+new Date().getTime());

</script>