<?php
class BDWP_RegisterUserProvider {

	private $m_RelayUrl;
    private $m_Email;
    private $m_PluginVersion;

	public function __construct($p_Data) {
		$this->m_Email = $p_Data['customer_email'];
        $this->m_RelayUrl = $p_Data['relay_url'];
        $this->m_PluginVersion = $p_Data['plugin_version'];
	}

	public function DoRegisterUser() {

		$registerStatus = $registerMessage = '';

		$customerRequest = array(
            'requestAction' => 'DL_PROD',
            'technology'	=> 'PHP',
            'source'		=> 'WORDPRESS',
            'email'			=> $this->m_Email,
            'source_data'	=> array(
					    		'wp_version' 	=>	BDWP_Diagnostics::GetWordPressVersion(),
					    		'bdwp_version' 	=>	BDWP_PluginInfo::GetVersion()
					    	)
        );

        @ini_set('max_execution_time', 200);
        @set_time_limit(200);
        $response = wp_remote_post($this->m_RelayUrl, array(
                'timeout' => 0,
                'body'    => array('data' => json_encode($customerRequest))
            )
        );
        
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 200) {

        	$arrResponse = (array)json_decode(wp_remote_retrieve_body($response));

        	if ($arrResponse['status'] == 'ERR_INVALIDEMAIL') {
        		$registerStatus = $arrResponse['status'];
        		$registerMessage = __('Please enter a valid email address.', 'botdetect-wp-captcha');
        	} else {
        		// Email is valid
        		$registerStatus = $arrResponse['status'];
        		$this->RegisterDownload();
        	}
        }

        return array(
        	'register_status' => $registerStatus,
        	'register_message' => $registerMessage
        );
	}

	public function RegisterDownload() {
        printf('<iframe style="border: 0px; display: none" src="%scaptcha.com/forms/integration/download.php?utm_source=plugin&amp;utm_medium=wp&amp;utm_campaign=%s&technology=PHP&email=%s&integration=wp&integration_version=%s" scrolling="no" marginwidth="0" marginheight="0" frameborder="0"></iframe>', BDWP_HttpHelpers::GetProtocol(), $this->m_PluginVersion, $this->m_Email, $this->m_PluginVersion);
    }
}
