<?php

namespace App\Providers;

use App\Repositories\Movies\MovieRepository;
use App\Repositories\Movies\MovieRepositoryInterface;
use Illuminate\Support\ServiceProvider;

// use App\Repositories\Contracts\RepositoryInterface;
// use App\Repositories\Eloquent\Repository;

use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;

use App\Repositories\UserDevice\UserDeviceRepository;
use App\Repositories\UserDevice\UserDeviceRepositoryInterface;

use App\Repositories\UserGroup\UserGroupRepository;
use App\Repositories\UserGroup\UserGroupRepositoryInterface;

use App\Repositories\UserGroupMember\UserGroupMemberRepository;
use App\Repositories\UserGroupMember\UserGroupMemberRepositoryInterface;

use App\Repositories\UserFeedback\UserFeedbackRepository;
use App\Repositories\UserFeedback\UserFeedbackRepositoryInterface;

use App\Repositories\UserFavorite\UserFavoriteRepository;
use App\Repositories\UserFavorite\UserFavoriteRepositoryInterface;

use App\Repositories\Genre\GenreRepository;
use App\Repositories\Genre\GenreRepositoryInterface;

use App\Repositories\StreamingProvider\StreamingProviderRepository;
use App\Repositories\StreamingProvider\StreamingProviderRepositoryInterface;

use App\Repositories\SearchTransaction\SearchTransactionRepository;
use App\Repositories\SearchTransaction\SearchTransactionRepositoryInterface;

use App\Repositories\SearchTransactionGroup\SearchTransactionGroupRepository;
use App\Repositories\SearchTransactionGroup\SearchTransactionGroupRepositoryInterface;

use App\Repositories\SearchTransactionUser\SearchTransactionUserRepository;
use App\Repositories\SearchTransactionUser\SearchTransactionUserRepositoryInterface;

use App\Repositories\MatchMaker\MatchMakerRepository;
use App\Repositories\MatchMaker\MatchMakerRepositoryInterface;

use App\Repositories\Business\BusinessRepository;
use App\Repositories\Business\BusinessRepositoryInterface;

use App\Repositories\SearchFilterOption\SearchFilterOptionRepository;
use App\Repositories\SearchFilterOption\SearchFilterOptionRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // $this->app->bind(RepositoryInterface::class, Repository::class);
        $this->app->bind(SearchFilterOptionRepositoryInterface::class, SearchFilterOptionRepository::class);
        $this->app->bind(BusinessRepositoryInterface::class, BusinessRepository::class);
        $this->app->bind(MovieRepositoryInterface::class, MovieRepository::class);

        $this->app->bind(SearchTransactionRepositoryInterface::class, SearchTransactionRepository::class);
        $this->app->bind(SearchTransactionGroupRepositoryInterface::class, SearchTransactionGroupRepository::class);

        $this->app->bind(SearchTransactionUserRepositoryInterface::class, SearchTransactionUserRepository::class);
        $this->app->bind(MatchMakerRepositoryInterface::class, MatchMakerRepository::class);

        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserDeviceRepositoryInterface::class, UserDeviceRepository::class);

        $this->app->bind(UserGroupRepositoryInterface::class, UserGroupRepository::class);
        $this->app->bind(UserGroupMemberRepositoryInterface::class, UserGroupMemberRepository::class);

        $this->app->bind(UserFeedbackRepositoryInterface::class, UserFeedbackRepository::class);
        $this->app->bind(UserFavoriteRepositoryInterface::class, UserFavoriteRepository::class);

        $this->app->bind(GenreRepositoryInterface::class, GenreRepository::class);
        $this->app->bind(StreamingProviderRepositoryInterface::class, StreamingProviderRepository::class);

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }


    public function provides()
    {
        // return [
        //     // RepositoryInterface::class,
        //     UserRepositoryInterface::class,
        //     UserDeviceRepositoryInterface::class
        // ];
    }
}
