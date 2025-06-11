{*
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
*}
<link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700" rel="stylesheet">
<script type="text/javascript">
    var ets_snw_link_ajax = "{$link_tab nofilter}";
    var ets_snw_msg_tagged = "{l s='has been tagged' mod='ets_affiliatemarketing' js='1'}";
    var ets_snw_suffix_level = "{l s='% of remaining sponsor cost after paying for higher levels' mod='ets_affiliatemarketing'}";
    var ets_snw_dumplicate_msg = "{l s='Duplicate item' mod='ets_affiliatemarketing'}";
    var ets_snw_date_msg = "{l s='To date must be greater than From date' mod='ets_affiliatemarketing'}";
    var ets_swn_currency_code = "{$currency->iso_code|escape:'html':'UTF-8'}";
    var ets_swn_chart_day = "{l s='Day' mod='ets_affiliatemarketing'}";
    var ets_swn_chart_month = "{l s='Month' mod='ets_affiliatemarketing'}";
    var ets_swn_chart_year = "{l s='Year' mod='ets_affiliatemarketing'}";
    var ets_snw_sub_level_desc = "{l s='Leave blank to not give reward to this level when new order is created' mod='ets_affiliatemarketing'}";
    var ets_swn_trans = [];
    ets_swn_trans['add_payment_method'] = "{l s='Add payment method' mod='ets_affiliatemarketing'}";
    ets_swn_trans['add_payment_method_field'] = "{l s='Add payment method field' mod='ets_affiliatemarketing'}";
    ets_swn_trans['method_field_title'] = "{l s='Method field title' mod='ets_affiliatemarketing'}";
    ets_swn_trans['method_field_type'] = "{l s='Method field type' mod='ets_affiliatemarketing'}";
    ets_swn_trans['method_name'] = "{l s='Method name' mod='ets_affiliatemarketing'}";
    ets_swn_trans['delete'] = "{l s='Delete' mod='ets_affiliatemarketing'}";
    ets_swn_trans['fee_type'] = "{l s='Fee type' mod='ets_affiliatemarketing'}";
    ets_swn_trans['fee_fixed'] = "{l s='Fee amount (fixed value)' mod='ets_affiliatemarketing'}";
    ets_swn_trans['fee_percent'] = "{l s='Fee amount (percentage)' mod='ets_affiliatemarketing'}";
    ets_swn_trans['enable'] = "{l s='Enabled' mod='ets_affiliatemarketing'}";
    ets_swn_trans['yes'] = "{l s='Yes' mod='ets_affiliatemarketing'}";
    ets_swn_trans['no'] = "{l s='No' mod='ets_affiliatemarketing'}";
    ets_swn_trans['percent'] = "{l s='Percentage' mod='ets_affiliatemarketing'}";
    ets_swn_trans['fixed'] = "{l s='Fixed' mod='ets_affiliatemarketing'}";
    ets_swn_trans['times'] = "{l s='Time' mod='ets_affiliatemarketing'}";
    ets_swn_trans['confirm_delete'] = "{l s='Do you want to delete this item?' mod='ets_affiliatemarketing'}";
    ets_swn_trans['loading'] = "{l s='Loading...' mod='ets_affiliatemarketing'}";
    ets_swn_trans['description'] = "{l s='Description' mod='ets_affiliatemarketing'}";
    ets_swn_trans['required'] = "{l s='Required' mod='ets_affiliatemarketing'}";
    ets_swn_trans['day'] = "{l s='Day' mod='ets_affiliatemarketing'}";
    ets_swn_trans['month'] = "{l s='Month' mod='ets_affiliatemarketing'}";
    ets_swn_trans['year'] = "{l s='Year' mod='ets_affiliatemarketing'}";
    ets_swn_trans['filter'] = "{l s='Filter' mod='ets_affiliatemarketing'}";
    ets_swn_trans['pm_fee_fixed_required'] = "{l s='Fixed fee of payment method is required' mod='ets_affiliatemarketing'}";
    ets_swn_trans['pm_fee_percent_required'] = "{l s='Fee percentage of payment method is required' mod='ets_affiliatemarketing'}";
    ets_swn_trans['pmf_title_required'] = "{l s='Title of payment method field is required' mod='ets_affiliatemarketing'}";
    ets_swn_trans['confirm_delete_reward'] = "{l s='Do you want to delete this reward?' mod='ets_affiliatemarketing'}";
    ets_swn_trans['confirm_cancel_reward'] = "{l s='Do you want to cancel this reward?' mod='ets_affiliatemarketing'}";
    ets_swn_trans['confirm_refund_reward'] = "{l s='Do you want to refund this reward?' mod='ets_affiliatemarketing'}";
    ets_swn_trans['confirm_suspend_user'] = "{l s='Do you want to suspend this user from using any marketing program?' mod='ets_affiliatemarketing'}";
    ets_swn_trans['confirm_delete_withdrawal'] = "{l s='Do you want to delete this withdrawal?' mod='ets_affiliatemarketing'}";
    ets_swn_trans['confirm_approve_withdrawal'] = "{l s='Do you want to approve this withdrawal?' mod='ets_affiliatemarketing'}";
    ets_swn_trans['confirm_decline_return_withdrawal'] = "{l s='Do you want to decline with return reward this withdrawal?' mod='ets_affiliatemarketing'}";
    ets_swn_trans['confirm_decline_deduct_withdrawal'] = "{l s='Do you want to decline with deduct reward this withdrawal?' mod='ets_affiliatemarketing'}";
    ets_swn_trans['confirm_delete_application'] = "{l s='Do you want to delete this application?' mod='ets_affiliatemarketing'}";
    ets_swn_trans['confirm_delete_photo'] = "{l s='Do you want to delete this photo?' mod='ets_affiliatemarketing'}";
    ets_swn_trans['confirm_approve_app'] = "{l s='Do you want to approve this application?' mod='ets_affiliatemarketing'}";
    ets_swn_trans['confirm_suspend_app'] = "{l s='Do you want to suspend this application?' mod='ets_affiliatemarketing'}";
    ets_swn_trans['confirm_decline_app'] = "{l s='Do you want to decline this application?' mod='ets_affiliatemarketing'}";
    ets_swn_trans['no_data'] = "{l s='No data found' mod='ets_affiliatemarketing'}";
    ets_swn_trans['clearing'] = "{l s='Clearing...' mod='ets_affiliatemarketing'}";
    ets_swn_trans['confirm_approve_program'] = "{l s='Do you want to approve this user from' mod='ets_affiliatemarketing'}";
    ets_swn_trans['confirm_suspend_program'] = "{l s='Do you want to suspend this user from' mod='ets_affiliatemarketing'}";
    ets_swn_trans['confirm_decline_program'] = "{l s='Do you want to decline this user from' mod='ets_affiliatemarketing'}";
    ets_swn_trans['affiliate_program'] = "{l s='Affliate program' mod='ets_affiliatemarketing'}";
    ets_swn_trans['referral_program'] = "{l s='Referral program' mod='ets_affiliatemarketing'}";
    ets_swn_trans['loyalty_program'] = "{l s='Loyalty program' mod='ets_affiliatemarketing'}";
    ets_swn_trans['confirm_clear_qrcode'] = '{l s='Do you want clear QR code cache?' mod='ets_affiliatemarketing' js=1}';
</script>
<script type="text/javascript" src="{$linkJs|escape:'html':'UTF-8'}"></script>
{assign var='_svg_check_icon' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1671 566q0 40-28 68l-724 724-136 136q-28 28-68 28t-68-28l-136-136-362-362q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 295 656-657q28-28 68-28t68 28l136 136q28 28 28 68z"/></svg></i>'}
{assign var='_svg_close_icon' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"/></svg></i>'}
{assign var='_svg_time_icon' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"/></svg></i>'}
{assign var='_svg_question_circle_icon' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1024 1376v-192q0-14-9-23t-23-9h-192q-14 0-23 9t-9 23v192q0 14 9 23t23 9h192q14 0 23-9t9-23zm256-672q0-88-55.5-163t-138.5-116-170-41q-243 0-371 213-15 24 8 42l132 100q7 6 19 6 16 0 25-12 53-68 86-92 34-24 86-24 48 0 85.5 26t37.5 59q0 38-20 61t-68 45q-63 28-115.5 86.5t-52.5 125.5v36q0 14 9 23t23 9h192q14 0 23-9t9-23q0-19 21.5-49.5t54.5-49.5q32-18 49-28.5t46-35 44.5-48 28-60.5 12.5-81zm384 192q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg></i>'}
{assign var='_svg_plus_circle' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg></i>'}
{assign var='_svg_plus' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1600 736v192q0 40-28 68t-68 28h-416v416q0 40-28 68t-68 28h-192q-40 0-68-28t-28-68v-416h-416q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h416v-416q0-40 28-68t68-28h192q40 0 68 28t28 68v416h416q40 0 68 28t28 68z"/></svg></i>'}
{assign var='_svg_undo' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1664 896q0 156-61 298t-164 245-245 164-298 61q-172 0-327-72.5t-264-204.5q-7-10-6.5-22.5t8.5-20.5l137-138q10-9 25-9 16 2 23 12 73 95 179 147t225 52q104 0 198.5-40.5t163.5-109.5 109.5-163.5 40.5-198.5-40.5-198.5-109.5-163.5-163.5-109.5-198.5-40.5q-98 0-188 35.5t-160 101.5l137 138q31 30 14 69-17 40-59 40h-448q-26 0-45-19t-19-45v-448q0-42 40-59 39-17 69 14l130 129q107-101 244.5-156.5t284.5-55.5q156 0 298 61t245 164 164 245 61 298z"/></svg></i>'}
{assign var='_svg_user_plus' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 2048 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 896q-159 0-271.5-112.5t-112.5-271.5 112.5-271.5 271.5-112.5 271.5 112.5 112.5 271.5-112.5 271.5-271.5 112.5zm960 128h352q13 0 22.5 9.5t9.5 22.5v192q0 13-9.5 22.5t-22.5 9.5h-352v352q0 13-9.5 22.5t-22.5 9.5h-192q-13 0-22.5-9.5t-9.5-22.5v-352h-352q-13 0-22.5-9.5t-9.5-22.5v-192q0-13 9.5-22.5t22.5-9.5h352v-352q0-13 9.5-22.5t22.5-9.5h192q13 0 22.5 9.5t9.5 22.5v352zm-736 224q0 52 38 90t90 38h256v238q-68 50-171 50h-874q-121 0-194-69t-73-190q0-53 3.5-103.5t14-109 26.5-108.5 43-97.5 62-81 85.5-53.5 111.5-20q19 0 39 17 79 61 154.5 91.5t164.5 30.5 164.5-30.5 154.5-91.5q20-17 39-17 132 0 217 96h-223q-52 0-90 38t-38 90v192z"/></svg></i>'}
{assign var='_svg_minus' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1600 736v192q0 40-28 68t-68 28h-1216q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h1216q40 0 68 28t28 68z"/></svg></i>'}
{assign var='_svg_rotate_right' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1664 256v448q0 26-19 45t-45 19h-448q-42 0-59-40-17-39 14-69l138-138q-148-137-349-137-104 0-198.5 40.5t-163.5 109.5-109.5 163.5-40.5 198.5 40.5 198.5 109.5 163.5 163.5 109.5 198.5 40.5q119 0 225-52t179-147q7-10 23-12 15 0 25 9l137 138q9 8 9.5 20.5t-7.5 22.5q-109 132-264 204.5t-327 72.5q-156 0-298-61t-245-164-164-245-61-298 61-298 164-245 245-164 298-61q147 0 284.5 55.5t244.5 156.5l130-129q29-31 70-14 39 17 39 59z"/></svg></i>'}
{assign var='_svg_trash' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg></i>'}
{assign var='_svg_desc' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 704q0 26-19 45l-448 448q-19 19-45 19t-45-19l-448-448q-19-19-19-45t19-45 45-19h896q26 0 45 19t19 45z"/></svg></i>'}
{assign var='_svg_asc' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 1216q0 26-19 45t-45 19h-896q-26 0-45-19t-19-45 19-45l448-448q19-19 45-19t45 19l448 448q19 19 19 45z"/></svg></i>'}
{assign var='_svg_search' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1216 832q0-185-131.5-316.5t-316.5-131.5-316.5 131.5-131.5 316.5 131.5 316.5 316.5 131.5 316.5-131.5 131.5-316.5zm512 832q0 52-38 90t-90 38q-54 0-90-38l-343-342q-179 124-399 124-143 0-273.5-55.5t-225-150-150-225-55.5-273.5 55.5-273.5 150-225 225-150 273.5-55.5 273.5 55.5 225 150 150 225 55.5 273.5q0 220-124 399l343 343q37 37 37 90z"/></svg></i>'}
{assign var='_svg_sitemap' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 1248v320q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-320q0-40 28-68t68-28h96v-192h-512v192h96q40 0 68 28t28 68v320q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-320q0-40 28-68t68-28h96v-192h-512v192h96q40 0 68 28t28 68v320q0 40-28 68t-68 28h-320q-40 0-68-28t-28-68v-320q0-40 28-68t68-28h96v-192q0-52 38-90t90-38h512v-192h-96q-40 0-68-28t-28-68v-320q0-40 28-68t68-28h320q40 0 68 28t28 68v320q0 40-28 68t-68 28h-96v192h512q52 0 90 38t38 90v192h96q40 0 68 28t28 68z"/></svg></i>'}
{assign var='_svg_share_alt' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 1024q133 0 226.5 93.5t93.5 226.5-93.5 226.5-226.5 93.5-226.5-93.5-93.5-226.5q0-12 2-34l-360-180q-92 86-218 86-133 0-226.5-93.5t-93.5-226.5 93.5-226.5 226.5-93.5q126 0 218 86l360-180q-2-22-2-34 0-133 93.5-226.5t226.5-93.5 226.5 93.5 93.5 226.5-93.5 226.5-226.5 93.5q-126 0-218-86l-360 180q2 22 2 34t-2 34l360 180q92-86 218-86z"/></svg></i>'}
{assign var='_svg_copy' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1696 384q40 0 68 28t28 68v1216q0 40-28 68t-68 28h-960q-40 0-68-28t-28-68v-288h-544q-40 0-68-28t-28-68v-672q0-40 20-88t48-76l408-408q28-28 76-48t88-20h416q40 0 68 28t28 68v328q68-40 128-40h416zm-544 213l-299 299h299v-299zm-640-384l-299 299h299v-299zm196 647l316-316v-416h-384v416q0 40-28 68t-68 28h-416v640h512v-256q0-40 20-88t48-76zm956 804v-1152h-384v416q0 40-28 68t-68 28h-416v640h896z"/></svg></i>'}
{assign var='_svg_envelop' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 710v794q0 66-47 113t-113 47h-1472q-66 0-113-47t-47-113v-794q44 49 101 87 362 246 497 345 57 42 92.5 65.5t94.5 48 110 24.5h2q51 0 110-24.5t94.5-48 92.5-65.5q170-123 498-345 57-39 100-87zm0-294q0 79-49 151t-122 123q-376 261-468 325-10 7-42.5 30.5t-54 38-52 32.5-57.5 27-50 9h-2q-23 0-50-9t-57.5-27-52-32.5-54-38-42.5-30.5q-91-64-262-182.5t-205-142.5q-62-42-117-115.5t-55-136.5q0-78 41.5-130t118.5-52h1472q65 0 112.5 47t47.5 113z"/></svg></i>'}
{assign var='_svg_twitter' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1684 408q-67 98-162 167 1 14 1 42 0 130-38 259.5t-115.5 248.5-184.5 210.5-258 146-323 54.5q-271 0-496-145 35 4 78 4 225 0 401-138-105-2-188-64.5t-114-159.5q33 5 61 5 43 0 85-11-112-23-185.5-111.5t-73.5-205.5v-4q68 38 146 41-66-44-105-115t-39-154q0-88 44-163 121 149 294.5 238.5t371.5 99.5q-8-38-8-74 0-134 94.5-228.5t228.5-94.5q140 0 236 102 109-21 205-78-37 115-142 178 93-10 186-50z"/></svg></i>'}
{assign var='_svg_facebook' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1343 12v264h-157q-86 0-116 36t-30 108v189h293l-39 296h-254v759h-306v-759h-255v-296h255v-218q0-186 104-288.5t277-102.5q147 0 228 12z"/></svg></i>'}
{assign var='_svg_clock_o' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1024 544v448q0 14-9 23t-23 9h-320q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h224v-352q0-14 9-23t23-9h64q14 0 23 9t9 23zm416 352q0-148-73-273t-198-198-273-73-273 73-198 198-73 273 73 273 198 198 273 73 273-73 198-198 73-273zm224 0q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg></i>'}
{assign var='_svg_fire' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1600 1696v64q0 13-9.5 22.5t-22.5 9.5h-1344q-13 0-22.5-9.5t-9.5-22.5v-64q0-13 9.5-22.5t22.5-9.5h1344q13 0 22.5 9.5t9.5 22.5zm-256-1056q0 78-24.5 144t-64 112.5-87.5 88-96 77.5-87.5 72-64 81.5-24.5 96.5q0 96 67 224l-4-1 1 1q-90-41-160-83t-138.5-100-113.5-122.5-72.5-150.5-27.5-184q0-78 24.5-144t64-112.5 87.5-88 96-77.5 87.5-72 64-81.5 24.5-96.5q0-94-66-224l3 1-1-1q90 41 160 83t138.5 100 113.5 122.5 72.5 150.5 27.5 184z"/></svg></i>'}
{assign var='_svg_trophy' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M522 883q-74-162-74-371h-256v96q0 78 94.5 162t235.5 113zm1078-275v-96h-256q0 209-74 371 141-29 235.5-113t94.5-162zm128-128v128q0 71-41.5 143t-112 130-173 97.5-215.5 44.5q-42 54-95 95-38 34-52.5 72.5t-14.5 89.5q0 54 30.5 91t97.5 37q75 0 133.5 45.5t58.5 114.5v64q0 14-9 23t-23 9h-832q-14 0-23-9t-9-23v-64q0-69 58.5-114.5t133.5-45.5q67 0 97.5-37t30.5-91q0-51-14.5-89.5t-52.5-72.5q-53-41-95-95-113-5-215.5-44.5t-173-97.5-112-130-41.5-143v-128q0-40 28-68t68-28h288v-96q0-66 47-113t113-47h576q66 0 113 47t47 113v96h288q40 0 68 28t28 68z"/></svg></i>'}
{assign var='_svg_user' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1536 1399q0 109-62.5 187t-150.5 78h-854q-88 0-150.5-78t-62.5-187q0-85 8.5-160.5t31.5-152 58.5-131 94-89 134.5-34.5q131 128 313 128t313-128q76 0 134.5 34.5t94 89 58.5 131 31.5 152 8.5 160.5zm-256-887q0 159-112.5 271.5t-271.5 112.5-271.5-112.5-112.5-271.5 112.5-271.5 271.5-112.5 271.5 112.5 112.5 271.5z"/></svg></i>'}
{assign var='_svg_mortar_board' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 2304 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1774 836l18 316q4 69-82 128t-235 93.5-323 34.5-323-34.5-235-93.5-82-128l18-316 574 181q22 7 48 7t48-7zm530-324q0 23-22 31l-1120 352q-4 1-10 1t-10-1l-652-206q-43 34-71 111.5t-34 178.5q63 36 63 109 0 69-58 107l58 433q2 14-8 25-9 11-24 11h-192q-15 0-24-11-10-11-8-25l58-433q-58-38-58-107 0-73 65-111 11-207 98-330l-333-104q-22-8-22-31t22-31l1120-352q4-1 10-1t10 1l1120 352q22 8 22 31z"/></svg></i>'}
{assign var='_svg_flag' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M320 256q0 72-64 110v1266q0 13-9.5 22.5t-22.5 9.5h-64q-13 0-22.5-9.5t-9.5-22.5v-1266q-64-38-64-110 0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm1472 64v763q0 25-12.5 38.5t-39.5 27.5q-215 116-369 116-61 0-123.5-22t-108.5-48-115.5-48-142.5-22q-192 0-464 146-17 9-33 9-26 0-45-19t-19-45v-742q0-32 31-55 21-14 79-43 236-120 421-120 107 0 200 29t219 88q38 19 88 19 54 0 117.5-21t110-47 88-47 54.5-21q26 0 45 19t19 45z"/></svg></i>'}
{assign var='_svg_angle_double_right' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M979 960q0 13-10 23l-466 466q-10 10-23 10t-23-10l-50-50q-10-10-10-23t10-23l393-393-393-393q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l466 466q10 10 10 23zm384 0q0 13-10 23l-466 466q-10 10-23 10t-23-10l-50-50q-10-10-10-23t10-23l393-393-393-393q-10-10-10-23t10-23l50-50q10-10 23-10t23 10l466 466q10 10 10 23z"/></svg></i>'}
{assign var='_svg_compress' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M896 960v448q0 26-19 45t-45 19-45-19l-144-144-332 332q-10 10-23 10t-23-10l-114-114q-10-10-10-23t10-23l332-332-144-144q-19-19-19-45t19-45 45-19h448q26 0 45 19t19 45zm755-672q0 13-10 23l-332 332 144 144q19 19 19 45t-19 45-45 19h-448q-26 0-45-19t-19-45v-448q0-26 19-45t45-19 45 19l144 144 332-332q10-10 23-10t23 10l114 114q10 10 10 23z"/></svg></i>'}
{assign var='_svg_pencil_square_o' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M888 1184l116-116-152-152-116 116v56h96v96h56zm440-720q-16-16-33 1l-350 350q-17 17-1 33t33-1l350-350q17-17 1-33zm80 594v190q0 119-84.5 203.5t-203.5 84.5h-832q-119 0-203.5-84.5t-84.5-203.5v-832q0-119 84.5-203.5t203.5-84.5h832q63 0 117 25 15 7 18 23 3 17-9 29l-49 49q-14 14-32 8-23-6-45-6h-832q-66 0-113 47t-47 113v832q0 66 47 113t113 47h832q66 0 113-47t47-113v-126q0-13 9-22l64-64q15-15 35-7t20 29zm-96-738l288 288-672 672h-288v-288zm444 132l-92 92-288-288 92-92q28-28 68-28t68 28l152 152q28 28 28 68t-28 68z"/></svg></i>'}
{assign var='_svg_pencil' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"/></svg></i>'}
{assign var='_svg_heart' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M896 1664q-26 0-44-18l-624-602q-10-8-27.5-26t-55.5-65.5-68-97.5-53.5-121-23.5-138q0-220 127-344t351-124q62 0 126.5 21.5t120 58 95.5 68.5 76 68q36-36 76-68t95.5-68.5 120-58 126.5-21.5q224 0 351 124t127 344q0 221-229 450l-623 600q-18 18-44 18z"/></svg></i>'}
{assign var='_svg_download' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 1344q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm256 0q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm128-224v320q0 40-28 68t-68 28h-1472q-40 0-68-28t-28-68v-320q0-40 28-68t68-28h465l135 136q58 56 136 56t136-56l136-136h464q40 0 68 28t28 68zm-325-569q17 41-14 70l-448 448q-18 19-45 19t-45-19l-448-448q-31-29-14-70 17-39 59-39h256v-448q0-26 19-45t45-19h256q26 0 45 19t19 45v448h256q42 0 59 39z"/></svg></i>'}
{assign var='_svg_home' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1472 992v480q0 26-19 45t-45 19h-384v-384h-256v384h-384q-26 0-45-19t-19-45v-480q0-1 .5-3t.5-3l575-474 575 474q1 2 1 6zm223-69l-62 74q-8 9-21 11h-3q-13 0-21-7l-692-577-692 577q-12 8-24 7-13-2-21-11l-62-74q-8-10-7-23.5t11-21.5l719-599q32-26 76-26t76 26l244 204v-195q0-14 9-23t23-9h192q14 0 23 9t9 23v408l219 182q10 8 11 21.5t-7 23.5z"/></svg></i>'}
{assign var='_svg_save' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M512 1536h768v-384h-768v384zm896 0h128v-896q0-14-10-38.5t-20-34.5l-281-281q-10-10-34-20t-39-10v416q0 40-28 68t-68 28h-576q-40 0-68-28t-28-68v-416h-128v1280h128v-416q0-40 28-68t68-28h832q40 0 68 28t28 68v416zm-384-928v-320q0-13-9.5-22.5t-22.5-9.5h-192q-13 0-22.5 9.5t-9.5 22.5v320q0 13 9.5 22.5t22.5 9.5h192q13 0 22.5-9.5t9.5-22.5zm640 32v928q0 40-28 68t-68 28h-1344q-40 0-68-28t-28-68v-1344q0-40 28-68t68-28h928q40 0 88 20t76 48l280 280q28 28 48 76t20 88z"/></svg></i>'}
{assign var='_svg_scissors' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M960 896q26 0 45 19t19 45-19 45-45 19-45-19-19-45 19-45 45-19zm300 64l507 398q28 20 25 56-5 35-35 51l-128 64q-13 7-29 7-17 0-31-8l-690-387-110 66q-8 4-12 5 14 49 10 97-7 77-56 147.5t-132 123.5q-132 84-277 84-136 0-222-78-90-84-79-207 7-76 56-147t131-124q132-84 278-84 83 0 151 31 9-13 22-22l122-73-122-73q-13-9-22-22-68 31-151 31-146 0-278-84-82-53-131-124t-56-147q-5-59 15.5-113t63.5-93q85-79 222-79 145 0 277 84 83 52 132 123t56 148q4 48-10 97 4 1 12 5l110 66 690-387q14-8 31-8 16 0 29 7l128 64q30 16 35 51 3 36-25 56zm-681-260q46-42 21-108t-106-117q-92-59-192-59-74 0-113 36-46 42-21 108t106 117q92 59 192 59 74 0 113-36zm-85 745q81-51 106-117t-21-108q-39-36-113-36-100 0-192 59-81 51-106 117t21 108q39 36 113 36 100 0 192-59zm178-613l96 58v-11q0-36 33-56l14-8-79-47-26 26q-3 3-10 11t-12 12q-2 2-4 3.5t-3 2.5zm224 224l96 32 736-576-128-64-768 431v113l-160 96 9 8q2 2 7 6 4 4 11 12t11 12l26 26zm704 416l128-64-520-408-177 138q-2 3-13 7z"/></svg></i>'}
{assign var='_svg_arrow_circle_left' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 960v-128q0-26-19-45t-45-19h-502l189-189q19-19 19-45t-19-45l-91-91q-18-18-45-18t-45 18l-362 362-91 91q-18 18-18 45t18 45l91 91 362 362q18 18 45 18t45-18l91-91q18-18 18-45t-18-45l-189-189h502q26 0 45-19t19-45zm256-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg></i>'}
{assign var='_svg_list' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M256 1312v192q0 13-9.5 22.5t-22.5 9.5h-192q-13 0-22.5-9.5t-9.5-22.5v-192q0-13 9.5-22.5t22.5-9.5h192q13 0 22.5 9.5t9.5 22.5zm0-384v192q0 13-9.5 22.5t-22.5 9.5h-192q-13 0-22.5-9.5t-9.5-22.5v-192q0-13 9.5-22.5t22.5-9.5h192q13 0 22.5 9.5t9.5 22.5zm0-384v192q0 13-9.5 22.5t-22.5 9.5h-192q-13 0-22.5-9.5t-9.5-22.5v-192q0-13 9.5-22.5t22.5-9.5h192q13 0 22.5 9.5t9.5 22.5zm1536 768v192q0 13-9.5 22.5t-22.5 9.5h-1344q-13 0-22.5-9.5t-9.5-22.5v-192q0-13 9.5-22.5t22.5-9.5h1344q13 0 22.5 9.5t9.5 22.5zm-1536-1152v192q0 13-9.5 22.5t-22.5 9.5h-192q-13 0-22.5-9.5t-9.5-22.5v-192q0-13 9.5-22.5t22.5-9.5h192q13 0 22.5 9.5t9.5 22.5zm1536 768v192q0 13-9.5 22.5t-22.5 9.5h-1344q-13 0-22.5-9.5t-9.5-22.5v-192q0-13 9.5-22.5t22.5-9.5h1344q13 0 22.5 9.5t9.5 22.5zm0-384v192q0 13-9.5 22.5t-22.5 9.5h-1344q-13 0-22.5-9.5t-9.5-22.5v-192q0-13 9.5-22.5t22.5-9.5h1344q13 0 22.5 9.5t9.5 22.5zm0-384v192q0 13-9.5 22.5t-22.5 9.5h-1344q-13 0-22.5-9.5t-9.5-22.5v-192q0-13 9.5-22.5t22.5-9.5h1344q13 0 22.5 9.5t9.5 22.5z"/></svg></i>'}
{assign var='_svg_rotate_right' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1664 256v448q0 26-19 45t-45 19h-448q-42 0-59-40-17-39 14-69l138-138q-148-137-349-137-104 0-198.5 40.5t-163.5 109.5-109.5 163.5-40.5 198.5 40.5 198.5 109.5 163.5 163.5 109.5 198.5 40.5q119 0 225-52t179-147q7-10 23-12 15 0 25 9l137 138q9 8 9.5 20.5t-7.5 22.5q-109 132-264 204.5t-327 72.5q-156 0-298-61t-245-164-164-245-61-298 61-298 164-245 245-164 298-61q147 0 284.5 55.5t244.5 156.5l130-129q29-31 70-14 39 17 39 59z"/></svg></i>'}
{assign var='_svg_check_square_o' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1472 930v318q0 119-84.5 203.5t-203.5 84.5h-832q-119 0-203.5-84.5t-84.5-203.5v-832q0-119 84.5-203.5t203.5-84.5h832q63 0 117 25 15 7 18 23 3 17-9 29l-49 49q-10 10-23 10-3 0-9-2-23-6-45-6h-832q-66 0-113 47t-47 113v832q0 66 47 113t113 47h832q66 0 113-47t47-113v-254q0-13 9-22l64-64q10-10 23-10 6 0 12 3 20 8 20 29zm231-489l-814 814q-24 24-57 24t-57-24l-430-430q-24-24-24-57t24-57l110-110q24-24 57-24t57 24l263 263 647-647q24-24 57-24t57 24l110 110q24 24 24 57t-24 57z"/></svg></i>'}
{assign var='_svg_bookmak' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1420 128q23 0 44 9 33 13 52.5 41t19.5 62v1289q0 34-19.5 62t-52.5 41q-19 8-44 8-48 0-83-32l-441-424-441 424q-36 33-83 33-23 0-44-9-33-13-52.5-41t-19.5-62v-1289q0-34 19.5-62t52.5-41q21-9 44-9h1048z"/></svg></i>'}
{assign var='_svg_comment' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 896q0 174-120 321.5t-326 233-450 85.5q-70 0-145-8-198 175-460 242-49 14-114 22-17 2-30.5-9t-17.5-29v-1q-3-4-.5-12t2-10 4.5-9.5l6-9 7-8.5 8-9q7-8 31-34.5t34.5-38 31-39.5 32.5-51 27-59 26-76q-157-89-247.5-220t-90.5-281q0-130 71-248.5t191-204.5 286-136.5 348-50.5q244 0 450 85.5t326 233 120 321.5z"/></svg></i>'}
{assign var='_svg_cloud_download' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 2048 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 928q0-14-9-23t-23-9h-224v-352q0-13-9.5-22.5t-22.5-9.5h-192q-13 0-22.5 9.5t-9.5 22.5v352h-224q-13 0-22.5 9.5t-9.5 22.5q0 14 9 23l352 352q9 9 23 9t23-9l351-351q10-12 10-24zm640 224q0 159-112.5 271.5t-271.5 112.5h-1088q-185 0-316.5-131.5t-131.5-316.5q0-130 70-240t188-165q-2-30-2-43 0-212 150-362t362-150q156 0 285.5 87t188.5 231q71-62 166-62 106 0 181 75t75 181q0 76-41 138 130 31 213.5 135.5t83.5 238.5z"/></svg></i>'}
{assign var='_svg_tasks' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1024 1408h640v-128h-640v128zm-384-512h1024v-128h-1024v128zm640-512h384v-128h-384v128zm512 832v256q0 26-19 45t-45 19h-1664q-26 0-45-19t-19-45v-256q0-26 19-45t45-19h1664q26 0 45 19t19 45zm0-512v256q0 26-19 45t-45 19h-1664q-26 0-45-19t-19-45v-256q0-26 19-45t45-19h1664q26 0 45 19t19 45zm0-512v256q0 26-19 45t-45 19h-1664q-26 0-45-19t-19-45v-256q0-26 19-45t45-19h1664q26 0 45 19t19 45z"/></svg></i>'}
{assign var='_svg_gift' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1056 1356v-716h-320v716q0 25 18 38.5t46 13.5h192q28 0 46-13.5t18-38.5zm-456-844h195l-126-161q-26-31-69-31-40 0-68 28t-28 68 28 68 68 28zm688-96q0-40-28-68t-68-28q-43 0-69 31l-125 161h194q40 0 68-28t28-68zm376 256v320q0 14-9 23t-23 9h-96v416q0 40-28 68t-68 28h-1088q-40 0-68-28t-28-68v-416h-96q-14 0-23-9t-9-23v-320q0-14 9-23t23-9h440q-93 0-158.5-65.5t-65.5-158.5 65.5-158.5 158.5-65.5q107 0 168 77l128 165 128-165q61-77 168-77 93 0 158.5 65.5t65.5 158.5-65.5 158.5-158.5 65.5h440q14 0 23 9t9 23z"/></svg></i>'}
{assign var='_svg_links' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1520 1216q0-40-28-68l-208-208q-28-28-68-28-42 0-72 32 3 3 19 18.5t21.5 21.5 15 19 13 25.5 3.5 27.5q0 40-28 68t-68 28q-15 0-27.5-3.5t-25.5-13-19-15-21.5-21.5-18.5-19q-33 31-33 73 0 40 28 68l206 207q27 27 68 27 40 0 68-26l147-146q28-28 28-67zm-703-705q0-40-28-68l-206-207q-28-28-68-28-39 0-68 27l-147 146q-28 28-28 67 0 40 28 68l208 208q27 27 68 27 42 0 72-31-3-3-19-18.5t-21.5-21.5-15-19-13-25.5-3.5-27.5q0-40 28-68t68-28q15 0 27.5 3.5t25.5 13 19 15 21.5 21.5 18.5 19q33-31 33-73zm895 705q0 120-85 203l-147 146q-83 83-203 83-121 0-204-85l-206-207q-83-83-83-203 0-123 88-209l-88-88q-86 88-208 88-120 0-204-84l-208-208q-84-84-84-204t85-203l147-146q83-83 203-83 121 0 204 85l206 207q83 83 83 203 0 123-88 209l88 88q86-88 208-88 120 0 204 84l208 208q84 84 84 204z"/></svg></i>'}
{assign var='_svg_cog' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 896q0-106-75-181t-181-75-181 75-75 181 75 181 181 75 181-75 75-181zm512-109v222q0 12-8 23t-20 13l-185 28q-19 54-39 91 35 50 107 138 10 12 10 25t-9 23q-27 37-99 108t-94 71q-12 0-26-9l-138-108q-44 23-91 38-16 136-29 186-7 28-36 28h-222q-14 0-24.5-8.5t-11.5-21.5l-28-184q-49-16-90-37l-141 107q-10 9-25 9-14 0-25-11-126-114-165-168-7-10-7-23 0-12 8-23 15-21 51-66.5t54-70.5q-27-50-41-99l-183-27q-13-2-21-12.5t-8-23.5v-222q0-12 8-23t19-13l186-28q14-46 39-92-40-57-107-138-10-12-10-24 0-10 9-23 26-36 98.5-107.5t94.5-71.5q13 0 26 10l138 107q44-23 91-38 16-136 29-186 7-28 36-28h222q14 0 24.5 8.5t11.5 21.5l28 184q49 16 90 37l142-107q9-9 24-9 13 0 25 10 129 119 165 170 7 8 7 22 0 12-8 23-15 21-51 66.5t-54 70.5q26 50 41 98l183 28q13 2 21 12.5t8 23.5z"/></svg></i>'}
{assign var='_svg_wrench' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M448 1472q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm644-420l-682 682q-37 37-90 37-52 0-91-37l-106-108q-38-36-38-90 0-53 38-91l681-681q39 98 114.5 173.5t173.5 114.5zm634-435q0 39-23 106-47 134-164.5 217.5t-258.5 83.5q-185 0-316.5-131.5t-131.5-316.5 131.5-316.5 316.5-131.5q58 0 121.5 16.5t107.5 46.5q16 11 16 28t-16 28l-293 169v224l193 107q5-3 79-48.5t135.5-81 70.5-35.5q15 0 23.5 10t8.5 25z"/></svg></i>'}
{assign var='_svg_list_ol' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M381 1620q0 80-54.5 126t-135.5 46q-106 0-172-66l57-88q49 45 106 45 29 0 50.5-14.5t21.5-42.5q0-64-105-56l-26-56q8-10 32.5-43.5t42.5-54 37-38.5v-1q-16 0-48.5 1t-48.5 1v53h-106v-152h333v88l-95 115q51 12 81 49t30 88zm2-627v159h-362q-6-36-6-54 0-51 23.5-93t56.5-68 66-47.5 56.5-43.5 23.5-45q0-25-14.5-38.5t-39.5-13.5q-46 0-81 58l-85-59q24-51 71.5-79.5t105.5-28.5q73 0 123 41.5t50 112.5q0 50-34 91.5t-75 64.5-75.5 50.5-35.5 52.5h127v-60h105zm1409 319v192q0 13-9.5 22.5t-22.5 9.5h-1216q-13 0-22.5-9.5t-9.5-22.5v-192q0-14 9-23t23-9h1216q13 0 22.5 9.5t9.5 22.5zm-1408-899v99h-335v-99h107q0-41 .5-121.5t.5-121.5v-12h-2q-8 17-50 54l-71-76 136-127h106v404h108zm1408 387v192q0 13-9.5 22.5t-22.5 9.5h-1216q-13 0-22.5-9.5t-9.5-22.5v-192q0-14 9-23t23-9h1216q13 0 22.5 9.5t9.5 22.5zm0-512v192q0 13-9.5 22.5t-22.5 9.5h-1216q-13 0-22.5-9.5t-9.5-22.5v-192q0-13 9.5-22.5t22.5-9.5h1216q13 0 22.5 9.5t9.5 22.5z"/></svg></i>'}
<div class="ets-sn-admin__tabs">
    <ul class="tab-list nav">
        {assign 'desc_tab' ''}
        {foreach $setting_tabs as $key=>$tab}
            <li class="li_aff_item_menu li_{$key|escape:'html':'UTF-8'}{if $key == $activetab || $menuActive == $key} active{/if}{if isset($tab.sub) && $tab.sub} has_sub{/if}" data-key="{$key|escape:'html':'UTF-8'}">
                {if isset($tab.sub) && $tab.sub}
                    <a href="#">
                        {if isset($tab.img) && isset($linkImg) && $linkImg}
                            <img src="{$linkImg|escape:'html':'UTF-8'}{$tab.img|escape:'html':'UTF-8'}" alt="{$tab.img|escape:'html':'UTF-8'}">
                        {/if}
                        <span class="tab-title">{$tab.title|escape:'html':'UTF-8'}</span>
                        {if isset($tab.subtitle) && $tab.subtitle}<span class="tab-sub-title">{$tab.subtitle|escape:'html':'UTF-8'}</span>{/if}
                    </a>

                    <ul class="sub-nav-tab">
                        {foreach $tab.sub as $k=> $sub}
                            {assign 'tab_id' $k}
                            <li class="{$k|escape:'html':'UTF-8'} {if $k == $activetab} active{/if}" data-active="{$activetab|escape:'html':'UTF-8'}" data-key="{$k|escape:'html':'UTF-8'}">
                                {if isset($sub.subtabs) && $sub.subtabs}
                                    {foreach $sub.subtabs as $sk=>$sv}
                                        {assign 'tab_id' $sk}
                                        {break}
                                    {/foreach}
                                    {foreach $sub.subtabs as $sk=>$sv}
                                        {if $sk == $activetab}
                                            {assign 'desc_tab' $sub.description}
                                            {break}
                                        {/if}
                                    {/foreach}
                                {else}
                                    {if $k == $activetab && isset($sub.description)}
                                        {assign 'desc_tab' $sub.description}
                                    {/if}
                                {/if}
                                <a href="{if isset($sub.link) && $sub.link}{$sub.link|escape:'html':'UTF-8'}{else}{$link_tab|escape:'html':'UTF-8'}{/if}&tabActive={$tab_id|escape:'html':'UTF-8'}">
                                        <span class="img-wrapper">
                                            <img src="{$linkImg|escape:'html':'UTF-8'}{$sub.img|escape:'html':'UTF-8'}" alt="{$sub.img|escape:'html':'UTF-8'}">
                                        </span>
                                    <span class="tab-info-wrapper">
                                            <span class="tab-title">{$sub.title|escape:'html':'UTF-8'}</span>
                                            <span class="tab-desc">{$sub.desc|escape:'html':'UTF-8'}</span>
                                        </span>
                                </a>
                            </li>
                        {/foreach}
                    </ul>
                {else}
                    {assign 'tab_id' $key}
                    {if isset($tab.subtabs) && $tab.subtabs}

                        {foreach $tab.subtabs as $sk=>$sv}
                            {assign 'tab_id' $sk}
                            {break}
                        {/foreach}
                        {foreach $tab.subtabs as $sk=>$sv}
                            {if $sk == $activetab && isset($tab.description)}
                                {assign 'desc_tab' $tab.description}
                                {break}
                            {/if}
                        {/foreach}
                    {else}
                        {if $key == $activetab && isset($tab.description)}
                            {assign 'desc_tab' $tab.description}
                        {/if}

                    {/if}
                    <a href="{if isset($tab.link)}{$tab.link|escape:'html':'UTF-8'}{else}{$link_tab|escape:'html':'UTF-8'}&tabActive={$tab_id|escape:'html':'UTF-8'}{/if}"
                       {if isset($tab.target)}target="{$tab.target|escape:'html':'UTF-8'}" {/if}
                       class="{if isset($tab.class)}{$tab.class|escape:'html':'UTF-8'}{else}link_{$tab_id|escape:'html':'UTF-8'}{/if}">
                        {if isset($tab.img) && isset($linkImg) && $linkImg}
                            <img src="{$linkImg|escape:'html':'UTF-8'}{$tab.img|escape:'html':'UTF-8'}" alt="{$tab.img|escape:'html':'UTF-8'}" />
                        {/if}
                        <span class="tab-title">{$tab.title|escape:'html':'UTF-8'}</span>
                        {if isset($tab.subtitle) && $tab.subtitle}<span class="tab-sub-title">{$tab.subtitle|escape:'html':'UTF-8'}</span>{/if}
                    </a>
                {/if}
            </li>
        {/foreach}
        <li class="more_menu">
            <span class="more_three_dots"></span>
        </li>

    </ul>
</div>
<div class="ets-sn-admin__tabs_height"></div>
{if !Module::isEnabled('ets_affiliatemarketing')}
    <div class="alert alert-warning">
        {l s='You must enable module Loyalty, referral and affiliate program (reward points) to configure its features' mod='ets_affiliatemarketing'}
    </div>
{/if}
{* ========== BREAD CRUMB====== *}
{foreach $breadcrumb_admin as $b}
    {if isset($b.subtabs)}
        {foreach $b.subtabs as $k => $t}
            {if isset($t.subtabs)}
                {foreach $t.subtabs as $sk => $st}
                    {if $sk == $activetab}
                        <div class="eam-breadcrumb">
                            <a href="{$link_tab|escape:'html':'UTF-8'}&tabActive=dashboard" title="" class="eam-breadcrumb-item">{$_svg_home nofilter}</a>
                            <span class="eam-breadcrumb-item"> {$b.title|escape:'html':'UTF-8'}</span>
                            <span class="eam-breadcrumb-item"> {$t.title|escape:'html':'UTF-8'}</span>
                        </div>
                        {break}
                    {/if}
                {/foreach}
            {else}
                {if $k == $activetab}
                    <div class="eam-breadcrumb">
                        <a href="{$link_tab|escape:'html':'UTF-8'}&tabActive=dashboard" title="" class="eam-breadcrumb-item">{$_svg_home nofilter}</a>
                        <span class="eam-breadcrumb-item"> {$b.title|escape:'html':'UTF-8'}</span>
                        <span class="eam-breadcrumb-item"> {$t.title|escape:'html':'UTF-8'}</span>
                    </div>
                    {break}
                {/if}
            {/if}
        {/foreach}
    {/if}
{/foreach}
{* ========== END BREAD CRUMB====== *}
<div class="ets-sn-admin__content {if $activetab == 'applications' || $activetab == 'reward_history' || $activetab == 'reward_users' || $activetab == 'import_export' || $activetab == 'withdraw_list' } eam-no-tab {elseif  $activetab == 'dashboard'} eam-dashboard-page {/if}">
    {if $activetab !== 'dashboard'}
        <div class="title-content">
            <h1>{if $caption.icon == 'heart'}{$_svg_heart nofilter}{elseif $caption.icon == 'user'}{$_svg_user nofilter}{elseif $caption.icon=='cloud-download'}{$_svg_cloud_download nofilter}{elseif $caption.icon=='tasks'}{$_svg_tasks nofilter}{else}
                <i class="fa fa-{$caption.icon|escape:'html':'UTF-8'}"></i>{/if} {$caption.title|escape:'html':'UTF-8'} {if isset($id_data) && $id_data}#{$id_data|escape:'html':'UTF-8'}{/if}<span class="eam-sub-title">{$desc_tab|escape:'html':'UTF-8'}</span></h1>
        </div>
    {/if}

    {assign 'subtabs' []}
    {if !isset($config_tabs[$activetab])}
        {foreach $config_tabs as $key=>$tab}
            {if isset($tab.subtabs) && $tab.subtabs && in_array($activetab, array_keys($tab.subtabs))}
                {assign 'subtabs' $tab.subtabs}
                {break}
            {/if}
        {/foreach}
    {/if}
    {if $subtabs}
        <div class="ets-sn-admin__subtabs">
            <ul class="subtab-list">
                {foreach $subtabs as $key=>$tab}
                    <li class="{if $activetab == $key} active {/if}">
                        <a href="{if isset($tab.link) && $tab.link}{$tab.link|escape:'html':'UTF-8'}{else}{$link_tab|escape:'html':'UTF-8'}{/if}&tabActive={$key|escape:'html':'UTF-8'}" title="">
                            {if isset($tab.icon) && $tab.icon}
                                {if $tab.icon == 'check-square-o'}{$_svg_check_square_o nofilter}{/if}
                                {if $tab.icon == 'bookmark'}{$_svg_bookmak nofilter}{/if}
                                {if $tab.icon == 'comment'}{$_svg_comment nofilter}{/if}
                                {if $tab.icon == 'gift'}{$_svg_gift nofilter}{/if}
                                {if $tab.icon == 'link'}{$_svg_links nofilter}{/if}
                                {if $tab.icon == 'envelope'}{$_svg_envelop nofilter}{/if}
                                {if $tab.icon == 'cog'}{$_svg_cog nofilter}{/if}
                                {if $tab.icon == 'list-ol'}{$_svg_list_ol nofilter}{/if}
                                {if $tab.icon == 'wrench'}{$_svg_wrench nofilter}{/if}

                            {/if}
                            {$tab.title nofilter}
                            {if $key == $activetab}<span class="eam-subtab-count-data"></span>{/if}
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
    {/if}
