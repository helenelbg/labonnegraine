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
{extends file='page.tpl'}
{block name="page_content"}
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
    {assign var='_svg_rotate_right' value='<i class="ets_svg"><svg width="1792" height="1792" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1664 256v448q0 26-19 45t-45 19h-448q-42 0-59-40-17-39 14-69l138-138q-148-137-349-137-104 0-198.5 40.5t-163.5 109.5-109.5 163.5-40.5 198.5 40.5 198.5 109.5 163.5 163.5 109.5 198.5 40.5q119 0 225-52t179-147q7-10 23-12 15 0 25 9l137 138q9 8 9.5 20.5t-7.5 22.5q-109 132-264 204.5t-327 72.5q-156 0-298-61t-245-164-164-245-61-298 61-298 164-245 245-164 298-61q147 0 284.5 55.5t244.5 156.5l130-129q29-31 70-14 39 17 39 59z"/></svg></i>'}

    <div class="ets-am-program ets-am-content">
        <div class="navbar-page">
            <ul class="ets-am-content-links">
                <li class="list-title">
                    <h1>
                        {$_svg_trophy nofilter}
                        {l s='My rewards' mod='ets_affiliatemarketing'}
                    </h1>
                </li>
                <li>
                    <a href="{$link_reward nofilter}"
                       class="{if isset($controller) && $controller == 'dashboard'} active {/if}">{l s='Dashboard' mod='ets_affiliatemarketing'}</a>
                </li>
                <li><a href="{$link_reward_history nofilter}"
                       class="{if isset($controller) && $controller == 'history'} active {/if}">{l s='Reward history' mod='ets_affiliatemarketing'}</a>
                </li>
                {if isset($allow_withdraw) && $allow_withdraw}
                    <li>
                        <a href="{$link_withdraw nofilter}"
                           class="{if isset($controller) && $controller == 'withdraw'} active {/if}">{l s='Withdrawals' mod='ets_affiliatemarketing'}</a>
                    </li>
                {/if}
                {if isset($allow_convert_voucher) && $allow_convert_voucher}
                    <li>
                        <a href="{$link_voucher nofilter}"
                           class="{if isset($controller) && $controller == 'voucher'} active {/if}">{l s='Convert into vouchers' mod='ets_affiliatemarketing'}</a>
                    </li>
                {/if}
            </ul>
        </div>
        <div class="ets-am-content eam-my20">
            {if $controller == 'dashboard'}
            <div class="eam-rewards-boxes boxes-color">
                {if isset($eam_allow_withdraw_loyalty) && $eam_allow_withdraw_loyalty}
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-lg-6">
                            <div class="box box-pink">
                                <h5 class="box-title">{l s='Reward balance' mod='ets_affiliatemarketing'}</h5>
                                <div class="box-data">
                                    {$eam_total_reward_balance nofilter}
                                </div>
                                <div class="box-desc">
                                    {l s="Total remaining reward after withdrawing, converting into voucher or paying for orders." mod='ets_affiliatemarketing'}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-lg-6">
                            <div class="box box-teal">
                                <h5 class="box-title">{l s='Reward used' mod='ets_affiliatemarketing'}</h5>
                                <div class="box-data">
                                    {$eam_total_usage nofilter}
                                </div>
                                <div class="box-desc">
                                    {l s="Total reward used to withdraw, convert into voucher or pay for orders." mod='ets_affiliatemarketing'}
                                </div>
                            </div>
                        </div>
                    </div>
                {else}
                    <div class="row">
                        <div class="eam-rewards-boxes-item col-xs-12 col-md-3 col-sm-6 col-lg-3">
                            <div class="box box-pink box-col-3 eam-box-tooltip" data-placement="bottom"
                         data-title="{l s='Total remaining reward amount including loyalty reward and earning reward after withdrawing, converting into voucher or paying for orders.' mod='ets_affiliatemarketing'}">
                                <h5 class="box-title">{l s='Reward balance' mod='ets_affiliatemarketing'}</h5>
                                <div class="box-data">
                                    {$eam_total_reward_balance nofilter}
                                </div>
                            </div>
                        </div>
                        <div class="eam-rewards-boxes-item col-xs-12 col-md-3 col-sm-6 col-lg-3">
                            <div class="box box-teal box-col-3 eam-box-tooltip" data-placement="bottom" data-title="{l s='Total remaining reward earned from loyalty program.' mod='ets_affiliatemarketing'} {if $message_title_reward}{l s='Can be used for the following purpose(s):' mod='ets_affiliatemarketing'} {$message_title_reward|escape:'html':'UTF-8'}{/if}">
                                <h5 class="box-title">{l s='Loyalty reward' mod='ets_affiliatemarketing'}</h5>
                                <div class="box-data">
                                    {$eam_total_loyalty_left nofilter}
                                </div>
                            </div>
                        </div>
                        <div class="eam-rewards-boxes-item col-xs-12 col-md-3 col-sm-6 col-lg-3">
                            <div class="box box-orange box-col-3 eam-box-tooltip" data-placement="bottom" data-title="{l s='Total remaining reward earned from Referral program and affiliate program.' mod='ets_affiliatemarketing'} {if $message_title_reward_earning}{l s='Can be used for the following purpose(s):' mod='ets_affiliatemarketing'} {$message_title_reward_earning|escape:'html':'UTF-8'}{/if}">
                                <h5 class="box-title">{l s='Earning reward' mod='ets_affiliatemarketing'}</h5>
                                <div class="box-data">
                                    {$eam_total_earning_left nofilter}
                                </div>
                            </div>
                        </div>
                        <div class="eam-rewards-boxes-item col-xs-12 col-md-3 col-sm-6 col-lg-3">
                            <div class="box box-blue box-col-3 eam-box-tooltip" data-placement="bottom" data-title="{l s='Total reward used to pay for orders, convert into voucher or withdraw.' mod='ets_affiliatemarketing'}">
                                <h5 class="box-title">{l s='Reward used' mod='ets_affiliatemarketing'}</h5>
                                <div class="box-data">
                                    {$eam_total_usage nofilter}
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}
            </div>
        </div>
        <div class="stats-data-reward eam-list-box-dashboard">
            <div class="panel">
                <div class="panel-body pl-25 pr-25">
                    <div class="stats-container eam-dasboad-reward">
                        <div class="stats-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="eam-box-chart">
                                        <div class="box-header">
                                            <h3 class="box-title">{l s='Earned and used rewards' mod='ets_affiliatemarketing'}</h3>
                                        </div>
                                        <div class="box-body">
                                            <div id="eam_stats_reward_line">
                                                <svg></svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="eam-box-chart">
                                        <div class="box-header">
                                            <h3 class="box-title">{l s='Reward ratio' mod='ets_affiliatemarketing'} <small>({$eam_currency_code|escape:'html':'UTF-8'})</small></h3>
                                        </div>
                                        <div class="box-body">
                                            <div id="eam_stats_reward_pie">
                                                <span class="eam-chart-no-data">{l s='No data found' mod='ets_affiliatemarketing'}</span>
                                                <svg></svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="stat-filter eam-box-filter eam-my30 eam-br6">
                            <form class="form-inline" action="" method="post">
                                <div class="row">

                                    <div class="eam_select_filter">
                                        <label>{l s='Reward status' mod='ets_affiliatemarketing'}</label>
                                        <select name="status" class="form-control">
                                            <option value="all">{l s='All' mod='ets_affiliatemarketing'}</option>
                                            <option value="approved"
                                                    selected>{l s='Approved' mod='ets_affiliatemarketing'}</option>
                                            <option value="pending">{l s='Pending' mod='ets_affiliatemarketing'}</option>
                                            <option value="canceled">{l s='Canceled' mod='ets_affiliatemarketing'}</option>
                                            <option value="expired">{l s='Expired' mod='ets_affiliatemarketing'}</option>
                                        </select>
                                    </div>
                                    <div class="eam_select_filter col-lg-5 col-mb-12">
                                        <div>
                                            <label>{l s='Time range' mod='ets_affiliatemarketing'}</label>
                                            <select name="type_date_filter" class="form-control field-inline">
                                                <option value="all_times" {if isset($data_stats.distance) && $data_stats.distance > 5}selected="selected"{/if}>{l s='All the time' mod='ets_affiliatemarketing'}</option>
                                                <option value="this_month">{l s="This month" mod='ets_affiliatemarketing'} - {date('m/Y') nofilter}</option>
                                                <option value="this_year" {if isset($data_stats.distance) && $data_stats.distance <= 5}selected="selected"{/if}>{l s="This year" mod='ets_affiliatemarketing'} - {date('Y') nofilter}</option>
                                                <option value="time_ranger">{l s='Time range' mod='ets_affiliatemarketing'}</option>
                                            </select>
                                            <div class="box-date-ranger">
                                                <input type="text" name="date_ranger" value=""
                                                       class="form-control eam_date_ranger_filter">
                                                <input type="hidden" name="date_from_reward"
                                                       class="date_from_reward"
                                                       value="{date('Y-m-01') nofilter}">
                                                <input type="hidden" name="date_to_reward"
                                                       class="date_to_reward"
                                                       value="{date('Y-m-t') nofilter}">
                                                <input type="hidden" name="type_stats" value="reward">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="eam_action">
                                        <div class="form-group">
                                            <button type="button"
                                                    class="btn btn-default btn-block js-btn-submit-filter">{$_svg_search nofilter} {l s='Filter' mod='ets_affiliatemarketing'}
                                            </button>
                                            <button type="button"
                                                    class="btn btn-default btn-block js-btn-reset-filter">{$_svg_undo nofilter} {l s='Reset' mod='ets_affiliatemarketing'}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {elseif $controller == 'withdraw'}
        {if isset($is_request_withdraw_page) && $is_request_withdraw_page}
            {if isset($eam_allow_withdraw) && $eam_allow_withdraw}
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="fs-16 text-uppercase mb-15">{l s='Submit withdrawal request' mod='ets_affiliatemarketing'}</h3>
                        {if isset($eam_payment_method)}
                            <div class="payment-info">
                                <p class="mb-0">{l s='Payment method' mod='ets_affiliatemarketing'}:
                                    <strong>{$eam_payment_method.title|escape:'html':'UTF-8'}</strong></p>
                                <p class="mb-0">{l s='Fee' mod='ets_affiliatemarketing'}:
                                    <strong>{$eam_payment_method.fee|escape:'html':'UTF-8'}</strong></p>
                                {if isset($eam_payment_method.estimated_processing_time) && $eam_payment_method.estimated_processing_time}
                                <p class="mb-0">{l s='Estimated processing time' mod='ets_affiliatemarketing'}:
                                    <strong>{$eam_payment_method.estimated_processing_time|escape:'html':'UTF-8'}</strong> {l s='day(s)' mod='ets_affiliatemarketing'}</p>
                                {/if}
                                {if isset($eam_payment_method.note) && $eam_payment_method.note}
                                <p class="mb-15">{l s='Note' mod='ets_affiliatemarketing'}
                                    : {$eam_payment_method.note nofilter}</p>
                                {/if}
                                <p class="mb-15">{l s='Balance available for withdrawal' mod='ets_affiliatemarketing'}
                                    : <strong>{$eam_can_withdraw nofilter}</strong></p>
                            </div>
                        {/if}
                    </div>
                </div>
                {if isset($eam_payment_method) && count($eam_payment_method) && isset($eam_payment_fields) && count($eam_payment_fields)}
                    <div class="row">
                        <div class="col-md-12">
                            {if (isset($eam_reward_enough) && $eam_reward_enough)}
                                {if isset($eam_reward_has_pending) && !$eam_reward_has_pending}
                                    <form class="eam-withdraw-form" novalidate method="post"
                                          action=""
                                          autocomplete="off" enctype="multipart/form-data">
                                        <p class="mb-15">{l s='Please fill in the fields below with required information then submit your withdrawal request.' mod='ets_affiliatemarketing'}</p>
                                        <div class="eam-box-content-withdraw">
                                            <div class="form-panel">
                                                <div class="form-panel-header">
                                                    <h4 class="form-panel-title">{l s='Amount to withdraw' mod='ets_affiliatemarketing'}</h4>
                                                </div>
                                                <div class="form-panel-body mb-5">
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <div class="form-group {if isset($eam_form_errors) && array_key_exists('EAM_AMOUNT_WITHDRAW', $eam_form_errors)}has-error{/if}">
                                                                <div class="input-group display-flex mb-5">
                                                                    <input type="text"
                                                                           name="EAM_AMOUNT_WITHDRAW"
                                                                           class="form-control"
                                                                           aria-describedby="EAM_AMOUNT_WITHDRAW-addon"
                                                                           id="EAM_AMOUNT_WITHDRAW"
                                                                           value="{if isset($eam_form_old_data) && $eam_form_old_data.EAM_AMOUNT_WITHDRAW}{$eam_form_old_data.EAM_AMOUNT_WITHDRAW nofilter}{/if}"
                                                                           placeholder="{'0.00' nofilter}">
                                                                    <div class="input-group-append">
                                                                                <span class="input-group-text"
                                                                                      id="EAM_AMOUNT_WITHDRAW-addon">{$currency['iso_code']|escape:'html':'UTF-8'}</span>
                                                                    </div>
                                                                </div>
                                                                {if isset($eam_withdraw_condition) && (isset($eam_withdraw_condition.min) ||  isset($eam_withdraw_condition.max))}
                                                                    <p class="eam-note">
                                                                        {l s='Note:' mod='ets_affiliatemarketing'}
                                                                        {if isset($eam_withdraw_condition) && isset($eam_withdraw_condition.min) && $eam_withdraw_condition.min}
                                                                            {l s='Min amount' mod='ets_affiliatemarketing'} {$eam_withdraw_condition.min|escape:'html':'UTF-8'}.
                                                                        {/if}
                                                                        {if isset($eam_withdraw_condition) && isset($eam_withdraw_condition.max) && $eam_withdraw_condition.max}
                                                                            {l s='Max amount' mod='ets_affiliatemarketing'} {$eam_withdraw_condition.max|escape:'html':'UTF-8'}.
                                                                        {/if}
                                                                    </p>
                                                                {/if}
                                                                {if isset($eam_form_errors) && array_key_exists('EAM_AMOUNT_WITHDRAW', $eam_form_errors)}
                                                                    {foreach from=$eam_form_errors key=key item=error}
                                                                        {if $error@iteration == 1}
                                                                            <span class="help-block">{$eam_form_errors.EAM_AMOUNT_WITHDRAW nofilter}</span>
                                                                        {/if}
                                                                    {/foreach}
                                                                {/if}
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="eam-withdraw-boxes">
                                                                <h3>
                                                                    <small>{l s='You will receive:' mod='ets_affiliatemarketing'}</small>
                                                                    <span class="price">{$currency['sign'] nofilter}{'0.00' nofilter}</span>
                                                                </h3>
                                                                <p class="eam-note">
                                                                    {l s='Note: Withdrawal fee has been calculated.' mod='ets_affiliatemarketing'}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-panel">
                                                <div class="form-panel-header">
                                                    <h4 class="form-panel-title">{l s='Additional information' mod='ets_affiliatemarketing'}</h4>
                                                </div>
                                                <div class="form-panel-body">
                                                    <div class="row">
                                                        <div class="col-md-8 col-sm-full">
                                                            <div class="form-payment-fields">
                                                                {foreach from=$eam_payment_fields item=field}
                                                                    <div class="row">
                                                                        <div class="form-group {if isset($eam_form_errors) && array_key_exists($field.field_alias, $eam_form_errors)}has-error{/if}">
                                                                            <label class="col-md-4 mt-5 pr-10">{$field.field_title nofilter}
                                                                                {if $field.required == 1}
                                                                                    <sup>*</sup>
                                                                                {/if}</label>
                                                                            <div class="col-md-7 p-0">
                                                                                {assign "field_val" ''}
                                                                                {if isset($eam_form_old_data) && count($eam_form_old_data)}
                                                                                    {foreach from=$eam_form_old_data key=k item=v}
                                                                                        {if $k == $field.field_alias}
                                                                                            {assign "field_val" $v|strip}
                                                                                        {/if}
                                                                                    {/foreach}
                                                                                {else}
                                                                                    {if isset($eam_payment_history) && count($eam_payment_history)}
                                                                                        {foreach from=$eam_payment_history item=history }
                                                                                            {if $history.id_ets_am_withdrawal_field == $field.field_id}
                                                                                                {assign "field_val" $history.value|strip}
                                                                                            {/if}
                                                                                        {/foreach}
                                                                                    {/if}
                                                                                {/if}
                                                                                {if !$field_val && $field_values && isset($field_values[$field.field_id])}
                                                                                    {assign "field_val" $field_values[$field.field_id].value}
                                                                                {/if}
                                                                                {if $field.field_type == 'text' || $field.field_type == 'file'}
                                                                                    {if $field.field_type != 'file'}

                                                                                        <input type="{$field.field_type|escape:'html':'UTF-8'}"
                                                                                               name="{$field.field_alias|escape:'html':'UTF-8'}"
                                                                                                {if $field.required == 1}
                                                                                                    required
                                                                                                {/if}
                                                                                               class="form-control"
                                                                                               value="{$field_val|escape:'html':'UTF-8'}" />
                                                                                    {else}
                                                                                        <div class="eam-box-upload-invoice">
                                                                                            <p class="eam-file-upload-invoice-return">
                                                                                                <label>{l s='Upload your invoice' mod='ets_affiliatemarketing'}</label>
                                                                                            </p>
                                                                                            <input type="{$field.field_type|escape:'html':'UTF-8'}"
                                                                                                   name="{$field.field_alias|escape:'html':'UTF-8'}"
                                                                                                    {if $field.required == 1}
                                                                                                        required
                                                                                                    {/if}
                                                                                                   class="form-control"
                                                                                                   id="eam-input-upload-invoice">
                                                                                            <label tabindex="0"
                                                                                                   class="eam-input-upload-invoice-trigger">Select</label>
                                                                                        </div>
                                                                                    {/if}
                                                                                {else}
                                                                                    <textarea
                                                                                            name="{$field.field_alias|escape:'html':'UTF-8'}"
                                                                                            cols="10"
                                                                                            rows="5" class="form-control eam-bg-white">{$field_val|escape:'html':'UTF-8'}</textarea>
                                                                                {/if}
                                                                                {if isset($eam_form_errors) && array_key_exists($field.field_alias, $eam_form_errors)}
                                                                                    {foreach from=$eam_form_errors key=key item=error}
                                                                                        {if $error@iteration == 1}
                                                                                            <span class="help-block">{$eam_form_errors.$key nofilter}</span>
                                                                                        {/if}
                                                                                    {/foreach}
                                                                                {/if}

                                                                            </div>
                                                                            {if isset($field.description) && $field.description}
                                                                               &nbsp; <span class="eam-help eam-tooltip-bs"
                                                                                            data-toggle="tooltip" data-placement="top" title="{$field.description nofilter}">{$_svg_question_circle_icon nofilter}</span>
                                                                            {/if}
                                                                        </div>

                                                                    </div>
                                                                {/foreach}
                                                                {if isset($eam_form_errors.eam_withdraw_permission)}
                                                                <div class="alert alert-danger">
                                                                    {$eam_form_errors.eam_withdraw_permission|escape:'html':'UTF-8'}
                                                                </div>
                                                                {/if}
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <p class="note mb-35">
                                                                        {l s='*Note: Please enter the required information above exactly to receive your funds. Wrong information may result in losing the money that you’re withdrawing' mod='ets_affiliatemarketing'}
                                                                    </p>
                                                                    <div class="form-buttons">
                                                                        <input type="hidden" value="1"
                                                                               name="eam_withdraw_submit">
                                                                        <button type="submit"
                                                                                class="btn btn-info eam-button eam-submit-request">{l s='Withdraw Funds' mod='ets_affiliatemarketing'}</button>
                                                                        <a href="{$link_withdraw nofilter}"
                                                                           class="eam-button eam-button-default eam-button-cancel btn btn-default">{l s='Cancel' mod='ets_affiliatemarketing'}</a>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                {else}
                                    {if isset($message)}
                                        <div class="alert alert-warning">
                                            <p class="mb-0">{$message nofilter}</p>
                                        </div>
                                    {/if}
                                {/if}
                            {else}
                                {if isset($message)}
                                    <div class="alert alert-warning">
                                        <p class="mb-0">{$message nofilter}</p>
                                    </div>
                                {/if}
                            {/if}
                        </div>
                    </div>
                {else}
                    <div class="alert alert-danger alert-error">
                        {l s='We\'re sorry! This payment method is not available, please select other method ' mod='ets_affiliatemarketing'}
                    </div>
                {/if}
            {/if}
        {else}
            {if isset($eam_allow_withdraw) && $eam_allow_withdraw}
                <div class="row">
                    <div class="col-md-12">
                        <p class="mb-25">{l s='Select one of available withdrawal methods below to submit your money withdrawal request' mod='ets_affiliatemarketing'}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="table-responsive">
                            <table class="eam-table-data table table-striped mb-50">
                                <thead>
                                <tr>
                                    <th>{l s='Method' mod='ets_affiliatemarketing'}</th>
                                    <th>{l s='Description' mod='ets_affiliatemarketing'}</th>
                                    <th>{l s='Estimate processing time' mod='ets_affiliatemarketing'}</th>
                                    <th>{l s='Fee' mod='ets_affiliatemarketing'}</th>
                                </tr>
                                </thead>
                                <tbody>
                                {if isset($eam_payment_methods) && count($eam_payment_methods)}
                                    {foreach from=$eam_payment_methods item=method}
                                        <tr>
                                            <td>
                                                <a href="{$method.link nofilter}">{$method.title nofilter}</a>
                                            </td>
                                            <td>{$method.description nofilter}</td>
                                            <td>{if $method.estimated_processing_time}{$method.estimated_processing_time nofilter nofilter} {l s='day(s)' mod='ets_affiliatemarketing'}{else} -- {/if}</td>
                                            <td>{$method.fee nofilter}</td>
                                        </tr>
                                    {/foreach}
                                {else}
                                    <tr class="text-center">
                                        <td colspan="100%">
                                            {l s='No data found' mod='ets_affiliatemarketing'}
                                        </td>
                                    </tr>
                                {/if}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class=" table-responsive">
                            <table class="table eam-table-data table-striped">
                                <thead>
                                <tr>
                                    <th>{l s='Available balance for withdrawal' mod='ets_affiliatemarketing'}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="fs-18 fw-b">
                                        {$eam_can_withdraw nofilter}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            {/if}
            <div class="row">
                <div class="col-md-12">
                    <h4 class="text-uppercase fs-14 mb-20">{l s='Your last withdrawal requests' mod='ets_affiliatemarketing'}</h4>
                    {if isset($eam_success_message)}
                        <div class="alert alert-success alert-dismissible eam-alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            {$eam_success_message nofilter}
                        </div>
                    {/if}
                    <div class="table-response table-responsive">
                        <table class="table eam-table-flat table-label-custom">
                            <thead>
                            <tr>
                                <th class="text-center">{l s='Withdrawal ID' mod='ets_affiliatemarketing'}</th>
                                <th class="text-center">{l s='Amount' mod='ets_affiliatemarketing'}</th>
                                <th class="text-center">{l s='Payment method' mod='ets_affiliatemarketing'}</th>
                                <th class="text-center">{l s='Status' mod='ets_affiliatemarketing'}</th>
                                <th class="text-center">{l s='Processed date' mod='ets_affiliatemarketing'}</th>
                                <th class="text-center">{l s='Note' mod='ets_affiliatemarketing'}</th>
                            </tr>
                            </thead>
                            <tbody>
                            {if isset($eam_withdrawal_requests) && isset($eam_withdrawal_requests.results) && count($eam_withdrawal_requests.results)}
                                {foreach from=$eam_withdrawal_requests.results item=request}
                                    <tr>
                                        <td class="text-center">{$request.id_ets_am_withdrawal nofilter}</td>
                                        <td class="text-center">{$request.display_amount nofilter}</td>
                                        <td class="text-center">{$request.title nofilter}</td>
                                        <td class="text-center">
                                            {if $request.status == 1}
                                                <span class="label label-success">{l s='Approved' mod='ets_affiliatemarketing'}</span>
                                            {elseif $request.status == 0}
                                                <span class="label label-warning">{l s='Pending' mod='ets_affiliatemarketing'}</span>
                                            {elseif $request.status == -1}
                                                <span class="label label-default">{l s='Declined' mod='ets_affiliatemarketing'}</span>
                                            {else}
                                                <span class="label label-default">{l s='Canceled' mod='ets_affiliatemarketing'}</span>
                                            {/if}
                                        </td>
                                        <td class="text-center">
                                            {if isset($request.date_process) && $request.date_process && $request.date_process!='0000-00-00'}
                                                {dateFormat date=$request.date_process full=0}
                                            {/if}

                                        </td>
                                        <td class="text-center">{$request.note nofilter}</td>
                                    </tr>
                                {/foreach}
                            {else}
                                <tr class="text-center">
                                    <td colspan="100%">
                                        {l s='No data found' mod='ets_affiliatemarketing'}
                                    </td>
                                </tr>
                            {/if}
                            </tbody>
                        </table>
                        {if $eam_withdrawal_requests.total_page > 1}
                            <div class="eam-pagination">
                                <ul>
                                    {if $eam_withdrawal_requests.current_page > 1}
                                        <li>
                                            <a href="javascript:void(0)" data-page="{$eam_withdrawal_requests.current_page - 1|escape:'html':'UTF-8'}" class="js-eam-page-item">{l s='Previous' mod='ets_affiliatemarketing'}</a>
                                        </li>
                                    {/if}
                                    {assign 'minRange' 1}
                                    {assign 'maxRange' $eam_withdrawal_requests.total_page}
                                    {if $eam_withdrawal_requests.total_page > 10}
                                        {if $eam_withdrawal_requests.current_page < ($eam_withdrawal_requests.total_page - 3)}
                                            {assign 'maxRange' $eam_withdrawal_requests.current_page + 2}
                                        {/if}
                                        {if $eam_withdrawal_requests.current_page > 3}
                                            {assign 'minRange' $eam_withdrawal_requests.current_page - 2}
                                        {/if}
                                    {/if}
                                    {if $minRange > 1}
                                        <li><span class="eam-page-3dot">...</span></li>
                                    {/if}
                                    {for $page=$minRange to $maxRange}
                                        <li class="{if $page == $eam_withdrawal_requests.current_page} active {/if}">
                                            <a href="javascript:void(0)" data-page="{$page|escape:'html':'UTF-8'}" class="js-eam-page-item">{$page|escape:'html':'UTF-8'}</a>
                                        </li>
                                    {/for}
                                    {if $maxRange < $eam_withdrawal_requests.total_page}
                                        <li><span class="eam-page-3dot">...</span></li>
                                    {/if}
                                    {if $eam_withdrawal_requests.current_page < $eam_withdrawal_requests.total_page}
                                        <li>
                                            <a href="javascript:void(0)" data-page="{$eam_withdrawal_requests.current_page + 1|escape:'html':'UTF-8'}" class="js-eam-page-item">{l s='Next' mod='ets_affiliatemarketing'}</a>
                                        </li>
                                    {/if}
                                </ul>
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        {/if}
        {elseif $controller == 'voucher'}
        <div class="eam-dashboard eam-convert-voucher">
            <div class="eam-voucer-message">
                {if isset($eam_voucher_success_message)}
                    <div class="alert alert-success alert-dismissable">
                        {$eam_voucher_success_message nofilter}
                        <a href="javascript:void(0)"
                           class="btn btn-success eam-apply-voucher b-radius-3 text-uppercase"
                           data-voucher-code="{$eam_voucher_id|escape:'html':'UTF-8'}">{l s='Apply Voucher code to my cart' mod='ets_affiliatemarketing'}</a>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                    </div>
                {/if}
            </div>
            <div class="eam-form eam-voucher-form mb-40">
                {if $total_balance >0}
                    <p>{$eam_voucher_info|replace:'[strong]':'<strong>'|replace:'[endstrong]':'</strong>' nofilter}</p>
                    <form action="" method="post">
                        <div class="form-group {if isset($eam_form_error) && array_key_exists('EAM_VOUCHER_AMOUNT', $eam_form_error)}has-error{/if}">
                            <label class="fw-b mb-5"
                                   for="EAM_VOUCHER_AMOUNT">{l s='Amount to convert:' mod='ets_affiliatemarketing'}</label>
                            <div class="input-group mb-5">
                                <input type="text"
                                       class="form-control"
                                       name="EAM_VOUCHER_AMOUNT"
                                       placeholder="0.00" aria-label="0.00"
                                       value="{if isset($eam_form_data) && isset($eam_form_data.EAM_VOUCHER_AMOUNT)}{$eam_form_data.EAM_VOUCHER_AMOUNT nofilter}{/if}"
                                       aria-describedby="EAM_VOUCHER_AMOUNT">
                                <div class="input-group-append">
                                                    <span class="input-group-text"
                                                          id="EAM_VOUCHER_AMOUNT">{$currency['iso_code'] nofilter}</span>
                                </div>
                            </div>
                            {if isset($eam_form_error) && array_key_exists('EAM_VOUCHER_AMOUNT', $eam_form_error)}
                                {foreach from=$eam_form_error key=key item=error}
                                    {if $error@iteration == 1}
                                        <span class="help-block">{$eam_form_error.EAM_VOUCHER_AMOUNT nofilter}</span>
                                    {/if}
                                {/foreach}
                            {/if}
                            {if isset($eam_form_error.eam_voucher_permission)}
                            <div class="alert alert-danger">
                                {$eam_form_error.eam_voucher_permission|escape:'html':'UTF-8'}
                            </div>
                            {/if}
                            <p class="eam-note mb-20">
                                {l s='Note:' mod='ets_affiliatemarketing'}
                                {if isset($eam_voucher_min) && $eam_voucher_min}
                                    {l s='Min amount to convert %min_convert%.' mod='ets_affiliatemarketing' sprintf=['%min_convert%' => $eam_voucher_min]}
                                {/if}
                                {if isset($eam_voucher_max) && $eam_voucher_max}
                                    {l s='Max amount to convert %max_convert%.' mod='ets_affiliatemarketing' sprintf=['%max_convert%' => $eam_voucher_max]}
                                {/if}
                                {if isset($ETS_AM_VOUCHER_AVAILABILITY) && $ETS_AM_VOUCHER_AVAILABILITY}
                                    {l s='Voucher availability: [1]%ETS_AM_VOUCHER_AVAILABILITY% days[/1].' tags=['<strong>'] mod='ets_affiliatemarketing' sprintf=['%ETS_AM_VOUCHER_AVAILABILITY%' => $ETS_AM_VOUCHER_AVAILABILITY]}
                                {/if}
                            </p>
                        </div>
                        <input type="hidden" name="eam-submit-voucher" value="1">
                        <button class="btn btn-primary text-uppercase b-radius-3 fs-14"
                                type="submit">{l s='Convert now' mod='ets_affiliatemarketing'}</button>
                    </form>
                {else}
                    <div class="alert alert-warning">{l s='Voucher is not available. You are required to have positive balance in order to submit your convert request.' mod='ets_affiliatemarketing'}</div>
                {/if}
            </div>
            <div class="eam-voucher-history">
                <h4 class="text-uppercase fs-14 mb-15">{l s='Your voucher codes' mod='ets_affiliatemarketing'}</h4>
                <div class="table-responsive">
                    <table class="table eam-table-flat">
                        <thead>
                        <tr>
                            <th>{l s='Code' d='Shop.Theme.Checkout'}</th>
                            <th>{l s='Description' d='Shop.Theme.Checkout'}</th>
                            <th class="text-center">{l s='Quantity' d='Shop.Theme.Checkout'}</th>
                            <th>{l s='Value' d='Shop.Theme.Checkout'}</th>
                            <th class="text-center">{l s='Minimum' d='Shop.Theme.Checkout'}</th>
                            <th class="text-center">{l s='Cumulative' d='Shop.Theme.Checkout'}</th>
                            <th class="text-center">{l s='Expiration date' d='Shop.Theme.Checkout'}</th>
                            <th class="text-center">{l s='Status' d='Shop.Theme.Checkout'}</th>
                        </tr>
                        </thead>
                        <tbody>
                        {if $cart_rules.results }
                            {foreach from=$cart_rules.results item=cart_rule}
                                <tr>
                                    <td>{$cart_rule.code nofilter}</td>
                                    <td>{$cart_rule.name nofilter}</td>
                                    <td class="text-center">{$cart_rule.quantity_per_user nofilter}</td>
                                    <td>{$cart_rule.value nofilter}</td>
                                    <td class="text-center">{$cart_rule.voucher_minimal nofilter}</td>
                                    <td class="text-center">{$cart_rule.voucher_cumulable nofilter}</td>
                                    <td class="text-center">
                                        {if $cart_rule.voucher_date && $cart_rule.voucher_date!='0000-00-00 00:00:00'}
                                            {dateFormat date=$cart_rule.voucher_date full=0}
                                        {/if}
                                    </td>
                                    <td class="text-center">
                                        {if $cart_rule.status == 1}
                                            <span class="i-mr-2 text-warning">{$_svg_clock_o nofilter}</span>{l s='Used' mod='ets_affiliatemarketing'}
                                        {elseif $cart_rule.status == -1}
                                            <span class="text-danger">{$_svg_check_icon nofilter}</span>{l s='Expired' mod='ets_affiliatemarketing'}

                                        {else}
                                            <span class="text-success">{$_svg_check_icon nofilter}</span>{l s='Available' mod='ets_affiliatemarketing'}
                                        {/if}
                                    </td>
                                </tr>
                            {/foreach}
                        {else}
                            <tr class="text-center">
                                <td colspan="100%">
                                    {l s='No data found' mod='ets_affiliatemarketing'}
                                </td>
                            </tr>
                        {/if}

                        </tbody>
                    </table>
                    {if $cart_rules.total_page > 1}
                        <div class="eam-pagination">
                            <ul>
                                {if $cart_rules.current_page > 1}
                                    <li>
                                        <a href="javascript:void(0)" data-page="{$cart_rules.current_page - 1|escape:'html':'UTF-8'}" class="js-eam-page-item">{l s='Previous' mod='ets_affiliatemarketing'}</a>
                                    </li>
                                {/if}
                                {assign 'minRange' 1}
                                {assign 'maxRange' $eam_withdrawal_requests.total_page}
                                {if $cart_rules.total_page > 10}
                                    {if $cart_rules.current_page < ($cart_rules.total_page - 3)}
                                        {assign 'maxRange' $cart_rules.current_page + 2}
                                    {/if}
                                    {if $eam_withdrawal_requests.current_page > 3}
                                        {assign 'minRange' $cart_rules.current_page - 2}
                                    {/if}
                                {/if}
                                {if $minRange > 1}
                                    <li><span class="eam-page-3dot">...</span></li>
                                {/if}
                                {for $page=$minRange to $maxRange}
                                    <li class="{if $page == $cart_rules.current_page} active {/if}">
                                        <a href="javascript:void(0)" data-page="{$page|escape:'html':'UTF-8'}" class="js-eam-page-item">{$page|escape:'html':'UTF-8'}</a>
                                    </li>
                                {/for}
                                {if $maxRange < $cart_rules.total_page}
                                    <li><span class="eam-page-3dot">...</span></li>
                                {/if}
                                {if $cart_rules.current_page < $cart_rules.total_page}
                                    <li>
                                        <a href="javascript:void(0)" data-page="{$cart_rules.current_page + 1|escape:'html':'UTF-8'}" class="js-eam-page-item">{l s='Next' mod='ets_affiliatemarketing'}</a>
                                    </li>
                                {/if}
                            </ul>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
        {elseif isset($controller) && $controller == 'history'}
        <div class="eam-dashboard">
            <div class="table-responsive ">
                <table class="table eam-table-flat  table-label-custom">
                    <thead>
                    <tr>
                        <th width="10%" class="text-center">{l s='Reward ID' mod='ets_affiliatemarketing'}</th>
                        <th width="10%" class="text-center">{l s='Reward value' mod='ets_affiliatemarketing'}</th>
                        <th class="text-center">{l s='Program' mod='ets_affiliatemarketing'}</th>
                        <th class="text-left">{l s='Products' mod='ets_affiliatemarketing'}</th>
                        <th width="10%" class="text-center">{l s='Status' mod='ets_affiliatemarketing'}</th>
                        <th width="20%" class="text-center">{l s='Note' mod='ets_affiliatemarketing'}</th>
                        <th width="15%" class="text-center">{l s='Date' mod='ets_affiliatemarketing'}</th>
                    </tr>
                    </thead>
                    <tbody>
                    {if $reward_history.results}
                        {foreach $reward_history.results as $reward}
                            <tr>
                                <td class="text-center">{$reward.id_ets_am_reward nofilter}</td>
                                <td class="text-center">
                                    {if $reward.amount|strpos:'-' !== false}
                                        <span class="eam-reward-usage">{$reward.amount|escape:'html':'UTF-8'}</span>
                                    {else}
                                        {$reward.amount|escape:'html':'UTF-8'}
                                    {/if}
                                </td>
                                <td class="text-center">{$reward.program nofilter}</td>
                                <td class="text-left">

                                    {if $reward.products}
                                        {$reward.product_name nofilter}
                                    {else}
                                        --
                                    {/if}
                                </td>
                                <td class="text-center">
                                    {if $reward.amount|strpos:'-' !== false}
                                        {if $reward.status == 0}
                                            <label class="label label-refunded">{l s='Refunded' mod='ets_affiliatemarketing'}</label>
                                        {else}
                                            <label class="label label-deducted">{l s='Deducted' mod='ets_affiliatemarketing'}</label>
                                        {/if}
                                    {else}
                                        {if $reward.status == -2}
                                            <label class="label label-danger">{l s='Expired' mod='ets_affiliatemarketing'}</label>
                                        {elseif $reward.status == -1}
                                            <label class="label label-default">{l s='Canceled' mod='ets_affiliatemarketing'}</label>
                                        {elseif $reward.status == 0}
                                            <label class="label label-warning">{l s='Pending' mod='ets_affiliatemarketing'}</label>
                                        {else}
                                            <label class="label label-success">{l s='Approved' mod='ets_affiliatemarketing'}</label>
                                        {/if}
                                    {/if}
                                </td>
                                <td class="text-center">{if $reward.note}{$reward.note nofilter}{else}--{/if}</td>
                                <td class="text-center">{dateFormat date=$reward.datetime_added full=1}</td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr class="text-center">
                            <td colspan="100%">
                                {l s='No data found' mod='ets_affiliatemarketing'}
                            </td>
                        </tr>
                    {/if}
                    </tbody>
                </table>
                {if $reward_history.total_page > 1}
                    <div class="eam-pagination">
                        <ul>
                            {if $reward_history.current_page > 1}
                                <li class="{if $reward_history.current_page == 1} active {/if}">
                                    <a href="javascript:void(0)" data-page="{$reward_history.current_page - 1|escape:'html':'UTF-8'}" class="js-eam-page-item">{l s='Previous' mod='ets_affiliatemarketing'}</a>
                                </li>
                            {/if}
                            {assign 'minRange' 1}
                            {assign 'maxRange' $reward_history.total_page}
                            {if $reward_history.total_page > 10}
                                {if $reward_history.current_page < ($reward_history.total_page - 3)}
                                    {assign 'maxRange' $reward_history.current_page + 2}
                                {/if}
                                {if $reward_history.current_page > 3}
                                    {assign 'minRange' $reward_history.current_page - 2}
                                {/if}
                            {/if}
                            {if $minRange > 1}
                                <li><span class="eam-page-3dot">...</span></li>
                            {/if}
                            {for $page=$minRange to $maxRange}
                                <li class="{if $page == $reward_history.current_page} active {/if}">
                                    <a href="javascript:void(0)" data-page="{$page|escape:'html':'UTF-8'}"
                                       class="js-eam-page-item">{$page|escape:'html':'UTF-8'}</a>
                                </li>
                            {/for}
                            {if $maxRange < $reward_history.total_page}
                                <li><span class="eam-page-3dot">...</span></li>
                            {/if}
                            {if $reward_history.current_page < $reward_history.total_page}
                                <li>
                                    <a href="javascript:void(0)" data-page="{$reward_history.current_page + 1|escape:'html':'UTF-8'}"
                                       class="js-eam-page-item">{l s='Next' mod='ets_affiliatemarketing'} </a>
                                </li>
                            {/if}
                        </ul>
                    </div>
                {/if}
            </div>

            <div class="stat-filter eam-box-filter">
                <form class="form-inline" action="" method="post">
                    <div class="row">
                        <div class="eam_select_filter">
                            <label>{l s='Program' mod='ets_affiliatemarketing'}</label>
                            <select name="program" id="" class="form-control" style="max-width: 150px;">
                                <option value="all"
                                        {if isset($query.program) && $query.program == 'all'}selected="selected"{/if}>{l s='All' mod='ets_affiliatemarketing'}</option>
                                <option value="loy"
                                        {if isset($query.program) && $query.program == 'loy'}selected="selected"{/if}>{l s='Loyalty' mod='ets_affiliatemarketing'}</option>
                                <option value="ref"
                                        {if isset($query.program) && $query.program == 'ref'}selected="selected"{/if}>{l s='Referral' mod='ets_affiliatemarketing'}</option>
                                <option value="aff"
                                        {if isset($query.program) && $query.program == 'aff'}selected="selected"{/if}>{l s='Affiliate' mod='ets_affiliatemarketing'}</option>
                                <option value="reward_used" {if isset($query.program) && $query.program == 'reward_used'} selected="selected" {/if}>{l s='Rewards used only' mod='ets_affiliatemarketing'}</option>

                            </select>
                        </div>
                        <div class="eam_select_filter">
                            <label>{l s='Reward status' mod='ets_affiliatemarketing'}</label>
                            <select name="status" class="form-control">
                                <option value="all"
                                        {if isset($query.status) && $query.status == 'all'}selected="selected"{/if}>{l s='All' mod='ets_affiliatemarketing'}</option>
                                <option value="1"
                                        {if isset($query.status) && $query.status == 1 }selected="selected"{/if}>{l s='Approved' mod='ets_affiliatemarketing'}</option>
                                <option value="0"
                                        {if isset($query.status) && $query.status == 0 && $query.status != 'all'}selected="selected"{/if}>{l s='Pending' mod='ets_affiliatemarketing'}</option>
                                <option value="-1"
                                        {if isset($query.status) && $query.status == -1}selected="selected"{/if}>{l s='Canceled' mod='ets_affiliatemarketing'}</option>
                                <option value="-2"
                                        {if isset($query.status) && $query.status == -2}selected="selected"{/if}>{l s='Expired' mod='ets_affiliatemarketing'}</option>
                            </select>
                        </div>
                        <div class="eam_select_filter col-mb-12">
                            <div>
                                <label>{l s='Time frame' mod='ets_affiliatemarketing'}</label>

                                <select name="type_date_filter" class="form-control field-inline">
                                    <option value="all_times"
                                            {if isset($query.type_date_filter) && $query.type_date_filter == 'all_times'}selected="selected"{/if}>{l s='All the time' mod='ets_affiliatemarketing'}</option>
                                    <option value="this_month"
                                            {if isset($query.type_date_filter) && $query.type_date_filter == 'this_month'}selected="selected"{/if}>{l s="This month" mod='ets_affiliatemarketing'} - {date('m/Y') nofilter}</option>
                                    <option value="this_year"
                                            {if isset($query.type_date_filter) && $query.type_date_filter == 'this_year'}selected="selected"{/if}>{l s="This year" mod='ets_affiliatemarketing'} - {date('Y') nofilter}</option>

                                    <option value="time_ranger"
                                            {if isset($query.type_date_filter) && $query.type_date_filter == 'time_ranger'}selected="selected"{/if}>{l s='Time range' mod='ets_affiliatemarketing'}</option>
                                </select>
                                <div class="box-date-ranger"
                                     {if isset($query.type_date_filter) && $query.type_date_filter == 'time_ranger'}style="display-block;"{/if}>
                                    <input type="text" name="date_ranger" value=""
                                           class="form-control eam_date_ranger_filter">
                                    <input type="hidden" name="date_from_reward"
                                           class="date_from_reward"
                                           value="{date('Y-m-01') nofilter}">
                                    <input type="hidden" name="date_to_reward"
                                           class="date_to_reward"
                                           value="{date('Y-m-t') nofilter}">
                                    <input type="hidden" name="type_stats" value="reward">
                                </div>
                            </div>
                        </div>

                        <div class="eam_action">
                            <div class="form-group">
                                <button type="submit"
                                        class="btn btn-default btn-block js-btn-submit-filter">{$_svg_search nofilter} {l s='Filter' mod='ets_affiliatemarketing'}
                                </button>
                                <button type="button"
                                        class="btn btn-default btn-block js-btn-reset-filter">{$_svg_undo nofilter} {l s='Reset' mod='ets_affiliatemarketing'}
                                </button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        {/if}
    </div>
    {if isset($eam_currency)}
        <script>
            var eam_currency_sign = '{$eam_currency.sign nofilter}';
        </script>
    {/if}
    {if isset($controller) && $controller == 'dashboard'}
        <script type="text/javascript">
            var eam_chart_day = "{l s='Day' mod='ets_affiliatemarketing'}";
            var eam_chart_month = "{l s='Month' mod='ets_affiliatemarketing'}";
            var eam_chart_year = "{l s='Year' mod='ets_affiliatemarketing'}";
            var eam_chart_currency_code = "{$eam_currency_code|escape:'html':'UTF-8'}";
            var eam_data_stats = '{$data_stats|json_encode}';
            eam_data_stats = JSON.parse(eam_data_stats.replace(/&quot;/g, '"'));
            var eam_data_pie_chart = '{$pie_reward|@json_encode nofilter}';
        </script>
    {elseif isset($controller) && $controller == 'withdraw'}
        <script>
            var eam_confirmation_withdraw = "{$eam_confirm nofilter}";
        </script>
    {elseif isset($controller) && $controller == 'voucher'}
        <script>
            var eam_confirm_convert_voucher = "{$eam_confirm_convert_voucher nofilter}";
        </script>
    {/if}

{/block}
{block name='page_footer'}
<div class="eam-back-section">
    <a href="{if isset($my_account_link)}{$my_account_link|escape:'html':'UTF-8'}{/if}" title="{l s='Back to your account' mod='ets_affiliatemarketing'}" class="eam-back-link eam-link-go-myaccount">
        <i class="icon_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1203 544q0 13-10 23l-393 393 393 393q10 10 10 23t-10 23l-50 50q-10 10-23 10t-23-10l-466-466q-10-10-10-23t10-23l466-466q10-10 23-10t23 10l50 50q10 10 10 23z"/></svg>
        </i> {l s='Back to your account' mod='ets_affiliatemarketing'}</a>
    <a href="{if isset($home_link)}{$home_link|escape:'html':'UTF-8'}{/if}" title="{l s='Home' mod='ets_affiliatemarketing'}" class="eam-back-link eam-link-go-home">
        <i class="svg_icon"><svg width="15" height="15" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1472 992v480q0 26-19 45t-45 19h-384v-384h-256v384h-384q-26 0-45-19t-19-45v-480q0-1 .5-3t.5-3l575-474 575 474q1 2 1 6zm223-69l-62 74q-8 9-21 11h-3q-13 0-21-7l-692-577-692 577q-12 8-24 7-13-2-21-11l-62-74q-8-10-7-23.5t11-21.5l719-599q32-26 76-26t76 26l244 204v-195q0-14 9-23t23-9h192q14 0 23 9t9 23v408l219 182q10 8 11 21.5t-7 23.5z"/></svg>
        </i> {l s='Home' mod='ets_affiliatemarketing'}</a>
</div>
{/block}
