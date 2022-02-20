<?php

declare(strict_types=1);

namespace Semaio\RequestId\Test\Unit\Policy;

use PHPUnit\Framework\TestCase;
use Semaio\RequestId\Policy\DefaultPolicy;
use Semaio\RequestId\Policy\PolicyInterface;
use Symfony\Component\HttpFoundation\Request;

class DefaultPolicyTest extends TestCase
{
    private PolicyInterface $policy;

    protected function setUp(): void
    {
        $this->policy = new DefaultPolicy();
    }

    /**
     * @test
     */
    public function it_can_check_policy(): void
    {
        $request = $this->createMock(Request::class);

        static::assertTrue($this->policy->canHandleRequest($request));
        static::assertTrue($this->policy->canTrustRequestIdHeader($request));
    }
}
