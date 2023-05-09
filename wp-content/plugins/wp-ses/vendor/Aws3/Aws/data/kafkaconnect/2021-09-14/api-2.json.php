<?php

namespace DeliciousBrains\WP_Offload_SES\Aws3;

// This file was auto-generated from sdk-root/src/data/kafkaconnect/2021-09-14/api-2.json
return ['version' => '2.0', 'metadata' => ['apiVersion' => '2021-09-14', 'endpointPrefix' => 'kafkaconnect', 'jsonVersion' => '1.1', 'protocol' => 'rest-json', 'serviceAbbreviation' => 'Kafka Connect', 'serviceFullName' => 'Managed Streaming for Kafka Connect', 'serviceId' => 'KafkaConnect', 'signatureVersion' => 'v4', 'signingName' => 'kafkaconnect', 'uid' => 'kafkaconnect-2021-09-14'], 'operations' => ['CreateConnector' => ['name' => 'CreateConnector', 'http' => ['method' => 'POST', 'requestUri' => '/v1/connectors', 'responseCode' => 200], 'input' => ['shape' => 'CreateConnectorRequest'], 'output' => ['shape' => 'CreateConnectorResponse'], 'errors' => [['shape' => 'NotFoundException'], ['shape' => 'ConflictException'], ['shape' => 'BadRequestException'], ['shape' => 'ForbiddenException'], ['shape' => 'ServiceUnavailableException'], ['shape' => 'TooManyRequestsException'], ['shape' => 'UnauthorizedException'], ['shape' => 'InternalServerErrorException']]], 'CreateCustomPlugin' => ['name' => 'CreateCustomPlugin', 'http' => ['method' => 'POST', 'requestUri' => '/v1/custom-plugins', 'responseCode' => 200], 'input' => ['shape' => 'CreateCustomPluginRequest'], 'output' => ['shape' => 'CreateCustomPluginResponse'], 'errors' => [['shape' => 'NotFoundException'], ['shape' => 'ConflictException'], ['shape' => 'BadRequestException'], ['shape' => 'ForbiddenException'], ['shape' => 'ServiceUnavailableException'], ['shape' => 'TooManyRequestsException'], ['shape' => 'UnauthorizedException'], ['shape' => 'InternalServerErrorException']]], 'CreateWorkerConfiguration' => ['name' => 'CreateWorkerConfiguration', 'http' => ['method' => 'POST', 'requestUri' => '/v1/worker-configurations', 'responseCode' => 200], 'input' => ['shape' => 'CreateWorkerConfigurationRequest'], 'output' => ['shape' => 'CreateWorkerConfigurationResponse'], 'errors' => [['shape' => 'NotFoundException'], ['shape' => 'ConflictException'], ['shape' => 'BadRequestException'], ['shape' => 'ForbiddenException'], ['shape' => 'ServiceUnavailableException'], ['shape' => 'TooManyRequestsException'], ['shape' => 'UnauthorizedException'], ['shape' => 'InternalServerErrorException']]], 'DeleteConnector' => ['name' => 'DeleteConnector', 'http' => ['method' => 'DELETE', 'requestUri' => '/v1/connectors/{connectorArn}', 'responseCode' => 200], 'input' => ['shape' => 'DeleteConnectorRequest'], 'output' => ['shape' => 'DeleteConnectorResponse'], 'errors' => [['shape' => 'NotFoundException'], ['shape' => 'BadRequestException'], ['shape' => 'ForbiddenException'], ['shape' => 'ServiceUnavailableException'], ['shape' => 'TooManyRequestsException'], ['shape' => 'UnauthorizedException'], ['shape' => 'InternalServerErrorException']], 'idempotent' => \true], 'DeleteCustomPlugin' => ['name' => 'DeleteCustomPlugin', 'http' => ['method' => 'DELETE', 'requestUri' => '/v1/custom-plugins/{customPluginArn}', 'responseCode' => 200], 'input' => ['shape' => 'DeleteCustomPluginRequest'], 'output' => ['shape' => 'DeleteCustomPluginResponse'], 'errors' => [['shape' => 'NotFoundException'], ['shape' => 'BadRequestException'], ['shape' => 'ForbiddenException'], ['shape' => 'ServiceUnavailableException'], ['shape' => 'TooManyRequestsException'], ['shape' => 'UnauthorizedException'], ['shape' => 'InternalServerErrorException']], 'idempotent' => \true], 'DescribeConnector' => ['name' => 'DescribeConnector', 'http' => ['method' => 'GET', 'requestUri' => '/v1/connectors/{connectorArn}', 'responseCode' => 200], 'input' => ['shape' => 'DescribeConnectorRequest'], 'output' => ['shape' => 'DescribeConnectorResponse'], 'errors' => [['shape' => 'NotFoundException'], ['shape' => 'BadRequestException'], ['shape' => 'ForbiddenException'], ['shape' => 'ServiceUnavailableException'], ['shape' => 'TooManyRequestsException'], ['shape' => 'UnauthorizedException'], ['shape' => 'InternalServerErrorException']]], 'DescribeCustomPlugin' => ['name' => 'DescribeCustomPlugin', 'http' => ['method' => 'GET', 'requestUri' => '/v1/custom-plugins/{customPluginArn}', 'responseCode' => 200], 'input' => ['shape' => 'DescribeCustomPluginRequest'], 'output' => ['shape' => 'DescribeCustomPluginResponse'], 'errors' => [['shape' => 'NotFoundException'], ['shape' => 'BadRequestException'], ['shape' => 'ForbiddenException'], ['shape' => 'ServiceUnavailableException'], ['shape' => 'TooManyRequestsException'], ['shape' => 'UnauthorizedException'], ['shape' => 'InternalServerErrorException']]], 'DescribeWorkerConfiguration' => ['name' => 'DescribeWorkerConfiguration', 'http' => ['method' => 'GET', 'requestUri' => '/v1/worker-configurations/{workerConfigurationArn}', 'responseCode' => 200], 'input' => ['shape' => 'DescribeWorkerConfigurationRequest'], 'output' => ['shape' => 'DescribeWorkerConfigurationResponse'], 'errors' => [['shape' => 'NotFoundException'], ['shape' => 'BadRequestException'], ['shape' => 'ForbiddenException'], ['shape' => 'ServiceUnavailableException'], ['shape' => 'TooManyRequestsException'], ['shape' => 'UnauthorizedException'], ['shape' => 'InternalServerErrorException']]], 'ListConnectors' => ['name' => 'ListConnectors', 'http' => ['method' => 'GET', 'requestUri' => '/v1/connectors', 'responseCode' => 200], 'input' => ['shape' => 'ListConnectorsRequest'], 'output' => ['shape' => 'ListConnectorsResponse'], 'errors' => [['shape' => 'NotFoundException'], ['shape' => 'BadRequestException'], ['shape' => 'ForbiddenException'], ['shape' => 'ServiceUnavailableException'], ['shape' => 'TooManyRequestsException'], ['shape' => 'UnauthorizedException'], ['shape' => 'InternalServerErrorException']]], 'ListCustomPlugins' => ['name' => 'ListCustomPlugins', 'http' => ['method' => 'GET', 'requestUri' => '/v1/custom-plugins', 'responseCode' => 200], 'input' => ['shape' => 'ListCustomPluginsRequest'], 'output' => ['shape' => 'ListCustomPluginsResponse'], 'errors' => [['shape' => 'NotFoundException'], ['shape' => 'BadRequestException'], ['shape' => 'ForbiddenException'], ['shape' => 'ServiceUnavailableException'], ['shape' => 'TooManyRequestsException'], ['shape' => 'UnauthorizedException'], ['shape' => 'InternalServerErrorException']]], 'ListWorkerConfigurations' => ['name' => 'ListWorkerConfigurations', 'http' => ['method' => 'GET', 'requestUri' => '/v1/worker-configurations', 'responseCode' => 200], 'input' => ['shape' => 'ListWorkerConfigurationsRequest'], 'output' => ['shape' => 'ListWorkerConfigurationsResponse'], 'errors' => [['shape' => 'NotFoundException'], ['shape' => 'BadRequestException'], ['shape' => 'ForbiddenException'], ['shape' => 'ServiceUnavailableException'], ['shape' => 'TooManyRequestsException'], ['shape' => 'UnauthorizedException'], ['shape' => 'InternalServerErrorException']]], 'UpdateConnector' => ['name' => 'UpdateConnector', 'http' => ['method' => 'PUT', 'requestUri' => '/v1/connectors/{connectorArn}', 'responseCode' => 200], 'input' => ['shape' => 'UpdateConnectorRequest'], 'output' => ['shape' => 'UpdateConnectorResponse'], 'errors' => [['shape' => 'NotFoundException'], ['shape' => 'BadRequestException'], ['shape' => 'ForbiddenException'], ['shape' => 'ServiceUnavailableException'], ['shape' => 'TooManyRequestsException'], ['shape' => 'UnauthorizedException'], ['shape' => 'InternalServerErrorException']], 'idempotent' => \true]], 'shapes' => ['ApacheKafkaCluster' => ['type' => 'structure', 'required' => ['bootstrapServers', 'vpc'], 'members' => ['bootstrapServers' => ['shape' => '__string'], 'vpc' => ['shape' => 'Vpc']]], 'ApacheKafkaClusterDescription' => ['type' => 'structure', 'members' => ['bootstrapServers' => ['shape' => '__string'], 'vpc' => ['shape' => 'VpcDescription']]], 'AutoScaling' => ['type' => 'structure', 'required' => ['maxWorkerCount', 'mcuCount', 'minWorkerCount'], 'members' => ['maxWorkerCount' => ['shape' => '__integerMin1Max10'], 'mcuCount' => ['shape' => '__integerMin1Max8'], 'minWorkerCount' => ['shape' => '__integerMin1Max10'], 'scaleInPolicy' => ['shape' => 'ScaleInPolicy'], 'scaleOutPolicy' => ['shape' => 'ScaleOutPolicy']]], 'AutoScalingDescription' => ['type' => 'structure', 'members' => ['maxWorkerCount' => ['shape' => '__integer'], 'mcuCount' => ['shape' => '__integer'], 'minWorkerCount' => ['shape' => '__integer'], 'scaleInPolicy' => ['shape' => 'ScaleInPolicyDescription'], 'scaleOutPolicy' => ['shape' => 'ScaleOutPolicyDescription']]], 'AutoScalingUpdate' => ['type' => 'structure', 'required' => ['maxWorkerCount', 'mcuCount', 'minWorkerCount', 'scaleInPolicy', 'scaleOutPolicy'], 'members' => ['maxWorkerCount' => ['shape' => '__integerMin1Max10'], 'mcuCount' => ['shape' => '__integerMin1Max8'], 'minWorkerCount' => ['shape' => '__integerMin1Max10'], 'scaleInPolicy' => ['shape' => 'ScaleInPolicyUpdate'], 'scaleOutPolicy' => ['shape' => 'ScaleOutPolicyUpdate']]], 'BadRequestException' => ['type' => 'structure', 'members' => ['message' => ['shape' => '__string']], 'error' => ['httpStatusCode' => 400, 'senderFault' => \true], 'exception' => \true], 'Capacity' => ['type' => 'structure', 'members' => ['autoScaling' => ['shape' => 'AutoScaling'], 'provisionedCapacity' => ['shape' => 'ProvisionedCapacity']]], 'CapacityDescription' => ['type' => 'structure', 'members' => ['autoScaling' => ['shape' => 'AutoScalingDescription'], 'provisionedCapacity' => ['shape' => 'ProvisionedCapacityDescription']]], 'CapacityUpdate' => ['type' => 'structure', 'members' => ['autoScaling' => ['shape' => 'AutoScalingUpdate'], 'provisionedCapacity' => ['shape' => 'ProvisionedCapacityUpdate']]], 'CloudWatchLogsLogDelivery' => ['type' => 'structure', 'required' => ['enabled'], 'members' => ['enabled' => ['shape' => '__boolean'], 'logGroup' => ['shape' => '__string']]], 'CloudWatchLogsLogDeliveryDescription' => ['type' => 'structure', 'members' => ['enabled' => ['shape' => '__boolean'], 'logGroup' => ['shape' => '__string']]], 'ConflictException' => ['type' => 'structure', 'members' => ['message' => ['shape' => '__string']], 'error' => ['httpStatusCode' => 409, 'senderFault' => \true], 'exception' => \true], 'ConnectorState' => ['type' => 'string', 'enum' => ['RUNNING', 'CREATING', 'UPDATING', 'DELETING', 'FAILED']], 'ConnectorSummary' => ['type' => 'structure', 'members' => ['capacity' => ['shape' => 'CapacityDescription'], 'connectorArn' => ['shape' => '__string'], 'connectorDescription' => ['shape' => '__string'], 'connectorName' => ['shape' => '__string'], 'connectorState' => ['shape' => 'ConnectorState'], 'creationTime' => ['shape' => '__timestampIso8601'], 'currentVersion' => ['shape' => '__string'], 'kafkaCluster' => ['shape' => 'KafkaClusterDescription'], 'kafkaClusterClientAuthentication' => ['shape' => 'KafkaClusterClientAuthenticationDescription'], 'kafkaClusterEncryptionInTransit' => ['shape' => 'KafkaClusterEncryptionInTransitDescription'], 'kafkaConnectVersion' => ['shape' => '__string'], 'logDelivery' => ['shape' => 'LogDeliveryDescription'], 'plugins' => ['shape' => '__listOfPluginDescription'], 'serviceExecutionRoleArn' => ['shape' => '__string'], 'workerConfiguration' => ['shape' => 'WorkerConfigurationDescription']]], 'CreateConnectorRequest' => ['type' => 'structure', 'required' => ['capacity', 'connectorConfiguration', 'connectorName', 'kafkaCluster', 'kafkaClusterClientAuthentication', 'kafkaClusterEncryptionInTransit', 'kafkaConnectVersion', 'plugins', 'serviceExecutionRoleArn'], 'members' => ['capacity' => ['shape' => 'Capacity'], 'connectorConfiguration' => ['shape' => 'SyntheticCreateConnectorRequest__mapOf__string'], 'connectorDescription' => ['shape' => '__stringMax1024'], 'connectorName' => ['shape' => '__stringMin1Max128'], 'kafkaCluster' => ['shape' => 'KafkaCluster'], 'kafkaClusterClientAuthentication' => ['shape' => 'KafkaClusterClientAuthentication'], 'kafkaClusterEncryptionInTransit' => ['shape' => 'KafkaClusterEncryptionInTransit'], 'kafkaConnectVersion' => ['shape' => '__string'], 'logDelivery' => ['shape' => 'LogDelivery'], 'plugins' => ['shape' => '__listOfPlugin'], 'serviceExecutionRoleArn' => ['shape' => '__string'], 'workerConfiguration' => ['shape' => 'WorkerConfiguration']]], 'CreateConnectorResponse' => ['type' => 'structure', 'members' => ['connectorArn' => ['shape' => '__string'], 'connectorName' => ['shape' => '__string'], 'connectorState' => ['shape' => 'ConnectorState']]], 'CreateCustomPluginRequest' => ['type' => 'structure', 'required' => ['contentType', 'location', 'name'], 'members' => ['contentType' => ['shape' => 'CustomPluginContentType'], 'description' => ['shape' => '__stringMax1024'], 'location' => ['shape' => 'CustomPluginLocation'], 'name' => ['shape' => '__stringMin1Max128']]], 'CreateCustomPluginResponse' => ['type' => 'structure', 'members' => ['customPluginArn' => ['shape' => '__string'], 'customPluginState' => ['shape' => 'CustomPluginState'], 'name' => ['shape' => '__string'], 'revision' => ['shape' => '__long']]], 'CreateWorkerConfigurationRequest' => ['type' => 'structure', 'required' => ['name', 'propertiesFileContent'], 'members' => ['description' => ['shape' => '__stringMax1024'], 'name' => ['shape' => '__stringMin1Max128'], 'propertiesFileContent' => ['shape' => 'SyntheticCreateWorkerConfigurationRequest__string']]], 'CreateWorkerConfigurationResponse' => ['type' => 'structure', 'members' => ['creationTime' => ['shape' => '__timestampIso8601'], 'latestRevision' => ['shape' => 'WorkerConfigurationRevisionSummary'], 'name' => ['shape' => '__string'], 'workerConfigurationArn' => ['shape' => '__string']]], 'CustomPlugin' => ['type' => 'structure', 'required' => ['customPluginArn', 'revision'], 'members' => ['customPluginArn' => ['shape' => '__string'], 'revision' => ['shape' => '__longMin1']]], 'CustomPluginContentType' => ['type' => 'string', 'enum' => ['JAR', 'ZIP']], 'CustomPluginDescription' => ['type' => 'structure', 'members' => ['customPluginArn' => ['shape' => '__string'], 'revision' => ['shape' => '__long']]], 'CustomPluginFileDescription' => ['type' => 'structure', 'members' => ['fileMd5' => ['shape' => '__string'], 'fileSize' => ['shape' => '__long']]], 'CustomPluginLocation' => ['type' => 'structure', 'required' => ['s3Location'], 'members' => ['s3Location' => ['shape' => 'S3Location']]], 'CustomPluginLocationDescription' => ['type' => 'structure', 'members' => ['s3Location' => ['shape' => 'S3LocationDescription']]], 'CustomPluginRevisionSummary' => ['type' => 'structure', 'members' => ['contentType' => ['shape' => 'CustomPluginContentType'], 'creationTime' => ['shape' => '__timestampIso8601'], 'description' => ['shape' => '__string'], 'fileDescription' => ['shape' => 'CustomPluginFileDescription'], 'location' => ['shape' => 'CustomPluginLocationDescription'], 'revision' => ['shape' => '__long']]], 'CustomPluginState' => ['type' => 'string', 'enum' => ['CREATING', 'CREATE_FAILED', 'ACTIVE', 'UPDATING', 'UPDATE_FAILED', 'DELETING']], 'CustomPluginSummary' => ['type' => 'structure', 'members' => ['creationTime' => ['shape' => '__timestampIso8601'], 'customPluginArn' => ['shape' => '__string'], 'customPluginState' => ['shape' => 'CustomPluginState'], 'description' => ['shape' => '__string'], 'latestRevision' => ['shape' => 'CustomPluginRevisionSummary'], 'name' => ['shape' => '__string']]], 'DeleteConnectorRequest' => ['type' => 'structure', 'required' => ['connectorArn'], 'members' => ['connectorArn' => ['shape' => '__string', 'location' => 'uri', 'locationName' => 'connectorArn'], 'currentVersion' => ['shape' => '__string', 'location' => 'querystring', 'locationName' => 'currentVersion']]], 'DeleteConnectorResponse' => ['type' => 'structure', 'members' => ['connectorArn' => ['shape' => '__string'], 'connectorState' => ['shape' => 'ConnectorState']]], 'DeleteCustomPluginRequest' => ['type' => 'structure', 'required' => ['customPluginArn'], 'members' => ['customPluginArn' => ['shape' => '__string', 'location' => 'uri', 'locationName' => 'customPluginArn']]], 'DeleteCustomPluginResponse' => ['type' => 'structure', 'members' => ['customPluginArn' => ['shape' => '__string'], 'customPluginState' => ['shape' => 'CustomPluginState']]], 'DescribeConnectorRequest' => ['type' => 'structure', 'required' => ['connectorArn'], 'members' => ['connectorArn' => ['shape' => '__string', 'location' => 'uri', 'locationName' => 'connectorArn']]], 'DescribeConnectorResponse' => ['type' => 'structure', 'members' => ['capacity' => ['shape' => 'CapacityDescription'], 'connectorArn' => ['shape' => '__string'], 'connectorConfiguration' => ['shape' => 'SyntheticDescribeConnectorResponse__mapOf__string'], 'connectorDescription' => ['shape' => '__string'], 'connectorName' => ['shape' => '__string'], 'connectorState' => ['shape' => 'ConnectorState'], 'creationTime' => ['shape' => '__timestampIso8601'], 'currentVersion' => ['shape' => '__string'], 'kafkaCluster' => ['shape' => 'KafkaClusterDescription'], 'kafkaClusterClientAuthentication' => ['shape' => 'KafkaClusterClientAuthenticationDescription'], 'kafkaClusterEncryptionInTransit' => ['shape' => 'KafkaClusterEncryptionInTransitDescription'], 'kafkaConnectVersion' => ['shape' => '__string'], 'logDelivery' => ['shape' => 'LogDeliveryDescription'], 'plugins' => ['shape' => '__listOfPluginDescription'], 'serviceExecutionRoleArn' => ['shape' => '__string'], 'stateDescription' => ['shape' => 'StateDescription'], 'workerConfiguration' => ['shape' => 'WorkerConfigurationDescription']]], 'DescribeCustomPluginRequest' => ['type' => 'structure', 'required' => ['customPluginArn'], 'members' => ['customPluginArn' => ['shape' => '__string', 'location' => 'uri', 'locationName' => 'customPluginArn']]], 'DescribeCustomPluginResponse' => ['type' => 'structure', 'members' => ['creationTime' => ['shape' => '__timestampIso8601'], 'customPluginArn' => ['shape' => '__string'], 'customPluginState' => ['shape' => 'CustomPluginState'], 'description' => ['shape' => '__string'], 'latestRevision' => ['shape' => 'CustomPluginRevisionSummary'], 'name' => ['shape' => '__string'], 'stateDescription' => ['shape' => 'StateDescription']]], 'DescribeWorkerConfigurationRequest' => ['type' => 'structure', 'required' => ['workerConfigurationArn'], 'members' => ['workerConfigurationArn' => ['shape' => '__string', 'location' => 'uri', 'locationName' => 'workerConfigurationArn']]], 'DescribeWorkerConfigurationResponse' => ['type' => 'structure', 'members' => ['creationTime' => ['shape' => '__timestampIso8601'], 'description' => ['shape' => '__string'], 'latestRevision' => ['shape' => 'WorkerConfigurationRevisionDescription'], 'name' => ['shape' => '__string'], 'workerConfigurationArn' => ['shape' => '__string']]], 'FirehoseLogDelivery' => ['type' => 'structure', 'required' => ['enabled'], 'members' => ['deliveryStream' => ['shape' => '__string'], 'enabled' => ['shape' => '__boolean']]], 'FirehoseLogDeliveryDescription' => ['type' => 'structure', 'members' => ['deliveryStream' => ['shape' => '__string'], 'enabled' => ['shape' => '__boolean']]], 'ForbiddenException' => ['type' => 'structure', 'members' => ['message' => ['shape' => '__string']], 'error' => ['httpStatusCode' => 403, 'senderFault' => \true], 'exception' => \true], 'InternalServerErrorException' => ['type' => 'structure', 'members' => ['message' => ['shape' => '__string']], 'error' => ['httpStatusCode' => 500], 'exception' => \true, 'fault' => \true], 'KafkaCluster' => ['type' => 'structure', 'required' => ['apacheKafkaCluster'], 'members' => ['apacheKafkaCluster' => ['shape' => 'ApacheKafkaCluster']]], 'KafkaClusterClientAuthentication' => ['type' => 'structure', 'required' => ['authenticationType'], 'members' => ['authenticationType' => ['shape' => 'KafkaClusterClientAuthenticationType']]], 'KafkaClusterClientAuthenticationDescription' => ['type' => 'structure', 'members' => ['authenticationType' => ['shape' => 'KafkaClusterClientAuthenticationType']]], 'KafkaClusterClientAuthenticationType' => ['type' => 'string', 'enum' => ['NONE', 'IAM']], 'KafkaClusterDescription' => ['type' => 'structure', 'members' => ['apacheKafkaCluster' => ['shape' => 'ApacheKafkaClusterDescription']]], 'KafkaClusterEncryptionInTransit' => ['type' => 'structure', 'required' => ['encryptionType'], 'members' => ['encryptionType' => ['shape' => 'KafkaClusterEncryptionInTransitType']]], 'KafkaClusterEncryptionInTransitDescription' => ['type' => 'structure', 'members' => ['encryptionType' => ['shape' => 'KafkaClusterEncryptionInTransitType']]], 'KafkaClusterEncryptionInTransitType' => ['type' => 'string', 'enum' => ['PLAINTEXT', 'TLS']], 'ListConnectorsRequest' => ['type' => 'structure', 'members' => ['connectorNamePrefix' => ['shape' => '__string', 'location' => 'querystring', 'locationName' => 'connectorNamePrefix'], 'maxResults' => ['shape' => 'MaxResults', 'location' => 'querystring', 'locationName' => 'maxResults'], 'nextToken' => ['shape' => '__string', 'location' => 'querystring', 'locationName' => 'nextToken']]], 'ListConnectorsResponse' => ['type' => 'structure', 'members' => ['connectors' => ['shape' => '__listOfConnectorSummary'], 'nextToken' => ['shape' => '__string']]], 'ListCustomPluginsRequest' => ['type' => 'structure', 'members' => ['maxResults' => ['shape' => 'MaxResults', 'location' => 'querystring', 'locationName' => 'maxResults'], 'nextToken' => ['shape' => '__string', 'location' => 'querystring', 'locationName' => 'nextToken']]], 'ListCustomPluginsResponse' => ['type' => 'structure', 'members' => ['customPlugins' => ['shape' => '__listOfCustomPluginSummary'], 'nextToken' => ['shape' => '__string']]], 'ListWorkerConfigurationsRequest' => ['type' => 'structure', 'members' => ['maxResults' => ['shape' => 'MaxResults', 'location' => 'querystring', 'locationName' => 'maxResults'], 'nextToken' => ['shape' => '__string', 'location' => 'querystring', 'locationName' => 'nextToken']]], 'ListWorkerConfigurationsResponse' => ['type' => 'structure', 'members' => ['nextToken' => ['shape' => '__string'], 'workerConfigurations' => ['shape' => '__listOfWorkerConfigurationSummary']]], 'LogDelivery' => ['type' => 'structure', 'required' => ['workerLogDelivery'], 'members' => ['workerLogDelivery' => ['shape' => 'WorkerLogDelivery']]], 'LogDeliveryDescription' => ['type' => 'structure', 'members' => ['workerLogDelivery' => ['shape' => 'WorkerLogDeliveryDescription']]], 'MaxResults' => ['type' => 'integer', 'max' => 100, 'min' => 1], 'NotFoundException' => ['type' => 'structure', 'members' => ['message' => ['shape' => '__string']], 'error' => ['httpStatusCode' => 404, 'senderFault' => \true], 'exception' => \true], 'Plugin' => ['type' => 'structure', 'required' => ['customPlugin'], 'members' => ['customPlugin' => ['shape' => 'CustomPlugin']]], 'PluginDescription' => ['type' => 'structure', 'members' => ['customPlugin' => ['shape' => 'CustomPluginDescription']]], 'ProvisionedCapacity' => ['type' => 'structure', 'required' => ['mcuCount', 'workerCount'], 'members' => ['mcuCount' => ['shape' => '__integerMin1Max8'], 'workerCount' => ['shape' => '__integerMin1Max10']]], 'ProvisionedCapacityDescription' => ['type' => 'structure', 'members' => ['mcuCount' => ['shape' => '__integer'], 'workerCount' => ['shape' => '__integer']]], 'ProvisionedCapacityUpdate' => ['type' => 'structure', 'required' => ['mcuCount', 'workerCount'], 'members' => ['mcuCount' => ['shape' => '__integerMin1Max8'], 'workerCount' => ['shape' => '__integerMin1Max10']]], 'S3Location' => ['type' => 'structure', 'required' => ['bucketArn', 'fileKey'], 'members' => ['bucketArn' => ['shape' => '__string'], 'fileKey' => ['shape' => '__string'], 'objectVersion' => ['shape' => '__string']]], 'S3LocationDescription' => ['type' => 'structure', 'members' => ['bucketArn' => ['shape' => '__string'], 'fileKey' => ['shape' => '__string'], 'objectVersion' => ['shape' => '__string']]], 'S3LogDelivery' => ['type' => 'structure', 'required' => ['enabled'], 'members' => ['bucket' => ['shape' => '__string'], 'enabled' => ['shape' => '__boolean'], 'prefix' => ['shape' => '__string']]], 'S3LogDeliveryDescription' => ['type' => 'structure', 'members' => ['bucket' => ['shape' => '__string'], 'enabled' => ['shape' => '__boolean'], 'prefix' => ['shape' => '__string']]], 'ScaleInPolicy' => ['type' => 'structure', 'required' => ['cpuUtilizationPercentage'], 'members' => ['cpuUtilizationPercentage' => ['shape' => '__integerMin1Max100']]], 'ScaleInPolicyDescription' => ['type' => 'structure', 'members' => ['cpuUtilizationPercentage' => ['shape' => '__integer']]], 'ScaleInPolicyUpdate' => ['type' => 'structure', 'required' => ['cpuUtilizationPercentage'], 'members' => ['cpuUtilizationPercentage' => ['shape' => '__integerMin1Max100']]], 'ScaleOutPolicy' => ['type' => 'structure', 'required' => ['cpuUtilizationPercentage'], 'members' => ['cpuUtilizationPercentage' => ['shape' => '__integerMin1Max100']]], 'ScaleOutPolicyDescription' => ['type' => 'structure', 'members' => ['cpuUtilizationPercentage' => ['shape' => '__integer']]], 'ScaleOutPolicyUpdate' => ['type' => 'structure', 'required' => ['cpuUtilizationPercentage'], 'members' => ['cpuUtilizationPercentage' => ['shape' => '__integerMin1Max100']]], 'ServiceUnavailableException' => ['type' => 'structure', 'members' => ['message' => ['shape' => '__string']], 'error' => ['httpStatusCode' => 503], 'exception' => \true, 'fault' => \true], 'StateDescription' => ['type' => 'structure', 'members' => ['code' => ['shape' => '__string'], 'message' => ['shape' => '__string']]], 'SyntheticCreateConnectorRequest__mapOf__string' => ['type' => 'map', 'key' => ['shape' => '__string'], 'value' => ['shape' => '__string'], 'sensitive' => \true], 'SyntheticCreateWorkerConfigurationRequest__string' => ['type' => 'string', 'sensitive' => \true], 'SyntheticDescribeConnectorResponse__mapOf__string' => ['type' => 'map', 'key' => ['shape' => '__string'], 'value' => ['shape' => '__string'], 'sensitive' => \true], 'SyntheticWorkerConfigurationRevisionDescription__string' => ['type' => 'string', 'sensitive' => \true], 'TooManyRequestsException' => ['type' => 'structure', 'members' => ['message' => ['shape' => '__string']], 'error' => ['httpStatusCode' => 429, 'senderFault' => \true], 'exception' => \true], 'UnauthorizedException' => ['type' => 'structure', 'members' => ['message' => ['shape' => '__string']], 'error' => ['httpStatusCode' => 401, 'senderFault' => \true], 'exception' => \true], 'UpdateConnectorRequest' => ['type' => 'structure', 'required' => ['capacity', 'connectorArn', 'currentVersion'], 'members' => ['capacity' => ['shape' => 'CapacityUpdate'], 'connectorArn' => ['shape' => '__string', 'location' => 'uri', 'locationName' => 'connectorArn'], 'currentVersion' => ['shape' => '__string', 'location' => 'querystring', 'locationName' => 'currentVersion']]], 'UpdateConnectorResponse' => ['type' => 'structure', 'members' => ['connectorArn' => ['shape' => '__string'], 'connectorState' => ['shape' => 'ConnectorState']]], 'Vpc' => ['type' => 'structure', 'required' => ['subnets'], 'members' => ['securityGroups' => ['shape' => '__listOf__string'], 'subnets' => ['shape' => '__listOf__string']]], 'VpcDescription' => ['type' => 'structure', 'members' => ['securityGroups' => ['shape' => '__listOf__string'], 'subnets' => ['shape' => '__listOf__string']]], 'WorkerConfiguration' => ['type' => 'structure', 'required' => ['revision', 'workerConfigurationArn'], 'members' => ['revision' => ['shape' => '__longMin1'], 'workerConfigurationArn' => ['shape' => '__string']]], 'WorkerConfigurationDescription' => ['type' => 'structure', 'members' => ['revision' => ['shape' => '__long'], 'workerConfigurationArn' => ['shape' => '__string']]], 'WorkerConfigurationRevisionDescription' => ['type' => 'structure', 'members' => ['creationTime' => ['shape' => '__timestampIso8601'], 'description' => ['shape' => '__string'], 'propertiesFileContent' => ['shape' => 'SyntheticWorkerConfigurationRevisionDescription__string'], 'revision' => ['shape' => '__long']]], 'WorkerConfigurationRevisionSummary' => ['type' => 'structure', 'members' => ['creationTime' => ['shape' => '__timestampIso8601'], 'description' => ['shape' => '__string'], 'revision' => ['shape' => '__long']]], 'WorkerConfigurationSummary' => ['type' => 'structure', 'members' => ['creationTime' => ['shape' => '__timestampIso8601'], 'description' => ['shape' => '__string'], 'latestRevision' => ['shape' => 'WorkerConfigurationRevisionSummary'], 'name' => ['shape' => '__string'], 'workerConfigurationArn' => ['shape' => '__string']]], 'WorkerLogDelivery' => ['type' => 'structure', 'members' => ['cloudWatchLogs' => ['shape' => 'CloudWatchLogsLogDelivery'], 'firehose' => ['shape' => 'FirehoseLogDelivery'], 's3' => ['shape' => 'S3LogDelivery']]], 'WorkerLogDeliveryDescription' => ['type' => 'structure', 'members' => ['cloudWatchLogs' => ['shape' => 'CloudWatchLogsLogDeliveryDescription'], 'firehose' => ['shape' => 'FirehoseLogDeliveryDescription'], 's3' => ['shape' => 'S3LogDeliveryDescription']]], '__boolean' => ['type' => 'boolean'], '__integer' => ['type' => 'integer'], '__integerMin1Max10' => ['type' => 'integer', 'max' => 10, 'min' => 1], '__integerMin1Max100' => ['type' => 'integer', 'max' => 100, 'min' => 1], '__integerMin1Max8' => ['type' => 'integer', 'max' => 8, 'min' => 1], '__listOfConnectorSummary' => ['type' => 'list', 'member' => ['shape' => 'ConnectorSummary']], '__listOfCustomPluginSummary' => ['type' => 'list', 'member' => ['shape' => 'CustomPluginSummary']], '__listOfPlugin' => ['type' => 'list', 'member' => ['shape' => 'Plugin']], '__listOfPluginDescription' => ['type' => 'list', 'member' => ['shape' => 'PluginDescription']], '__listOfWorkerConfigurationSummary' => ['type' => 'list', 'member' => ['shape' => 'WorkerConfigurationSummary']], '__listOf__string' => ['type' => 'list', 'member' => ['shape' => '__string']], '__long' => ['type' => 'long'], '__longMin1' => ['type' => 'long', 'max' => 9223372036854775807, 'min' => 1], '__string' => ['type' => 'string'], '__stringMax1024' => ['type' => 'string', 'max' => 1024, 'min' => 0], '__stringMin1Max128' => ['type' => 'string', 'max' => 128, 'min' => 1], '__timestampIso8601' => ['type' => 'timestamp', 'timestampFormat' => 'iso8601']]];
