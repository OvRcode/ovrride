<?php

namespace DeliciousBrains\WP_Offload_SES\Aws3;

// This file was auto-generated from sdk-root/src/data/sms-voice/2018-09-05/endpoint-tests-1.json
return ['testCases' => [['documentation' => 'For region us-gov-east-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://sms-voice.pinpoint-fips.us-gov-east-1.api.aws']], 'params' => ['Region' => 'us-gov-east-1', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region us-gov-east-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://sms-voice.pinpoint-fips.us-gov-east-1.amazonaws.com']], 'params' => ['Region' => 'us-gov-east-1', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region us-gov-east-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://sms-voice.pinpoint.us-gov-east-1.api.aws']], 'params' => ['Region' => 'us-gov-east-1', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region us-gov-east-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://sms-voice.pinpoint.us-gov-east-1.amazonaws.com']], 'params' => ['Region' => 'us-gov-east-1', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region cn-north-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://sms-voice.pinpoint-fips.cn-north-1.api.amazonwebservices.com.cn']], 'params' => ['Region' => 'cn-north-1', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region cn-north-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://sms-voice.pinpoint-fips.cn-north-1.amazonaws.com.cn']], 'params' => ['Region' => 'cn-north-1', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region cn-north-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://sms-voice.pinpoint.cn-north-1.api.amazonwebservices.com.cn']], 'params' => ['Region' => 'cn-north-1', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region cn-north-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://sms-voice.pinpoint.cn-north-1.amazonaws.com.cn']], 'params' => ['Region' => 'cn-north-1', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region us-iso-east-1 with FIPS enabled and DualStack enabled', 'expect' => ['error' => 'FIPS and DualStack are enabled, but this partition does not support one or both'], 'params' => ['Region' => 'us-iso-east-1', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region us-iso-east-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://sms-voice.pinpoint-fips.us-iso-east-1.c2s.ic.gov']], 'params' => ['Region' => 'us-iso-east-1', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region us-iso-east-1 with FIPS disabled and DualStack enabled', 'expect' => ['error' => 'DualStack is enabled but this partition does not support DualStack'], 'params' => ['Region' => 'us-iso-east-1', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region us-iso-east-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://sms-voice.pinpoint.us-iso-east-1.c2s.ic.gov']], 'params' => ['Region' => 'us-iso-east-1', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region us-east-1 with FIPS enabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://sms-voice.pinpoint-fips.us-east-1.api.aws']], 'params' => ['Region' => 'us-east-1', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region us-east-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://sms-voice.pinpoint-fips.us-east-1.amazonaws.com']], 'params' => ['Region' => 'us-east-1', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region us-east-1 with FIPS disabled and DualStack enabled', 'expect' => ['endpoint' => ['url' => 'https://sms-voice.pinpoint.us-east-1.api.aws']], 'params' => ['Region' => 'us-east-1', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region us-east-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://sms-voice.pinpoint.us-east-1.amazonaws.com']], 'params' => ['Region' => 'us-east-1', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For region us-isob-east-1 with FIPS enabled and DualStack enabled', 'expect' => ['error' => 'FIPS and DualStack are enabled, but this partition does not support one or both'], 'params' => ['Region' => 'us-isob-east-1', 'UseDualStack' => \true, 'UseFIPS' => \true]], ['documentation' => 'For region us-isob-east-1 with FIPS enabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://sms-voice.pinpoint-fips.us-isob-east-1.sc2s.sgov.gov']], 'params' => ['Region' => 'us-isob-east-1', 'UseDualStack' => \false, 'UseFIPS' => \true]], ['documentation' => 'For region us-isob-east-1 with FIPS disabled and DualStack enabled', 'expect' => ['error' => 'DualStack is enabled but this partition does not support DualStack'], 'params' => ['Region' => 'us-isob-east-1', 'UseDualStack' => \true, 'UseFIPS' => \false]], ['documentation' => 'For region us-isob-east-1 with FIPS disabled and DualStack disabled', 'expect' => ['endpoint' => ['url' => 'https://sms-voice.pinpoint.us-isob-east-1.sc2s.sgov.gov']], 'params' => ['Region' => 'us-isob-east-1', 'UseDualStack' => \false, 'UseFIPS' => \false]], ['documentation' => 'For custom endpoint with fips disabled and dualstack disabled', 'expect' => ['endpoint' => ['url' => 'https://example.com']], 'params' => ['Region' => 'us-east-1', 'UseDualStack' => \false, 'UseFIPS' => \false, 'Endpoint' => 'https://example.com']], ['documentation' => 'For custom endpoint with fips enabled and dualstack disabled', 'expect' => ['error' => 'Invalid Configuration: FIPS and custom endpoint are not supported'], 'params' => ['Region' => 'us-east-1', 'UseDualStack' => \false, 'UseFIPS' => \true, 'Endpoint' => 'https://example.com']], ['documentation' => 'For custom endpoint with fips disabled and dualstack enabled', 'expect' => ['error' => 'Invalid Configuration: Dualstack and custom endpoint are not supported'], 'params' => ['Region' => 'us-east-1', 'UseDualStack' => \true, 'UseFIPS' => \false, 'Endpoint' => 'https://example.com']]], 'version' => '1.0'];
