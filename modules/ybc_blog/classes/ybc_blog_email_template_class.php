<?php
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

if (!defined('_PS_VERSION_')) { exit; }
class Ybc_blog_email_template_class extends ObjectModel
{
    public static $instance;
    public $active;
    public $template;
    public $subject;
    public static $definition = array(
		'table' => 'ybc_blog_email_template',
		'primary' => 'id_ybc_blog_email_template',
		'multilang' => true,
		'fields' => array(
            'active' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'template' => array('type' => self::TYPE_STRING), 
            'subject' => array('type' => self::TYPE_STRING,'lang' => true),
        ),
    );
    public static function getInstance()
    {
        if (!(isset(self::$instance)) || !self::$instance) {
            self::$instance = new Ybc_blog_email_template_class();
        }
        return self::$instance;
    }
    public function l($string,$file_name='')
    {
        return Translate::getModuleTranslation('ybc_blog', $string, $file_name ? : pathinfo(__FILE__, PATHINFO_FILENAME));
    }
    static $cache_subject = [];

    public function getSubjects($template = null,$default=true)
    {
        if (!self::$cache_subject) {
            self::$cache_subject = array(
                'new_comment_0' => array(
                    'og' => 'New customer commented on your post',
                    't' => $this->l('New customer commented on your post'),
                    'desc' => $this->l('Send a notification email to admin when a customer commented on a blog post and is waiting for approval'),
                    'active'=>1,
                    'tags' => $this->l('Available tags: [highlight]{shop_name}[end_highlight], [highlight]{shop_url}[end_highlight], [highlight]{author_name}[end_highlight], [highlight]{post_title}[end_highlight], [highlight]{post_link}[end_highlight],[highlight]{link_view_comment}[end_highlight].')
                ),
                'new_comment_1' => array(
                    'og' => 'New customer commented on your post',
                    't' => $this->l('New customer commented on your post'),
                    'desc' => $this->l('Send a notification email to admin when a customer commented on a blog post and was approved automatically'),
                    'active'=>1,
                    'tags' => $this->l('Available tags: [highlight]{shop_name}[end_highlight], [highlight]{shop_url}[end_highlight], [highlight]{author_name}[end_highlight], [highlight]{post_title}[end_highlight], [highlight]{post_link}[end_highlight],[highlight]{link_view_comment}[end_highlight].')
                ),
                'new_comment_customer_0' => array( // YBC_BLOG_ENABLE_MAIL_NEW_COMMENT
                    'og' => 'Your comment has been submitted',
                    't' => $this->l('Your comment has been submitted'),
                    'desc' => $this->l('Send a notification email to customer when his/her comment was submitted and is waiting for approval'),
                    'active'=>$default ? 1 :(int)Configuration::get('YBC_BLOG_ENABLE_MAIL_NEW_COMMENT'),
                    'tags' => $this->l('Available tags: [highlight]{shop_name}[end_highlight], [highlight]{shop_url}[end_highlight], [highlight]{customer_name}[end_highlight], [highlight]{post_title}[end_highlight], [highlight]{post_link}[end_highlight].')
                ),
                'new_comment_customer_1'=> array(
                    'og' => 'Your comment has been submitted',
                    't' => $this->l('Your comment has been submitted'),
                    'desc' => $this->l('Send a notification email to customer when his/her comment was submitted and was automatically approved'),
                    'active'=> $default ? 1 :(int)Configuration::get('YBC_BLOG_ENABLE_MAIL_NEW_COMMENT'),
                    'tags' => $this->l('Available tags: [highlight]{shop_name}[end_highlight], [highlight]{shop_url}[end_highlight], [highlight]{customer_name}[end_highlight], [highlight]{post_title}[end_highlight], [highlight]{post_link}[end_highlight].')
                ),
                'approved_comment' => array( // YBC_BLOG_ENABLE_MAIL_APPROVED
                    'og' => 'Your comment has been approved',
                    't' => $this->l('Your comment has been approved'),
                    'desc' => $this->l('Send a notification email to customer when his/her comment was approved'),
                    'active'=> $default ? 1 :(int)Configuration::get('YBC_BLOG_ENABLE_MAIL_APPROVED'),
                    'tags' => $this->l('Available tags: [highlight]{shop_name}[end_highlight], [highlight]{shop_url}[end_highlight], [highlight]{customer_name}[end_highlight], [highlight]{post_title}[end_highlight], [highlight]{post_link}[end_highlight].')
                ),
                'edit_comment' => array( // YBC_BLOG_ENABLE_MAIL_EDIT_COMMENT
                    'og' => 'A comment is modified',
                    't' => $this->l('A comment is modified'),
                    'desc' => $this->l('Send a notification email to admin when a comment was modified by a customer'),
                    'active'=> $default ? 1 :(int)Configuration::get('YBC_BLOG_ENABLE_MAIL_EDIT_COMMENT'),
                    'tags' => $this->l('Available tags: [highlight]{shop_name}[end_highlight], [highlight]{shop_url}[end_highlight], [highlight]{customer_name}[end_highlight], [highlight]{post_title}[end_highlight], [highlight]{post_link}[end_highlight], [highlight]{comment_old}[end_highlight], [highlight]{comment}[end_highlight].')
                ),
                'edit_comment_customer' => array( // YBC_BLOG_ENABLE_MAIL_EDIT_COMMENT_CUSTOMER
                    'og' => 'Your comment has been updated',
                    't' => $this->l('Your comment has been updated'),
                    'desc' => $this->l('Send a notification email to customer when his/her comment was updated'),
                    'active'=> $default ? 1 :(int)Configuration::get('YBC_BLOG_ENABLE_MAIL_EDIT_COMMENT_CUSTOMER'),
                    'tags' => $this->l('Available tags: [highlight]{shop_name}[end_highlight], [highlight]{shop_url}[end_highlight], [highlight]{customer}[end_highlight], [highlight]{subject}[end_highlight] [highlight]{comment}[end_highlight], [highlight]{rating}[end_highlight].')
                ),
                'report_comment' => array( // YBC_BLOG_ENABLE_MAIL_REPORT
                    'og' => 'We have received your feedback',
                    't' => $this->l('We have received your feedback'),
                    'desc' => $this->l('Send a notification email to admin when customer reported a comment as abused'),
                    'active'=> $default ? 1 :(int)Configuration::get('YBC_BLOG_ENABLE_MAIL_REPORT'),
                    'tags' => $this->l('Available tags: [highlight]{shop_name}[end_highlight], [highlight]{shop_url}[end_highlight], [highlight]{author_name}[end_highlight], [highlight]{post_title}[end_highlight], [highlight]{post_link}[end_highlight], [highlight]{comment}[end_highlight], [highlight]{link_view_comment}[end_highlight], [highlight]{reporter}[end_highlight].')
                ),
                'reported_comment_customer' => array( // YBC_BLOG_ENABLE_MAIL_REPORTED_CUSTOMER
                    'og' => 'You have successfully reported a comment as abused',
                    't' => $this->l('You have successfully reported a comment as abused'),
                    'desc' => $this->l('Send a notification email to customer when his/her report was submitted successfully'),
                    'active'=> $default ? 1 :(int)Configuration::get('YBC_BLOG_ENABLE_MAIL_REPORTED_CUSTOMER'),
                    'tags' => $this->l('Available tags: [highlight]{shop_name}[end_highlight], [highlight]{shop_url}[end_highlight], [highlight]{post_title}[end_highlight], [highlight]{post_link}[end_highlight], [highlight]{comment}[end_highlight], [highlight]{reporter}[end_highlight].')
                ),
                'admin_reply_comment_to_customer' =>array( // YBC_BLOG_ENABLE_MAIL_REPLY_CUSTOMER
                    'og' => 'New reply to your comment',
                    't' => $this->l('New reply to your comment'),
                    'desc' => $this->l('Send a notification email to customer when his/her comment was replied by another customer'),
                    'active'=> $default ? 1 :(int)Configuration::get('YBC_BLOG_ENABLE_MAIL_REPLY_CUSTOMER'),
                    'tags' => $this->l('Available tags: [highlight]{shop_name}[end_highlight], [highlight]{shop_url}[end_highlight], [highlight]{post_title}[end_highlight], [highlight]{post_link}[end_highlight], [highlight]{customer_name}[end_highlight], [highlight]{replier}[end_highlight], [highlight]{comment_reply}[end_highlight].')
                ),
                'customer_reply_comment_to_admin_0' => array( // YBC_BLOG_ENABLE_MAIL_REPLY
                    'og' => 'A customer has replied to a comment on [post_title]',
                    't' => $this->l('A customer has replied to a comment on [post_title]'),
                    'desc' => $this->l('Send a notification email to author when a customer replied to a comment on his/her post and is waiting for approval'),
                    'active'=> $default ? 1 :(int)Configuration::get('YBC_BLOG_ENABLE_MAIL_REPLY'),
                    'tags' => $this->l('Available tags: [highlight]{shop_name}[end_highlight], [highlight]{shop_url}[end_highlight], [highlight]{post_title}[end_highlight], [highlight]{post_link}[end_highlight], [highlight]{author_name}[end_highlight], [highlight]{replier}[end_highlight], [highlight]{comment_reply}[end_highlight].')
                ),
                'customer_reply_comment_to_admin_1' => array(
                    'og' => 'A customer has replied to a comment on [post_title]',
                    't' => $this->l('A customer has replied to a comment on [post_title]'),
                    'desc' => $this->l('Send a notification email to author when a customer replied to a comment on his/her post and was approved automatically'),
                    'active'=> $default ? 1 :(int)Configuration::get('YBC_BLOG_ENABLE_MAIL_REPLY'),
                    'tags' => $this->l('Available tags: [highlight]{shop_name}[end_highlight], [highlight]{shop_url}[end_highlight], [highlight]{post_title}[end_highlight], [highlight]{post_link}[end_highlight], [highlight]{author_name}[end_highlight], [highlight]{replier}[end_highlight], [highlight]{comment_reply}[end_highlight].')
                ),
                'new_vote_admin' => array( // YBC_BLOG_ENABLE_MAIL_POLLS
                    'og' => 'A customer just left a feedback on your post',
                    't' => $this->l('A customer just left a feedback on your post'),
                    'desc' => $this->l('Send a notification email to admin when a customer left a feedback on a blog post'),
                    'active'=> $default ? 1 :(int)Configuration::get('YBC_BLOG_ENABLE_MAIL_POLLS'),
                    'tags' => $this->l('Available tags: [highlight]{shop_name}[end_highlight], [highlight]{shop_url}[end_highlight], [highlight]{post_title}[end_highlight], [highlight]{post_link}[end_highlight], [highlight]{feedback}[end_highlight], [highlight]{polls_helpful}[end_highlight].')
                ),
                'new_vote_customer' => array( // YBC_BLOG_ENABLE_MAIL_NEW_POLLS
                    'og' => 'We have received your feedback',
                    't' => $this->l('We have received your feedback'),
                    'desc' => $this->l('Send a notification email to customer when his/her feedback was submitted successfully'),
                    'active'=> $default ? 1 :(int)Configuration::get('YBC_BLOG_ENABLE_MAIL_NEW_POLLS'),
                    'tags' => $this->l('Available tags: [highlight]{shop_name}[end_highlight], [highlight]{shop_url}[end_highlight], [highlight]{post_title}[end_highlight], [highlight]{post_link}[end_highlight], [highlight]{customer_name}[end_highlight], [highlight]{feedback}[end_highlight], [highlight]{polls_helpful}[end_highlight].')
                ),
                'new_blog_admin_0' => array( // YBC_BLOG_ADMIN_EMAIL_NEW_POST
                    'og' => 'Community author submitted a new post',
                    't' => $this->l('Community author submitted a new post and is waiting for approval'),
                    'desc' => $this->l('Send a notification email to admin when a community author submitted a new post and is waiting for approval'),
                    'active'=> $default ? 1 :(int)Configuration::get('YBC_BLOG_ADMIN_EMAIL_NEW_POST'),
                    'tags' => $this->l('Available tags: [highlight]{shop_name}[end_highlight], [highlight]{shop_url}[end_highlight], [highlight]{post_title}[end_highlight], [highlight]{post_link}[end_highlight], [highlight]{customer_name}[end_highlight].')
                ),
                'new_blog_admin_1'=> array(
                    'og' => 'Community author submitted a new post',
                    't' => $this->l('Community author submitted a new post'),
                    'desc' => $this->l('Send a notification email to admin when a community author submitted a new post and was approved automatically'),
                    'active'=> $default ? 1 :(int)Configuration::get('YBC_BLOG_ADMIN_EMAIL_NEW_POST'),
                    'tags' => $this->l('Available tags: [highlight]{shop_name}[end_highlight], [highlight]{shop_url}[end_highlight], [highlight]{post_title}[end_highlight], [highlight]{post_link}[end_highlight], [highlight]{customer_name}[end_highlight].')
                ),
                'new_blog_customer_0'=> array( // YBC_BLOG_CUSTOMER_EMAIL_NEW_POST
                    'og' => 'Your post has been submitted',
                    't' => $this->l('Your post has been submitted and is waiting for approval'),
                    'desc' => $this->l('Send a notification email to community author when his/her post was submitted and is waiting for approval'),
                    'active'=> $default ? 1 :(int)Configuration::get('YBC_BLOG_CUSTOMER_EMAIL_NEW_POST'),
                    'tags' => $this->l('Available tags: [highlight]{shop_name}[end_highlight], [highlight]{shop_url}[end_highlight], [highlight]{post_title}[end_highlight], [highlight]{post_link}[end_highlight], [highlight]{customer_name}[end_highlight].')
                ),
                'new_blog_customer_1'=> array(
                    'og' => 'Your post has been submitted',
                    't' => $this->l('Your post has been submitted and approved'),
                    'desc' => $this->l('Send a notification email to community author when his/her post was submitted and was approved automatically'),
                    'active'=> $default ? 1 :(int)Configuration::get('YBC_BLOG_CUSTOMER_EMAIL_NEW_POST'),
                    'tags' => $this->l('Available tags: [highlight]{shop_name}[end_highlight], [highlight]{shop_url}[end_highlight], [highlight]{post_title}[end_highlight], [highlight]{post_link}[end_highlight], [highlight]{customer_name}[end_highlight].')
                ),
                'approved_blog_customer' => array( // YBC_BLOG_CUSTOMER_EMAIL_APPROVED_POST
                    'og' => 'Your post has been approved',
                    't' => $this->l('Your post has been approved'),
                    'desc' => $this->l('Send a notification email to customer when his/her blog post was approved'),
                    'active'=> $default ? 1 :(int)Configuration::get('YBC_BLOG_CUSTOMER_EMAIL_APPROVED_POST'),
                    'tags' => $this->l('Available tags: [highlight]{shop_name}[end_highlight], [highlight]{shop_url}[end_highlight], [highlight]{post_title}[end_highlight], [highlight]{post_link}[end_highlight], [highlight]{customer_name}[end_highlight].')
                ),
            );
        }
        return $template != null && isset(self::$cache_subject[$template]) ? self::$cache_subject[$template] : ($template == null ? self::$cache_subject : []);
    }
    public function initEmailTemplate($default=true)
    {
        $subjects = $this->getSubjects(null,$default);
        $partialQueries = [];
        foreach (array_keys($subjects) as $template) {
            $partialQueries[] = '(\'' . pSQL($template) . '\','.(int)$subjects[$template]['active'].')';
        }
        $res = Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'ybc_blog_email_template`;');
        $res &= Db::getInstance()->execute('TRUNCATE TABLE `' . _DB_PREFIX_ . 'ybc_blog_email_template_lang`');
        if ($partialQueries)
            $res &= Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ . 'ybc_blog_email_template` (`template`,`active`) VALUES' . implode(',', $partialQueries));
       
        if ($res) {
            $res &= Db::getInstance()->execute('
                INSERT INTO `' . _DB_PREFIX_ . 'ybc_blog_email_template_lang`(`id_ybc_blog_email_template`, `id_lang`, `subject`)
                SELECT et.id_ybc_blog_email_template, IFNULL(lang.id_lang, 0), \'\' `subject`
                FROM `' . _DB_PREFIX_ . 'ybc_blog_email_template` et 
                CROSS JOIN `' . _DB_PREFIX_ . 'lang` lang
            ');
            
            $templates = Db::getInstance()->executeS('
                SELECT etl.*, et.template, l.iso_code FROM `' . _DB_PREFIX_ . 'ybc_blog_email_template_lang` etl
                INNER JOIN  `' . _DB_PREFIX_ . 'ybc_blog_email_template` et ON (et.id_ybc_blog_email_template = etl.id_ybc_blog_email_template)
                INNER JOIN `' . _DB_PREFIX_ . 'lang` l ON (l.id_lang = etl.id_lang)
            ');
            if (count($templates) > 0) {
                $sql = 'UPDATE `' . _DB_PREFIX_ . 'ybc_blog_email_template_lang` SET ';
                $queries = [];
                foreach ($templates as $template) {
                    if (isset($template['template']) && trim($template['template']) !== '' && isset($subjects[trim($template['template'])]) && ($subject = $subjects[trim($template['template'])])) {
                        $text = self::getTextLang($subject['og'], trim($template['iso_code'])) ?: $subject['og'];
                        
                        $queries[] = $sql . '`subject`=\'' . pSQL($text) . '\' WHERE `id_ybc_blog_email_template`=' . (int)$template['id_ybc_blog_email_template'] . ' AND `id_lang`=' . (int)$template['id_lang'];
                    }
                }
                if ($queries)
                {
                    foreach($queries as $query)
                    {
                        $res &= Db::getInstance()->execute($query);
                    }
                }
            }
        }

        return $res;
    }
    public static function getTextLang($text, $iso_code, $specific = 'ybc_blog_email_template')
    {
        if ($iso_code == '' || !Validate::isLangIsoCode($iso_code)) {
            return $text;
        }
        $files_by_priority = _PS_MODULE_DIR_ . 'ybc_blog/translations/' . $iso_code . '.' . 'php';
        if (!@file_exists($files_by_priority)) {
            return $text;
        }
        $string = preg_replace("/\\\*'/", "\'", $text);
        $key = md5($string);
        $default_key = Tools::strtolower('<{ybc_blog}prestashop>' . ($specific ? Tools::strtolower($specific) : 'ybc_blog')) . '_' . $key;

        preg_match('/(\$_MODULE\[\'' . preg_quote($default_key) . '\'\]\s*=\s*\')(.*)(\';)/', Tools::file_get_contents($files_by_priority), $matches);

        if ($matches && isset($matches[2])) {
            return $matches[2];
        }
        return $text;
    }
    public function renderList($params=array())
    {
        $fields_list = array(
            'input_box' => array(
                'title'=> '',
                'type' => 'text',
            ),
            'id_ybc_blog_email_template' => array(
                'title' => $this->l('ID'),
                'width' => 40,
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'template'=> array(
                'title'=> $this->l('Template'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'subject'=> array(
                'title'=> $this->l('Subject'),
                'type' => 'text',
                'sort' => true,
                'filter' => true,
            ),
            'send_to' => array(
                'title' => $this->l('Send to'),
                'type' => 'select',
                'filter' => true,
                'strip_tag' => false,
                'filter_list' => array(
                    'id_option' => 'id_option',
                    'value' => 'title',
                    'list' => array(
                        0 => array(
                            'id_option' => 1,
                            'title' => $this->l('Customer')
                        ),
                        1 => array(
                            'id_option' => 0,
                            'title' => $this->l('Admin')
                        )
                    )
                )
            ),
            'content_txt'=> array(
                'title'=> $this->l('Content'),
                'type' => 'text',
            ),
            'active' =>array(
                'title' => $this->l('Enabled'),
                'type' => 'active',
                'sort' => true,
                'filter' => true,
                'strip_tag' => false,
                'filter_list' => array(
                    'id_option' => 'id_option',
                    'value' => 'title',
                    'list' => array(
                        0 => array(
                            'id_option' => 1,
                            'title' => $this->l('Yes')
                        ),
                        1 => array(
                            'id_option' => 0,
                            'title' => $this->l('No')
                        )
                    )
                )
            ),
        );
        $filter ='';
        if(isset($params['id_ybc_blog_email_template']) && ($id_ybc_blog_email_template = (int)$params['id_ybc_blog_email_template']) )
            $filter .=' AND et.id_ybc_blog_email_template='.(int)$id_ybc_blog_email_template;
        if(isset($params['template']) && ($template = $params['template']) && Validate::isCleanHtml($template))
            $filter .=' AND et.template LIKE "%'.pSQL($template).'%"';
        if(isset($params['active']) &&  ($active = $params['active'])!='' && Validate::isInt($active))
            $filter .=' AND et.active = "'.(int)$active.'"';
        if(isset($params['subject']) &&  ($subject = $params['subject'])!='' && Validate::isCleanHtml($subject))
            $filter .=' AND etl.subject LIKE "%'.pSQL($subject).'%"';
        if(isset($params['send_to']) &&  ($send_to = $params['send_to'])!='' && Validate::isInt($send_to))
        {
            if($send_to==1)
                $filter .=' AND (et.template LIKE "%_customer%" OR et.template="approved_comment")';
            else
                $filter .=' AND et.template NOT LIKE "%_customer%" AND et.template != "approved_comment"';
        }
        if($filter)
            $show_reset=true;    
        else
           $show_reset=false; 
        $sort = "";
        $sort_type= isset($params['sort_type']) && $params['sort_type'] ? $params['sort_type']:'asc';
        $sort_value =isset($params['sort']) && $params['sort'] ? $params['sort']: 'id_ybc_blog_email_template';
        if($sort_value)
        {
            switch ($sort_value) {
                case 'id_ybc_blog_email_template':
                    $sort .=' et.id_ybc_blog_email_template';
                    break;
                case 'template':
                    $sort .=' et.template';
                    break;
                case 'subject':
                    $sort .=' etl.subject';
                    break;
                case 'active':
                    $sort .=' et.active';
                    break;
    
            }
            if($sort && $sort_type && in_array($sort_type,array('asc','desc')))
                $sort .= ' '.$sort_type;
        }
        $module = Module::getInstanceByName('ybc_blog');
        $page = isset($page['page']) && $params['page'] ? (int)$params['page']:1;
        if($page<=1)
            $page =1;
        $totalRecords = $this->getEmailTemplatesWithFilter($filter, null, null, null,true);    ;
        $paggination = new Ybc_blog_paggination_class();            
        $paggination->total = $totalRecords;
        $paggination->url = Context::getContext()->link->getAdminLink('AdminYbcBlogSetting', true).'&control=email&page=_page_'.$module->getUrlExtra($fields_list);
        $paggination->limit =  isset($params['paginator_ybc_email_select_limit'])  && $params['paginator_ybc_email_select_limit']? $params['paginator_ybc_email_select_limit']:20;
        $paggination->name ='ybc_email';
        $totalPages = ceil($totalRecords / $paggination->limit);
        if($page > $totalPages)
            $page = $totalPages;
        $paggination->page = $page;
        $start = $paggination->limit * ($page - 1);
        if($start < 0)
            $start = 0;
        $templates = $this->getEmailTemplatesWithFilter($filter, $sort, $start, $paggination->limit,false);        
        if($templates)
        {
            $theme = (version_compare(_PS_VERSION_, '1.7', '>=') ? Context::getContext()->shop->theme->getName() : Context::getContext()->shop->getTheme());
            $basePathList = array(
                _PS_ROOT_DIR_ . '/themes/' . $theme . '/modules/ybc_blog/mails/',
                $module->getLocalPath() . 'mails/',
            );
            foreach($templates as &$template)
            {
                if(Tools::strpos($template['template'],'_customer') >0 || $template['template']=='approved_comment')
                    $template['send_to'] = $this->l('Customer');
                else
                    $template['send_to'] = $this->l('Admin');
                foreach ($basePathList as $path) {
                    $flag = false;
                    $iso_path = $path . Context::getContext()->language->iso_code . '/' . $template['template'];
                    if (@file_exists($iso_path . '.html')) {
                        $template['content_html'] = Tools::file_get_contents($iso_path . '.html');
                        $flag = true;
                    }
                    if (@file_exists($iso_path . '.txt')) {
                        $template['content_txt'] = Tools::file_get_contents($iso_path . '.txt');
                    }
                    if ($flag)
                        break;
                }
            }
        }
        $paggination->text =  $this->l('Showing {start} to {end} of {total} ({pages} Pages)');
        $paggination->style_links = $this->l('links');
        $paggination->style_results = $this->l('results');
        $listData = array(
            'name' => 'ybc_email',
            'actions' => array('view'),
            'currentIndex' => Context::getContext()->link->getAdminLink('AdminYbcBlogSetting', true).'&control=email'.($paggination->limit!=20 ? '&paginator_ybc_email_select_limit='.$paggination->limit:''),
            'identifier' => 'id_ybc_blog_email_template',
            'show_toolbar' => true,
            'show_action' => true,
            'title' => $this->l('Email templates'),
            'fields_list' => $fields_list,
            'field_values' => $templates,
            'paggination' => $paggination->render(),
            'filter_params' => $module->getFilterParams($fields_list),
            'show_reset' =>  $show_reset,
            'totalRecords' => $totalRecords,
            'preview_link' => false,
            'sort' => $sort_value ? : 'id_ybc_blog_email_template',   
            'sort_type' => $sort_type,     
            'show_add_new' => false,    
            'icon'=> 'icon-email' ,
            'show_bulk_action' => true, 
        );            
        return $module->renderList($listData);   
    }
    public static function getEmailTemplatesWithFilter($filter='',$sort='',$start=0,$limit=10,$total=false)
    {
        $id_lang = (int)Context::getContext()->language->id;
        if($total)
            $sql ='SELECT COUNT(DISTINCT et.id_ybc_blog_email_template) FROM  `'._DB_PREFIX_.'ybc_blog_email_template` et';
        else
            $sql ='SELECT et.*,etl.subject FROM  `'._DB_PREFIX_.'ybc_blog_email_template` et';
        $sql .=' LEFT JOIN  `'._DB_PREFIX_.'ybc_blog_email_template_lang` etl ON (et.id_ybc_blog_email_template=etl.id_ybc_blog_email_template AND etl.id_lang="'.(int)$id_lang.'")
        WHERE 1 '.($filter ? $filter: '');
        if($total)
            return Db::getInstance()->getValue($sql);
        else
        {
            $sql .=($sort ? ' ORDER BY '.$sort: ' ORDER BY et.id_ybc_blog_email_template asc').' LIMIT '.(int)$start.','.(int)$limit.'';
            return Db::getInstance()->executeS($sql);
        }    
    }
    public function renderForm()
    {
        $module = Module::getInstanceByName('ybc_blog');
        $subjects = $this->getSubjects();
        $fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Edit email template'),	
                    'icon'=> 'icon-email'			
				),
				'input' => array(	
                    array(
						'type' => 'switch',
						'label' => $this->l('Enabled'),
						'name' => 'active',
                        'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						),
                        'desc' => isset($subjects[$this->template]) ? $subjects[$this->template]['desc']:'',					
					),				
					array(
						'type' => 'text',
						'label' => $this->l('Subject'),
						'name' => 'subject',
						'lang' => true,    
                        'required' => true, 
                        'class' => 'title',                             
					),
                    array(
                        'type' => 'textarea',
						'label' => $this->l('Content in TXT form'),
						'name' => 'content_txt',
						'lang' => true, 
                        'required' => true,
                        'desc' => isset($subjects[$this->template]['tags']) ? $subjects[$this->template]['tags']:'',
                    ),
                    array(
                        'type' => 'textarea',
						'label' => $this->l('Content in HTML form'),
						'name' => 'content_html',
						'lang' => true,    
                        'required' => true,
                        'autoload_rte' => true,
                        'desc' => isset($subjects[$this->template]['tags']) ? $subjects[$this->template]['tags']:'',
                    ),
                    
                    array(
                        'type' => 'hidden', 
                        'name' => 'id_ybc_blog_email_template'
                    )
                ),
                'submit' => array(
					'title' => $this->l('Save'),
				),
            ),
		);
        $helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = 'ybc_blog_email_template';
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->module = $module;
		$helper->identifier = 'id_ybc_blog_email_template';
		$helper->submit_action = 'saveEmailTemplate';
		$helper->currentIndex = Context::getContext()->link->getAdminLink('AdminYbcBlogSetting', false).'&control=email&id_ybc_blog_email_template='.$this->id;
		$helper->token = Context::getContext()->employee->id ? Tools::getAdminTokenLite('AdminYbcBlogSetting'): false;
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->tpl_vars = array(
			'base_url' => Context::getContext()->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
            'PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'),
			'fields_value' => $module->getFieldsMailTemplateValues($this),
			'languages' => Context::getContext()->controller->getLanguages(),
			'id_language' => Context::getContext()->language->id,
            'link' => Context::getContext()->link,
            'post_key' => 'id_ybc_blog_email_template',
            'cancel_url' => Context::getContext()->link->getAdminLink('AdminYbcBlogSetting', true).'&control=email',
            'image_baseurl' => _PS_YBC_BLOG_IMG_.'post/',
            'image_baseurl_thumb' => _PS_YBC_BLOG_IMG_.'post/thumb/',
		);
        return $helper->generateForm(array($fields_form));	
    }

    public function previewTemplate()
    {
        $module = Module::getInstanceByName('ybc_blog');
        $languages = Language::getLanguages();
        $theme = (version_compare(_PS_VERSION_, '1.7', '>=') ? Context::getContext()->shop->theme->getName() : Context::getContext()->shop->getTheme());
        $basePathList = array(
            _PS_ROOT_DIR_ . '/themes/' . $theme . '/modules/ybc_blog/mails/',
            $module->getLocalPath() . 'mails/',
        );
        $content_txt = array();
        $content_html = array();
        foreach($languages as $language)
        {
            $id_lang = (int)$language['id_lang'];
            foreach ($basePathList as $path) {
                $flag = false;
                $iso_path = $path . $language['iso_code'] . '/' . $this->template;
                if (@file_exists($iso_path . '.html')) {
                    $content_html[$id_lang] = Tools::file_get_contents($iso_path . '.html');
                    $flag = true;
                }
                if (@file_exists($iso_path . '.txt')) {
                    $content_txt[$id_lang] = Tools::file_get_contents($iso_path . '.txt');
                }
                if ($flag)
                    break;
            }
        }
        if (false !== Configuration::get('PS_LOGO_MAIL') &&
            file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO_MAIL'))
        ) {
            $logo =  Configuration::get('PS_LOGO_MAIL');
        } else {
            if (file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO'))) {
                $logo = Configuration::get('PS_LOGO');
            } else {
                $logo ='';
            }
        }
        if($logo)
            $PS_SHOP_LOGO = Context::getContext()->link->getMediaLink(_PS_IMG_.$logo);
        else
            $PS_SHOP_LOGO ='';
        Context::getContext()->smarty->assign(
            array(
                'languages' => $languages,
                'content_html' => $content_html,
                'content_txt' => $content_txt,
                'id_curent_lang' => Context::getContext()->language->id,
                'PS_SHOP_LOGO' => $PS_SHOP_LOGO,
                'PS_SHOP_NAME' => Configuration::get('PS_SHOP_NAME'),
                'PS_SHOP_URL' => Context::getContext()->link->getPageLink('index'),
            )
        );
        return Context::getContext()->smarty->fetch(_PS_MODULE_DIR_.'ybc_blog/views/templates/hook/email_peview.tpl');
    }
    public static function getSubjectByTemplate($template,$id_lang=0)
    {
        if(!$id_lang)
        {
            $id_lang = Context::getContext()->language->id;
        }
        $sql = 'SELECT et.id_ybc_blog_email_template,etl.subject FROM `'._DB_PREFIX_.'ybc_blog_email_template` et
        LEFT JOIN `'._DB_PREFIX_.'ybc_blog_email_template_lang` etl ON (et.id_ybc_blog_email_template=etl.id_ybc_blog_email_template AND etl.id_lang="'.(int)$id_lang.'")
        WHERE et.active=1 AND et.template="'.pSQL($template).'"';
        if($template = Db::getInstance()->getRow($sql))
        {
            return $template['subject'];
        }
        return false;
    }
    public static function submitBulkEnabled($ids)
    {
        if($ids)
        {
            return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ybc_blog_email_template` SET active=1 WHERE id_ybc_blog_email_template IN ('.implode(',',array_map('intval',array_keys($ids))).')');
        }
        return true;
    }
    public static function submitBulkDiasabled($ids)
    {
        if($ids)
        {
            return Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ybc_blog_email_template` SET active=0 WHERE id_ybc_blog_email_template IN ('.implode(',',array_map('intval',array_keys($ids))).')');
        }
        return true;
    }
}