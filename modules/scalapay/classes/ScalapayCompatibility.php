<?php
/**
 * Scalapay
 *
 * @author Scalapay Plugin Integration Team
 * @copyright 2022 Scalapay
 * @license LICENCE.md for license details.
 */

namespace Scalapay;

class ScalapayCompatibility
{
    /**
     * @var array
     */
    protected $modulesChangingCartController = [
        '1.6' => [],
        '1.7' => ['onepagecheckoutps'],
        '8' => []];

    public function isCartControllerChanged($psVersion)
    {
        foreach ($this->modulesChangingCartController[$this->getPrestashopVersion($psVersion)] as $moduleName) {
            if (\Module::isEnabled($moduleName)) {
                return true;
            }
        }

        return false;
    }

    protected function getPrestashopVersion($psVersion)
    {
        /* @phpstan-ignore-next-line */
        if (version_compare($psVersion, '1.7.0', '<')) {
            return '1.6';
            /* @phpstan-ignore-next-line */
        } elseif (version_compare($psVersion, '8.0', '<')) {
            return '1.7';
            /* @phpstan-ignore-next-line */
        } else {
            return '8';
        }
    }
}
