<?php
/**
 * @package FacebookCommerce
 */
if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('WC_Facebook_Github_Updater')) :

/**
 * Auto update plugin class
 */
class WC_Facebook_Github_Updater {

  private static $instance;

  private $slug;
  private $pluginData;
  private $username;
  private $repo;
  private $pluginFile;
  private $githubAPIResult;
  private $pluginActivated;

  public static function get_instance(
    $pluginFile,
    $gitHubUsername,
    $gitHubProjectName) {
    return self::$instance === null
      ? (self::$instance = new self(
        $pluginFile,
        $gitHubUsername,
        $gitHubProjectName))
      : self::$instance;

  }

  public function __construct(
    $pluginFile,
    $gitHubUsername,
    $gitHubProjectName) {
      $this->pluginFile = $pluginFile;
      $this->username = $gitHubUsername;
      $this->repo = $gitHubProjectName;
      $this->slug = plugin_basename($this->pluginFile);
      $this->pluginData = null;
      add_filter("pre_set_site_transient_update_plugins",
        array($this, "setTransient"));
      add_filter("plugins_api", array($this, "setPluginInfo"), 10, 3);
      add_filter("upgrader_pre_install", array($this, "preInstall" ), 10, 3);
      add_filter("upgrader_post_install", array($this, "postInstall"), 10, 3);
  }

  // Get plugin information from gitHub
  private function getRepoReleaseInfo() {
    // only do this once
    if (!empty($this->githubAPIResult)) {
      return;
    }
    // Query the GitHub API
    $url =
      "https://api.github.com/repos/{$this->username}/{$this->repo}/releases";
    $response = wp_remote_get($url);
    // is_wp_error to check instance of WP_Error
    // response code to check whether this ERROR is an empty ERROR object
    if (!is_wp_error($response) ||
      wp_remote_retrieve_response_code($response) === 200) {
      $body = wp_remote_retrieve_body($response);
      if (!empty($body)) {
        $body = @json_decode($body);
        if (is_array($body)) {
          foreach ($body as $release) {
            if (strpos($release->tag_name, 'beta') === false) {
              $this->githubAPIResult = $release;
              break;
            }
          }
        }
      }
    }

    if (empty($this->githubAPIResult)) {
      if (is_wp_error($response)) {
        WC_Facebookcommerce_Utils::fblog($response->get_error_message());
      } else {
        WC_Facebookcommerce_Utils::fblog('Fail to get Github request correctly '
        . $response);
      }
    }
  }

  // Set plugin version information to get the update notification
  public function setTransient($transient) {
    // Only check once
    if (empty($transient->checked) ||
      !isset($transient->checked[$this->slug])) {
      return $transient;
    }
    if (!$this->pluginData) {
      $this->pluginData = get_plugin_data($this->pluginFile);
    }
    // Get plugin & GitHub release information
    $this->getRepoReleaseInfo();
    // Check the versions if we need to do an update
    $shouldUpdate = version_compare(
      substr($this->githubAPIResult->tag_name, 1),
      $transient->checked[$this->slug]);

    // Only process download url if shouldUpdate
    if ($shouldUpdate == 1) {
      $package = $this->get_asset_download_url($this->githubAPIResult->assets);
      if (!$package) {
        return $transient;
      }
      $obj = new stdClass();
      $obj->slug = $this->slug;
      $obj->new_version = $this->githubAPIResult->tag_name;
      $obj->url = $this->pluginData["PluginURI"];
      $obj->package = $package;
      $transient->response[$this->slug] = $obj;
    }
    return $transient;
  }

  // Set plugin version information to display in the details lightbox
  public function setPluginInfo($false, $action, $response) {
    // For multiple self-host plugins, check slug.
    if (!isset($response->slug) || ($response->slug != $this->slug)) {
      return $false;
    }
    if (!$this->pluginData) {
      $this->pluginData = get_plugin_data($this->pluginFile);
    }
    $this->getRepoReleaseInfo();
    // Add our plugin information
    $response->last_updated = $this->githubAPIResult->published_at;
    $response->slug = $this->slug;
    $response->plugin_name  = $this->pluginData["Name"];
    $response->version = $this->githubAPIResult->tag_name;
    $response->author = $this->pluginData["AuthorName"];
    $response->homepage = $this->pluginData["PluginURI"];

    // This is our release download zip file
    $downloadLink =
      $this->get_asset_download_url($this->githubAPIResult->assets);
    $response->download_link = $downloadLink;
    // Create tabs in the lightbox
    $response->sections = array(
      'description' => $this->pluginData["Description"],
      'changelog' => self::getAPIResultBody($this->githubAPIResult->body)
    );

    return $response;
  }

  // Unzip and install plugin
  public function postInstall($true, $hook_extra, $result) {
    if (!in_array($this->slug, $hook_extra)) {
      return $result;
    }
    // Since we are hosted in GitHub, our plugin folder would have a dirname of
    // reponame-tagname change it to our original one:
    global $wp_filesystem;
    $pluginFolder =
      WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . dirname($this->slug);
    $wp_filesystem->move($result['destination'], $pluginFolder);
    $result['destination'] = $pluginFolder;
    // Re-activate plugin if needed
    if ($this->pluginActivated) {
      $activate = activate_plugin($this->slug);
    }
    return $result;
  }

  // Perform check before installation starts.
  public function preInstall($true, $args) {
    $this->pluginActivated = is_plugin_active($this->slug);
  }

  private static function getAPIResultBody($text) {
    $text = str_replace(array("\r\n", "\r"), "\n", $text);
    $text = trim($text, "\n");
    $lines = explode("\n", $text);
    $lines = array_filter($lines, 'strlen');
    $woo_changelog = array_filter($lines, function($line) {
      return !is_numeric(strpos($line, 'WordPress'));
    });
    $woo_changelog = implode("<br>", $woo_changelog);
    return $woo_changelog;
  }

  public function get_asset_download_url($assets) {
    $assets = json_decode(json_encode($assets), true);
    array_filter($assets, function($asset) {
      return strpos($asset["browser_download_url"],
        "facebook-for-woocommerce-v") !== false;
    });
    if (!empty($assets)) {
      $valid_asset = reset($assets);
      return $valid_asset["browser_download_url"];
    }
    return false;
  }

}

endif;
