<?php

class CustomSettings
{
    public static function getFileName()
    {
        return SC_TOOLS_DIR.'custom_settings.json';
    }

    public static function getCustomSetting()
    {
        $file = self::getFileName();

        if (file_exists($file))
        {
            $content = file_get_contents($file);
        }
        else
        {
            $content = '';
        }

        $content_arr = json_decode($content, true);

        return $content_arr;
    }

    public static function getCustomSettingDetail($lib, $type)
    {
        global $sc_agent;
        $settings = self::getCustomSetting();

        if (empty($settings))
        {
            return '';
        }

        return $settings[$lib][$type][$sc_agent->id_employee];
    }

    /*
     * -lib (cat,ord...)
     * ---type
     * ------sc_agen_id : data
     */
    public static function addCustomSetting($lib, $type, $data)
    {
        global $sc_agent;

        $file = self::getFileName();
        $settings = self::getCustomSetting();
        $settings[$lib][$type][$sc_agent->id_employee][] = $data;

        $contentToWrite = json_encode($settings);
        if (file_put_contents($file, $contentToWrite) !== false)
        {
            return $settings[$lib][$type][$sc_agent->id_employee];
        }
    }

    public static function deleteCustomSetting($lib, $type, $name)
    {
        global $sc_agent;

        $file = self::getFileName();
        $settings = self::getCustomSetting();
        $towrite = array();

        foreach ($settings[$lib][$type][$sc_agent->id_employee] as $key => $setting)
        {
            if ($setting['name'] != $name)
            {
                $towrite[] = $setting;
            }
        }
        $settings[$lib][$type][$sc_agent->id_employee] = $towrite;

        $contentToWrite = json_encode($settings);

        return file_put_contents($file, $contentToWrite);
    }
}
