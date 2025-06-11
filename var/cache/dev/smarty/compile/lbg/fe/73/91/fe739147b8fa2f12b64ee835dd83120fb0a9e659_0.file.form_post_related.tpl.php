<?php
/* Smarty version 4.2.1, created on 2025-06-02 08:49:20
  from '/home/dev.labonnegraine.com/public_html/modules/ybc_blog/views/templates/hook/form_post_related.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683d4970973083_45457970',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fe739147b8fa2f12b64ee835dd83120fb0a9e659' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/modules/ybc_blog/views/templates/hook/form_post_related.tpl',
      1 => 1738070956,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_683d4970973083_45457970 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['is_ps16']->value) {?>
<div class="from_product_post_is16">
    <div class="form-group">
        <label class="control-label col-lg-3" for="form_step6_post_product">
            <span> <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Related posts','mod'=>'ybc_blog'),$_smarty_tpl ) );?>
 </span>
        </label>
        <div class="col-lg-9">
            <div class="col-xl-12 col-lg-12" id="form_step6_post_product_field">
                <div class="search search-with-icon">
                    <input id="form_step6_post_product" name="form_step6_post_product" class="form-control ac_input" placeholder="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Search post by title or ID','mod'=>'ybc_blog'),$_smarty_tpl ) );?>
" type="text" autocomplete="off" />
                    <input id="id_post_product" type="hidden" value="<?php echo intval($_smarty_tpl->tpl_vars['id_product']->value);?>
" />
                </div>
            </div>
            <div id="related-posts"  class="col-xl-12 col-lg-12">
                <?php if ($_smarty_tpl->tpl_vars['selected_posts']->value) {?>
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['selected_posts']->value, 'post');
$_smarty_tpl->tpl_vars['post']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['post']->value) {
$_smarty_tpl->tpl_vars['post']->do_else = false;
?>
                        <div class="related-post related-post-<?php echo intval($_smarty_tpl->tpl_vars['post']->value['id_post']);?>
">
                            <button type="button" class="btn btn-default remove_button" onclick="ybcDelPostRelated(<?php echo intval($_smarty_tpl->tpl_vars['post']->value['id_post']);?>
);" name="<?php echo intval($_smarty_tpl->tpl_vars['post']->value['id_post']);?>
">
                                <i class="icon-remove text-danger"></i>
                            </button>
                            <img src="<?php if ($_smarty_tpl->tpl_vars['post']->value['thumb']) {
echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['post']->value['thumb'],'html','UTF-8' ));
} else {
echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['post']->value['image'],'html','UTF-8' ));
}?>" style="width:32px;" />
                            <a href="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['post']->value['link'],'html','UTF-8' ));?>
" target="_blank">
                                <?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['post']->value['title'],'html','UTF-8' ));?>

                            </a>
                        </div>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                <?php }?>
            </div>
        </div>
    </div>
</div>
<?php } else { ?>
    <div class="form-group mb-4">
        <h2><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Related posts','mod'=>'ybc_blog'),$_smarty_tpl ) );?>
</h2>
        <div class="row">
            <div class="col-xl-12 col-lg-12" id="form_step6_post_product_field">
                <div class="search search-with-icon">
                    <input id="form_step6_post_product" name="form_step6_post_product" class="form-control ac_input" placeholder="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Search post by title or ID','mod'=>'ybc_blog'),$_smarty_tpl ) );?>
" type="text" autocomplete="off" />
                    <input id="id_post_product" type="hidden" value="<?php echo intval($_smarty_tpl->tpl_vars['id_product']->value);?>
" />
                </div>
            </div>
            <div id="related-posts"  class="col-xl-12 col-lg-12">
                <?php if ($_smarty_tpl->tpl_vars['selected_posts']->value) {?>
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['selected_posts']->value, 'post');
$_smarty_tpl->tpl_vars['post']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['post']->value) {
$_smarty_tpl->tpl_vars['post']->do_else = false;
?>
                        <div class="related-post related-post-<?php echo intval($_smarty_tpl->tpl_vars['post']->value['id_post']);?>
">
                            <button type="button" class="btn btn-default remove_button" onclick="ybcDelPostRelated(<?php echo intval($_smarty_tpl->tpl_vars['post']->value['id_post']);?>
);" name="<?php echo intval($_smarty_tpl->tpl_vars['post']->value['id_post']);?>
">
                                <i class="icon-remove text-danger"></i>
                            </button>
                            <img src="<?php if ($_smarty_tpl->tpl_vars['post']->value['thumb']) {
echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['post']->value['thumb'],'html','UTF-8' ));
} else {
echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['post']->value['image'],'html','UTF-8' ));
}?>" style="width:32px;" />
                            <a href="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['post']->value['link'],'html','UTF-8' ));?>
" target="_blank">
                                <?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['post']->value['title'],'html','UTF-8' ));?>

                            </a>
                        </div>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                <?php }?>
            </div>
        </div>
    </div>
<?php }
echo '<script'; ?>
 type="text/javascript">
    var link_search_post ='<?php echo $_smarty_tpl->tpl_vars['link_search_post']->value;?>
';
    var confirm_del_post ='<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Do you want to delete this post?','mod'=>'ybc_blog','js'=>1),$_smarty_tpl ) );?>
';
    var xhr_post;
    
    $(document).ready(function(){
        if($('.from_product_post_is16').length){
            $('#product-informations .panel-footer').before($('.from_product_post_is16').html());
            $('.from_product_post_is16').remove();
        }
        $(document).on('blur','#form_step6_post_product',function(){
            $('.list_posts li.active').removeClass('active');
        });
        $(document).on('keyup','#form_step6_post_product',function(e){
            if((e.keyCode==13 || e.keyCode==38 || e.keyCode==40) && $('.list_posts').length)
            {
                if(e.keyCode==40)
                {
                    if($('.list_posts li.active').length==0)
                    {
                        $('.list_posts li:first').addClass('active');
                    }
                    else
                    {
                        var $li_active = $('.list_posts li.active');
                        $('.list_posts li.active').removeClass('active');
                        if($li_active.next('li').length)
                            $li_active.next('li').addClass('active');
                        else
                            $('.list_posts li:first').addClass('active');
                    }
                }
                if(e.keyCode==38)
                {
                    if($('.list_posts li.active').length==0)
                    {
                        $('.list_posts li:last').addClass('active');
                    }
                    else
                    {
                        var $li_active = $('.list_posts li.active');
                        $('.list_posts li.active').removeClass('active');
                        if($li_active.prev('li').length)
                            $li_active.prev('li').addClass('active');
                        else
                            $('.list_posts li:last').addClass('active');
                    }
                }
                if(e.keyCode==13)
                {
                    $('.list_posts li.active').click();
                }
            }
            else
            {
                if(xhr_post)
                    xhr_post.abort();
                $('#form_step6_post_product').next('.list_posts').remove();
                xhr = $.ajax({
                    type: 'POST',
                    headers: { "cache-control": "no-cache" },
                    url: link_search_post,
                    async: true,
                    cache: false,
                    dataType : "json",
                    data: 'searchPostByQuery=1&q='+$('#form_step6_post_product').val(),
                    success: function(json)
                    {
                        if(json.posts)
                        {
                            var $html ='<ul class="list_posts">';
                            $(json.posts).each(function(){
                                $html +='<li data-id_post="'+this.id_post+'"> <img src="'+(this.thumb ? this.thumb : this.image )+'" style="width:32px;" /> <a href="'+this.link+'" target="_blank">'+this.title+'</a> </li>';
                            });
                            $html +='</ul>';
                            if($('.list_posts').length)
                                $('.list_posts').remove();
                            $('#form_step6_post_product').after($html);
                            $('.list_posts li').hover(function(){ $('.list_posts li.active').removeClass('active'); $(this).addClass('active');});
                        }
                    }
                });
            }
        });
        $(document).on('click','.list_posts a',function(e){
           e.preventDefault();
        });
        $(document).on('click','.list_posts li',function(){
            var id_product = $('#id_post_product').val();
            var id_post = $(this).data('id_post');
            var post_name = $(this).html();
            $.ajax({
                type: 'POST',
                headers: { "cache-control": "no-cache" },
                url: link_search_post,
                async: true,
                cache: false,
                dataType : "json",
                data:'submitAddPostRelatedProduct=1&id_product='+id_product+'&id_post='+id_post,
                success: function(json)
                {
                    if(json.success)
                    {
                        $.growl.notice({ message: json.success });
                        $('#related-posts').append('<div class="related-post related-post-'+id_post+'"><button type="button" class="btn btn-default remove_button" onclick="ybcDelPostRelated('+id_post+');" name="'+id_post+'"><i class="icon-remove text-danger"></i></button>'+post_name+'</div>');
                        $('.list_posts').remove();
                        $('#form_step6_post_product').val('');
                    }
                    if(json.errors)
                        $.growl.error({message:json.errors});
                }
            });
        });
    });
    function ybcDelPostRelated(id_post)
    {
        if(confirm(confirm_del_post))
        {
            var id_product = $('#id_post_product').val();
            $.ajax({
                type: 'POST',
                headers: { "cache-control": "no-cache" },
                url: link_search_post,
                async: true,
                cache: false,
                dataType : "json",
                data:'submitDeletePostProduct=1&id_post='+id_post+'&id_product='+id_product,
                success: function(json)
                {
                    if(json.success)
                    {
                        $.growl.notice({ message: json.success });
                        $('.related-post-'+id_post).remove();
                    }
                    if(json.errors)
                        $.growl.error({message:json.errors});
                }
            });
        }
    }
    
<?php echo '</script'; ?>
><?php }
}
