<?php

use Mockery as m;
use Belt\Core\Events;
use Belt\Core\Listeners;
use Belt\Core\Mail\TeamWelcomeEmail;
use Belt\Core\Testing;
use Belt\Core\Team;
use Belt\Core\User;
use Illuminate\Support\Facades\Mail;

class SendTeamWelcomeEmailTest extends Testing\BeltTestCase
{

    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers \Belt\Core\Listeners\SendTeamWelcomeEmail::__construct
     * @covers \Belt\Core\Listeners\SendTeamWelcomeEmail::handle
     */
    public function test()
    {
        Mail::fake();

        $team = factory(Team::class)->make();
        $user = factory(User::class)->make();
        $team->defaultUser = $user;

        $event = new Events\TeamCreated($team);

        $listener = new Listeners\SendTeamWelcomeEmail();
        $listener->handle($event);

        $mailable = new TeamWelcomeEmail([
            'team' => $team,
        ]);

        Mail::shouldReceive('to')->with($user->email);
        Mail::shouldReceive('send')->with($mailable);
    }

}