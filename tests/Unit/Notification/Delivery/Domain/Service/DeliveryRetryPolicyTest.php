<?php

declare(strict_types=1);

namespace App\Tests\Unit\Notification\Delivery\Domain\Service;

use App\Notification\Delivery\Domain\Service\DeliveryRetryPolicy;
use PHPUnit\Framework\TestCase;

class DeliveryRetryPolicyTest extends TestCase
{
    public function testShouldRetryReturnsTrueWhenMaxRetriesNotExceeded(): void
    {
        $maxRetries = 3;
        $retryDelay = 1000;
        $retryDelayMultiplier = 2;
        
        $attemptNr = 1;
        $deliveryRetryPolicy = new DeliveryRetryPolicy($maxRetries, $retryDelay, $retryDelayMultiplier);
        $result = $deliveryRetryPolicy->shouldRetry($attemptNr);

        $this->assertTrue($result);
    }

    public function testShouldRetryReturnsFalseWhenMaxRetriesExceeded(): void
    {
        $maxRetries = 3;
        $retryDelay = 1000;
        $retryDelayMultiplier = 2;
        
        $attemptNr = 3;
        $deliveryRetryPolicy = new DeliveryRetryPolicy($maxRetries, $retryDelay, $retryDelayMultiplier);
        $result = $deliveryRetryPolicy->shouldRetry($attemptNr);

        $this->assertFalse($result);
    }

    public function testGetRetryDelay(): void
    {
        $maxRetries = 3;
        $retryDelay = 1000;
        $retryDelayMultiplier = 2;
        
        $attemptNr = 2;
        $deliveryRetryPolicy = new DeliveryRetryPolicy($maxRetries, $retryDelay, $retryDelayMultiplier);
        $result = $deliveryRetryPolicy->getRetryDelay($attemptNr);

        $this->assertEquals($attemptNr*$retryDelay*$retryDelayMultiplier, $result);
    }
}