<?php
/**
 * Cart Reminder
 *
 *    @author    EIRL Timactive De Véra
 *    @copyright Copyright (c) TIMACTIVE 2015 -EIRL Timactive De Véra
 *    @license   Commercial license
 *
 *    @category pricing_promotion
 *
 *    @version 1.1.0
 *************************************
 **         CART REMINDER            *
 **          V 1.0.0                 *
 *************************************
 *  _____ _            ___       _   _
 * |_   _(_)          / _ \     | | (_)
 *   | |  _ _ __ ___ / /_\ \ ___| |_ ___   _____
 *   | | | | '_ ` _ \|  _  |/ __| __| \ \ / / _ \
 *   | | | | | | | | | | | | (__| |_| |\ V /  __/
 *   \_/ |_|_| |_| |_\_| |_/\___|\__|_| \_/ \___|
 *
 * +
 * + Languages: EN, FR
 * + PS version: 1.5,1.6
 * TACartReminderTools common action process(control, check, send email)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
if (function_exists('set_time_limit')) {
    @set_time_limit(1200);
}
if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
    // nothing to do
} elseif (version_compare(_PS_VERSION_, '1.6.1.5', '>=')) {
    include_once _PS_SWIFT_DIR_ . 'swift_required.php';
} else {
    include_once _PS_SWIFT_DIR_ . 'Swift.php';
    include_once _PS_SWIFT_DIR_ . 'Swift/Connection/SMTP.php';
    include_once _PS_SWIFT_DIR_ . 'Swift/Connection/NativeMail.php';
    include_once _PS_SWIFT_DIR_ . 'Swift/Plugin/Decorator.php';
}

class TACartReminderTools
{
    // Constant for ename use stand mail function
    const FORCE_USE_STD_PRESTASHOP_FUNCTION = true;

    /**
     * Test if $val1 == $val2
     *
     * @param $val1
     * @param $val2
     *
     * @return bool
     */
    public static function isEqual($val1, $val2)
    {
        return isset($val1) && isset($val2) ? $val1 == $val2 : false;
    }

    /**
     * Test if $val1 < $val2
     *
     * @param $val1
     * @param $val2
     *
     * @return bool
     */
    public static function isLessThan($val1, $val2)
    {
        return isset($val1) && isset($val2) ? $val1 < $val2 : false;
    }

    /**
     * Test if $val1 <= $val2
     *
     * @param $val1
     * @param $val2
     *
     * @return bool
     */
    public static function isLessOrEqualThan($val1, $val2)
    {
        return isset($val1) && isset($val2) ? $val1 <= $val2 : false;
    }

    /**
     * Test if $val1 > $val2
     *
     * @param $val1
     * @param $val2
     *
     * @return bool
     */
    public static function isGreaterThan($val1, $val2)
    {
        return isset($val1) && isset($val2) ? $val1 > $val2 : false;
    }

    /**
     * Test if $val1 >= $val2
     *
     * @param $val1
     * @param $val2
     *
     * @return bool
     */
    public static function isGreaterOrEqualThan($val1, $val2)
    {
        return isset($val1) && isset($val2) ? $val1 >= $val2 : false;
    }

    /**
     * Test if $val1 != $val2(different)
     *
     * @param $val1
     * @param $val2
     *
     * @return bool
     */
    public static function isDifferent($val1, $val2)
    {
        return isset($val1) && isset($val2) ? $val1 != $val2 : false;
    }

    /**
     * Test if $val1 character present in $val2
     *
     * @param $val1
     * @param $val2
     *
     * @return bool
     */
    public static function contain($val1, $val2)
    {
        return isset($val1) && isset($val2) ? strpos($val1, $val2) !== false : false;
    }

    /**
     * Test if $val1 character not in $val2
     *
     * @param $val1
     * @param $val2
     *
     * @return bool
     */
    public static function notContain($val1, $val2)
    {
        return isset($val1) && isset($val2) ? (strpos($val1, $val2) === false) : false;
    }

    /**
     * Test if regular expression match
     *
     * @param $val1
     * @param $val2
     *
     * @return bool|int
     */
    public static function match($val1, $val2)
    {
        return isset($val1) && isset($val2) ? preg_match($val2, $val1) : false;
    }

    /**
     * Get the parent category
     *
     * @param $cat_id
     * @param $lang_id
     * @param string $cat_name
     *
     * @return string
     */
    public static function getParentCategory($cat_id, $lang_id, $cat_name = '')
    {
        if ($cat_name == '') {
            $cat_name = self::getCategoryName($cat_id, $lang_id);
        } else {
            $cat_name = self::getCategoryName($cat_id, $lang_id) . ' > ' . $cat_name;
        }
        $sql = 'SELECT id_parent FROM `' . _DB_PREFIX_ . 'category`  WHERE id_category=' . (int) $cat_id;
        $result = Db::getInstance()->getRow($sql);
        if ($result && isset($result['id_parent']) && ((int) $result['id_parent'] > 1)) {
            $cat_name = self::getParentCategory((int) $result['id_parent'], $lang_id, $cat_name);
        }

        return $cat_name;
    }

    /**
     * Get the category name
     *
     * @param $cat_id
     * @param $lang_id
     *
     * @return string
     */
    public static function getCategoryName($cat_id, $lang_id)
    {
        $sql = 'SELECT cl.name  as category_name ';
        $sql .= 'FROM ' . _DB_PREFIX_ . 'category_lang cl ';
        $sql .= ' WHERE cl.id_lang=' . (int) $lang_id . ' AND cl.id_category=' . (int) $cat_id;
        $result = Db::getInstance()->getRow($sql);
        $cat_name = '';
        if ($result) {
            $cat_name = trim($result['category_name']);
        }

        return trim($cat_name);
    }

    /**
     * Send email
     *
     * @param string $idLang
     * @param string $template_txt content use for old client messagery
     * @param string $template_html
     * @param string $subject
     * @param array $template_vars
     * @param string $to
     * @param string $to_name
     * @param int $id_shop
     * @param bool|true $send_txt_mail
     * @param string $bcc
     *
     * @return bool
     */
    public static function send(
        $idLang = null,
        $template_txt = '',
        $template_html = '',
        $subject = '',
        $template_vars = [],
        $to = null,
        $to_name = null,
        $id_shop = null,
        $send_txt_mail = true,
        $bcc = null
    ) {
        $from = null;
        $from_name = null;
        $file_attachment = null;
        $die = false;
        $configuration = Configuration::getMultiple([
            'PS_SHOP_EMAIL',
            'PS_MAIL_METHOD',
            'PS_MAIL_SERVER',
            'PS_MAIL_USER',
            'PS_MAIL_PASSWD',
            'PS_SHOP_NAME',
            'PS_MAIL_SMTP_ENCRYPTION',
            'PS_MAIL_SMTP_PORT',
            'PS_MAIL_TYPE',
        ], null, null, $id_shop);
        // Returns immediatly if emails are deactivated
        if ($configuration['PS_MAIL_METHOD'] == 3) {
            return true;
        }
        if (version_compare(_PS_VERSION_, '1.6.1.5', '<') && !isset($configuration['PS_MAIL_SMTP_ENCRYPTION'])) {
            $configuration['PS_MAIL_SMTP_ENCRYPTION'] = 'off';
        }
        if (version_compare(_PS_VERSION_, '1.6.1.5', '>=') && (!isset($configuration['PS_MAIL_SMTP_ENCRYPTION']) || $configuration['PS_MAIL_SMTP_ENCRYPTION'] === 'off')) {
            $configuration['PS_MAIL_SMTP_ENCRYPTION'] = false;
        }
        if (!isset($configuration['PS_MAIL_SMTP_PORT'])) {
            $configuration['PS_MAIL_SMTP_PORT'] = 'default';
        }
        // Sending an e-mail can be of vital importance for the merchant, when his
        // password is lost for example, so we must not die but do our best to send the e-mail
        if (!isset($from) || !Validate::isEmail($from)) {
            $from = $configuration['PS_SHOP_EMAIL'];
        }
        if (!Validate::isEmail($from)) {
            $from = null;
        }
        // $from_name is not that important, no need to die if it is not valid
        if (!isset($from_name) || !Validate::isMailName($from_name)) {
            $from_name = $configuration['PS_SHOP_NAME'];
        }
        if (!Validate::isMailName($from_name)) {
            $from_name = null;
        }
        if (!is_array($to) && !Validate::isEmail($to)) {
            Tools::dieOrLog(Tools::displayError('Error: parameter "to" is corrupted'), $die);

            return false;
        }
        if (!is_array($template_vars)) {
            $template_vars = [];
        }
        // Do not crash for this error, that may be a complicated customer name
        if (is_string($to_name) && !empty($to_name) && !Validate::isMailName($to_name)) {
            $to_name = null;
        }
        if (!Validate::isMailSubject($subject)) {
            Tools::dieOrLog(Tools::displayError('Error: invalid e-mail subject'), $die);

            return false;
        }
        $message = null;
        if (version_compare(_PS_VERSION_, '1.7.7.0', '>=')) {
            $message = new Swift_Message();
        } elseif (version_compare(_PS_VERSION_, '1.6.1.5', '>=')) {
            $message = Swift_Message::newInstance();
        } else {
            $message = new Swift_RecipientList();
        }
        // If prestashop 8.0.0 add DKIM configuration in configuration
        if (version_compare(_PS_VERSION_, '8.0.0', '>=')
            && (int) Configuration::get('PS_MAIL_DKIM_ENABLE', null, null, $id_shop) === 1) {
            $configuration['PS_MAIL_DKIM_KEY'] = Configuration::get('PS_MAIL_DKIM_KEY', null, null, $id_shop);
            $configuration['PS_MAIL_DKIM_SELECTOR'] = Configuration::get('PS_MAIL_DKIM_SELECTOR', null, null, $id_shop);
            $configuration['PS_MAIL_DKIM_DOMAIN'] = Configuration::get('PS_MAIL_DKIM_DOMAIN', null, null, $id_shop);
            $signer = new Swift_Signers_DKIMSigner(
                $configuration['PS_MAIL_DKIM_KEY'],
                $configuration['PS_MAIL_DKIM_DOMAIN'],
                $configuration['PS_MAIL_DKIM_SELECTOR']
            );
            $message->attachSigner($signer);
        }
        if (is_array($to) && isset($to)) {
            foreach ($to as $key => $addr) {
                $addr = trim($addr);
                if (!Validate::isEmail($addr)) {
                    Tools::dieOrLog(Tools::displayError('Error: invalid e-mail address'), $die);

                    return false;
                }
                if (is_array($to_name)) {
                    if ($to_name && is_array($to_name) && Validate::isGenericName($to_name[$key])) {
                        $to_name = $to_name[$key];
                    }
                }
                if ($to_name == null || $to_name == $addr) {
                    $to_name = '';
                } else {
                    $to_name = self::mimeEncode($to_name);
                }
                $message->addTo($addr, $to_name);
            }
            $to_plugin = $to[0];
        } else {
            /* Simple recipient, one address */
            $to_plugin = $to;
            if ($to_name == null || $to_name == $to) {
                $to_name = '';
            } else {
                $to_name = self::mimeEncode($to_name);
            }
            $message->addTo($to, $to_name);
        }
        if (isset($bcc)) {
            $message->addBcc($bcc);
        }
        try {
            /* Connect with the appropriate configuration */
            if ($configuration['PS_MAIL_METHOD'] == 2) {
                if (empty($configuration['PS_MAIL_SERVER']) || empty($configuration['PS_MAIL_SMTP_PORT'])) {
                    Tools::dieOrLog(Tools::displayError('Error: invalid SMTP server or SMTP port'), $die);

                    return false;
                }
                $connection = null;
                if (version_compare(_PS_VERSION_, '1.7.7.0', '>=')) {
                    $connection = (new Swift_SmtpTransport(
                        $configuration['PS_MAIL_SERVER'],
                        $configuration['PS_MAIL_SMTP_PORT'],
                        $configuration['PS_MAIL_SMTP_ENCRYPTION']
                    ))
                        ->setUsername($configuration['PS_MAIL_USER'])
                        ->setPassword($configuration['PS_MAIL_PASSWD']);
                } elseif (version_compare(_PS_VERSION_, '1.6.1.5', '>=')) {
                    $connection = Swift_SmtpTransport::newInstance($configuration['PS_MAIL_SERVER'], $configuration['PS_MAIL_SMTP_PORT'], $configuration['PS_MAIL_SMTP_ENCRYPTION'])->setUsername($configuration['PS_MAIL_USER'])->setPassword($configuration['PS_MAIL_PASSWD']);
                } else {
                    $connection = new Swift_Connection_SMTP($configuration['PS_MAIL_SERVER'], $configuration['PS_MAIL_SMTP_PORT'], $configuration['PS_MAIL_SMTP_ENCRYPTION'] == 'ssl' ? Swift_Connection_SMTP::ENC_SSL : ($configuration['PS_MAIL_SMTP_ENCRYPTION'] == 'tls' ? Swift_Connection_SMTP::ENC_TLS : Swift_Connection_SMTP::ENC_OFF));
                    $connection->setTimeout(4);
                    if (!$connection) {
                        return false;
                    }
                    if (!empty($configuration['PS_MAIL_USER'])) {
                        $connection->setUsername($configuration['PS_MAIL_USER']);
                    }
                    if (!empty($configuration['PS_MAIL_PASSWD'])) {
                        $connection->setPassword($configuration['PS_MAIL_PASSWD']);
                    }
                }
            } else {
                if (version_compare(_PS_VERSION_, '1.7.7.0', '>=')) {
                    $connection = new Swift_SendmailTransport();
                } elseif (version_compare(_PS_VERSION_, '1.6.1.5', '>=')) {
                    $connection = Swift_MailTransport::newInstance();
                } else {
                    $connection = new Swift_Connection_NativeMail();
                }
            }
            if (!$connection) {
                return false;
            }
            $swift = null;
            if (version_compare(_PS_VERSION_, '1.7.7.0', '>=')) {
                $swift = new Swift_Mailer($connection);
            } elseif (version_compare(_PS_VERSION_, '1.6.1.5', '>=')) {
                $swift = Swift_Mailer::newInstance($connection);
            } else {
                $swift = new Swift($connection, Configuration::get('PS_MAIL_DOMAIN', null, null, $id_shop));
            }
            /* Get templates content */
            $template_txt = strip_tags($template_txt);
            /* Create mail and attach differents parts */
            if (version_compare(_PS_VERSION_, '1.6.1.5', '<')) {
                $message = new Swift_Message($subject);
            } else {
                $message->setSubject($subject);
            }
            $message->setCharset('utf-8');
            /* Set Message-ID - getmypid() is blocked on some hosting */
            $message->setId(self::generateId());
            if (version_compare(_PS_VERSION_, '1.6.1.5', '<')) {
                $message->headers->setEncoding('Q');
            }
            if (version_compare(_PS_VERSION_, '1.7.7.0', '<')) {
                $template_vars = array_map(
                    [
                        'Tools',
                        'htmlentitiesDecodeUTF8',
                    ],
                    $template_vars
                );
                $template_vars = array_map(
                    [
                        'Tools',
                        'stripslashes',
                    ],
                    $template_vars
                );
            }
            if (Configuration::get('PS_LOGO_MAIL') !== false && file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO_MAIL', null, null, $id_shop))) {
                $logo = _PS_IMG_DIR_ . Configuration::get('PS_LOGO_MAIL', null, null, $id_shop);
            } else {
                if (file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, $id_shop))) {
                    $logo = _PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, $id_shop);
                } else {
                    $template_vars['{shop_logo}'] = '';
                }
            }
            if (version_compare(_PS_VERSION_, '1.5.5.0') >= 0) {
                ShopUrl::cacheMainDomainForShop((int) $id_shop);
            }
            /* don't attach the logo as */
            if (isset($logo) && (int) Configuration::get('TA_CARTR_SHOPLOGO')) {
                if (version_compare(_PS_VERSION_, '1.6.1.5', '<')) {
                    $template_vars['{shop_logo}'] = $message->attach(new Swift_Message_EmbeddedFile(new Swift_File($logo), null, self::getMimeTypeByExtension($logo)));
                } else {
                    $template_vars['{shop_logo}'] = $message->embed(Swift_Image::fromPath($logo));
                }
            }
            if ((Context::getContext()->link instanceof Link) === false) {
                Context::getContext()->link = new Link();
            }
            $shop_link_url = Context::getContext()->link->getPageLink(
                'index',
                true,
                Context::getContext()->language->id,
                null,
                false,
                $id_shop
            );
            $shop_base_url = 'https://' . Context::getContext()->shop->domain . Context::getContext()->shop->getBaseURI();
            $template_vars['{shop_name}'] = Tools::safeOutput(Configuration::get('PS_SHOP_NAME', null, null, $id_shop));
            $template_vars['{shop_url}'] = $shop_link_url;
            $template_vars['{shop_link_url}'] = $shop_link_url;
            $template_vars['{shop_base_url}'] = $shop_base_url;
            $template_vars['{my_account_url}'] = Context::getContext()->link->getPageLink('my-account', true, Context::getContext()->language->id, null, false, $id_shop);
            $template_vars['{guest_tracking_url}'] = Context::getContext()->link->getPageLink('guest-tracking', true, Context::getContext()->language->id, null, false, $id_shop);
            $template_vars['{history_url}'] = Context::getContext()->link->getPageLink('history', true, Context::getContext()->language->id, null, false, $id_shop);
            $template_vars['{color}'] = Tools::safeOutput(Configuration::get('PS_MAIL_COLOR', null, null, $id_shop));
            if (version_compare(_PS_VERSION_, '1.6.1.5', '<')) {
                $swift->attachPlugin(
                    new Swift_Plugin_Decorator(
                        [
                            $to_plugin => $template_vars,
                        ]
                    ),
                    'decorator'
                );
            } else {
                $swift->registerPlugin(new Swift_Plugins_DecoratorPlugin([
                    self::toPunycode($to_plugin) => $template_vars,
                ]));
            }
            if ($send_txt_mail && ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_TEXT)) {
                if (version_compare(_PS_VERSION_, '1.6.1.5', '<')) {
                    $message->attach(new Swift_Message_Part($template_txt, 'text/plain', '8bit', 'utf-8'));
                } else {
                    $message->addPart($template_txt, 'text/plain', 'utf-8');
                }
            }
            $template_html = self::cartCSSToInline($template_html);
            if ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_HTML) {
                if (version_compare(_PS_VERSION_, '1.6.1.5', '<')) {
                    $message->attach(new Swift_Message_Part($template_html, 'text/html', '8bit', 'utf-8'));
                } else {
                    $message->addPart($template_html, 'text/html', 'utf-8');
                }
            }
            if ($file_attachment && !empty($file_attachment)) {
                // Multiple attachments?
                if (!is_array(current($file_attachment))) {
                    $file_attachment = [
                        $file_attachment,
                    ];
                }
                foreach ($file_attachment as $attachment) {
                    if (isset($attachment['content']) && isset($attachment['name']) && isset($attachment['mime'])) {
                        $message->attach(new Swift_Message_Attachment($attachment['content'], $attachment['name'], $attachment['mime']));
                        if (version_compare(_PS_VERSION_, '1.6.1.5', '<')) {
                            $message->attach(
                                new Swift_Message_Attachment(
                                    $attachment['content'],
                                    $attachment['name'],
                                    $attachment['mime']
                                )
                            );
                        } else {
                            if (version_compare(_PS_VERSION_, '1.7.7.0', '>=')) {
                                $message->attach(
                                    (new Swift_Attachment())->setFilename(
                                        $attachment['name']
                                    )->setContentType($attachment['mime'])
                                        ->setBody($attachment['content'])
                                );
                            } else {
                                $message->attach(
                                    Swift_Attachment::newInstance()->setFilename($attachment['name'])
                                        ->setContentType($attachment['mime'])
                                        ->setBody($attachment['content'])
                                );
                            }
                        }
                    }
                }
            }
            /* Send mail */
            if (version_compare(_PS_VERSION_, '1.6.1.5', '<')) {
                $send = $swift->send($message, $to, new Swift_Address($from, $from_name));
                $swift->disconnect();
            } else {
                $message->setFrom([
                    $from => $from_name,
                ]);
                $send = $swift->send($message);
            }
            if (version_compare(_PS_VERSION_, '1.6.1.5', '>=')) {
                if ($send && Configuration::get('PS_LOG_EMAILS')) {
                    $mail = new Mail();
                    $mail->template = 'smart_cart_reminder';
                    $mail->subject = Tools::substr($subject, 0, 255);
                    $mail->id_lang = (int) $idLang;
                    $recipientsTo = $message->getTo();
                    $recipientsCc = $message->getCc();
                    $recipientsBcc = $message->getBcc();
                    if (!is_array($recipientsTo)) {
                        $recipientsTo = [];
                    }
                    if (!is_array($recipientsCc)) {
                        $recipientsCc = [];
                    }
                    if (!is_array($recipientsBcc)) {
                        $recipientsBcc = [];
                    }
                    foreach (array_merge($recipientsTo, $recipientsCc, $recipientsBcc) as $email => $recipient_name) {
                        /* @var Swift_Address $recipient */
                        $mail->id = null;
                        $mail->recipient = Tools::substr($email, 0, 255);
                        $mail->add();
                    }
                }
                if (version_compare(_PS_VERSION_, '1.5.5.0') >= 0) {
                    ShopUrl::resetMainDomainCache();
                }
            }

            return $send;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Automatically convert email to Punycode.
     *
     * Try to use INTL_IDNA_VARIANT_UTS46 only if defined, else use INTL_IDNA_VARIANT_2003
     * See https://wiki.php.net/rfc/deprecate-and-remove-intl_idna_variant_2003
     *
     * @param string $to Email address
     *
     * @return string
     */
    public static function toPunycode($to)
    {
        $address = explode('@', $to);
        if (empty($address[0]) || empty($address[1])) {
            return $to;
        }

        if (defined('INTL_IDNA_VARIANT_UTS46')) {
            return $address[0] . '@' . idn_to_ascii($address[1], 0, INTL_IDNA_VARIANT_UTS46);
        }

        /*
         * INTL_IDNA_VARIANT_2003 const will be removed in PHP 8.
         * See https://wiki.php.net/rfc/deprecate-and-remove-intl_idna_variant_2003
         */
        if (defined('INTL_IDNA_VARIANT_2003')) {
            return $address[0] . '@' . idn_to_ascii($address[1], 0, INTL_IDNA_VARIANT_2003);
        }

        return $address[0] . '@' . idn_to_ascii($address[1]);
    }

    /**
     * Generate uid
     *
     * @param string $idstring
     *
     * @return string
     */
    public static function generateId($idstring = null)
    {
        $midparams = [
            'utctime' => (new DateTime('now', new DateTimeZone('UTC')))->format('YmdHis'),
            'randint' => mt_rand(),
            'customstr' => (preg_match('/^(?<!\\.)[a-z0-9\\.]+(?!\\.)\$/iD', $idstring) ? $idstring : 'swift'),
            'hostname' => (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : php_uname('n')),
        ];
        if (version_compare(_PS_VERSION_, '1.6.1.5', '<')) {
            return vsprintf('<%s.%d.%s@%s>', $midparams);
        }

        return vsprintf('%s.%d.%s@%s', $midparams);
    }

    /**
     * Get Image hack for track read in email
     *
     * @param $id_reminder
     * @param $track_read_code
     *
     * @return string <img src="url"/>
     */
    public static function getImageHack($id_reminder, $track_read_code)
    {
        $url = Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/tacartreminder/mail_img_trc_r.php';
        $url .= '?uidtrc=' . $track_read_code;
        $url .= '&id_reminder=' . $id_reminder;
        $img = '<img src="' . $url . '" width="1" border="0" height="1" />';

        return $img;
    }

    /**
     * Get recover url
     *
     * @param $id_reminder
     * @param $cart
     * @param Context $context
     * @param int $step
     * @param int $cart_rule_add
     *
     * @return mixed
     */
    public static function getCartRecoverUrl($id_reminder, $cart, $context = null, $step = 3, $cart_rule_add = 0)
    {
        if (!isset($context)) {
            $context = self::buildContextByCart($cart);
        }
        $query_params = [
            'ta_c' => (int) $cart->id,
            'ta_re' => (int) $id_reminder,
            'ta_token' => md5(_COOKIE_KEY_ . 'recover_cart_' . (int) $cart->id),
        ];
        if ($step > 1) {
            $query_params['ta_st'] = $step;
        }
        if ((int) $cart_rule_add) {
            $query_params['ta_cr'] = 1;
        }
        $url = $context->link->getModuleLink(
            'tacartreminder',
            'cartrecover',
            $query_params,
            true,
            (int) $cart->id_lang,
            (int) $cart->id_shop
        );

        return $url;
    }

    /**
     * Get front url to unsubscribe
     *
     * @param $cart
     * @param null $context
     *
     * @return mixed
     */
    public static function getUnscribeUrl($cart, $context = null)
    {
        if (!isset($context)) {
            $context = self::buildContextByCart($cart);
        }
        $params = [
            'id_customer' => $cart->id_customer,
            'id_shop' => $cart->id_shop,
            'token_unscribe' => md5(
                Configuration::get('TA_CARTR_KEY') . 'unscribe_' . $cart->id_customer . '_' . $cart->id_shop
            ),
        ];
        $url = $context->link->getModuleLink('tacartreminder', 'unscribe', $params);

        return $url;
    }

    /**
     * Build context by cart
     * assure to work with good context will all setting need to calculate good price
     * depending by many parameter( address invoice)
     *
     * @param $cart
     *
     * @return mixed
     */
    public static function buildContextByCart($cart)
    {
        $context = Context::getContext()->cloneContext();
        $context->cart = $cart;
        if (Validate::isLoadedObject($context->cart) && $context->cart->id) {
            if ((int) $cart->id_customer) {
                $context->customer = new Customer($cart->id_customer);
            }
            $context->shop = new Shop((int) $context->cart->id_shop);
            $test_shop_context = Shop::getContextShopID();
            if (!(int) $test_shop_context) {
                Shop::setContext(Shop::CONTEXT_SHOP, (int) $context->cart->id_shop);
            }
            $language = new Language($context->cart->id_lang);
            $currency = new Currency($context->cart->id_currency);
            if (!Validate::isLoadedObject($language) || !$language->id || !$language->active) {
                $language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
            }
            if (!Validate::isLoadedObject($currency) || !$currency->id || !$currency->active) {
                $currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
            }
            $context->language = $language;
            $context->currency = $currency;
            $protocol_link = Configuration::get(
                'PS_SSL_ENABLED',
                null,
                $cart->id_shop_group,
                $cart->id_shop
            ) ? 'https://' : 'http://';
            $use_ssl = Configuration::get('PS_SSL_ENABLED') ? true : false;
            $protocol_content = ($use_ssl) ? 'https://' : 'http://';
            $link = new Link($protocol_link, $protocol_content);
            $context->link = $link;
        }

        return $context;
    }

    /**
     * Return price formatted to display
     *
     * @param Cart $cart
     *
     * @return mixed
     */
    public static function getCartPriceDisplay($cart)
    {
        Context::getContext()->customer = new Customer((int) $cart->id_customer);
        Context::getContext()->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        Context::getContext()->cart = $cart;
        $amount = $cart->getOrderTotal();
        $price_display = Tools::displayPrice($amount, Context::getContext()->currency);

        return $price_display;
    }

    /**
     * Get content cart in html
     *
     * @param $cart
     * @param Content $context
     * @param string $type_render
     *
     * @return string
     */
    public static function getContentCart($cart, $context = null, $type_render = 'html')
    {
        if (!isset($context)) {
            $context = self::buildContextByCart($cart);
        }
        if (Validate::isLoadedObject($context->cart) && $context->cart->id) {
            if (!isset(Context::getContext()->cart)) {
                Context::getContext()->cart = $cart;
            }
            if (!isset(Context::getContext()->customer) && (int) $cart->id_customer) {
                Context::getContext()->customer = new Customer($cart->id_customer);
            }
            $currency = new Currency($context->cart->id_currency);
            Context::getContext()->currency = $currency;
            // $order_total = $context->cart->getOrderTotal();
            $summary = $context->cart->getSummaryDetails();
            foreach ($summary['products'] as $key => &$product) {
                $product['price_dp'] = Tools::displayPrice((float) $product['price'], $currency, false);
                $product['price_wt_dp'] = Tools::displayPrice((float) $product['price_wt'], $currency, false);
                $product['quantity'] = $product['cart_quantity']; // for compatibility with 1.2 themes
                if ($context->shop->id != $product['id_shop']) {
                    $context->shop = new Shop((int) $product['id_shop']);
                }
                $nothing = null;
                $use_group_reduction = false;
                $product['price_without_specific_price'] = Product::getPriceStatic(
                    $product['id_product'],
                    !Product::getTaxCalculationMethod($cart->id_customer),
                    $product['id_product_attribute'],
                    2,
                    null,
                    false,
                    false,
                    1,
                    false,
                    null,
                    null,
                    null,
                    $nothing,
                    true,
                    $use_group_reduction,
                    $context
                );
                $product['price_without_specific_price_dp'] = Tools::displayPrice(
                    (float) $product['price_without_specific_price'],
                    $currency,
                    false
                );
                if (Product::getTaxCalculationMethod($cart->id_customer)) {
                    $product['is_discounted'] = $product['price_without_specific_price'] != $product['price'];
                } else {
                    $product['is_discounted'] = $product['price_without_specific_price'] != $product['price_wt'];
                }
            }
            $show_taxes = Configuration::get('PS_TAX_DISPLAY') == 1 && (int) Configuration::get('PS_TAX');
            $context->smarty->assign($summary);
            $context->smarty->assign(
                [
                    'id_lang' => (int) $cart->id_lang,
                    'link' => $context->link,
                    'total_price' => (float) $summary['total_price'],
                    'total_price_dp' => Tools::convertPrice((float) $summary['total_price'], $currency),
                    'priceDisplay' => Product::getTaxCalculationMethod((int) $context->cart->id_customer),
                    'use_taxes' => (int) Configuration::get('PS_TAX'),
                    'tpl_mt_path' => dirname(__FILE__) . '/../views/templates/admin/mail_template',
                    'tpl_product_line_txt_path' => self::getTemplateDirForFetch(
                        'views/templates/admin/mail_template/shopping-cart-product-line-txt.tpl'
                    ),
                    'tpl_product_line_path' => self::getTemplateDirForFetch(
                        'views/templates/admin/mail_template/shopping-cart-product-line.tpl'
                    ),
                    'show_taxes' => (int) $show_taxes,
                ]
            );
            if ($type_render == 'html') {
                return $context->smarty->fetch(
                    self::getTemplateDirForFetch('views/templates/admin/mail_template/shopping-cart.tpl')
                );
            } else {
                return $context->smarty->fetch(
                    self::getTemplateDirForFetch('views/templates/admin/mail_template/shopping-cart-txt.tpl')
                );
            }
        }

        return '';
    }

    public static function getTemplateDirForFetch($template)
    {
        if (Tools::file_exists_cache(_PS_THEME_DIR_ . 'modules/tacartreminder/' . $template)) {
            return _PS_THEME_DIR_ . 'modules/tacartreminder/' . $template;
        }

        return _PS_MODULE_DIR_ . '/tacartreminder/' . $template;
    }

    public static function getMailVariables(
        $id_reminder = 0,
        $id_cart = 0,
        $title = '',
        $voucher_code = '############'
    ) {
        if (!(int) $id_cart) {
            return false;
        }
        $cart = new Cart($id_cart);
        $context = self::buildContextByCart($cart);
        $customer = new Customer($cart->id_customer);
        $ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
        $shop_base_url = (($ssl) ? 'https://' . $context->shop->domain_ssl : 'http://' . $context->shop->domain);
        $shop_base_url .= $context->shop->getBaseURI();
        $unscibe_url = TACartReminderTools::getUnscribeUrl($cart, $context);
        $shop_link_url = $context->link->getPageLink(
            'index',
            true,
            $context->language->id,
            null,
            false,
            $cart->id_shop
        );
        $auto_add_cr = (int) Configuration::get('TA_CARTR_AUTO_ADD_CR');
        $cart_recover_url = self::getCartRecoverUrl($id_reminder, $cart, $context, 3, $auto_add_cr);
        $cart_recover_url_s0 = self::getCartRecoverUrl($id_reminder, $cart, $context, 1, $auto_add_cr);
        $cart_recover_url_s1 = self::getCartRecoverUrl($id_reminder, $cart, $context, 1, $auto_add_cr);
        $cart_recover_url_s2 = self::getCartRecoverUrl($id_reminder, $cart, $context, 2, $auto_add_cr);
        $cart_recover_url_no_coupon_s0 = self::getCartRecoverUrl($id_reminder, $cart, $context, 0, 0);
        $cart_recover_url_no_coupon_s1 = self::getCartRecoverUrl($id_reminder, $cart, $context, 1, 0);
        $cart_recover_url_no_coupon_s2 = self::getCartRecoverUrl($id_reminder, $cart, $context, 2, 0);
        $cart_recover_url_no_coupon_s3 = self::getCartRecoverUrl($id_reminder, $cart, $context, 3, 0);
        $expiration_date = date('Y-m-d', strtotime(date('Y-m-d', time()) . ' + 7 days'));
        if ($voucher_code != '############') {
            $cart_rule_id = CartRule::getIdByCode($voucher_code);
            $cart_rule = new CartRule($cart_rule_id);
            if ((int) $cart_rule->id) {
                $expiration_date = $cart_rule->date_to;
            }
        }
        $logoData = '';
        if (file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, $cart->id_shop))) {
            $logo = _PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, $cart->id_shop);
            $logoData = self::dataUri($logo);
        }

        return [
            '{shop_name}' => Tools::safeOutput(
                Configuration::get('PS_SHOP_NAME',
                    null,
                    null,
                    $cart->id_shop)
            ),
            '{shop_url}' => $shop_link_url, // @deprecated, use '{shop_link_url}
            '{shop_link_url}' => $shop_link_url,
            '{shop_base_url}' => $shop_base_url,
            '{message_title}' => $title,
            '{my_account_url}' => $context->link->getPageLink(
                'my-account',
                true,
                $context->language->id,
                null,
                false,
                $cart->id_shop
            ),
            '{customer_firstname}' => $customer->firstname,
            '{customer_lastname}' => $customer->lastname,
            '{cart_url_no_coupon_s0}' => $cart_recover_url_no_coupon_s0,
            '{cart_url_no_coupon_s1}' => $cart_recover_url_no_coupon_s1,
            '{cart_url_no_coupon_s2}' => $cart_recover_url_no_coupon_s2,
            '{cart_url_no_coupon_s3}' => $cart_recover_url_no_coupon_s3,
            '{cart_url}' => $cart_recover_url,
            '{cart_url_s0}' => $cart_recover_url_s0,
            '{cart_url_s1}' => $cart_recover_url_s1,
            '{cart_url_s2}' => $cart_recover_url_s2,
            '{cart_url_s3}' => $cart_recover_url,
            '{cart_link_start}' => '<a href="' . $unscibe_url . '">',
            '{cart_link_end}' => '</a>',
            '{unscribe_link_start}' => '<a href="' . $cart_recover_url . '">',
            '{unscribe_link_end}' => '</a>',
            '{unscribe_url}' => $unscibe_url,
            '{cart_products_txt}' => self::getContentCart($cart, $context, 'txt'),
            '{cart_products}' => self::getContentCart($cart, $context, 'html'),
            '{voucher_code}' => $voucher_code,
            '{voucher_expirate_date}' => $expiration_date,
            '{shop_logo}' => $logoData,
        ];
    }

    /**
     * Generate email in html format
     *
     * @param int $id_reminder
     * @param $id_cart
     * @param string $content
     * @param string $title
     * @param string $voucher_code
     * @param bool $only_preview indicate if this is for send after or just preview
     *
     * @return mixed
     */
    public static function renderMail(
        $id_reminder = 0,
        $id_cart = 0,
        $content = '',
        $title = '',
        $voucher_code = '############',
        $only_preview = false
    ) {
        if (!(int) $id_cart) {
            return false;
        }
        $cart = new Cart($id_cart);
        $context = self::buildContextByCart($cart);
        $customer = new Customer($cart->id_customer);
        $ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
        $shop_base_url = (($ssl) ? 'https://' . $context->shop->domain_ssl : 'http://' . $context->shop->domain);
        $shop_base_url .= $context->shop->getBaseURI();
        $unscibe_url = TACartReminderTools::getUnscribeUrl($cart, $context);
        $content = str_replace(
            '{shop_name}',
            Tools::safeOutput(Configuration::get('PS_SHOP_NAME', null, null, $cart->id_shop)),
            $content
        );
        $shop_link_url = $context->link->getPageLink(
            'index',
            true,
            $context->language->id,
            null,
            false,
            $cart->id_shop
        );
        $content = str_replace(
            '{shop_url}',
            $shop_link_url,
            $content
        );
        $content = str_replace(
            '{shop_link_url}',
            $shop_link_url,
            $content
        );
        $content = str_replace('{shop_base_url}', $shop_base_url, $content);
        $content = str_replace('{message_title}', $title, $content);
        $content = str_replace(
            '{my_account_url}',
            $context->link->getPageLink(
                'my-account',
                true,
                $context->language->id,
                null,
                false,
                $cart->id_shop
            ),
            $content
        );
        $content = str_replace('{customer_firstname}', $customer->firstname, $content);
        $content = str_replace('{customer_lastname}', $customer->lastname, $content);
        $auto_add_cr = (int) Configuration::get('TA_CARTR_AUTO_ADD_CR');
        $cart_recover_url = self::getCartRecoverUrl($id_reminder, $cart, $context, 3, $auto_add_cr);
        $cart_recover_url_s0 = self::getCartRecoverUrl($id_reminder, $cart, $context, 1, $auto_add_cr);
        $cart_recover_url_s1 = self::getCartRecoverUrl($id_reminder, $cart, $context, 1, $auto_add_cr);
        $cart_recover_url_s2 = self::getCartRecoverUrl($id_reminder, $cart, $context, 2, $auto_add_cr);
        $cart_recover_url_no_coupon_s0 = self::getCartRecoverUrl($id_reminder, $cart, $context, 0, 0);
        $cart_recover_url_no_coupon_s1 = self::getCartRecoverUrl($id_reminder, $cart, $context, 1, 0);
        $cart_recover_url_no_coupon_s2 = self::getCartRecoverUrl($id_reminder, $cart, $context, 2, 0);
        $cart_recover_url_no_coupon_s3 = self::getCartRecoverUrl($id_reminder, $cart, $context, 3, 0);
        $content = str_replace('{cart_url_no_coupon_s0}', $cart_recover_url_no_coupon_s0, $content);
        $content = str_replace('{cart_url_no_coupon_s1}', $cart_recover_url_no_coupon_s1, $content);
        $content = str_replace('{cart_url_no_coupon_s2}', $cart_recover_url_no_coupon_s2, $content);
        $content = str_replace('{cart_url_no_coupon_s3}', $cart_recover_url_no_coupon_s3, $content);
        $content = str_replace('{cart_url}', $cart_recover_url, $content);
        $content = str_replace('{cart_url_s0}', $cart_recover_url_s0, $content);
        $content = str_replace('{cart_url_s1}', $cart_recover_url_s1, $content);
        $content = str_replace('{cart_url_s2}', $cart_recover_url_s2, $content);
        $content = str_replace('{cart_url_s3}', $cart_recover_url, $content);
        $content = str_replace('{cart_link_start}', '<a href="' . $cart_recover_url . '">', $content);
        $content = str_replace('{cart_link_end}', '</a>', $content);
        $content = str_replace('{unscribe_link_start}', '<a href="' . $unscibe_url . '">', $content);
        $content = str_replace('{unscribe_link_end}', '</a>', $content);
        $content = str_replace('{unscribe_url}', $unscibe_url, $content);
        $content = str_replace('{cart_products_txt}', self::getContentCart($cart, $context, 'txt'), $content);
        $content = str_replace('{cart_products}', self::getContentCart($cart, $context, 'html'), $content);
        $content = str_replace('{voucher_code}', $voucher_code, $content);
        $expirate_date = date('Y-m-d', strtotime(date('Y-m-d', time()) . ' + 7 days'));
        if ($voucher_code != '############') {
            $cart_rule_id = CartRule::getIdByCode($voucher_code);
            $cart_rule = new CartRule($cart_rule_id);
            if ((int) $cart_rule->id) {
                $expirate_date = $cart_rule->date_to;
            }
        }
        $content = str_replace('{voucher_expirate_date}', Tools::displayDate($expirate_date, null), $content);
        if ($only_preview) {
            if (Configuration::get('PS_LOGO_MAIL') !== false
                && file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO_MAIL', null, null, $cart->id_shop))
            ) {
                $logo = _PS_IMG_DIR_ . Configuration::get('PS_LOGO_MAIL', null, null, $cart->id_shop);
            } else {
                if (file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, $cart->id_shop))) {
                    $logo = _PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, $cart->id_shop);
                } else {
                    $content = str_replace('{shop_logo}', '', $content);
                }
            }
            if (isset($logo)) {
                $content = str_replace('{shop_logo}', self::dataUri($logo), $content);
            }
        }
        $content = self::cartCSSToInline($content);

        return $content;
    }

    /**
     * Build Data by file
     * For image to include in email for example
     *
     * @param $file
     * @param string $mime
     *
     * @return string
     */
    private static function dataUri($file, $mime = '')
    {
        $contents = Tools::file_get_contents($file);
        $base64 = self::base64Encode($contents);

        return 'data:' . (function_exists('mime_content_type') ? mime_content_type($file) : $mime) . ';base64,' . $base64;
    }

    /**
     * Encode the content in base64
     *
     * @param $contents
     *
     * @return string
     */
    public static function base64Encode($contents)
    {
        return base64_encode($contents); // required for email logo, viewed with Francois Gaillard & Emmanuel
    }

    public static function convertHtmlToText($html)
    {
        $html = self::fixNewlines($html);
        $doc = new DOMDocument();
        if (!$doc->loadHTML($html)) {
            throw new TACRHtml2TextException('Could not load HTML - badly formed?', $html);
        }
        $output = self::iterateOverNode($doc);
        // remove leading and trailing spaces on each line
        $output = preg_replace('/[ \t]*\n[ \t]*/im', "\n", $output);
        // remove leading and trailing whitespace
        $output = trim($output);

        return $output;
    }

    /**
     * Unify newlines; in particular, \r\n becomes \n, and
     * then \r becomes \n.
     * This means that all newlines (Unix, Windows, Mac)
     * all become \ns.
     *
     * @param
     *            string text text with any number of \r, \r\n and \n combinations
     *
     * @return string the fixed text
     */
    private static function fixNewlines($text)
    {
        // replace \r\n to \n
        $text = str_replace("\r\n", "\n", $text);
        // remove \rs
        $text = str_replace("\r", "\n", $text);

        return $text;
    }

    private static function nextChildName($node)
    {
        // get the next child
        $next_node = $node->nextSibling;
        while ($next_node != null) {
            if ($next_node instanceof DOMElement) {
                break;
            }
            $next_node = $next_node->nextSibling;
        }
        $next_name = null;
        if ($next_node instanceof DOMElement && $next_node != null) {
            $next_name = Tools::strtolower($next_node->nodeName);
        }

        return $next_name;
    }

    public static function prevChildName($node)
    {
        $next_node = $node->previousSibling;
        while ($next_node != null) {
            if ($next_node instanceof DOMElement) {
                break;
            }
            $next_node = $next_node->previousSibling;
        }
        $next_name = null;
        if ($next_node instanceof DOMElement && $next_node != null) {
            $next_name = Tools::strtolower($next_node->nodeName);
        }

        return $next_name;
    }

    public static function iterateOverNode($node)
    {
        if ($node instanceof DOMText) {
            return preg_replace('/[\\t\\n\\v\\f\\r ]+/im', ' ', $node->wholeText);
        }
        if ($node instanceof DOMDocumentType) {
            return '';
        }
        $next_name = self::nextChildName($node);
        self::prevChildName($node);
        $name = Tools::strtolower($node->nodeName);
        // start whitespace
        switch ($name) {
            case 'hr':
                return "------\n";
            case 'style':
            case 'head':
            case 'title':
            case 'meta':
            case 'script':
                // ignore these tags
                return '';
            case 'h1':
            case 'h2':
            case 'h3':
            case 'h4':
            case 'h5':
            case 'h6':
                // add two newlines
                $output = "\n";
                break;
            case 'p':
            case 'div':
                // add one line
                $output = "\n";
                break;
            default:
                // print out contents of unknown tags
                $output = '';
                break;
        }
        if (isset($node->childNodes)) {
            for ($i = 0; $i < $node->childNodes->length; ++$i) {
                $n = $node->childNodes->item($i);
                $text = self::iterateOverNode($n);
                $output .= $text;
            }
        }
        // end whitespace
        switch ($name) {
            case 'style':
            case 'head':
            case 'title':
            case 'meta':
            case 'script':
                // ignore these tags
                return '';
            case 'h1':
            case 'h2':
            case 'h3':
            case 'h4':
            case 'h5':
            case 'h6':
                $output .= "\n";
                break;
            case 'p':
            case 'br':
                // add one line
                if ($next_name != 'div') {
                    $output .= "\n";
                }
                break;
            case 'div':
                // add one line only if the next child isn't a div
                if ($next_name != 'div' && $next_name != null) {
                    $output .= "\n";
                }
                break;
            case 'a':
                // links are returned in [text](link) format
                $href = $node->getAttribute('href');
                if ($href == null) {
                    // it doesn't link anywhere
                    if ($node->getAttribute('name') != null) {
                        $output = "[$output]";
                    }
                } else {
                    if ($href == $output || $href == "mailto:$output"
                        || $href == "http://$output" || $href == "https://$output") {
                    } else {
                        $output = "[$output]($href)";
                    }
                }
                // does the next node require additional whitespace?
                switch ($next_name) {
                    case 'h1':
                    case 'h2':
                    case 'h3':
                    case 'h4':
                    case 'h5':
                    case 'h6':
                        $output .= "\n";
                        break;
                }
                // no break
            default:
                // do nothing
        }

        return $output;
    }

    public static function isMultibyte($data)
    {
        $length = Tools::strlen($data);
        for ($i = 0; $i < $length; ++$i) {
            $result = ord($data[$i]);
            if ($result > 128) {
                return true;
            }
        }

        return false;
    }

    public static function mimeEncode($string, $charset = 'UTF-8', $newline = "\r\n")
    {
        if (!self::isMultibyte($string) && Tools::strlen($string) < 75) {
            return $string;
        }
        $charset = Tools::strtoupper($charset);
        $start = '=?' . $charset . '?B?';
        $end = '?=';
        $sep = $end . $newline . ' ' . $start;
        $length = 75 - Tools::strlen($start) - Tools::strlen($end);
        $length = $length - ($length % 4);
        if ($charset === 'UTF-8') {
            $parts = [];
            $maxchars = floor(($length * 3) / 4);
            $string_length = Tools::strlen($string);
            while ($string_length > $maxchars) {
                $i = (int) $maxchars;
                $result = ord($string[$i]);
                while ($result >= 128 && $result <= 191) {
                    --$i;
                    $result = ord($string[$i]);
                }
                $parts[] = self::base64Encode(Tools::substr($string, 0, $i));
                $string = Tools::substr($string, $i);
                $string_length = Tools::strlen($string);
            }
            $parts[] = self::base64Encode($string);
            $string = implode($sep, $parts);
        } else {
            $string = chunk_split(self::base64Encode($string), $length, $sep);
            $string = preg_replace('/' . preg_quote($sep) . '$/', '', $string);
        }

        return $start . $string . $end;
    }

    public static function cartCSSToInline($content)
    {
        $pattern = '/\/\*CSSTOINLINECART\*\/(.*)\/\*ENDCSSTOINLINECART\*\//s';
        preg_match($pattern, $content, $matches);
        if ($matches && count($matches) > 0) {
            $css_cart = $matches[1];
            $pattern_css = '/\.([^\{]*)\{([^\}]*)\}/s';
            preg_match_all($pattern_css, $css_cart, $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                $pattern_replace = '/class=("|".*\s)' . trim($match[1]) . '(\s.*"|")/';
                $replacement = '${0} style="' . $match[2] . '"';
                $content = preg_replace($pattern_replace, $replacement, $content);
            }
        }

        return $content;
    }

    public static function getMimeTypeByExtension($file_name)
    {
        $types = [
            'image/gif' => [
                'gif',
            ],
            'image/jpeg' => [
                'jpg',
                'jpeg',
            ],
            'image/png' => [
                'png',
            ],
        ];
        $extension = Tools::substr($file_name, strrpos($file_name, '.') + 1);
        $mime_type = null;
        foreach ($types as $mime => $exts) {
            if (in_array($extension, $exts)) {
                $mime_type = $mime;
                break;
            }
        }
        if ($mime_type === null) {
            $mime_type = 'image/jpeg';
        }

        return $mime_type;
    }

    public static function removePersonalDataPSGDPR($email, $id_customer)
    {
        $data_reminder = [
            'email' => 'anonymous-psgdpr-' . $id_customer . '@psgdpr.com',
        ];
        try {
            /* close current journal */
            $shop_ids = Shop::getShops(false, null, true);
            foreach ($shop_ids as $id_shop) {
                $journals = TACartReminderJournal::getJournalsByCustomer($email, $id_shop);
                foreach ($journals as $journal) {
                    if ($journal['state'] != 'CANCELED' || $journal['state'] != 'FINISHED') {
                        $journal_to_cancel = new TACartReminderJournal((int) $journal['id_journal']);
                        $journal_to_cancel->state = 'CANCELED';
                        $journal_to_cancel->update();
                        $mess = new TACartReminderMessage();
                        $mess->id_journal = (int) $journal['id_journal'];
                        $mess->message = 'PSGPDR deleted data';
                        $mess->add();
                    }
                }
            }
            Db::getInstance()->update(
                'ta_cartreminder_journal',
                $data_reminder,
                '`email` = \'' . pSQL($email) . '\''
            );
            Db::getInstance()->update(
                'ta_cartreminder_customer_unsubscribe',
                $data_reminder,
                '`email` = \'' . pSQL($email) . '\''
            );
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Get Carts to check for remind
     * Condition :
     * Cart without order
     * Cart with customer ID
     * IS Abandonned Cart? with date_upd > time abandonned
     * Cart date_upd > max date_upd reminded
     * Last Cart By email
     * TODO RUNNING
     *
     * @param int $id_shop
     *
     * @return mixed
     */
    public static function getCartsToCheck($id_shop)
    {
        $sql = 'SELECT c.`id_cart`,c.`date_add`, c.`date_upd`, c.`id_customer`
			   FROM `' . _DB_PREFIX_ . 'cart` c
			   INNER JOIN `' . _DB_PREFIX_ . 'customer` cust ON cust.`id_customer` = c.`id_customer` 
			   LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON (o.id_cart = c.id_cart)
			   LEFT JOIN `' . _DB_PREFIX_ . 'shop` shop ON c.id_shop = shop.id_shop
			   LEFT JOIN `' . _DB_PREFIX_ . 'ta_cartreminder_journal` j ON j.id_cart = c.id_cart
			   LEFT JOIN `' . _DB_PREFIX_ . 'ta_cartreminder_journal` jrunning ON jrunning.`email` = cust.`email` AND jrunning.`state` = \'RUNNING\'
			   WHERE c.id_shop = ' . (int) $id_shop . ' AND o.id_order IS NULL
			   AND c.id_customer != 0 AND j.id_journal IS NULL AND jrunning.id_journal IS NULL
			   AND c.`date_upd` > DATE_SUB(NOW(),INTERVAL ' . Configuration::get('TA_CARTR_STOPREMINDER_NB_HOUR') . ' HOUR)
			   AND (DATE_SUB(NOW(),INTERVAL ' . Configuration::get('TA_CARTR_ABANDONNED_NB_HOUR') . ' HOUR) >= c.date_upd)
			   AND c.`date_upd` >
			   (SELECT IFNULL(DATE_FORMAT(MAX(j2.date_upd_cart),\'%Y-%m-%d %H:%i:%s\'),\'0000-00-00 00:00:00\')
                  FROM `' . _DB_PREFIX_ . 'ta_cartreminder_journal` j2
                  WHERE j2.`email`=cust.`email` and j.`id_shop` = c.`id_shop`)
			   AND c.id_cart = (SELECT id_cart FROM `' . _DB_PREFIX_ . 'cart` c2
			        INNER JOIN `' . _DB_PREFIX_ . 'customer` cust2 ON cust2.`id_customer` = c2.`id_customer` 
			        WHERE cust2.`email` = cust.`email`
			        AND c2.`id_shop` = c.`id_shop`
			        ORDER BY c2.`date_upd` DESC LIMIT 1)
			   ';
        $results = Db::getInstance()->executeS($sql, true, false);

        return $results;
    }
}
