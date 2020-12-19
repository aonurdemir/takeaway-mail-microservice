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

    public function test_api_send_grid_active()
    {
        $sendGridResponse = $this->mockSendGridAPIResponseWithCode(200);
        $sendGridAPI = $this->mockSendGridAPIWithResponse($sendGridResponse);
        $this->instance(
            SendGridAPI::class,
            $sendGridAPI
        );

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

    private function mockSendGridAPIResponseWithCode(int $code)
    {
        /** @var SendGridResponse $mock */
        $mock = $this->mock(
            SendGridResponse::class,
            function (MockInterface $mock) use ($code) {
                $mock->shouldReceive('statusCode')->once()
                     ->andReturn($code);
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

    private function mockMailjetClientResponseWithCode(int $code)
    {
        /** @var MailjetResponse $mock */
        $mock = $this->mock(
            MailjetResponse::class,
            function (MockInterface $mock) use ($code) {
                $mock->shouldReceive('success')
                     ->andReturn($code < 300);
                $mock->shouldReceive('getStatus')
                     ->andReturn($code);
            }
        );

        return $mock;
    }

    private function mockMailjetClientWithResponse(MailjetResponse $response)
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