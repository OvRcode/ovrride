<?php

namespace DeliciousBrains\WP_Offload_SES\Aws3;

// This file was auto-generated from sdk-root/src/data/migration-hub-refactor-spaces/2021-10-26/endpoint-tests-1.json
return ['testCases' => [['documentation' => 'For custom endpoint with fips disabled and dualstack disabled', 'expect' => ['endpoint' => ['url' => 'https://example.com']], 'params' => ['UseFIPS' => \false, 'Region' => 'us-east-1', 'UseDualStack' => \false, 'Endpoint' => 'https://example.com']], ['documentation' => 'For custom endpoint with fips enabled and dualstack disabled', 'expect' => ['error' => 'Invalid Configuration: FIPS and custom endpoint are not supported'], 'params' => ['UseFIPS' => \true, 'Region' => 'us-east-1', 'UseDualStack' => \false, 'Endpoint' => 'https://example.com']], ['documentation' => 'For custom endpoint with fips disabled and dualstack enabled', 'expect' => ['error' => 'Invalid Configuration: Dualstack and custom endpoint are not supported'], 'params' => ['UseFIPS' => \false, 'Region' => 'us-east-1', 'UseDualStack' => \true, 'Endpoint' => 'https://example.com']]], 'version' => '1.0'];
