<?php

namespace Tests\Feature\Repositories;

use App\Repositories\EloquentMailRepository;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Utils\MailTestBase;


class EloquentMailRepositoryTest extends MailTestBase
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
        $attrAfterCreation = array_merge($attributes, ['state' => 'created']);

        $mail = $repository->create($attributes);
        $this->assertRequiredFields($mail, $attrAfterCreation);
        $this->assertOptionalFields($mail, $attrAfterCreation);

        $this->assertDatabaseHasHelper($attrAfterCreation);
    }

    public function test_create_without_optionals()
    {
        $repository = new EloquentMailRepository();

        $attributes = [
            'to'   => 'to@mail.com',
            'from' => 'from@mail.com',
        ];
        $attrAfterCreation = array_merge(
            $attributes,
            [
                'state'   => 'created',
                'subject' => null,
                'content' => null,
            ]
        );

        $mail = $repository->create($attributes);
        $this->assertRequiredFields($mail, $attrAfterCreation);
        $this->assertOptionalFields($mail, $attrAfterCreation);
        $this->assertDatabaseHasHelper($attrAfterCreation);
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


}
