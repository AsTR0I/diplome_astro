<?php

namespace App\Policies;

use App\Agent;
use App\Account;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the operator can view any accounts.
     *
     * @param  \App\Agent  $agent
     * @return mixed
     */
    public function viewAny(Agent $agent)
    {
        // if ($agent->can('view accounts')) {
        //     return true;
        // }
        return true;
    }

    /**
     * Determine whether the operator can view the account.
     *
     * @param  \App\Agent  $agent
     * @param  \App\Account  $account
     * @return mixed
     */
    public function view(Agent $agent, Account $account)
    {
        // if ($agent->can('view accounts')) {
        //     return true;
        // }
        return true;
    }

    /**
     * Determine whether the operator can create accounts.
     *
     * @param  \App\Agent  $agent
     * @return mixed
     */
    public function create(Agent $agent)
    {
        if ($agent->can('edit accounts')) {
            return true;
        }
    }

    /**
     * Determine whether the operator can update the account.
     *
     * @param  \App\Agent  $agent
     * @param  \App\Account  $account
     * @return mixed
     */
    public function update(Agent $agent, Account $account)
    {
        if ($agent->can('edit accounts')) {
            return true;
        }
    }

    /**
     * Determine whether the operator can delete the account.
     *
     * @param  \App\Agent  $agent
     * @param  \App\Account  $account
     * @return mixed
     */
    public function delete(Agent $agent, Account $account)
    {
        if ($agent->can('edit accounts')) {
            return true;
        }
    }
}
