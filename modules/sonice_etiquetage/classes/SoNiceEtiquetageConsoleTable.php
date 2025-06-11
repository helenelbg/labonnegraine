<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from Common-Services Co., Ltd.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL SMC is strictly forbidden.
 * In order to obtain a license, please contact us: contact@common-services.com
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe Common-Services Co., Ltd.
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la Common-Services Co. Ltd. est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter Common-Services Co., Ltd. a l'adresse: contact@common-services.com
 * ...........................................................................
 *
 * @package   sonice_etiquetage
 * @author    Alexandre D.
 * @copyright Copyright (c) 2011-2015 Common Services Co Ltd - 90/25 Sukhumvit 81 - 10260 Bangkok - Thailand
 * @license   Commercial license
 * Support by mail  :  support.sonice@common-services.com
 */

/**
 * Class SoNiceEtiquetageConsoleTable
 */
class SoNiceEtiquetageConsoleTable
{

    protected $headers = array();
    protected $rows = array();
    protected $column_sizes = array();
    protected $format = '';
    protected $separator = '';
    protected $output = '';

    /**
     * SoNiceEtiquetageConsoleTable constructor.
     *
     * @param array $header
     * @param array $rows
     */
    public function __construct($header = null, $rows = null)
    {
        $this->headers = $header;
        $this->rows = $rows;
    }

    /**
     * @param $header
     */
    public function setHeader($header)
    {
        $this->headers = $header;
    }

    /**
     * @return array|null
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param $rows
     */
    public function setRows($rows)
    {
        $this->rows = $rows;
    }

    /**
     * @return array|null
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @param array $header
     * @return int
     */
    protected function getLongestColmunSize($header)
    {
        $longest = Tools::strlen($header);

        foreach (SoColissimoTools::arrayColumn($this->rows, $header) as $value) {
            $longest = max($longest, Tools::strlen($this->sanitize($value)));
        }

        return $longest;
    }

    /**
     * Sanitize string for proper display.
     *
     * @param string $str
     * @return string
     */
    protected function sanitize($str)
    {
        $str = str_replace('°', '', $str);

        if (mb_detect_encoding((string)$str) != 'UTF-8') {
            iconv('ISO-8859-15', 'UTF-8//TRANSLIT', $str);
        }

        $str = mb_convert_encoding($str, 'HTML-ENTITIES', 'UTF-8');
        $searches = array('&szlig;', '&(..)lig;', '&([aouAOU])uml;', '&(.)[^;]*;');
        $replacements = array('ss', '\\1', '\\1e', '\\1');
        foreach ($searches as $key => $search) {
            $str = mb_ereg_replace($search, $replacements[$key], $str);
        }

        return $str;
    }

    /**
     * @return string
     */
    protected function render()
    {
        $total_line_length = 0;
        $this->format = '|';
        $this->separator = '+';

        foreach ($this->headers as $header) {
            $len = (int)$this->getLongestColmunSize($header);
            $this->format .= ' %-'.$len.'s |';
            $this->separator .= '-'.str_repeat('-', $len).'-+';
            $total_line_length += 3 + $len; // 2 spaces + 1 | = 3 + $len
        }

        $this->format .= '<br>';
        $this->separator .= '<br>';

        $this->output .= $this->separator;
        $this->output .= vsprintf($this->format, $this->headers);
        $this->output .= $this->separator;

        foreach ($this->rows as $row) {
            $this->output .= vsprintf($this->format, array_map([$this, 'sanitize'], $row));
        }

        $this->output .= $this->separator;

        return $this->output;
    }

    /**
     * @return string
     */
    public function fetch()
    {
        return $this->render();
    }

    /**
     * Display the resulted array.
     */
    public function display()
    {
        echo $this->render();
    }
}
