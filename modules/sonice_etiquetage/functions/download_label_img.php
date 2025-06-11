<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SARL SMC
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL SMC is strictly forbidden.
 * In order to obtain a license, please contact us: contact@common-services.com
 *
 * @package   sonice_etiquetage
 * @author    debuss-a <alexandre@common-services.com>
 * @copyright Copyright(c) 2010-2015 S.A.R.L S.M.C - http://www.common-services.com
 * @license   Commercial license
 */

if (isset($_SERVER['DropBox']) && $_SERVER['DropBox']) {
    @require_once(readlink(dirname($_SERVER['SCRIPT_FILENAME']).'/../../../config/config.inc.php'));
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage'.DIRECTORY_SEPARATOR.'sonice_etiquetage.php'));
} else {
    @require_once(dirname(__FILE__).'/../../../config/config.inc.php');
    require_once(_PS_MODULE_DIR_.'sonice_etiquetage'.DIRECTORY_SEPARATOR.'sonice_etiquetage.php');
}


class SoColissimoDownload extends SoNice_Etiquetage
{



    public function action()
    {
        $file = $_REQUEST['file'];

        if (!file_exists($this->downloadFolder.$file)) {
            die('File not found');
        }

        /** @see http://th1.php.net/manual/en/function.header.php#102175 */
        header('Pragma: public'); // required
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false); // required for certain browsers
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="'.$file.'";');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: '.filesize($this->downloadFolder.$file));
        flush();
        readfile($this->downloadFolder.$file);
    }
}



$soColissimoDownload = new SoColissimoDownload();
$soColissimoDownload->action();
