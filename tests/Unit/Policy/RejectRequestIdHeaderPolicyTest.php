<?php

declare(strict_types=1);

namespace Semaio\RequestId\Test\Unit\Policy;

use PHPUnit\Framework\TestCase;
use Semaio\RequestId\Policy\PolicyInterface;
use Semaio\RequestId\Policy\RejectRequestIdHeaderPolicy;
use Symfony\Component\HttpFoundation\Request;

class RejectRequestIdHeaderPolicyTest extends TestCase
{
    private PolicyInterface $policy;

    protected function setUp(): void
    {
        $this->policy = new RejectRequestIdHeaderPolicy();
    }

    /**
     * @test
     */
    public function it_can_check_policy(): void
    {
        $request = $this->createMock(Request::class);

        static::assertTrue($this->policy->canHandleRequest($request));
        static::assertFalse($this->policy->canTrustRequestIdHeader($request));
    }
}
