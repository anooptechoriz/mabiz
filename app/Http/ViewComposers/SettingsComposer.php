<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Models\Language;

use Illuminate\Support\Facades\Auth;
use Request;
use DB;


class SettingsComposer
{
    /**
     * The user repository implementation.
     *
     * @var UserRepository
     */
    protected $users;

    /**
     * Create a new profile composer.
     *
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct( )
    {
        // Dependencies automatically resolved by service container...
        //$this->users = $users;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $site_languages = Language::all();

        $view->with(['site_languages'=>$site_languages]);
    }

}
