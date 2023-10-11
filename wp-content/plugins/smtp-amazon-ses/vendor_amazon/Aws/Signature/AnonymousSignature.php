<?php

namespace YaySMTPAmazonSES\Aws3\Aws\Signature;

use YaySMTPAmazonSES\Aws3\Aws\Credentials\CredentialsInterface;
use YaySMTPAmazonSES\Aws3\Psr\Http\Message\RequestInterface;
/**
 * Provides anonymous client access (does not sign requests).
 */
class AnonymousSignature implements \YaySMTPAmazonSES\Aws3\Aws\Signature\SignatureInterface
{
    public function signRequest(\YaySMTPAmazonSES\Aws3\Psr\Http\Message\RequestInterface $request, \YaySMTPAmazonSES\Aws3\Aws\Credentials\CredentialsInterface $credentials)
    {
        return $request;
    }
    public function presign(\YaySMTPAmazonSES\Aws3\Psr\Http\Message\RequestInterface $request, \YaySMTPAmazonSES\Aws3\Aws\Credentials\CredentialsInterface $credentials, $expires)
    {
        return $request;
    }
}
