<?php

declare(strict_types=1);

/**
 * Copyright (c) 2021 Kai Sassnowski
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/roach-php/roach
 */

namespace Sassnowski\Roach\Tests\Downloader\Middleware;

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use Sassnowski\Roach\Downloader\Middleware\FakeMiddleware;
use Sassnowski\Roach\Http\Request;
use Sassnowski\Roach\Http\Response;
use Sassnowski\Roach\Tests\InteractsWithRequests;
use Sassnowski\Roach\Tests\InteractsWithResponses;

/**
 * @internal
 */
final class FakeMiddlewareTest extends TestCase
{
    use InteractsWithRequests;
    use InteractsWithResponses;

    public function testReturnRequestUnchangedByDefault(): void
    {
        $middleware = new FakeMiddleware();
        $request = $this->createRequest();

        $result = $middleware->handleRequest($request);

        self::assertSame($request, $result);
    }

    public function testCallsRequestHandlerCallbackIfProvided(): void
    {
        $middleware = new FakeMiddleware(static fn (Request $request) => $request->drop('::reason::'));
        $request = $this->createRequest();

        $result = $middleware->handleRequest($request);

        self::assertTrue($result->wasDropped());
    }

    public function testAssertRequestHandledPassesWhenMiddlewareWasCalledWithCorrectRequest(): void
    {
        $middleware = new FakeMiddleware();
        $request = $this->createRequest();

        $middleware->handleRequest($request);

        $middleware->assertRequestHandled($request);
    }

    public function testAssertRequestHandledFailsIfMiddlewareWasNotCalledAtAll(): void
    {
        $middleware = new FakeMiddleware();
        $request = $this->createRequest();

        $this->expectException(AssertionFailedError::class);
        $middleware->assertRequestHandled($request);
    }

    public function testAssertRequestHandledFailsIfMiddlewareWasNotCalledWithRequest(): void
    {
        $middleware = new FakeMiddleware();
        $request = $this->createRequest();
        $otherRequest = $this->createRequest();

        $middleware->handleRequest($otherRequest);

        $this->expectException(AssertionFailedError::class);
        $middleware->assertRequestHandled($request);
    }

    public function testAssertRequestNotHandled(): void
    {
        $middleware = new FakeMiddleware();
        $request = $this->createRequest();
        $otherRequest = $this->createRequest();

        $middleware->assertRequestNotHandled($request);

        $middleware->handleRequest($otherRequest);
        $middleware->assertRequestNotHandled($request);

        $middleware->handleRequest($request);
        $this->expectException(AssertionFailedError::class);
        $middleware->assertRequestNotHandled($request);
    }

    public function testAssertNoRequestsHandled(): void
    {
        $middleware = new FakeMiddleware();
        $request = $this->createRequest();

        $middleware->assertNoRequestsHandled();

        $middleware->handleRequest($request);
        $this->expectException(AssertionFailedError::class);
        $middleware->assertNoRequestsHandled();
    }

    public function testReturnResponseUnchangedByDefault(): void
    {
        $middleware = new FakeMiddleware();
        $response = $this->makeResponse();

        $result = $middleware->handleResponse($response);

        self::assertSame($response, $result);
    }

    public function testCallsResponseHandlerCallbackIfProvided(): void
    {
        $middleware = new FakeMiddleware(null, static fn (Response $response) => $response->drop('::reason::'));
        $response = $this->makeResponse();

        $result = $middleware->handleResponse($response);

        self::assertTrue($result->wasDropped());
    }

    public function testAssertResponseHandledPassesWhenMiddlewareWasCalledWithCorrectRequest(): void
    {
        $middleware = new FakeMiddleware();
        $response = $this->makeResponse();

        $middleware->handleResponse($response);

        $middleware->assertResponseHandled($response);
    }

    public function testAssertResponseHandledFailsIfMiddlewareWasNotCalledAtAll(): void
    {
        $middleware = new FakeMiddleware();
        $response = $this->makeResponse();

        $this->expectException(AssertionFailedError::class);
        $middleware->assertResponseHandled($response);
    }

    public function testAssertResponseHandledFailsIfMiddlewareWasNotCalledWithRequest(): void
    {
        $middleware = new FakeMiddleware();
        $response = $this->makeResponse();
        $otherResponse = $this->makeResponse();

        $middleware->handleResponse($otherResponse);

        $this->expectException(AssertionFailedError::class);
        $middleware->assertResponseHandled($response);
    }

    public function testAssertResponseNotHandled(): void
    {
        $middleware = new FakeMiddleware();
        $response = $this->makeResponse();
        $otherResponse = $this->makeResponse();

        $middleware->assertResponseNotHandled($response);

        $middleware->handleResponse($otherResponse);
        $middleware->assertResponseNotHandled($response);

        $middleware->handleResponse($response);
        $this->expectException(AssertionFailedError::class);
        $middleware->assertResponseNotHandled($response);
    }

    public function testAssertNoResponseHandled(): void
    {
        $middleware = new FakeMiddleware();
        $response = $this->makeResponse();

        $middleware->assertNoResponseHandled();

        $middleware->handleResponse($response);
        $this->expectException(AssertionFailedError::class);
        $middleware->assertNoResponseHandled();
    }
}