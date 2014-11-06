<?php
require_once( ABSPATH . 'wp-load.php' );
require_once( ABSPATH . 'wp-admin/includes/file.php');

class BDWP_InstallCaptchaProviderBotDetectLibrary {

    private $m_RelayUrl;
    private $m_FolderPlugin;
    private $m_Email;
    private $m_PluginVersion;

    private $m_DisplayMessages;
    private $m_ErrorMessage = array();

    public function __construct($p_Config) {
        $this->m_Email = $p_Config['customer_email'];
        $this->m_RelayUrl = $p_Config['relay_url'];
        $this->m_FolderPlugin = $p_Config['folder_plugin'];
        $this->m_PluginVersion = $p_Config['plugin_version'];

        $this->m_DisplayMessages = $this->NotificationMessages();
    }
		
    public function NotificationMessages() {
        return array(
            'MSG_ERR_DEFAULT' => __('Error occured while installing BotDetect Captcha library.', 'botdetect-wp-captcha'),
            'MSG_ERR_DETAILS' => __('Error details: ', 'botdetect-wp-captcha'),
            'MSG_ERR_EMAIL' => __('Please enter a valid email address.', 'botdetect-wp-captcha'),
            'MSG_ERR_UNKNOWN_PROBLEM' => __('Installation failed because of an unknown reason. Please try once more, and if the issue occurs again, we would appreciate if you let us know what happened (so we can investigate, and fix if possible).', 'botdetect-wp-captcha'),
            'MSG_ERR_FILE_EXTENSION' => __('Download doesn\'t have ".zip" extension.', 'botdetect-wp-captcha'),
            'MSG_ERR_DOWNLOAD_LIBRARY' => __('Installation wasn\'t able to download the BotDetect Captcha library to your WordPress server. This problem may be due to restricted write permissions to the plugin folder on your WordPress server. Please check with your WordPress server administrator if this was the case and let us know.', 'botdetect-wp-captcha'),
            'MSG_ERR_UNZIP_LIBRARY' => __('Installation wasn\'t able to download the BotDetect Captcha library to your WordPress server. This problem may be due to restricted write permissions to the plugin folder on your WordPress server. Please check with your WordPress server administrator if this was the case and let us know.', 'botdetect-wp-captcha'),
            'MSG_ERR_OPEN_RELAY_URL' => __('Installation wasn\'t able to download the BotDetect Captcha library from captcha.com. Please check if the captcha.com site being inaccessible from your WordPress server is a) temporary (captcha.com is momentarily unavailable even from browsers on other computers), or b) persistent (web access is restricted on your WordPress server). If the issue is temporary, just try again in a few minutes when captcha.com comes back online. If the issue is persistent, please contact your WordPress server administrator.', 'botdetect-wp-captcha'),
            'MSG_ERR_NETWORK_PROBLEM' => __('Installation wasn\'t able to download the BotDetect Captcha library due to network-related issues. Please try once more, and if the issue occurs again, we would appreciate if you let us know what happened (so we can investigate, and fix if possible).', 'botdetect-wp-captcha'),
        );
    }

    public function GetErrorMessageResponse($p_Response, $p_MessageDefault, $p_ErrorCode) {

        if (!is_wp_error($p_Response)) {
            return $this->m_DisplayMessages['MSG_ERR_DEFAULT'] . '<br>' . $p_MessageDefault;
        }

        $msgTemp = $p_Response->get_error_message();
        if (empty($msgTemp)) {
        	return $this->m_DisplayMessages['MSG_ERR_DEFAULT'] . '<br>'. $this->m_DisplayMessages['MSG_ERR_OPEN_RELAY_URL'];
        }
        
        $arrResponse = (array)$p_Response;
        if (is_array($arrResponse) && array_key_exists('errors', $arrResponse)) {

            $errors = $arrResponse['errors'];
            if (is_array($errors)) {

                if (array_key_exists('http_request_failed', $errors)) {
                    return $this->m_DisplayMessages['MSG_ERR_DEFAULT'] . '<br>'. $this->m_DisplayMessages['MSG_ERR_NETWORK_PROBLEM']; 
                }
            }
        }
        return $this->m_DisplayMessages['MSG_ERR_DEFAULT'] . '<br>' . $this->m_DisplayMessages['MSG_ERR_DETAILS'] . $p_ErrorCode . ' ' . $msgTemp;
    }

    /**
     *  Install BotDetect Captcha Library
     */
    public function DoInstall() {
        $status = $message = $pathFileLibraryOnDisk = '';

        $response = @$this->GetBotDetectLibDownloadUrlResponse($this->m_RelayUrl);

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {
            $status = 'ERR_OCCURED';
            $message = $this->GetErrorMessageResponse($response, $this->m_DisplayMessages['MSG_ERR_UNKNOWN_PROBLEM'], '[#c11]');
        }
        else {
            $arrResponse = (array)json_decode(wp_remote_retrieve_body($response));

            if ($arrResponse['status'] == 'ERR_INVALIDEMAIL') {
                $status = $arrResponse['status'];
                $message = $this->m_DisplayMessages['MSG_ERR_EMAIL'];
            }
            else if ($arrResponse['status'] == 'OK') {

                // Load stats download url
                $this->RegisterDownload($this->m_Email);

            	$libraryDownloadUrl = $arrResponse['downloadUrl'];
                $libraryUrlOnDisk = $this->m_FolderPlugin . DIRECTORY_SEPARATOR . basename($libraryDownloadUrl);

                $config = array(
                    'LibraryDownloadUrl' => $libraryDownloadUrl,
                    'PathFileUnZip' => $libraryUrlOnDisk
                );

                if ($this->DownloadAndUnzip($config)) {
                    $status = 'SUCESSFULLY_INSTALLED';
                    $pathFileLibraryOnDisk = $libraryUrlOnDisk;
                } else {
                    if (!empty($this->m_ErrorMessage)) {
                        $errMsg = '';
                        foreach ($this->m_ErrorMessage as $error) {
                            $errMsg .= $error . '<br>';
                        }
                        $message = $this->m_DisplayMessages['MSG_ERR_DEFAULT'] . '<br>' . $this->m_DisplayMessages['MSG_ERR_DETAILS'] . $errMsg;
                    } else {
                        $message = $this->m_DisplayMessages['MSG_ERR_DEFAULT'] . '<br>[#c12] ' . $this->m_DisplayMessages['MSG_ERR_UNKNOWN_PROBLEM'];
                    }
                    $status = 'ERR_OCCURED';
                }
            }
            else {
                // Received incorrect data
                $status = 'ERR_OCCURED';
                $message = $this->m_DisplayMessages['MSG_ERR_DEFAULT'];
            }
        }

        return array(
            'status' => $status,
            'message' => $message,
            'pathFileLibraryOnDisk' => $pathFileLibraryOnDisk
        );
    }

    /**
     *	Download and Unzip BotDetect Captcha Library
     */
    public function DownloadAndUnzip($p_Config) {

        if (!is_array($p_Config)) return false;

        $result= $this->DownloadLibrary($p_Config['LibraryDownloadUrl']);
        if ($result) {
            return $this->UnZipFile($p_Config['PathFileUnZip'], $this->m_FolderPlugin);
        }
        return false;
    }

    /**
     * Post data and get content from a url
     */
    public function GetBotDetectLibDownloadUrlResponse($p_Url) {

    	$sourceData = array(
    		'wp_version' 	=>	BDWP_Diagnostics::GetWordPressVersion(),
    		'bdwp_version' 	=>	$this->m_PluginVersion
    	);

        $customerRequest = array(
            'requestAction' => 'DL_PROD',
            'technology'	=> 'PHP',
            'source'		=> 'WORDPRESS',
            'email'			=> wp_filter_nohtml_kses($this->m_Email),
            'source_data'	=> $sourceData
        );

        @ini_set('max_execution_time', 200);
        @set_time_limit(200);
        $response = wp_remote_post( $p_Url, array(
                'timeout' => 0,
                'body'    => array('data' => json_encode($customerRequest))
            )
        );
        return $response;
    }

    /**
     *	Download BotDetect Captcha Library into plugin folder
     */
    public function DownloadLibrary($p_Url) {

        @ini_set('max_execution_time', 200);
        @set_time_limit(200);

        $fileName = basename($p_Url);
        list($name, $ext) = explode(".", $fileName);

        $response = wp_remote_get($p_Url, array('timeout' => 0));

        try {
        	if ($ext != "zip") {
        		array_push($this->m_ErrorMessage, $this->m_DisplayMessages['MSG_ERR_FILE_EXTENSION']);
                return false;
            }

            if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {

                $tempMsg = $this->GetErrorMessageResponse($response, $this->m_DisplayMessages['MSG_ERR_DOWNLOAD_LIBRARY'], '[#c41]');
                array_push($this->m_ErrorMessage, $tempMsg);
                return false;
            }

            $fileContents = wp_remote_retrieve_body($response);
            $fileLibrary = $this->m_FolderPlugin . DIRECTORY_SEPARATOR . $fileName;
            $upload = file_put_contents($fileLibrary, $fileContents);
            
            if (file_exists($fileLibrary)) {
            	return $upload;
            } else {
            	array_push($this->m_ErrorMessage, '[#c42] ' . $this->m_DisplayMessages['MSG_ERR_DOWNLOAD_LIBRARY']);
        		return false;
            }
        } catch (Exception $e) {
            array_push($this->m_ErrorMessage, '[#e41] ' . $e->getMessage());
            return false;
        }
    }

    /**
     *	Unzip a zip file
     */
    public function UnZipFile($p_PathFileUnZip, $p_FolderPlugin) {
        try {
            if (!file_exists($p_PathFileUnZip)) return false;

            WP_Filesystem();
            @chmod($p_PathFileUnZip, 0755); 
			$result = unzip_file($p_PathFileUnZip, $p_FolderPlugin);

			if (is_wp_error($result)) {
				array_push($this->m_ErrorMessage, '[#c51] '. $this->m_DisplayMessages['MSG_ERR_UNZIP_LIBRARY']);
                return false;
			}
			
			if (is_dir($p_FolderPlugin . DIRECTORY_SEPARATOR . 'lib')) {
                return true;
            } else {
                array_push($this->m_ErrorMessage, '[#c52] '. $this->m_DisplayMessages['MSG_ERR_UNZIP_LIBRARY']);
                return false;
            }

        } catch (Exception $e) {
            array_push($this->m_ErrorMessage, '[#e51] ' . $e->getMessage());
            return false;
        }
    }

    public function RegisterDownload($p_Email) {
        printf('<iframe style="border: 0px; display: none" src="%scaptcha.com/forms/integration/download.php?utm_source=plugin&amp;utm_medium=wp&amp;utm_campaign=%s&technology=PHP&email=%s&integration=wp&integration_version=%s" scrolling="no" marginwidth="0" marginheight="0" frameborder="0"></iframe>', BDWP_HttpHelpers::GetProtocol(), $this->m_PluginVersion, $p_Email, $this->m_PluginVersion);
    }
}
