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

class Ets_affiliatemarketingRefer_friendsModuleFrontController extends Ets_affiliatemarketingAllModuleFrontController
{
    public $auth = true;
    public $guestAllowed = false;
    public $authRedirection = URL_REF_PROGRAM;

    public function init()
    {
        parent::init();
        if (!$this->module->is17) {
            $this->display_column_left = false;
            $this->display_column_right = false;
        }
        if(!$this->context->customer->isLogged())
            Tools::redirect($this->context->link->getPageLink('my-account'));
    }

    /**
     * @throws PrestaShopException
     */
    public function initContent()
    {
        parent::initContent();
        $link_tab = array(
            'my_friends' =>  Ets_AM::getBaseUrlDefault('myfriend'),
            'ref_friends' => Ets_AM::getBaseUrlDefault('refer_friends'),
        );
        //Set meta
        $page= 'module-'.$this->module->name.'-refer_friends';
        $meta = Meta::getMetaByPage($page,$this->context->language->id);
        $this->setMetas(array(
            'title' => isset($meta['title']) && $meta['title'] ? $meta['title'] : $this->module->l('How to refer friends','refer_friends'),
            'keywords' => isset($meta['keywords']) && $meta['keywords'] ? $meta['keywords'] : $this->module->l('Referral','refer_friends'),
            'description' => isset($meta['description']) && $meta['description'] ? $meta['description'] : $this->module->l('Referral','refer_friends'),
        ));

        $this->context->smarty->assign(array(
            'link_tab' => $link_tab,
        ));

        if(Ets_Sponsor::isRefferalProgramReady()){
            $customer = $this->context->customer;
            /* == Check state program ======*/
            $alert_type = '';
            $userExists = Ets_User::getUserByCustomerId($this->context->customer->id);
            if($userExists){
                if( $userExists['status'] == -1){
                    $alert_type = 'account_banned';
                }
                elseif($userExists['status'] > 0 && $userExists['ref'] == 1 ){
                    $alert_type = 'registered';
                }
                elseif($userExists['status'] > 0 && $userExists['ref'] == -1 ){
                    $alert_type = 'program_suspened';
                }
                elseif($userExists['status'] > 0 && $userExists['ref'] == -2 ){
                    $alert_type = 'program_declined';
                } 
                else {
                    $p = Ets_Participation::getProgramRegistered($this->context->customer->id, 'ref');
                    if($p){
                        if($p['status'] == 0){
                            $alert_type = 'register_success';
                        }
                        elseif($p['status'] == 1){
                            $alert_type = 'registered';
                        }
                        elseif($p['status'] < 0){
                            $alert_type = 'program_declined';
                        }
                    }
                    else{

                        if(Configuration::get('ETS_AM_REF_REGISTER_REQUIRED')){
                            $url_register = Ets_AM::getBaseUrlDefault('register',array('p'=>'ref'));
                            Tools::redirect($url_register);
                        }
                    }
                }
            } else{
                $p = Ets_Participation::getProgramRegistered($this->context->customer->id, 'ref');
                if($p){
                    if($p['status'] == 0){
                        $alert_type = 'register_success';
                    }
                    elseif($p['status'] == 1){
                        $alert_type = 'registered';
                    }
                    elseif($p['status'] < 0){
                        $alert_type = 'program_declined';
                    }
                }
                else{

                    if(Configuration::get('ETS_AM_REF_REGISTER_REQUIRED')){
                        $url_register = Ets_AM::getBaseUrlDefault('register',array('p'=>'ref'));
                        Tools::redirect($url_register);
                    }
                }
            }

            $message = '';
            if(!$alert_type){
                $res_data = Ets_Sponsor::canUseRefferalProgramReturn(Context::getContext()->customer->id);
                if(!$res_data['success']){
                    $alert_type = 'need_condition';
                    $message = Configuration::get('ETS_AM_REF_MSG_CONDITION', $this->context->language->id) ? strip_tags(Configuration::get('ETS_AM_REF_MSG_CONDITION', $this->context->language->id)) : '';
                    if(isset($res_data['min_order']) && isset($res_data['total_order'])){
                        $message  = str_replace('[min_order_total]', Ets_affiliatemarketing::displayPrice(Tools::convertPrice($res_data['min_order'], $this->context->currency->id, true), $this->context->currency->id), $message);
                        $message  = str_replace('[total_past_order]', Ets_affiliatemarketing::displayPrice(Tools::convertPrice($res_data['total_order'], $this->context->currency->id, true), $this->context->currency->id), $message);
                        $message  = str_replace('[amount_left]', Ets_affiliatemarketing::displayPrice(Tools::convertPrice((float)$res_data['min_order'] - (float)$res_data['total_order'], $this->context->currency->id, true), $this->context->currency->id), $message);
                    }
                    elseif(isset($res_data['not_in_group'])){
                        $message = '';
                    }
                    if(!$message){
                        //Tools::redirect($this->context->link->getPageLink('my-account', true));
                    }
                }
            }
            $this->context->smarty->assign(array(
                'alert_type' => $alert_type,
                'message' => $message
            ));
            /* == End Check state program ======*/
                
            if (Ets_Sponsor::isJoinedRef($customer->id)  && (!$alert_type || $alert_type == 'registered')) {
                //Set meta
                if(Tools::isSubmit('load_more') && Tools::isSubmit('ajax'))
                {
                    $params = array(
                        'id_customer' => $this->context->customer->id
                    );
                    if ($page = (int)Tools::getValue('page')) {
                        $params['page'] = $page;
                    }
                    $this->context->smarty->assign(
                        array(
                            'invitations' => Ets_Invitation::getInvitations($params),
                        )
                    );
                    die(
                        json_encode(
                            array(
                                'list_html' => $this->module->display($this->module->getLocalPath(),'list_invitations.tpl'),
                            )
                        )
                    );
                }
                $this->setMetas(array(
                    'title' => $this->module->l('How to refer friend','refer_friends'),
                    'keywords' => $this->module->l('How to refer friend','refer_friends'),
                    'description' => $this->module->l('How to refer friend','refer_friends'),
                ));
                $template = 'sponsorship_refer_friend.tpl';
                $total_sent = Ets_Invitation::totalEmailInvited($customer->id);
                $banner = Ets_Banner::getBanerByIdCustomer($customer->id);
                $banner_img = '';
                if ($banner && is_array($banner)) {
                    $banner_img = _PS_ETS_EAM_IMG_.$banner['img'];
                    $banner_is_default = false;
                } else {
                    $banner_default = Configuration::get('ETS_AM_REF_DEFAULT_BANNER');
                    if ($banner_default) {
                        $banner_img = _PS_ETS_EAM_IMG_ . $banner_default;
                    }
                    $banner_is_default = true;
                }
                $params = array(
                    'id_customer' => $customer->id
                );
                if ($page = (int)Tools::getValue('page')) {
                    $params['page'] = $page;
                }
                $max_email_invatation = Configuration::get('ETS_AM_REF_MAX_INVITATION');
                if ($max_email_invatation) {
                    $max_email_invatation = (int)$max_email_invatation;
                } else {
                    if (!$max_email_invatation && $max_email_invatation != '0') {
                        $max_email_invatation = 'unlimited';
                    }
                }
                $url_ref = $this->context->link->getPageLink('index',null,null,array('refs' => $customer->id));
                $qrcodeImage = $customer->id.'_'.$this->context->shop->id.'.png';
                if(@file_exists(EAM_PATH_IMAGE_BANER.'qrcode/'.$qrcodeImage) || Ets_aff_qr_code::createQRCode($qrcodeImage, $url_ref))
                    $file_qr_image = _PS_ETS_EAM_IMG_.'qrcode/'.$customer->id.'_'.$this->context->shop->id.'.png';
                else
                    $file_qr_image = null;
                $this->context->smarty->assign(array(
                    'ets_customer' => $customer,
                    'url_ref' => $url_ref,
                    'total_email_sent' => $total_sent,
                    'file_qr_image' => $file_qr_image,
                    'max_invitation' => $max_email_invatation,
                    'invitation_left' => $max_email_invatation !== 'unlimited' ? ($max_email_invatation - $total_sent) : '',
                    'banner' => $banner_img,
                    'embed_code' => $this->getImgBanner(Ets_Banner::renderBannerCode($customer->id, $banner_img)),
                    'explaination' => Configuration::get('ETS_AM_REF_TEXT_EXPLANATION', $this->context->language->id),
                    'enable_invitation' => (int)Configuration::get('ETS_AM_REF_EMAIL_INVITE_FRIEND'),
                    'invitations' => Ets_Invitation::getInvitations($params),
                    'allow_upload_banner' => (int)Configuration::get('ETS_AM_REF_ALLOW_CUSTOM_BANNER'),
                    'resize_width' => Configuration::get('ETS_AM_RESIZE_BANNER_WITH'),
                    'resize_height' => Configuration::get('ETS_AM_RESIZE_BANNER_HEIGHT'),
                    'banner_is_default' => $banner_is_default,
                ));
                $this->context->smarty->assign(array(
                    'template' => $template,
                    'tab_active' => 'how-to-refer-friends',
                    'query' => Tools::getAllValues()
                ));
            }
            else{
                if(!$alert_type){
                    $alert_type = 'register_success';
                }
                
                $template = 'my_friends.tpl';
                $this->context->smarty->assign(array(
                    'alert_type' => $alert_type,
                    'template' => $template,
                ));
            }
        }
        else{
            $this->context->smarty->assign(array(
                'alert_type' => 'disabled',
            ));
        };
        if(Configuration::get('ETS_AM_SELL_APPLY_DISCOUNT')=='PERCENT')
        {
            $discount_value = Configuration::get('ETS_AM_SELL_REDUCTION_PERCENT').'%';
        }
        elseif(Configuration::get('ETS_AM_SELL_APPLY_DISCOUNT')=='AMOUNT')
        {
            if($id_currency = Configuration::get('ETS_AM_SELL_ID_CURRENCY'))
                $currency = new Currency($id_currency);
            else
                $currency = $this->context->currency;
            $discount_value = Ets_affiliatemarketing::displayPrice(Configuration::get('ETS_AM_SELL_REDUCTION_AMOUNT'),$currency);
        }
        else
            $discount_value ='';
        $this->context->smarty->assign(
            array(
                'voucher_code' => Ets_Voucher::getVoucherCodeByIDCustomer($this->context->customer->id),
                'ETS_AM_REF_DISPLAY_URL' => Configuration::hasKey('ETS_AM_REF_DISPLAY_URL') ? (int)Configuration::get('ETS_AM_REF_DISPLAY_URL'):1,
                'ETS_AM_SELL_OFFER_VOUCHER' => Configuration::get('ETS_AM_SELL_OFFER_VOUCHER'),
                'ETS_AM_REF_VOUCHER_CODE_DESC' => strip_tags(str_replace('[discount_value]',$discount_value,Configuration::get('ETS_AM_REF_VOUCHER_CODE_DESC',$this->context->language->id) ? Configuration::get('ETS_AM_REF_VOUCHER_CODE_DESC',$this->context->language->id) : $this->module->l('Share this voucher code to your friends. They will get [discount_value] off for their order and you will also get commission on your friends` orders.','refer_friends'))) 
            )
        );
        if ($this->module->is17) {
            $this->setTemplate('module:ets_affiliatemarketing/views/templates/front/sponsorship.tpl');
        } else {
            $this->setTemplate('sponsorship16.tpl');
        }
    }

    protected function sendMailInviting($mail_datas)
    {
        if(!Configuration::get('ETS_AM_REF_EMAIL_INVITE_FRIEND') || !$this->context->customer->isLogged())
        {
            die(json_encode(array(
                'success' => false,
                'message' => $this->module->l('You do not have permission to send this email', 'refer_friends'),
                'limited' => true
            )));
        }
        if ($mail_datas && isset($mail_datas['email']) && Validate::isEmail($mail_datas['email'])) {
            $name = $mail_datas['name'];
            $email = $mail_datas['email'];
            $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
            $id_shop = $this->context->shop->id;
            $id_customer = $this->context->customer->id;
            $link_ref = $this->context->link->getPageLink('authentication',null,null,array('create_account'=>1,'refs'=>$id_customer));
            //Check remail number emails can be send
            $total_sent = Ets_Invitation::totalEmailInvited($id_customer);
            $max_email_invatation = Configuration::get('ETS_AM_REF_MAX_INVITATION');
            if ($max_email_invatation) {
                $max_email_invatation = (int)$max_email_invatation;
            } else {
                if (!$max_email_invatation && $max_email_invatation != '0') {
                    $max_email_invatation = 'unlimited';
                }
            }
            $total_left = 'unlimited';
            if ($max_email_invatation != 'unlimited') {
                $total_left = $max_email_invatation - $total_sent;
            }
            //Send mail
            if ($max_email_invatation == 'unlimited' || $total_left === 'unlimited' || $total_left > 0) {
                $limited = false;
                $email_invited = Ets_Invitation::emailIsInvited($email);
                if (!$email_invited) {
                    if ($this->sendMail(array('email' => $email, 'name' => $name), $link_ref, $language->id, $id_shop)) {
                        die(json_encode(array(
                            'success' => true,
                            'message' => $this->module->l('Email sent successfully.', 'refer_friends'),
                            'limited' => $limited
                        )));
                    }
                    die(json_encode(array(
                        'success' => false,
                        'message' => $this->module->l('Could not send email. Please check your mail configuration or network and try again.', 'refer_friends'),
                        'limited' => $limited
                    )));

                } else {
                    die(json_encode(array(
                        'success' => false,
                        'message' => sprintf($this->module->l('Email %s has been registered. Please invite another friend', 'refer_friends'), $email),
                        'limited' => $limited
                    )));
                }

            } else {
                die(json_encode(array(
                    'success' => false,
                    'message' => $this->module->l('Your email invitations are limited', 'refer_friends'),
                    'limited' => true
                )));
            }
        }
        die(json_encode(array(
            'success' => false,
            'message' => $this->module->l('There was an error occurred while sending email', 'refer_friends'),
            'limited' => true
        )));
    }

    protected function sendMail($mail, $link_ref)
    {
        $email = $mail['email'];
        $username = $mail['name'];
        $data = array(
            '{email}' => $email,
            '{username}' => $username,
            '{your_friend}' => $this->context->customer->firstname.' '. $this->context->customer->lastname,
            '{link_ref}' => $link_ref
        );
        $subjects = array(
            'translation' => $this->module->l('You are invited to join us','refer_friends'),
            'origin'=> 'You are invited to join us',
            'specific'=>'refer_friends'
        );
        $mail_sent = Ets_aff_email::send(0,'invite_referral',$subjects,$data,array('customer'=>$email));
        //Add to database if sent success
        if ($mail_sent) {

            $id_friend = Ets_Invitation::getIdCustomerByEmail($email);

            $invitation = new Ets_Invitation();
            $invitation->email = $email;
            $invitation->name = $username;
            $invitation->datetime_sent = date('Y-m-d H:i:s');
            $invitation->id_friend = $id_friend;
            $invitation->id_sponsor = $this->context->customer->id;
            $invitation->add();
            return true;
        }
        return false;
    }

    protected function uploadBanner($banner)
    {
        $error = false;
        if($banner['error'] <= 0){
            $allowExtentions = array('png', 'jpg', 'jpeg', 'gif');
            $imagesize = @getimagesize($banner['tmp_name']);
            $ext = Tools::strtolower(Tools::substr(strrchr($banner['name'], '.'), 1));
            $ext2 = Tools::strtolower(Tools::substr(strrchr($imagesize['mime'], '/'), 1));
            $max_file_size = Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')*1024*1024;
            if(!Configuration::get('ETS_AM_REF_ALLOW_CUSTOM_BANNER'))
                $error = $this->module->l('You do not have permission to upload this banner','refer_friends');
            elseif(!Validate::isFileName(str_replace(' ', '', $banner['name'])))
            {
                $error = sprintf($this->module->l('The file name "%s" is invalid','refer_friends'),$banner['name']);
            }
            elseif(!in_array($ext, $allowExtentions) || !in_array($ext2, $allowExtentions))
            {
                $error = sprintf($this->module->l('The file name "%s" is not in the correct format, accepted formats: %s','refer_friends'),$banner['name'],'.'.trim(implode(', .',$allowExtentions),', .'));
            }
            elseif($banner['size'] > $max_file_size)
                $error = sprintf($this->module->l('The file name "%s" is too large. Limit: %s','refer_friends'),$banner['name'],Tools::ps_round($max_file_size/1048576,2).'Mb');
            elseif (in_array($ext, $allowExtentions) && in_array($ext2, $allowExtentions)){
                $id_customer = $this->context->customer->id;
                //create path
                Ets_AM::createPath( EAM_PATH_IMAGE_BANER);
                $img_name = $id_customer . '.jpg';
                $path_img =  EAM_PATH_IMAGE_BANER . $img_name;
                // Upload file
                $banner_exists = Ets_Banner::getBanerByIdCustomer($id_customer);
                $img_exists = $banner_exists && is_array($banner_exists) ? $banner_exists['img'] : '';
                $tmp_name = sha1(microtime()).$img_exists;
                if($img_exists && file_exists( EAM_PATH_IMAGE_BANER.$img_exists)){
                    Ets_affiliatemarketing::makeCacheDir();
                    rename( EAM_PATH_IMAGE_BANER.$img_exists, _PS_CACHE_DIR_.'ets_affiliatemarketing/'.$tmp_name);
                    @unlink( EAM_PATH_IMAGE_BANER.$img_exists);
                }
                $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                if(move_uploaded_file($banner['tmp_name'], $temp_name)){
                    Ets_AM::createPath( EAM_PATH_IMAGE_BANER);
                    $resize_with = null;
                    $resize_height = null;
                    if((int)Configuration::get('ETS_AM_RESIZE_BANNER')){
                        $resize_with = (int)Configuration::get('ETS_AM_RESIZE_BANNER_WITH');
                        $resize_height = (int)Configuration::get('ETS_AM_RESIZE_BANNER_HEIGHT');
                    }
                    if(ImageManager::resize($temp_name, $path_img, $resize_with, $resize_height)){
                        if (isset($temp_name) && file_exists($temp_name)) {
                            @unlink($temp_name);
                        }
                        if(file_exists(_PS_CACHE_DIR_.'ets_affiliatemarketing/'.$tmp_name))
                            @unlink(_PS_CACHE_DIR_.'ets_affiliatemarketing/'.$tmp_name);
                        if ($banner_exists && is_array($banner_exists)) {
                            $b = new Ets_Banner($banner_exists['id_ets_am_banner']);
                            if ($b) {
                                $b->img = $img_name;
                                $b->datetime_added = date('Y-m-d H:i:s');
                                $b->update();
                            }
                        } else {
                            //Add to database
                            $b = new Ets_Banner();
                            $b->id_sponsor = $id_customer;
                            $b->datetime_added = date('Y-m-d H:i:s');
                            $b->img = $img_name;
                            $b->save();
                        }
                        $path_img =  _PS_ETS_EAM_IMG_ . $img_name;
                        die(json_encode(array(
                            'success' => true,
                            'message' =>$this->module->l('Successful update','refer_friends'),
                            'img' => $path_img,
                            'embed_code' => $this->getImgBanner(Ets_Banner::renderBannerCode($id_customer, $path_img))
                        )));
                    }
                    else
                    {
                        $error = $this->module->l('An error occurred while uploading the image','refer_friends');
                        if(isset($tmp_name) && $tmp_name && isset($img_exists) && $img_exists && file_exists(_PS_CACHE_DIR_.'ets_affiliatemarketing/'.$tmp_name))
                        {
                            rename( _PS_CACHE_DIR_.'ets_affiliatemarketing/'.$tmp_name,  EAM_PATH_IMAGE_BANER.$img_exists);
                            @unlink(_PS_CACHE_DIR_.'ets_affiliatemarketing/'.$tmp_name);
                        }
                    }
                    if (isset($temp_name) && file_exists($temp_name)) {
                        @unlink($temp_name);
                    }
                }
                else
                {
                    $error = $this->module->l('An error occurred while uploading the image','refer_friends');
                    if($img_exists)
                    {
                        if(file_exists(_PS_CACHE_DIR_.'ets_affiliatemarketing/'.$tmp_name))
                        {
                            rename( _PS_CACHE_DIR_.'ets_affiliatemarketing/'.$tmp_name,  EAM_PATH_IMAGE_BANER.$img_exists);
                            @unlink(_PS_CACHE_DIR_.'ets_affiliatemarketing/'.$tmp_name);
                        }
                        
                    }    
                }
            }
            if($error)
            {
                die(json_encode(array(
                    'success' => false,
                    'message' => $error
                )));
            }
            
        }
        
    }

    public function deleteBanner($id_customer)
    {
        if(!Configuration::get('ETS_AM_REF_ALLOW_CUSTOM_BANNER'))
        {
            die(json_encode(array(
                'success' => false,
                'message' => $this->module->l('You do not have permission to delete this banner', 'refer_friends')
            )));
        }
        $banner = Ets_Banner::deleteBanner($id_customer);
        if ($banner !== false) {
            die(json_encode(array(
                'success' => true,
                'message' => $this->module->l('Sponsor banner deleted','refer_friends'),
                'img' => $banner,
                'embed_code' => $this->getImgBanner(Ets_Banner::renderBannerCode($id_customer, $banner))
            )));
        }
        die(json_encode(array(
            'success' => false,
            'message' => $this->module->l('Could not delete banner', 'refer_friends')
        )));
    }
    public function postProcess()
    {
        $customer = $this->context->customer;
        //Send mail invitation
        if (Tools::isSubmit('send_mail_invite')) {
            if(!$this->isTokenValid()){
                die(json_encode(array(
                    'success' => false,
                    'message' =>$this->module->l('Token is invalid', 'refer_friends')
                )));
            }
            if ($mails_inviting = Tools::getValue('mails')) {
                $this->sendMailInviting($mails_inviting);
            } else {
                die(json_encode(array(
                    'success' => false,
                    'message' => $this->module->l('Error', 'refer_friends')
                )));
            }
        }

        //Upload banner
        if (Tools::isSubmit('upload_banner')) {
            if(!$this->isTokenValid()){
                die(json_encode(array(
                    'success' => false,
                    'message' => $this->module->l('Token is invalid', 'refer_friends')
                )));
            }
            if (isset($_FILES['banner'])) {
                $fileName = str_replace(' ', '', $_FILES['banner']['name']);
                if(!Validate::isFileName($fileName)){
                    die(json_encode(array(
                        'success' => false,
                        'message' => $this->module->l('File name is invalid', 'refer_friends')
                    )));
                }
                $this->uploadBanner($_FILES['banner']);
            }
            die(json_encode(array(
                'success' => false,
                'message' => $this->module->l('No files selected', 'refer_friends')
            )));
        }

        //Delete Banner
        if (Tools::isSubmit('delete_banner')) {
            if(!$this->isTokenValid()){
                die(json_encode(array(
                    'success' => false,
                    'message' => $this->module->l('Token is invalid', 'refer_friends')
                )));
            }
            $this->deleteBanner((int)$customer->id);
        }

        if(Tools::isSubmit('create_voucher_code_sell'))
        {
            if(Configuration::get('ETS_AM_SELL_OFFER_VOUCHER'))
            {
                if(!($voucher_code = Ets_Voucher::getVoucherCodeByIDCustomer($this->context->customer->id)))
                {
                    if($cartRuleObj = $this->module->saveCartRule())
                    {
                        Ets_Voucher::addCartRuleToCustomer($this->context->customer->id,$cartRuleObj);
                        die(
                            json_encode(
                                array(
                                    'success' => true,
                                    'code' => $cartRuleObj->code,
                                )
                            )
                        );
                    }
                    else
                    {
                        die(
                            json_encode(
                                array(
                                    'success' => false,
                                    'error' => $this->module->l('Add cart rule error','refer_friends'),
                                )
                            )  
                        );
                    }
                }
                else
                {
                    die(
                        json_encode(
                            array(
                                'success' => false,
                                'error' => $this->module->l('Voucher code is exists','refer_friends'),
                            )
                        )  
                    );
                }
                
            }
        }
    }

    public function setMedia(){
        parent::setMedia();
        $this->addJs(_PS_MODULE_DIR_ . 'ets_affiliatemarketing/views/js/share_social.js');
    }
    public function getImgBanner($data = array()){
        if(isset($data['link_img']) && $data['link_img'] && isset($data['src_img']) && $data['src_img'])
        {
            return EtsAffDefine::displayText(EtsAffDefine::displayText(null,'img',null,null,null,null,$data['src_img']),'a',null,null,$data['link_img']);
        }
        return '';
    }
}