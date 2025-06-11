<?php
abstract class ObjectModel extends ObjectModelCore
{
    public function validateFields($die = false, $error_return = true)
    {
        foreach ($this->def['fields'] as $field => $data) {
            if (!empty($data['lang'])) {
                continue;
            }

            if (is_array($this->update_fields) && empty($this->update_fields[$field]) && isset($this->def['fields'][$field]['shop']) && $this->def['fields'][$field]['shop']) {
                continue;
            }

            $message = $this->validateField($field, $this->$field);
            if ($message !== true) {
                if ($die) {
                    throw new PrestaShopException($message);
                }

                return $error_return ? $message : false;
            }
        }

        return true;
    }
}
?>