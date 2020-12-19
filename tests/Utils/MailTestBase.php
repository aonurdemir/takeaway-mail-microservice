<?php


namespace Tests\Utils;


use App\Models\Mail;
use Tests\TestCase;

class MailTestBase extends TestCase
{
    public function assertRequiredFields(Mail $mail, $expectedAttr)
    {
        $this->assertInstanceOf(Mail::class, $mail);
        $this->assertEquals($expectedAttr['to'], $mail->to);
        $this->assertEquals($expectedAttr['from'], $mail->from);
        $this->assertEquals($expectedAttr['state'], $mail->state);
    }

    public function assertOptionalFields(Mail $mail, $expectedAttr)
    {
        $this->assertInstanceOf(Mail::class, $mail);
        $this->assertEquals($expectedAttr['subject'], $mail->subject);
        $this->assertEquals($expectedAttr['content'], $mail->content);
    }

    public function assertDatabaseHasHelper($expectedAttr)
    {
        $this->assertDatabaseHas(
            'mails',
            [
                'to'                               => $expectedAttr['to'],
                'from'                             => $expectedAttr['from'],
                'subject'                          => $expectedAttr['subject'],
                'content'                          => $expectedAttr['content'],
                'state'                            => $expectedAttr['state'],
                'sender_third_party_provider_name' => $expectedAttr['sender_third_party_provider_name'] ?? null,
            ]
        );
    }

    public function assertMailsEqual(Mail $expected, Mail $actual)
    {
        $this->assertEquals($expected->to, $actual->to);
        $this->assertEquals($expected->from, $actual->from);
        $this->assertEquals($expected->subject, $actual->subject);
        $this->assertEquals($expected->content, $actual->content);
        $this->assertEquals($expected->state, $actual->state);
    }

}