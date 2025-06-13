/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
$(document).ready(function(){
    ybcBlogPrettyPhoto();
});
function ybcBlogPrettyPhoto() {
    if ($('.prettyPhoto').length > 0) {
        $("a[class^='prettyPhoto']").prettyPhoto({ animation_speed: 'normal', theme: YBC_BLOG_GALLERY_SKIN, slideshow: YBC_BLOG_GALLERY_SPEED, autoplay_slideshow: false, social_tools: '', deeplinking: false });
    }
    if ($('.gallery_block_slider').length > 0) {
        if ($('.gallery_block_slider').length == 1)
            YBC_BLOG_GALLERY_AUTO_PLAY = false;
        $("a[rel^='prettyPhotoBlock']").prettyPhoto({ animation_speed: 'normal', theme: YBC_BLOG_GALLERY_SKIN, slideshow: YBC_BLOG_GALLERY_SPEED, autoplay_slideshow: YBC_BLOG_GALLERY_AUTO_PLAY, social_tools: '', deeplinking: false });
    }
    //Gallery
    if ($('.ybc-gallery .gallery_item').length > 0)
        $("a[rel^='prettyPhotoGalleryPage']").prettyPhoto({ animation_speed: 'normal', theme: YBC_BLOG_GALLERY_SKIN, slideshow: YBC_BLOG_GALLERY_SPEED, autoplay_slideshow: ($('.ybc-gallery .gallery_item').length > 1 ? YBC_BLOG_GALLERY_AUTO_PLAY : false), social_tools: '', deeplinking: false });
    if ($('.blog_description.popup_image img').length) {
        $('.blog_description img').each(function () {
            const $_this = $(this),
                _this = this;
            $_this.wrap('<a href="' + $(this).attr('src') + '" class="prettyPhoto"></a>');
            if ('undefined' != typeof _this.style.marginLeft && 'auto' == _this.style.marginLeft
                && 'undefined' != typeof _this.style.marginRight && 'auto' == _this.style.marginRight) {
                _this.style.display = 'block';
            }
        });
        $("a[class^='prettyPhoto']").prettyPhoto({ animation_speed: 'normal', theme: YBC_BLOG_GALLERY_SKIN, slideshow: YBC_BLOG_GALLERY_SPEED, autoplay_slideshow: false, social_tools: '', deeplinking: false });
    }
}