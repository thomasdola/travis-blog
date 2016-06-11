<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['name'];

    public function add($member)
    {

        $method = $member instanceof User ? 'save' : 'saveMany';

        if($member instanceof User){
            $this->guardAgainstTooManyMembers();
            return $this->users()->$method($member);
        }

        $this->guardAgainstTooManyMembers(collect($member)->count());
        $this->users()->$method($member);

    }

    public function remove($member)
    {
        if($member instanceof User ){
            return $member->leaveTeam();
        }
        $member->each(function(User $member){
            $member->leaveTeam();
        });
    }

    public function wipe()
    {
        $members = $this->users();
        if(! $members){
            return;
        }

        $members->each(function(User $members){
            $this->remove($members);
        });
    }

    public function members()
    {
        return $this->users()->get();
    }

    protected function users()
    {
        return $this->hasMany(User::class, 'team_id');
    }

    private function guardAgainstTooManyMembers($multiple_count = null)
    {
        $team_total_size = $this->members()->count();
        if($multiple_count){
            if($this->size <= $team_total_size || $this->size <= $multiple_count){
                throw new \Exception;
            }
        }

        if($this->size <= $team_total_size){
            throw new \Exception;
        }
    }
}
