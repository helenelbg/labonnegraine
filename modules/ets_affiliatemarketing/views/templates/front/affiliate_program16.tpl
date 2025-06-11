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
{if isset($flash_message)}
    <div class="alert alert-info alert-dismissible est-am-alert" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span>
        </button>
        {$flash_message nofilter}
    </div>
{/if}
{if isset($valid) && $valid}
    <h1>{l s='Affiliate Program' mod='ets_affiliatemarketing'}</h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="box eam-statistic-reward">
                <div class="box-header">
                    <h3>{l s='Statistic' mod='ets_affiliatemarketing'}</h3>
                </div>
                <div class="box-body">
                    <div class="stats">
                        <div class="stats-filter">
                            <form id="eam_data_filter_stat_reward">
                                <div class="row">
                                    <div class="col-lg-5">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>{l s='Status' mod='ets_affiliatemarketing'}</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="status[]" class="status_reward filter_status_reward" value="-2">
                                                        {l s='Stopped' mod='ets_affiliatemarketing'}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="status[]" class="status_reward filter_status_reward" value="-1">
                                                        {l s='Canceled' mod='ets_affiliatemarketing'}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="status[]" class="status_reward filter_status_reward" value="0">
                                                        {l s='Pending' mod='ets_affiliatemarketing'}
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="status[]" class="status_reward filter_status_reward" value="1">
                                                        {l s='Approve' mod='ets_affiliatemarketing'}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <label>{l s='Filter date' mod='ets_affiliatemarketing'}</label>
                                            <input type="text" name="date_ranger_filter" value="" placeholder="" class="form-control eam-daterange eam_date_ranger_filter">
                                            <input type="hidden" name="date_from" value="">
                                            <input type="hidden" name="date_to" value="">
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="form-group">
                                            <label style="display: block;">&nbsp;</label>
                                            <button type="button" class="btn btn-default js-btn-reset-filter">{l s='Reset' mod='ets_affiliatemarketing'}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="stats-body">
                            <div class="stats-loading">
                                <div class="loading-text">
                                    {l s='Loading...' mod='ets_affiliatemarketing'}
                                </div>
                            </div>
                            <canvas id="eam-stats-programs"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}