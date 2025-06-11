<?php

$id_lang = Tools::getValue('id_lang', null);
$formId = Tools::getValue('formId');
$ccType = Tools::getValue('cc_type', 'BCC');
$type = Tools::getValue('type', null);
$recipients = Tools::getValue('recipients', null);
$customersIds = Tools::getValue('customers_ids', null);
$subject = Tools::getValue('subject', null);
$message = Tools::getValue('message', null);

// traitement des fichiers joints
$attachments = glob(SC_MAIL_ATTACHMENT_DIR.$formId.DIRECTORY_SEPARATOR.'*.*');
$files = array();
foreach ($attachments as $attachment)
{
    $files[] = array(
        'content' => file_get_contents($attachment),
        'name' => basename($attachment),
        'mime' => mime_content_type($attachment),
    );
}

generateAttachmentsZip($formId);

// traitement des cc/bcc

if ($type === 'customers')
{
    $ids = pInSQL($customersIds);
    $sql = 'SELECT * 
            FROM '._DB_PREFIX_.'customer
            WHERE id_customer IN ('.pInSQL($ids).')
    ';
    $customers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    $recipients = array_map(function ($e)
    {
        return $e['email'];
    }, $customers);
    createThreads($formId, $customers, $subject, $message, $sc_agent->id_employee, $files);
}
else
{
    $recipients = array_filter(explode("\n", $recipients));
    sendMail($ccType, $subject, $message, $recipients, $files);
}

function sendMail($ccType, $subject, $message, $to, $file_attachment = null)
{
    try
    {
        $to_name = array();
        $tpl_var = array(
            '{firstname}' => ' ',
            '{lastname}' => ' ',
            '{message}' => $message,
        );
        $bcc = null;
        if ($ccType == 'BCC')
        {
            $recipients = $to;
            $to = array_shift($recipients);
            $bcc = $recipients;
        }

        Mail::Send(
            Configuration::get('PS_LANG_DEFAULT'),
            'newsletter',
            $subject,
            $tpl_var,
            $to,
            $to_name,
            null,
            null,
            $file_attachment,
            null,
            _PS_MAIL_DIR_,
            null,
            null,
            $bcc
        );
    }
    catch (Exception $e)
    {
        echo json_encode(
            array(
                'status' => false,
                'message' => $e->getMessage(),
            )
        );
    }
}

function createThreads($formId, $customers, $subject, $message, $id_employee, $file_attachment = null)
{
    try
    {
        foreach ($customers as $customer)
        {
            $customer = new Customer($customer['id_customer']);
            $customerThread = new CustomerThread(0, $customer->id_lang, $customer->id_shop);
            $customerThread->id_shop = $customer->id_shop;
            $customerThread->id_contact = (int) $id_employee;
            $customerThread->id_customer = $customer->id;
            $customerThread->token = Tools::passwdGen(12);
            $customerThread->status = 'open';
            $customerThread->email = $customer->email;
            $customerThread->add();
            $customerMessage = new CustomerMessage();
            $customerMessage->id_employee = (int) $id_employee;
            $customerMessage->id_customer_thread = $customerThread->id;

            $customerMessage->file_name = $formId.'.zip';
            $customerMessage->message = $message;
            $customerMessage->ip_address = ip2long($_SERVER['REMOTE_ADDR']);
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $customerMessage->private = 1; ## private by default
            }

            if ($customerMessage->add())
            {
                $link = new Link();
                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    $params = array(
                        '{reply}' => nl2br($message),
                        '{link}' => Tools::url(
                            $link->getPageLink('contact', true),
                            'id_customer_thread='.(int) $customerThread->id.'&token='.$customerThread->token
                        ),
                        '{firstname}' => $customer->firstname,
                        '{lastname}' => $customer->lastname,
                    );
                }
                else
                {
                    $params = array(
                        '{reply}' => nl2br($message),
                        '{link}' => $link->getPageLink('contact', true),
                        'id_customer_thread='.(int) $customerThread->id.'&token='.$customerThread->token,
                    );
                }

                // Envoi du message au client
                $to_update = false;
                $to = $customer->email;

                if (!SCMS)
                {
                    Mail::Send(
                        (int) $customer->id_lang,
                        'reply_msg',
                        sprintf(
                            SCI::translateSubjectMail(
                                $subject,
                                $customerThread->id_lang
                            ),
                            $customerThread->id,
                            $customerThread->token
                        ),
                        $params,
                        $to,
                        null,
                        null,
                        null,
                        $file_attachment,
                        null,
                        _PS_MAIL_DIR_,
                        true
                    );
                }
                else
                {
                    Mail::Send(
                        (int) $customer->id_lang,
                        'reply_msg',
                        sprintf(
                            SCI::translateSubjectMail(
                                $subject,
                                $customerThread->id_lang
                            ),
                            $customerThread->id,
                            $customerThread->token
                        ),
                        $params,
                        $to,
                        null,
                        null,
                        null,
                        $file_attachment,
                        null,
                        _PS_MAIL_DIR_,
                        true,
                         $customer->id_shop
                    );
                }
                $customerThread->save();
                $success = true;
            }
        }
    }
    catch (Exception $e)
    {
        echo json_encode(
            array(
                'status' => false,
                'message' => $e->getMessage(),
            )
        );
    }
}

$response = array(
    'state' => true,
    'message' => _l('The message was successfully sent'),
);

exit(json_encode($response));

function generateAttachmentsZip($formId)
{
    if (is_dir(SC_MAIL_ATTACHMENT_DIR.$formId))
    {
        $zipManager = new \PrestaShopBundle\Utils\ZipManager();
        $zipManager->createArchive(SC_MAIL_ATTACHMENT_DIR.$formId.'.zip', SC_MAIL_ATTACHMENT_DIR.$formId);
    }
}
