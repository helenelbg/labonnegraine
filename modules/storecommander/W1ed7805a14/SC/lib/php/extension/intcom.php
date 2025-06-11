<?php
$intcom_ignore = SCI::getConfigurationValue('SC_IGNORE_INTCOM');
if (!$intcom_ignore && (SC_DEMO || defined('SUB6TYP2')))
{
    $intercom_hash = getIntercomHash($sc_agent);
    if (!empty($intercom_hash))
    {
        $intercom_params = array(
            'app_id' => 'fl4pkxli',
            'name' => $sc_agent->firstname.' '.$sc_agent->lastname,
            'lastname' => $sc_agent->lastname,
            'firstname' => $sc_agent->firstname,
            'email' => $sc_agent->email,
            'user_hash' => $intercom_hash,
            'language_override' => SC_ISO_LANG_FOR_EXTERNAL,
            'Last techno used' => 'PS',
            'session_duration' => 3600000, ## session 1h en ms
        );
        if (!SC_DEMO)
        {
            $access_details = access_details();
            $licence = SCI::getConfigurationValue('SC_LICENSE_KEY');
            $type = str_replace('SUBSCRIPTION ', '', SCLIMREF);
            switch ($type){
                case 'SOLO':
                    $type = 'SOLO-';
                    break;
                case 'MULTI':
                    $type = 'MS-';
                    break;
                case 'MULTI+':
                    $type = 'MS+';
                    break;
            }
            $intercom_params['Last seen on'] = $access_details['domain'];
            $intercom_params['company'] = array(
                'company_id' => $licence,
                'name' => $licence,
                'plan' => $type,
                'Company version' => SC_VERSION.(SC_BETA ? ' BETA' : '').(SC_GRIDSEDITOR_INSTALLED ? ' GE'.(SC_GRIDSEDITOR_PRO_INSTALLED ? 'P' : '') : ''),
                'Company technology' => 'Prestashop',
            );
        } ?>
    <script>window.intercomSettings = <?php echo json_encode($intercom_params); ?>;</script>
    <script>(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',w.intercomSettings);}else{var d=document;var i=function(){i.c(arguments);};i.q=[];i.c=function(args){i.q.push(args);};w.Intercom=i;var l=function(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/wurnhz1x';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);};if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();</script>
<?php
    }
}
