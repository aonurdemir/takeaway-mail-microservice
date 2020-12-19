<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\MockInterface;
use SendGrid\Client;
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
        $sendGridResponse = $this->mockSendGridClientResponseWithCode(200);
        $sendGridClient = $this->mockSendGridClientWithResponse($sendGridResponse);
        $this->instance(
            Client::class,
            $sendGridClient
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

    private function mockSendGridClientResponseWithCode(int $code)
    {
        /** @var \SendGrid\Response $mock */
        $mock = $this->mock(
            \SendGrid\Response::class,
            function (MockInterface $mock) use ($code) {
                $mock->shouldReceive('statusCode')->once()
                     ->andReturn($code);
            }
        );

        return $mock;
    }

    private function mockSendGridClientWithResponse(\SendGrid\Response $response)
    {
        /** @var \SendGrid $mock */
        $mock = $this->mock(
            SendGrid::class,
            function (MockInterface $mock) use ($response) {
                $mock->shouldReceive('send')->once()
                     ->andReturn($response);
            }
        );

        return $mock;
    }

    private function mockMailjetClientResponseWithCode(int $code)
    {
        /** @var \Mailjet\Response $mock */
        $mock = $this->mock(
            \Mailjet\Response::class,
            function (MockInterface $mock) use ($code) {
                $mock->shouldReceive('success')
                     ->andReturn($code < 300);
                $mock->shouldReceive('getStatus')
                     ->andReturn($code);
            }
        );

        return $mock;
    }

    private function mockMailjetClientWithResponse(\Mailjet\Response $response)
    {
        /** @var \Mailjet\Client $mock */
        $mock = $this->mock(
            \Mailjet\Client::class,
            function (MockInterface $mock) use ($response) {
                $mock->shouldReceive('post')->once()
                     ->andReturn($response);
            }
        );

        return $mock;
    }
}