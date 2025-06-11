<?php
/* Smarty version 4.2.1, created on 2025-05-23 12:15:17
  from '/home/dev.labonnegraine.com/public_html/modules/ybc_blog/views/templates/hook/admin_footer.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_68304ab5aef956_49641056',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd1526bfde5ea7254fc65e3c967da6e992b52abac' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/modules/ybc_blog/views/templates/hook/admin_footer.tpl',
      1 => 1738070956,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68304ab5aef956_49641056 (Smarty_Internal_Template $_smarty_tpl) {
echo '<script'; ?>
 type="text/javascript">
var link_ajax='<?php echo $_smarty_tpl->tpl_vars['link_ajax']->value;?>
';
$(document).ready(function(){
    $.ajax({
        url: link_ajax,
        data: 'action=getCountMessageYbcBlog',
        type: 'post',
        dataType: 'json',                
        success: function(json){ 
            if(parseInt(json.count) >0)
            {
                if($('#subtab-AdminYbcBlogComment span').length)
                    $('#subtab-AdminYbcBlogComment span').append('<span class="count_messages ">'+json.count+'</span>'); 
                else
                    $('#subtab-AdminYbcBlogComment a').append('<span class="count_messages ">'+json.count+'</span>');
            }
            else
            {
                if($('#subtab-AdminYbcBlogComment span').length)
                    $('#subtab-AdminYbcBlogComment span').append('<span class="count_messages hide">'+json.count+'</span>'); 
                else
                    $('#subtab-AdminYbcBlogComment a').append('<span class="count_messages hide">'+json.count+'</span>');
            }
                                                              
        },
    });
});
<?php echo '</script'; ?>
><?php }
}
