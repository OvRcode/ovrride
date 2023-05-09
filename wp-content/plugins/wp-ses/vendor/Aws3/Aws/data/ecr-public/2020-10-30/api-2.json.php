<?php

namespace DeliciousBrains\WP_Offload_SES\Aws3;

// This file was auto-generated from sdk-root/src/data/ecr-public/2020-10-30/api-2.json
return ['version' => '2.0', 'metadata' => ['apiVersion' => '2020-10-30', 'endpointPrefix' => 'api.ecr-public', 'jsonVersion' => '1.1', 'protocol' => 'json', 'serviceAbbreviation' => 'Amazon ECR Public', 'serviceFullName' => 'Amazon Elastic Container Registry Public', 'serviceId' => 'ECR PUBLIC', 'signatureVersion' => 'v4', 'signingName' => 'ecr-public', 'targetPrefix' => 'SpencerFrontendService', 'uid' => 'ecr-public-2020-10-30'], 'operations' => ['BatchCheckLayerAvailability' => ['name' => 'BatchCheckLayerAvailability', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'BatchCheckLayerAvailabilityRequest'], 'output' => ['shape' => 'BatchCheckLayerAvailabilityResponse'], 'errors' => [['shape' => 'RepositoryNotFoundException'], ['shape' => 'InvalidParameterException'], ['shape' => 'ServerException'], ['shape' => 'RegistryNotFoundException'], ['shape' => 'UnsupportedCommandException']]], 'BatchDeleteImage' => ['name' => 'BatchDeleteImage', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'BatchDeleteImageRequest'], 'output' => ['shape' => 'BatchDeleteImageResponse'], 'errors' => [['shape' => 'ServerException'], ['shape' => 'InvalidParameterException'], ['shape' => 'RepositoryNotFoundException'], ['shape' => 'UnsupportedCommandException']]], 'CompleteLayerUpload' => ['name' => 'CompleteLayerUpload', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'CompleteLayerUploadRequest'], 'output' => ['shape' => 'CompleteLayerUploadResponse'], 'errors' => [['shape' => 'ServerException'], ['shape' => 'InvalidParameterException'], ['shape' => 'RepositoryNotFoundException'], ['shape' => 'UploadNotFoundException'], ['shape' => 'InvalidLayerException'], ['shape' => 'LayerPartTooSmallException'], ['shape' => 'LayerAlreadyExistsException'], ['shape' => 'EmptyUploadException'], ['shape' => 'RegistryNotFoundException'], ['shape' => 'UnsupportedCommandException']]], 'CreateRepository' => ['name' => 'CreateRepository', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'CreateRepositoryRequest'], 'output' => ['shape' => 'CreateRepositoryResponse'], 'errors' => [['shape' => 'ServerException'], ['shape' => 'InvalidParameterException'], ['shape' => 'InvalidTagParameterException'], ['shape' => 'TooManyTagsException'], ['shape' => 'RepositoryAlreadyExistsException'], ['shape' => 'LimitExceededException'], ['shape' => 'UnsupportedCommandException']]], 'DeleteRepository' => ['name' => 'DeleteRepository', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'DeleteRepositoryRequest'], 'output' => ['shape' => 'DeleteRepositoryResponse'], 'errors' => [['shape' => 'ServerException'], ['shape' => 'InvalidParameterException'], ['shape' => 'RepositoryNotFoundException'], ['shape' => 'RepositoryNotEmptyException'], ['shape' => 'UnsupportedCommandException']]], 'DeleteRepositoryPolicy' => ['name' => 'DeleteRepositoryPolicy', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'DeleteRepositoryPolicyRequest'], 'output' => ['shape' => 'DeleteRepositoryPolicyResponse'], 'errors' => [['shape' => 'ServerException'], ['shape' => 'InvalidParameterException'], ['shape' => 'RepositoryNotFoundException'], ['shape' => 'RepositoryPolicyNotFoundException'], ['shape' => 'UnsupportedCommandException']]], 'DescribeImageTags' => ['name' => 'DescribeImageTags', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'DescribeImageTagsRequest'], 'output' => ['shape' => 'DescribeImageTagsResponse'], 'errors' => [['shape' => 'ServerException'], ['shape' => 'InvalidParameterException'], ['shape' => 'RepositoryNotFoundException'], ['shape' => 'UnsupportedCommandException']]], 'DescribeImages' => ['name' => 'DescribeImages', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'DescribeImagesRequest'], 'output' => ['shape' => 'DescribeImagesResponse'], 'errors' => [['shape' => 'ServerException'], ['shape' => 'InvalidParameterException'], ['shape' => 'RepositoryNotFoundException'], ['shape' => 'ImageNotFoundException'], ['shape' => 'UnsupportedCommandException']]], 'DescribeRegistries' => ['name' => 'DescribeRegistries', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'DescribeRegistriesRequest'], 'output' => ['shape' => 'DescribeRegistriesResponse'], 'errors' => [['shape' => 'InvalidParameterException'], ['shape' => 'UnsupportedCommandException'], ['shape' => 'ServerException']]], 'DescribeRepositories' => ['name' => 'DescribeRepositories', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'DescribeRepositoriesRequest'], 'output' => ['shape' => 'DescribeRepositoriesResponse'], 'errors' => [['shape' => 'ServerException'], ['shape' => 'InvalidParameterException'], ['shape' => 'RepositoryNotFoundException'], ['shape' => 'UnsupportedCommandException']]], 'GetAuthorizationToken' => ['name' => 'GetAuthorizationToken', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'GetAuthorizationTokenRequest'], 'output' => ['shape' => 'GetAuthorizationTokenResponse'], 'errors' => [['shape' => 'ServerException'], ['shape' => 'InvalidParameterException'], ['shape' => 'UnsupportedCommandException']]], 'GetRegistryCatalogData' => ['name' => 'GetRegistryCatalogData', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'GetRegistryCatalogDataRequest'], 'output' => ['shape' => 'GetRegistryCatalogDataResponse'], 'errors' => [['shape' => 'ServerException'], ['shape' => 'UnsupportedCommandException']]], 'GetRepositoryCatalogData' => ['name' => 'GetRepositoryCatalogData', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'GetRepositoryCatalogDataRequest'], 'output' => ['shape' => 'GetRepositoryCatalogDataResponse'], 'errors' => [['shape' => 'ServerException'], ['shape' => 'InvalidParameterException'], ['shape' => 'RepositoryCatalogDataNotFoundException'], ['shape' => 'RepositoryNotFoundException'], ['shape' => 'UnsupportedCommandException']]], 'GetRepositoryPolicy' => ['name' => 'GetRepositoryPolicy', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'GetRepositoryPolicyRequest'], 'output' => ['shape' => 'GetRepositoryPolicyResponse'], 'errors' => [['shape' => 'ServerException'], ['shape' => 'InvalidParameterException'], ['shape' => 'RepositoryNotFoundException'], ['shape' => 'RepositoryPolicyNotFoundException'], ['shape' => 'UnsupportedCommandException']]], 'InitiateLayerUpload' => ['name' => 'InitiateLayerUpload', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'InitiateLayerUploadRequest'], 'output' => ['shape' => 'InitiateLayerUploadResponse'], 'errors' => [['shape' => 'ServerException'], ['shape' => 'InvalidParameterException'], ['shape' => 'RepositoryNotFoundException'], ['shape' => 'RegistryNotFoundException'], ['shape' => 'UnsupportedCommandException']]], 'ListTagsForResource' => ['name' => 'ListTagsForResource', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'ListTagsForResourceRequest'], 'output' => ['shape' => 'ListTagsForResourceResponse'], 'errors' => [['shape' => 'InvalidParameterException'], ['shape' => 'RepositoryNotFoundException'], ['shape' => 'UnsupportedCommandException'], ['shape' => 'ServerException']]], 'PutImage' => ['name' => 'PutImage', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'PutImageRequest'], 'output' => ['shape' => 'PutImageResponse'], 'errors' => [['shape' => 'ServerException'], ['shape' => 'InvalidParameterException'], ['shape' => 'RepositoryNotFoundException'], ['shape' => 'ImageAlreadyExistsException'], ['shape' => 'LayersNotFoundException'], ['shape' => 'ReferencedImagesNotFoundException'], ['shape' => 'LimitExceededException'], ['shape' => 'ImageTagAlreadyExistsException'], ['shape' => 'ImageDigestDoesNotMatchException'], ['shape' => 'RegistryNotFoundException'], ['shape' => 'UnsupportedCommandException']]], 'PutRegistryCatalogData' => ['name' => 'PutRegistryCatalogData', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'PutRegistryCatalogDataRequest'], 'output' => ['shape' => 'PutRegistryCatalogDataResponse'], 'errors' => [['shape' => 'ServerException'], ['shape' => 'InvalidParameterException'], ['shape' => 'UnsupportedCommandException']]], 'PutRepositoryCatalogData' => ['name' => 'PutRepositoryCatalogData', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'PutRepositoryCatalogDataRequest'], 'output' => ['shape' => 'PutRepositoryCatalogDataResponse'], 'errors' => [['shape' => 'ServerException'], ['shape' => 'InvalidParameterException'], ['shape' => 'RepositoryNotFoundException'], ['shape' => 'UnsupportedCommandException']]], 'SetRepositoryPolicy' => ['name' => 'SetRepositoryPolicy', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'SetRepositoryPolicyRequest'], 'output' => ['shape' => 'SetRepositoryPolicyResponse'], 'errors' => [['shape' => 'ServerException'], ['shape' => 'InvalidParameterException'], ['shape' => 'RepositoryNotFoundException'], ['shape' => 'UnsupportedCommandException']]], 'TagResource' => ['name' => 'TagResource', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'TagResourceRequest'], 'output' => ['shape' => 'TagResourceResponse'], 'errors' => [['shape' => 'InvalidParameterException'], ['shape' => 'InvalidTagParameterException'], ['shape' => 'TooManyTagsException'], ['shape' => 'RepositoryNotFoundException'], ['shape' => 'UnsupportedCommandException'], ['shape' => 'ServerException']]], 'UntagResource' => ['name' => 'UntagResource', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'UntagResourceRequest'], 'output' => ['shape' => 'UntagResourceResponse'], 'errors' => [['shape' => 'InvalidParameterException'], ['shape' => 'InvalidTagParameterException'], ['shape' => 'TooManyTagsException'], ['shape' => 'RepositoryNotFoundException'], ['shape' => 'UnsupportedCommandException'], ['shape' => 'ServerException']]], 'UploadLayerPart' => ['name' => 'UploadLayerPart', 'http' => ['method' => 'POST', 'requestUri' => '/'], 'input' => ['shape' => 'UploadLayerPartRequest'], 'output' => ['shape' => 'UploadLayerPartResponse'], 'errors' => [['shape' => 'ServerException'], ['shape' => 'InvalidParameterException'], ['shape' => 'InvalidLayerPartException'], ['shape' => 'RepositoryNotFoundException'], ['shape' => 'UploadNotFoundException'], ['shape' => 'LimitExceededException'], ['shape' => 'RegistryNotFoundException'], ['shape' => 'UnsupportedCommandException']]]], 'shapes' => ['AboutText' => ['type' => 'string', 'max' => 25600], 'Architecture' => ['type' => 'string', 'max' => 50, 'min' => 1], 'ArchitectureList' => ['type' => 'list', 'member' => ['shape' => 'Architecture'], 'max' => 50], 'Arn' => ['type' => 'string', 'max' => 2048, 'min' => 1], 'AuthorizationData' => ['type' => 'structure', 'members' => ['authorizationToken' => ['shape' => 'Base64'], 'expiresAt' => ['shape' => 'ExpirationTimestamp']]], 'Base64' => ['type' => 'string', 'pattern' => '^\\S+$'], 'BatchCheckLayerAvailabilityRequest' => ['type' => 'structure', 'required' => ['repositoryName', 'layerDigests'], 'members' => ['registryId' => ['shape' => 'RegistryIdOrAlias'], 'repositoryName' => ['shape' => 'RepositoryName'], 'layerDigests' => ['shape' => 'BatchedOperationLayerDigestList']]], 'BatchCheckLayerAvailabilityResponse' => ['type' => 'structure', 'members' => ['layers' => ['shape' => 'LayerList'], 'failures' => ['shape' => 'LayerFailureList']]], 'BatchDeleteImageRequest' => ['type' => 'structure', 'required' => ['repositoryName', 'imageIds'], 'members' => ['registryId' => ['shape' => 'RegistryId'], 'repositoryName' => ['shape' => 'RepositoryName'], 'imageIds' => ['shape' => 'ImageIdentifierList']]], 'BatchDeleteImageResponse' => ['type' => 'structure', 'members' => ['imageIds' => ['shape' => 'ImageIdentifierList'], 'failures' => ['shape' => 'ImageFailureList']]], 'BatchedOperationLayerDigest' => ['type' => 'string', 'max' => 1000, 'min' => 0], 'BatchedOperationLayerDigestList' => ['type' => 'list', 'member' => ['shape' => 'BatchedOperationLayerDigest'], 'max' => 100, 'min' => 1], 'CompleteLayerUploadRequest' => ['type' => 'structure', 'required' => ['repositoryName', 'uploadId', 'layerDigests'], 'members' => ['registryId' => ['shape' => 'RegistryIdOrAlias'], 'repositoryName' => ['shape' => 'RepositoryName'], 'uploadId' => ['shape' => 'UploadId'], 'layerDigests' => ['shape' => 'LayerDigestList']]], 'CompleteLayerUploadResponse' => ['type' => 'structure', 'members' => ['registryId' => ['shape' => 'RegistryId'], 'repositoryName' => ['shape' => 'RepositoryName'], 'uploadId' => ['shape' => 'UploadId'], 'layerDigest' => ['shape' => 'LayerDigest']]], 'CreateRepositoryRequest' => ['type' => 'structure', 'required' => ['repositoryName'], 'members' => ['repositoryName' => ['shape' => 'RepositoryName'], 'catalogData' => ['shape' => 'RepositoryCatalogDataInput'], 'tags' => ['shape' => 'TagList']]], 'CreateRepositoryResponse' => ['type' => 'structure', 'members' => ['repository' => ['shape' => 'Repository'], 'catalogData' => ['shape' => 'RepositoryCatalogData']]], 'CreationTimestamp' => ['type' => 'timestamp'], 'DefaultRegistryAliasFlag' => ['type' => 'boolean'], 'DeleteRepositoryPolicyRequest' => ['type' => 'structure', 'required' => ['repositoryName'], 'members' => ['registryId' => ['shape' => 'RegistryId'], 'repositoryName' => ['shape' => 'RepositoryName']]], 'DeleteRepositoryPolicyResponse' => ['type' => 'structure', 'members' => ['registryId' => ['shape' => 'RegistryId'], 'repositoryName' => ['shape' => 'RepositoryName'], 'policyText' => ['shape' => 'RepositoryPolicyText']]], 'DeleteRepositoryRequest' => ['type' => 'structure', 'required' => ['repositoryName'], 'members' => ['registryId' => ['shape' => 'RegistryId'], 'repositoryName' => ['shape' => 'RepositoryName'], 'force' => ['shape' => 'ForceFlag']]], 'DeleteRepositoryResponse' => ['type' => 'structure', 'members' => ['repository' => ['shape' => 'Repository']]], 'DescribeImageTagsRequest' => ['type' => 'structure', 'required' => ['repositoryName'], 'members' => ['registryId' => ['shape' => 'RegistryId'], 'repositoryName' => ['shape' => 'RepositoryName'], 'nextToken' => ['shape' => 'NextToken'], 'maxResults' => ['shape' => 'MaxResults']]], 'DescribeImageTagsResponse' => ['type' => 'structure', 'members' => ['imageTagDetails' => ['shape' => 'ImageTagDetailList'], 'nextToken' => ['shape' => 'NextToken']]], 'DescribeImagesRequest' => ['type' => 'structure', 'required' => ['repositoryName'], 'members' => ['registryId' => ['shape' => 'RegistryId'], 'repositoryName' => ['shape' => 'RepositoryName'], 'imageIds' => ['shape' => 'ImageIdentifierList'], 'nextToken' => ['shape' => 'NextToken'], 'maxResults' => ['shape' => 'MaxResults']]], 'DescribeImagesResponse' => ['type' => 'structure', 'members' => ['imageDetails' => ['shape' => 'ImageDetailList'], 'nextToken' => ['shape' => 'NextToken']]], 'DescribeRegistriesRequest' => ['type' => 'structure', 'members' => ['nextToken' => ['shape' => 'NextToken'], 'maxResults' => ['shape' => 'MaxResults']]], 'DescribeRegistriesResponse' => ['type' => 'structure', 'required' => ['registries'], 'members' => ['registries' => ['shape' => 'RegistryList'], 'nextToken' => ['shape' => 'NextToken']]], 'DescribeRepositoriesRequest' => ['type' => 'structure', 'members' => ['registryId' => ['shape' => 'RegistryId'], 'repositoryNames' => ['shape' => 'RepositoryNameList'], 'nextToken' => ['shape' => 'NextToken'], 'maxResults' => ['shape' => 'MaxResults']]], 'DescribeRepositoriesResponse' => ['type' => 'structure', 'members' => ['repositories' => ['shape' => 'RepositoryList'], 'nextToken' => ['shape' => 'NextToken']]], 'EmptyUploadException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'ExceptionMessage']], 'exception' => \true], 'ExceptionMessage' => ['type' => 'string'], 'ExpirationTimestamp' => ['type' => 'timestamp'], 'ForceFlag' => ['type' => 'boolean'], 'GetAuthorizationTokenRequest' => ['type' => 'structure', 'members' => []], 'GetAuthorizationTokenResponse' => ['type' => 'structure', 'members' => ['authorizationData' => ['shape' => 'AuthorizationData']]], 'GetRegistryCatalogDataRequest' => ['type' => 'structure', 'members' => []], 'GetRegistryCatalogDataResponse' => ['type' => 'structure', 'required' => ['registryCatalogData'], 'members' => ['registryCatalogData' => ['shape' => 'RegistryCatalogData']]], 'GetRepositoryCatalogDataRequest' => ['type' => 'structure', 'required' => ['repositoryName'], 'members' => ['registryId' => ['shape' => 'RegistryId'], 'repositoryName' => ['shape' => 'RepositoryName']]], 'GetRepositoryCatalogDataResponse' => ['type' => 'structure', 'members' => ['catalogData' => ['shape' => 'RepositoryCatalogData']]], 'GetRepositoryPolicyRequest' => ['type' => 'structure', 'required' => ['repositoryName'], 'members' => ['registryId' => ['shape' => 'RegistryId'], 'repositoryName' => ['shape' => 'RepositoryName']]], 'GetRepositoryPolicyResponse' => ['type' => 'structure', 'members' => ['registryId' => ['shape' => 'RegistryId'], 'repositoryName' => ['shape' => 'RepositoryName'], 'policyText' => ['shape' => 'RepositoryPolicyText']]], 'Image' => ['type' => 'structure', 'members' => ['registryId' => ['shape' => 'RegistryIdOrAlias'], 'repositoryName' => ['shape' => 'RepositoryName'], 'imageId' => ['shape' => 'ImageIdentifier'], 'imageManifest' => ['shape' => 'ImageManifest'], 'imageManifestMediaType' => ['shape' => 'MediaType']]], 'ImageAlreadyExistsException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'ExceptionMessage']], 'exception' => \true], 'ImageDetail' => ['type' => 'structure', 'members' => ['registryId' => ['shape' => 'RegistryId'], 'repositoryName' => ['shape' => 'RepositoryName'], 'imageDigest' => ['shape' => 'ImageDigest'], 'imageTags' => ['shape' => 'ImageTagList'], 'imageSizeInBytes' => ['shape' => 'ImageSizeInBytes'], 'imagePushedAt' => ['shape' => 'PushTimestamp'], 'imageManifestMediaType' => ['shape' => 'MediaType'], 'artifactMediaType' => ['shape' => 'MediaType']]], 'ImageDetailList' => ['type' => 'list', 'member' => ['shape' => 'ImageDetail']], 'ImageDigest' => ['type' => 'string'], 'ImageDigestDoesNotMatchException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'ExceptionMessage']], 'exception' => \true], 'ImageFailure' => ['type' => 'structure', 'members' => ['imageId' => ['shape' => 'ImageIdentifier'], 'failureCode' => ['shape' => 'ImageFailureCode'], 'failureReason' => ['shape' => 'ImageFailureReason']]], 'ImageFailureCode' => ['type' => 'string', 'enum' => ['InvalidImageDigest', 'InvalidImageTag', 'ImageTagDoesNotMatchDigest', 'ImageNotFound', 'MissingDigestAndTag', 'ImageReferencedByManifestList', 'KmsError']], 'ImageFailureList' => ['type' => 'list', 'member' => ['shape' => 'ImageFailure']], 'ImageFailureReason' => ['type' => 'string'], 'ImageIdentifier' => ['type' => 'structure', 'members' => ['imageDigest' => ['shape' => 'ImageDigest'], 'imageTag' => ['shape' => 'ImageTag']]], 'ImageIdentifierList' => ['type' => 'list', 'member' => ['shape' => 'ImageIdentifier'], 'max' => 100, 'min' => 1], 'ImageManifest' => ['type' => 'string', 'max' => 4194304, 'min' => 1], 'ImageNotFoundException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'ExceptionMessage']], 'exception' => \true], 'ImageSizeInBytes' => ['type' => 'long'], 'ImageTag' => ['type' => 'string', 'max' => 300, 'min' => 1], 'ImageTagAlreadyExistsException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'ExceptionMessage']], 'exception' => \true], 'ImageTagDetail' => ['type' => 'structure', 'members' => ['imageTag' => ['shape' => 'ImageTag'], 'createdAt' => ['shape' => 'CreationTimestamp'], 'imageDetail' => ['shape' => 'ReferencedImageDetail']]], 'ImageTagDetailList' => ['type' => 'list', 'member' => ['shape' => 'ImageTagDetail']], 'ImageTagList' => ['type' => 'list', 'member' => ['shape' => 'ImageTag']], 'InitiateLayerUploadRequest' => ['type' => 'structure', 'required' => ['repositoryName'], 'members' => ['registryId' => ['shape' => 'RegistryIdOrAlias'], 'repositoryName' => ['shape' => 'RepositoryName']]], 'InitiateLayerUploadResponse' => ['type' => 'structure', 'members' => ['uploadId' => ['shape' => 'UploadId'], 'partSize' => ['shape' => 'PartSize']]], 'InvalidLayerException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'ExceptionMessage']], 'exception' => \true], 'InvalidLayerPartException' => ['type' => 'structure', 'members' => ['registryId' => ['shape' => 'RegistryId'], 'repositoryName' => ['shape' => 'RepositoryName'], 'uploadId' => ['shape' => 'UploadId'], 'lastValidByteReceived' => ['shape' => 'PartSize'], 'message' => ['shape' => 'ExceptionMessage']], 'exception' => \true], 'InvalidParameterException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'ExceptionMessage']], 'exception' => \true], 'InvalidTagParameterException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'ExceptionMessage']], 'exception' => \true], 'Layer' => ['type' => 'structure', 'members' => ['layerDigest' => ['shape' => 'LayerDigest'], 'layerAvailability' => ['shape' => 'LayerAvailability'], 'layerSize' => ['shape' => 'LayerSizeInBytes'], 'mediaType' => ['shape' => 'MediaType']]], 'LayerAlreadyExistsException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'ExceptionMessage']], 'exception' => \true], 'LayerAvailability' => ['type' => 'string', 'enum' => ['AVAILABLE', 'UNAVAILABLE']], 'LayerDigest' => ['type' => 'string', 'pattern' => '[a-zA-Z0-9-_+.]+:[a-fA-F0-9]+'], 'LayerDigestList' => ['type' => 'list', 'member' => ['shape' => 'LayerDigest'], 'max' => 100, 'min' => 1], 'LayerFailure' => ['type' => 'structure', 'members' => ['layerDigest' => ['shape' => 'BatchedOperationLayerDigest'], 'failureCode' => ['shape' => 'LayerFailureCode'], 'failureReason' => ['shape' => 'LayerFailureReason']]], 'LayerFailureCode' => ['type' => 'string', 'enum' => ['InvalidLayerDigest', 'MissingLayerDigest']], 'LayerFailureList' => ['type' => 'list', 'member' => ['shape' => 'LayerFailure']], 'LayerFailureReason' => ['type' => 'string'], 'LayerList' => ['type' => 'list', 'member' => ['shape' => 'Layer']], 'LayerPartBlob' => ['type' => 'blob', 'max' => 20971520, 'min' => 0], 'LayerPartTooSmallException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'ExceptionMessage']], 'exception' => \true], 'LayerSizeInBytes' => ['type' => 'long'], 'LayersNotFoundException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'ExceptionMessage']], 'exception' => \true], 'LimitExceededException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'ExceptionMessage']], 'exception' => \true], 'ListTagsForResourceRequest' => ['type' => 'structure', 'required' => ['resourceArn'], 'members' => ['resourceArn' => ['shape' => 'Arn']]], 'ListTagsForResourceResponse' => ['type' => 'structure', 'members' => ['tags' => ['shape' => 'TagList']]], 'LogoImageBlob' => ['type' => 'blob', 'max' => 512000, 'min' => 0], 'MarketplaceCertified' => ['type' => 'boolean'], 'MaxResults' => ['type' => 'integer', 'max' => 1000, 'min' => 1], 'MediaType' => ['type' => 'string'], 'NextToken' => ['type' => 'string'], 'OperatingSystem' => ['type' => 'string', 'max' => 50, 'min' => 1], 'OperatingSystemList' => ['type' => 'list', 'member' => ['shape' => 'OperatingSystem'], 'max' => 50], 'PartSize' => ['type' => 'long', 'min' => 0], 'PrimaryRegistryAliasFlag' => ['type' => 'boolean'], 'PushTimestamp' => ['type' => 'timestamp'], 'PutImageRequest' => ['type' => 'structure', 'required' => ['repositoryName', 'imageManifest'], 'members' => ['registryId' => ['shape' => 'RegistryIdOrAlias'], 'repositoryName' => ['shape' => 'RepositoryName'], 'imageManifest' => ['shape' => 'ImageManifest'], 'imageManifestMediaType' => ['shape' => 'MediaType'], 'imageTag' => ['shape' => 'ImageTag'], 'imageDigest' => ['shape' => 'ImageDigest']]], 'PutImageResponse' => ['type' => 'structure', 'members' => ['image' => ['shape' => 'Image']]], 'PutRegistryCatalogDataRequest' => ['type' => 'structure', 'members' => ['displayName' => ['shape' => 'RegistryDisplayName']]], 'PutRegistryCatalogDataResponse' => ['type' => 'structure', 'required' => ['registryCatalogData'], 'members' => ['registryCatalogData' => ['shape' => 'RegistryCatalogData']]], 'PutRepositoryCatalogDataRequest' => ['type' => 'structure', 'required' => ['repositoryName', 'catalogData'], 'members' => ['registryId' => ['shape' => 'RegistryId'], 'repositoryName' => ['shape' => 'RepositoryName'], 'catalogData' => ['shape' => 'RepositoryCatalogDataInput']]], 'PutRepositoryCatalogDataResponse' => ['type' => 'structure', 'members' => ['catalogData' => ['shape' => 'RepositoryCatalogData']]], 'ReferencedImageDetail' => ['type' => 'structure', 'members' => ['imageDigest' => ['shape' => 'ImageDigest'], 'imageSizeInBytes' => ['shape' => 'ImageSizeInBytes'], 'imagePushedAt' => ['shape' => 'PushTimestamp'], 'imageManifestMediaType' => ['shape' => 'MediaType'], 'artifactMediaType' => ['shape' => 'MediaType']]], 'ReferencedImagesNotFoundException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'ExceptionMessage']], 'exception' => \true], 'Registry' => ['type' => 'structure', 'required' => ['registryId', 'registryArn', 'registryUri', 'verified', 'aliases'], 'members' => ['registryId' => ['shape' => 'RegistryId'], 'registryArn' => ['shape' => 'Arn'], 'registryUri' => ['shape' => 'Url'], 'verified' => ['shape' => 'RegistryVerified'], 'aliases' => ['shape' => 'RegistryAliasList']]], 'RegistryAlias' => ['type' => 'structure', 'required' => ['name', 'status', 'primaryRegistryAlias', 'defaultRegistryAlias'], 'members' => ['name' => ['shape' => 'RegistryAliasName'], 'status' => ['shape' => 'RegistryAliasStatus'], 'primaryRegistryAlias' => ['shape' => 'PrimaryRegistryAliasFlag'], 'defaultRegistryAlias' => ['shape' => 'DefaultRegistryAliasFlag']]], 'RegistryAliasList' => ['type' => 'list', 'member' => ['shape' => 'RegistryAlias']], 'RegistryAliasName' => ['type' => 'string', 'max' => 50, 'min' => 2, 'pattern' => '[a-z][a-z0-9]+(?:[._-][a-z0-9]+)*'], 'RegistryAliasStatus' => ['type' => 'string', 'enum' => ['ACTIVE', 'PENDING', 'REJECTED']], 'RegistryCatalogData' => ['type' => 'structure', 'members' => ['displayName' => ['shape' => 'RegistryDisplayName']]], 'RegistryDisplayName' => ['type' => 'string', 'max' => 100, 'min' => 0], 'RegistryId' => ['type' => 'string', 'pattern' => '[0-9]{12}'], 'RegistryIdOrAlias' => ['type' => 'string', 'max' => 50, 'min' => 2], 'RegistryList' => ['type' => 'list', 'member' => ['shape' => 'Registry']], 'RegistryNotFoundException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'ExceptionMessage']], 'exception' => \true], 'RegistryVerified' => ['type' => 'boolean'], 'Repository' => ['type' => 'structure', 'members' => ['repositoryArn' => ['shape' => 'Arn'], 'registryId' => ['shape' => 'RegistryId'], 'repositoryName' => ['shape' => 'RepositoryName'], 'repositoryUri' => ['shape' => 'Url'], 'createdAt' => ['shape' => 'CreationTimestamp']]], 'RepositoryAlreadyExistsException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'ExceptionMessage']], 'exception' => \true], 'RepositoryCatalogData' => ['type' => 'structure', 'members' => ['description' => ['shape' => 'RepositoryDescription'], 'architectures' => ['shape' => 'ArchitectureList'], 'operatingSystems' => ['shape' => 'OperatingSystemList'], 'logoUrl' => ['shape' => 'ResourceUrl'], 'aboutText' => ['shape' => 'AboutText'], 'usageText' => ['shape' => 'UsageText'], 'marketplaceCertified' => ['shape' => 'MarketplaceCertified']]], 'RepositoryCatalogDataInput' => ['type' => 'structure', 'members' => ['description' => ['shape' => 'RepositoryDescription'], 'architectures' => ['shape' => 'ArchitectureList'], 'operatingSystems' => ['shape' => 'OperatingSystemList'], 'logoImageBlob' => ['shape' => 'LogoImageBlob'], 'aboutText' => ['shape' => 'AboutText'], 'usageText' => ['shape' => 'UsageText']]], 'RepositoryCatalogDataNotFoundException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'ExceptionMessage']], 'exception' => \true], 'RepositoryDescription' => ['type' => 'string', 'max' => 1024], 'RepositoryList' => ['type' => 'list', 'member' => ['shape' => 'Repository']], 'RepositoryName' => ['type' => 'string', 'max' => 205, 'min' => 2, 'pattern' => '(?:[a-z0-9]+(?:[._-][a-z0-9]+)*/)*[a-z0-9]+(?:[._-][a-z0-9]+)*'], 'RepositoryNameList' => ['type' => 'list', 'member' => ['shape' => 'RepositoryName'], 'max' => 100, 'min' => 1], 'RepositoryNotEmptyException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'ExceptionMessage']], 'exception' => \true], 'RepositoryNotFoundException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'ExceptionMessage']], 'exception' => \true], 'RepositoryPolicyNotFoundException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'ExceptionMessage']], 'exception' => \true], 'RepositoryPolicyText' => ['type' => 'string', 'max' => 10240, 'min' => 0], 'ResourceUrl' => ['type' => 'string', 'max' => 2048], 'ServerException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'ExceptionMessage']], 'exception' => \true, 'fault' => \true], 'SetRepositoryPolicyRequest' => ['type' => 'structure', 'required' => ['repositoryName', 'policyText'], 'members' => ['registryId' => ['shape' => 'RegistryId'], 'repositoryName' => ['shape' => 'RepositoryName'], 'policyText' => ['shape' => 'RepositoryPolicyText'], 'force' => ['shape' => 'ForceFlag']]], 'SetRepositoryPolicyResponse' => ['type' => 'structure', 'members' => ['registryId' => ['shape' => 'RegistryId'], 'repositoryName' => ['shape' => 'RepositoryName'], 'policyText' => ['shape' => 'RepositoryPolicyText']]], 'Tag' => ['type' => 'structure', 'members' => ['Key' => ['shape' => 'TagKey'], 'Value' => ['shape' => 'TagValue']]], 'TagKey' => ['type' => 'string', 'max' => 128, 'min' => 1], 'TagKeyList' => ['type' => 'list', 'member' => ['shape' => 'TagKey'], 'max' => 200, 'min' => 0], 'TagList' => ['type' => 'list', 'member' => ['shape' => 'Tag'], 'max' => 200, 'min' => 0], 'TagResourceRequest' => ['type' => 'structure', 'required' => ['resourceArn', 'tags'], 'members' => ['resourceArn' => ['shape' => 'Arn'], 'tags' => ['shape' => 'TagList']]], 'TagResourceResponse' => ['type' => 'structure', 'members' => []], 'TagValue' => ['type' => 'string', 'max' => 256, 'min' => 0], 'TooManyTagsException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'ExceptionMessage']], 'exception' => \true], 'UnsupportedCommandException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'ExceptionMessage']], 'exception' => \true], 'UntagResourceRequest' => ['type' => 'structure', 'required' => ['resourceArn', 'tagKeys'], 'members' => ['resourceArn' => ['shape' => 'Arn'], 'tagKeys' => ['shape' => 'TagKeyList']]], 'UntagResourceResponse' => ['type' => 'structure', 'members' => []], 'UploadId' => ['type' => 'string', 'pattern' => '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}'], 'UploadLayerPartRequest' => ['type' => 'structure', 'required' => ['repositoryName', 'uploadId', 'partFirstByte', 'partLastByte', 'layerPartBlob'], 'members' => ['registryId' => ['shape' => 'RegistryIdOrAlias'], 'repositoryName' => ['shape' => 'RepositoryName'], 'uploadId' => ['shape' => 'UploadId'], 'partFirstByte' => ['shape' => 'PartSize'], 'partLastByte' => ['shape' => 'PartSize'], 'layerPartBlob' => ['shape' => 'LayerPartBlob']]], 'UploadLayerPartResponse' => ['type' => 'structure', 'members' => ['registryId' => ['shape' => 'RegistryId'], 'repositoryName' => ['shape' => 'RepositoryName'], 'uploadId' => ['shape' => 'UploadId'], 'lastByteReceived' => ['shape' => 'PartSize']]], 'UploadNotFoundException' => ['type' => 'structure', 'members' => ['message' => ['shape' => 'ExceptionMessage']], 'exception' => \true], 'Url' => ['type' => 'string'], 'UsageText' => ['type' => 'string', 'max' => 25600]]];
