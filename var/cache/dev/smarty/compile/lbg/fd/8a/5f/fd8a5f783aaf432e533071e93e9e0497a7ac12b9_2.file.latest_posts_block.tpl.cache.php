<?php
/* Smarty version 4.2.1, created on 2025-06-02 08:50:56
  from '/home/dev.labonnegraine.com/public_html/modules/ybc_blog/views/templates/hook/latest_posts_block.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.2.1',
  'unifunc' => 'content_683d49d05314e5_66305266',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fd8a5f783aaf432e533071e93e9e0497a7ac12b9' => 
    array (
      0 => '/home/dev.labonnegraine.com/public_html/modules/ybc_blog/views/templates/hook/latest_posts_block.tpl',
      1 => 1738070956,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_683d49d05314e5_66305266 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->compiled->nocache_hash = '844173117683d49d0522430_75960535';
if ($_smarty_tpl->tpl_vars['posts']->value) {?>
    <div class="block ybc_block_latest <?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['blog_config']->value['YBC_BLOG_RTL_CLASS'],'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
 <?php if ((isset($_smarty_tpl->tpl_vars['blog_page']->value)) && $_smarty_tpl->tpl_vars['blog_page']->value) {?>page_<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['blog_page']->value,'html','UTF-8' )), ENT_QUOTES, 'UTF-8');
} else { ?>page_blog<?php }?> <?php if ((isset($_smarty_tpl->tpl_vars['blog_page']->value)) && $_smarty_tpl->tpl_vars['blog_page']->value == 'home') {
if ((isset($_smarty_tpl->tpl_vars['blog_config']->value['YBC_BLOG_HOME_POST_TYPE'])) && $_smarty_tpl->tpl_vars['blog_config']->value['YBC_BLOG_HOME_POST_TYPE'] == 'default' || count($_smarty_tpl->tpl_vars['posts']->value) <= 1) {?> ybc_block_default<?php } else { ?> ybc_block_default<?php }
} else {
if ((isset($_smarty_tpl->tpl_vars['blog_config']->value['YBC_BLOG_SIDEBAR_POST_TYPE'])) && $_smarty_tpl->tpl_vars['blog_config']->value['YBC_BLOG_SIDEBAR_POST_TYPE'] == 'default' || count($_smarty_tpl->tpl_vars['posts']->value) <= 1) {?> ybc_block_default<?php } else { ?> ybc_block_default<?php }
}?>">
        <h4 class="title_blog title_block"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Latest posts','mod'=>'ybc_blog'),$_smarty_tpl ) );?>
</h4>
        <?php if ((isset($_smarty_tpl->tpl_vars['blog_config']->value['YBC_BLOG_HOME_PER_ROW'])) && $_smarty_tpl->tpl_vars['blog_config']->value['YBC_BLOG_HOME_PER_ROW']) {?>
            <?php $_smarty_tpl->_assignInScope('product_row', intval($_smarty_tpl->tpl_vars['blog_config']->value['YBC_BLOG_HOME_PER_ROW']));?>
        <?php } else { ?>
            <?php $_smarty_tpl->_assignInScope('product_row', 4);?>
        <?php }?>
        <div class="block_content row">
            <div class="ybc_blog_content_block <?php if (count($_smarty_tpl->tpl_vars['posts']->value) > 1) {
if ((isset($_smarty_tpl->tpl_vars['blog_page']->value)) && $_smarty_tpl->tpl_vars['blog_page']->value == 'home' && $_smarty_tpl->tpl_vars['blog_config']->value['YBC_BLOG_HOME_POST_TYPE'] != 'default') {?>blog_type_slider<?php } elseif ((!(isset($_smarty_tpl->tpl_vars['blog_page']->value)) || ((isset($_smarty_tpl->tpl_vars['blog_page']->value)) && $_smarty_tpl->tpl_vars['blog_page']->value != 'home')) && $_smarty_tpl->tpl_vars['blog_config']->value['YBC_BLOG_SIDEBAR_POST_TYPE'] != 'default') {?>blog_type_slider<?php }
}?>">
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['posts']->value, 'post');
$_smarty_tpl->tpl_vars['post']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['post']->value) {
$_smarty_tpl->tpl_vars['post']->do_else = false;
?>
                    <div class="ybc_blog_content_block_item<?php if ($_smarty_tpl->tpl_vars['blog_page']->value == 'home') {?> col-xs-12 col-sm-4 col-lg-<?php echo htmlspecialchars((string) 12/intval($_smarty_tpl->tpl_vars['product_row']->value), ENT_QUOTES, 'UTF-8');
}?>">
                                                <div class="ybc-blog-latest-post-content">
                            <a class="ybc_title_block" href="<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['post']->value['link'],'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
">> <?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['post']->value['title'],'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
</a>
                            <div class="ybc-blog-sidear-post-meta">
                                <?php if ((isset($_smarty_tpl->tpl_vars['post']->value['categories'])) && $_smarty_tpl->tpl_vars['post']->value['categories']) {?>
                                    <div class="ybc-blog-categories">
                                        <?php $_smarty_tpl->_assignInScope('ik', 0);?>
                                        <?php $_smarty_tpl->_assignInScope('totalCat', count($_smarty_tpl->tpl_vars['post']->value['categories']));?>                        
                                        <div class="be-categories">
                                            <span class="be-label"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Posted in','mod'=>'ybc_blog'),$_smarty_tpl ) );?>
: </span>
                                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['post']->value['categories'], 'cat');
$_smarty_tpl->tpl_vars['cat']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['cat']->value) {
$_smarty_tpl->tpl_vars['cat']->do_else = false;
?>
                                                <?php $_smarty_tpl->_assignInScope('ik', $_smarty_tpl->tpl_vars['ik']->value+1);?>                                        
                                                <a href="<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['cat']->value['link'],'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( ucfirst($_smarty_tpl->tpl_vars['cat']->value['title']),'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
</a><?php if ($_smarty_tpl->tpl_vars['ik']->value < $_smarty_tpl->tpl_vars['totalCat']->value) {?><span class="comma">, </span><?php }?>
                                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                        </div>
                                    </div>
                                <?php }?>
                                <span class="post-date"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0], array( array('date'=>$_smarty_tpl->tpl_vars['post']->value['datetime_added'],'full'=>0),$_smarty_tpl ) );?>
</span>
                            </div>
                            <?php if ($_smarty_tpl->tpl_vars['allowComments']->value || $_smarty_tpl->tpl_vars['show_views']->value || $_smarty_tpl->tpl_vars['allow_like']->value) {?> 
                                <div class="ybc-blog-latest-toolbar">
                                    <?php if ($_smarty_tpl->tpl_vars['show_views']->value) {?>
                                        <span class="ybc-blog-latest-toolbar-views"><i class="ets_svg">
                                                                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1664 960q-152-236-381-353 61 104 61 225 0 185-131.5 316.5t-316.5 131.5-316.5-131.5-131.5-316.5q0-121 61-225-229 117-381 353 133 205 333.5 326.5t434.5 121.5 434.5-121.5 333.5-326.5zm-720-384q0-20-14-34t-34-14q-125 0-214.5 89.5t-89.5 214.5q0 20 14 34t34 14 34-14 14-34q0-86 61-147t147-61q20 0 34-14t14-34zm848 384q0 34-20 69-140 230-376.5 368.5t-499.5 138.5-499.5-139-376.5-368q-20-35-20-69t20-69q140-229 376.5-368t499.5-139 499.5 139 376.5 368q20 35 20 69z"/></svg>
                                                            </i> <?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['post']->value['click_number']), ENT_QUOTES, 'UTF-8');?>

                                            <?php if ($_smarty_tpl->tpl_vars['post']->value['click_number'] != 1) {
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'views','mod'=>'ybc_blog'),$_smarty_tpl ) );
} else {
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'view','mod'=>'ybc_blog'),$_smarty_tpl ) );
}?></span>
                                    <?php }?>   
                                    <?php if ($_smarty_tpl->tpl_vars['allowComments']->value && $_smarty_tpl->tpl_vars['post']->value['comments_num'] > 0) {?>
                                        <span class="ybc-blog-latest-toolbar-comments"><i class="ets_svg"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M896 384q-204 0-381.5 69.5t-282 187.5-104.5 255q0 112 71.5 213.5t201.5 175.5l87 50-27 96q-24 91-70 172 152-63 275-171l43-38 57 6q69 8 130 8 204 0 381.5-69.5t282-187.5 104.5-255-104.5-255-282-187.5-381.5-69.5zm896 512q0 174-120 321.5t-326 233-450 85.5q-70 0-145-8-198 175-460 242-49 14-114 22h-5q-15 0-27-10.5t-16-27.5v-1q-3-4-.5-12t2-10 4.5-9.5l6-9 7-8.5 8-9q7-8 31-34.5t34.5-38 31-39.5 32.5-51 27-59 26-76q-157-89-247.5-220t-90.5-281q0-174 120-321.5t326-233 450-85.5 450 85.5 326 233 120 321.5z"/></svg>
                                                                </i> <?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['post']->value['comments_num']), ENT_QUOTES, 'UTF-8');?>

                                            <?php if ($_smarty_tpl->tpl_vars['post']->value['comments_num'] != 1) {
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'comments','mod'=>'ybc_blog'),$_smarty_tpl ) );
} else {
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'comment','mod'=>'ybc_blog'),$_smarty_tpl ) );
}?></span>
                                    <?php }?>                                 
                                    <?php if ($_smarty_tpl->tpl_vars['allow_like']->value) {?>
                                        <span title="<?php if ($_smarty_tpl->tpl_vars['post']->value['liked']) {
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Liked','mod'=>'ybc_blog'),$_smarty_tpl ) );
} else {
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Like this post','mod'=>'ybc_blog'),$_smarty_tpl ) );
}?>" class="ybc-blog-like-span ybc-blog-like-span-<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['post']->value['id_post']), ENT_QUOTES, 'UTF-8');?>
 <?php if ($_smarty_tpl->tpl_vars['post']->value['liked']) {?>active<?php }?>"  data-id-post="<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['post']->value['id_post']), ENT_QUOTES, 'UTF-8');?>
">                        
                                            <i class="ets_svg">
                                            <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M384 1344q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm1152-576q0-51-39-89.5t-89-38.5h-352q0-58 48-159.5t48-160.5q0-98-32-145t-128-47q-26 26-38 85t-30.5 125.5-59.5 109.5q-22 23-77 91-4 5-23 30t-31.5 41-34.5 42.5-40 44-38.5 35.5-40 27-35.5 9h-32v640h32q13 0 31.5 3t33 6.5 38 11 35 11.5 35.5 12.5 29 10.5q211 73 342 73h121q192 0 192-167 0-26-5-56 30-16 47.5-52.5t17.5-73.5-18-69q53-50 53-119 0-25-10-55.5t-25-47.5q32-1 53.5-47t21.5-81zm128-1q0 89-49 163 9 33 9 69 0 77-38 144 3 21 3 43 0 101-60 178 1 139-85 219.5t-227 80.5h-129q-96 0-189.5-22.5t-216.5-65.5q-116-40-138-40h-288q-53 0-90.5-37.5t-37.5-90.5v-640q0-53 37.5-90.5t90.5-37.5h274q36-24 137-155 58-75 107-128 24-25 35.5-85.5t30.5-126.5 62-108q39-37 90-37 84 0 151 32.5t102 101.5 35 186q0 93-48 192h176q104 0 180 76t76 179z"></path></svg>
                                        </i> <span class="ben_<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['post']->value['id_post']), ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['post']->value['likes']), ENT_QUOTES, 'UTF-8');?>
</span>
                                            <span class="blog-post-like-text blog-post-like-text-<?php echo htmlspecialchars((string) intval($_smarty_tpl->tpl_vars['post']->value['id_post']), ENT_QUOTES, 'UTF-8');?>
"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Liked','mod'=>'ybc_blog'),$_smarty_tpl ) );?>
</span>

                                        </span> 
                                    <?php }?>
                                    
                                </div>
                            <?php }?> 
                                                                                    
                            
                        </div>
                    </div>
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            </div>
            <?php if (((isset($_smarty_tpl->tpl_vars['blog_config']->value['YBC_BLOG_DISPLAY_BUTTON_ALL_HOMEPAGE'])) && $_smarty_tpl->tpl_vars['blog_config']->value['YBC_BLOG_DISPLAY_BUTTON_ALL_HOMEPAGE']) || $_smarty_tpl->tpl_vars['blog_page']->value != 'home') {?>
                <div class="blog_view_all_button">
                    <a href="<?php echo htmlspecialchars((string) call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['latest_link']->value,'html','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
" class="view_all_link"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'View all latest posts','mod'=>'ybc_blog'),$_smarty_tpl ) );?>
</a>
                </div>
            <?php }?>
        </div>
        <div class="clear"></div>
    </div>
    
<?php }
}
}
