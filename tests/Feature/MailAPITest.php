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
        $sendGridAPI = $this->mockSendGridAPIWithResponse($sendGridResponse);

        $this->instance(SendGridAPI::class, $sendGridAPI);

        $payload = [
            'to'   => $this->faker->email,
            'from' => $this->faker->email,
        ];
        $expectedAttributes = array_merge(
            $payload,
            [
                'subject'                          => null,
                'content'                          => null,
                'state'                            => 'sent',
                'sender_third_party_provider_name' => 'sendgrid',
            ]
        );

        $response = $this->postJson('/api/v1/mails', $payload);

        $this->assertDatabaseHasHelper($expectedAttributes);
        $this->assertEquals(202, $response->getStatusCode());
    }

    public function test_api_send_grid_returns_error_mailjet_returns_success()
    {
        $sendGridResponse = $this->mockSendGridAPIErrorResponse();
        $sendGridAPI = $this->mockSendGridAPIWithResponse($sendGridResponse);

        $mailjetResponse = $this->mockMailjetAPISuccessResponse();
        $mailjetAPI = $this->mockMailjetAPIWithResponse($mailjetResponse);

        $this->instance(SendGridAPI::class, $sendGridAPI);
        $this->instance(MailjetAPI::class, $mailjetAPI);

        $payload = [
            'to'   => $this->faker->email,
            'from' => $this->faker->email,
        ];
        $expectedAttributes = array_merge(
            $payload,
            [
                'subject'                          => null,
                'content'                          => null,
                'state'                            => 'sent',
                'sender_third_party_provider_name' => 'mailjet',
            ]
        );

        $response = $this->postJson('/api/v1/mails', $payload);

        $this->assertDatabaseHasHelper($expectedAttributes);
        $this->assertEquals(202, $response->getStatusCode());
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

    private function mockSendGridAPIWithResponse(SendGridResponse $response)
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

    private function mockMailjetAPIWithResponse(MailjetResponse $response)
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
}