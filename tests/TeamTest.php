<?php

use App\Team;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TeamTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_a_name()
    {
        //given
        $team = new Team(['name' => 'Eureka Team']);

        //when

        //then
        $this->assertEquals('Eureka Team', $team->name);
    }

    /**  @test */
    public function it_can_add_members()
    {
        //given
        $user1 = factory(\App\User::class)->create();
        $user2 = factory(\App\User::class)->create();
        $team = factory(Team::class)->create(['size' => 10]);

        //when
        $team->add($user1);
        $team->add($user2);
        $members = $team->members();

        //then
        $this->assertCount(2, $members);
    }

    /**  @test */
    public function it_can_remove_members()
    {
        //given
        $user1 = factory(\App\User::class)->create();
        $user2 = factory(\App\User::class)->create();
        $team = factory(Team::class)->create(['size' => 10]);

        //when
        $team->add($user1);
        $team->add($user2);
        $members = $team->members();
        $this->assertCount(2, $members);

        $team->remove($user1);
        $members = $team->members();

        //then
        $this->assertCount(1, $members);
        $this->assertEquals($user2->id, $members->first()->id);
    }


    /** @test */
    public function it_has_a_maximum_size()
    {
        //given
        $user1 = factory(\App\User::class)->create();
        $user2 = factory(\App\User::class)->create();
        $team = factory(Team::class)->create(['size' => 2]);

        //when
        $team->add($user1);
        $team->add($user2);
        $this->assertCount(2, $team->members());

        $this->setExpectedException('Exception');
        $user3 = factory(\App\User::class)->create();
        $team->add($user3);

        //then

    }

    /** @test */
    public function it_can_add_multiple_members_at_once()
    {
        //given
        $users = factory(\App\User::class, 5)->create();
        $team = factory(Team::class)->create(['size' => 6]);

        //when
        $team->add($users);


        //then
        $this->assertCount(5, $team->members());

            //given
            $team2 = factory(Team::class)->create(['size' => 3]);
            //should
            $this->setExpectedException('Exception');
            //when
            $team2->add($users);
    }

    /** @test */
    public function it_can_remove_all_members_at_once()
    {
        //given
        $users = factory(\App\User::class, 5)->create();
        $team = factory(Team::class)->create(['size' => 6]);
        $team->add($users);

        //when
        $team->wipe();

        //then
        $this->assertCount(0, $team->members());
    }

    /** @test */
    public function it_can_remove_a_certain_number_of_members()
    {
        //given
        $users = factory(\App\User::class, 5)->create();
        $team = factory(Team::class)->create(['size' => 6]);
        $team->add($users);

        //when
        $team->remove($users->slice(0, 2));

        //then
        $this->assertCount(3, $team->members());
    }
}
