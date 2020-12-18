<?php

namespace Tests\Feature\Repositories;

use App\Models\Mail;
use App\Repositories\EloquentMailRepository;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;


class EloquentMailRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    public function test_create_main_scenario()
    {
        $repository = new EloquentMailRepository();

        $attributes = [
            'to'      => 'to@mail.com',
            'from'    => 'from@mail.com',
            'subject' => 'subject-text',
            'content' => 'content-text',
        ];

        $mail = $repository->create($attributes);
        $this->assertRequiredFields($mail);
        $this->assertEquals('subject-text', $mail->subject);
        $this->assertEquals('content-text', $mail->content);

        $this->assertDatabaseHas(
            'mails',
            [
                'to'      => 'to@mail.com',
                'from'    => 'from@mail.com',
                'subject' => 'subject-text',
                'content' => 'content-text',
                'state'   => 'created',
            ]
        );
    }

    public function test_create_without_optionals()
    {
        $repository = new EloquentMailRepository();

        $attributes = [
            'to'   => 'to@mail.com',
            'from' => 'from@mail.com',
        ];

        $mail = $repository->create($attributes);
        $this->assertRequiredFields($mail);
        $this->assertNull($mail->subject);
        $this->assertNull($mail->content);

        $this->assertDatabaseHas(
            'mails',
            [
                'to'      => 'to@mail.com',
                'from'    => 'from@mail.com',
                'subject' => null,
                'content' => null,
                'state'   => 'created',
            ]
        );
    }

    public function test_create_missing_required_arg()
    {
        $repository = new EloquentMailRepository();

        $attributes = [
            'to' => 'to@mail.com',
        ];

        $this->expectException(QueryException::class);
        $repository->create($attributes);

        $this->assertDatabaseMissing(
            'mails',
            [
                'to' => 'to@mail.com',
            ]
        );
    }

    private function assertRequiredFields($mail)
    {
        $this->assertInstanceOf(Mail::class, $mail);
        $this->assertEquals('to@mail.com', $mail->to);
        $this->assertEquals('from@mail.com', $mail->from);
        $this->assertEquals('created', $mail->state);
    }


}
