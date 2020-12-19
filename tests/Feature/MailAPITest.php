<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mailjet\Client as MailjetAPI;
use Mailjet\Response as MailjetResponse;
use Mockery\MockInterface;
use SendGrid as SendGridAPI;
use SendGrid\Response as SendGridResponse;
use Tests\Utils\Helper;
use Tests\Utils\MailTestBase;

class MailAPITest extends MailTestBase
{
    use RefreshDatabase;
    use WithFaker;

    private Helper $helper;

    public function setUp(): void
    {
        parent::setUp();

        $this->helper = new Helper();
    }

    public function test_api_send_grid_returns_success()
    {
        $sendGridResponse = $this->mockSendGridAPISuccessResponse();
        $sendGridAPI = $this->mockSendGridAPIReturnResponse($sendGridResponse);

        $this->instance(SendGridAPI::class, $sendGridAPI);

        $payload = $this->getRequiredPayload();
        $expectedAttributes = $this->mergePayloadWithExpectedAttributes($payload, 'sent', 'sendgrid');

        $response = $this->postJson('/api/v1/mails', $payload);

        $this->assertDatabaseHasHelper($expectedAttributes);
        $this->assertEquals(202, $response->getStatusCode());
    }

    public function test_api_send_grid_returns_error_mailjet_returns_success()
    {
        $sendGridResponse = $this->mockSendGridAPIErrorResponse();
        $sendGridAPI = $this->mockSendGridAPIReturnResponse($sendGridResponse);

        $mailjetResponse = $this->mockMailjetAPISuccessResponse();
        $mailjetAPI = $this->mockMailjetAPIReturnResponse($mailjetResponse);

        $this->instance(SendGridAPI::class, $sendGridAPI);
        $this->instance(MailjetAPI::class, $mailjetAPI);

        $payload = $this->getRequiredPayload();
        $expectedAttributes = $this->mergePayloadWithExpectedAttributes($payload, 'sent', 'mailjet');

        $response = $this->postJson('/api/v1/mails', $payload);

        $this->assertDatabaseHasHelper($expectedAttributes);
        $this->assertEquals(202, $response->getStatusCode());
    }

    public function test_api_send_grid_returns_error_mailjet_returns_error()
    {
        $sendGridResponse = $this->mockSendGridAPIErrorResponse();
        $sendGridAPI = $this->mockSendGridAPIReturnResponse($sendGridResponse);

        $mailjetResponse = $this->mockMailjetAPIErrorResponse();
        $mailjetAPI = $this->mockMailjetAPIReturnResponse($mailjetResponse);

        $this->instance(SendGridAPI::class, $sendGridAPI);
        $this->instance(MailjetAPI::class, $mailjetAPI);

        $payload = $this->getRequiredPayload();
        $expectedAttributes = $this->mergePayloadWithExpectedAttributes($payload, 'failed', null);

        $response = $this->postJson('/api/v1/mails', $payload);

        $this->assertDatabaseHasHelper($expectedAttributes);
        $this->assertEquals(202, $response->getStatusCode());
    }

    public function test_api_send_grid_returns_error_mailjet_throws_exception()
    {
        $sendGridResponse = $this->mockSendGridAPIErrorResponse();
        $sendGridAPI = $this->mockSendGridAPIReturnResponse($sendGridResponse);

        $mailjetAPI = $this->mockMailjetAPIThrowsException();

        $this->instance(SendGridAPI::class, $sendGridAPI);
        $this->instance(MailjetAPI::class, $mailjetAPI);

        $payload = $this->getRequiredPayload();
        $expectedAttributes = $this->mergePayloadWithExpectedAttributes($payload, 'failed', null);

        $response = $this->postJson('/api/v1/mails', $payload);

        $this->assertDatabaseHasHelper($expectedAttributes);
        $this->assertEquals(202, $response->getStatusCode());
    }

    public function test_api_send_grid_throws_exception_mailjet_returns_success()
    {
        $sendGridAPI = $this->mockSendGridAPIThrowsException();

        $mailjetResponse = $this->mockMailjetAPISuccessResponse();
        $mailjetAPI = $this->mockMailjetAPIReturnResponse($mailjetResponse);

        $this->instance(SendGridAPI::class, $sendGridAPI);
        $this->instance(MailjetAPI::class, $mailjetAPI);

        $payload = $this->getRequiredPayload();
        $expectedAttributes = $this->mergePayloadWithExpectedAttributes($payload, 'sent', 'mailjet');

        $response = $this->postJson('/api/v1/mails', $payload);

        $this->assertDatabaseHasHelper($expectedAttributes);
        $this->assertEquals(202, $response->getStatusCode());
    }

    public function test_api_send_grid_throws_exception_mailjet_returns_error()
    {
        $sendGridAPI = $this->mockSendGridAPIThrowsException();

        $mailjetResponse = $this->mockMailjetAPIErrorResponse();
        $mailjetAPI = $this->mockMailjetAPIReturnResponse($mailjetResponse);

        $this->instance(SendGridAPI::class, $sendGridAPI);
        $this->instance(MailjetAPI::class, $mailjetAPI);

        $payload = $this->getRequiredPayload();
        $expectedAttributes = $this->mergePayloadWithExpectedAttributes($payload, 'failed', null);

        $response = $this->postJson('/api/v1/mails', $payload);

        $this->assertDatabaseHasHelper($expectedAttributes);
        $this->assertEquals(202, $response->getStatusCode());
    }

    private function getRequiredPayload()
    {
        return [
            'to'   => $this->faker->email,
            'from' => $this->faker->email,
        ];
    }

    private function mergePayloadWithExpectedAttributes($payload, $state, $senderThirdPartyName)
    {
        return array_merge(
            $payload,
            [
                'subject'                          => null,
                'content'                          => null,
                'state'                            => $state,
                'sender_third_party_provider_name' => $senderThirdPartyName,
            ]
        );
    }

    private function mockSendGridAPIErrorResponse()
    {
        /** @var SendGridResponse $mock */
        $mock = $this->mock(
            SendGridResponse::class,
            function (MockInterface $mock) {
                $mock->shouldReceive('statusCode')
                     ->andReturn(400);
                $mock->shouldReceive('body')
                     ->andReturn('message body');
            }
        );

        return $mock;
    }

    private function mockSendGridAPISuccessResponse()
    {
        /** @var SendGridResponse $mock */
        $mock = $this->mock(
            SendGridResponse::class,
            function (MockInterface $mock) {
                $mock->shouldReceive('statusCode')->once()
                     ->andReturn(200);
            }
        );

        return $mock;
    }

    private function mockSendGridAPIReturnResponse(SendGridResponse $response)
    {
        /** @var SendGridAPI $mock */
        $mock = $this->mock(
            SendGridAPI::class,
            function (MockInterface $mock) use ($response) {
                $mock->shouldReceive('send')->once()
                     ->andReturn($response);
            }
        );

        return $mock;
    }

    private function mockSendGridAPIThrowsException()
    {
        /** @var SendGridAPI $mock */
        $mock = $this->mock(
            SendGridAPI::class,
            function (MockInterface $mock) {
                $mock->shouldReceive('send')->once()
                     ->andThrow(Exception::class);
            }
        );

        return $mock;
    }

    private function mockMailjetAPIErrorResponse()
    {
        /** @var MailjetResponse $mock */
        $mock = $this->mock(
            MailjetResponse::class,
            function (MockInterface $mock) {
                $mock->shouldReceive('success')
                     ->andReturn(false);
                $mock->shouldReceive('getStatus')
                     ->andReturn(400);
                $mock->shouldReceive('getReasonPhrase')
                     ->andReturn('message body');
            }
        );

        return $mock;
    }

    private function mockMailjetAPISuccessResponse()
    {
        /** @var MailjetResponse $mock */
        $mock = $this->mock(
            MailjetResponse::class,
            function (MockInterface $mock) {
                $mock->shouldReceive('success')
                     ->andReturn(true);
                $mock->shouldReceive('getStatus')
                     ->andReturn(200);
            }
        );

        return $mock;
    }

    private function mockMailjetAPIReturnResponse(MailjetResponse $response)
    {
        /** @var MailjetAPI $mock */
        $mock = $this->mock(
            MailjetAPI::class,
            function (MockInterface $mock) use ($response) {
                $mock->shouldReceive('post')->once()
                     ->andReturn($response);
            }
        );

        return $mock;
    }

    private function mockMailjetAPIThrowsException()
    {
        /** @var MailjetAPI $mock */
        $mock = $this->mock(
            MailjetAPI::class,
            function (MockInterface $mock) {
                $mock->shouldReceive('post')->once()
                     ->andThrow(Exception::class);
            }
        );

        return $mock;
    }
}