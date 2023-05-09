<?php

namespace DeliciousBrains\WP_Offload_SES\Aws3;

// This file was auto-generated from sdk-root/src/data/kinesis/2013-12-02/paginators-1.json
return ['pagination' => ['DescribeStream' => ['input_token' => 'ExclusiveStartShardId', 'limit_key' => 'Limit', 'more_results' => 'StreamDescription.HasMoreShards', 'output_token' => 'StreamDescription.Shards[-1].ShardId', 'result_key' => 'StreamDescription.Shards'], 'ListStreamConsumers' => ['input_token' => 'NextToken', 'limit_key' => 'MaxResults', 'output_token' => 'NextToken'], 'ListStreams' => ['input_token' => 'NextToken', 'limit_key' => 'Limit', 'more_results' => 'HasMoreStreams', 'output_token' => 'NextToken', 'result_key' => ['StreamNames', 'StreamSummaries']]]];
