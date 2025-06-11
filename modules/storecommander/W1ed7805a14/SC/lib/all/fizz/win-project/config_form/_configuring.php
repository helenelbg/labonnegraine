<script type="text/javascript" src="<?php echo SC_JQUERY; ?>"></script>

<center class="configuring" style="cursor: pointer;">
    <img src="lib/img/configuring.png?1" alt="" width="150px" />
    <br/> <br/>
</center>
<button class="btn center big configuring"><?php echo _l('Configure this project'); ?></button>

<script>
    $( ".configuring" ).on( "click", function() {
        $.post('index.php?ajax=1&act=all_fizz_win-project_update',
            {
                'id_project': "<?php echo $id_project; ?>",
                'action': 'configuring'
            },
            function(data){
                parent.ESPloadConfig();
            });
    });

</script>