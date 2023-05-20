<?php

namespace DeliciousBrains\WP_Offload_SES\Aws3\Aws\Exception;

use DeliciousBrains\WP_Offload_SES\Aws3\Aws\HasMonitoringEventsTrait;
use DeliciousBrains\WP_Offload_SES\Aws3\Aws\MonitoringEventsInterface;
class CredentialsException extends \RuntimeException implements MonitoringEventsInterface
{
    use HasMonitoringEventsTrait;
}