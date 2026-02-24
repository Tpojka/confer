<?php

namespace Tpojka\Confer\Tests;

use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use Tpojka\Confer\ConferServiceProvider;
use Tpojka\Confer\Facades\Push;

abstract class TestCase extends Orchestra
{
    protected $baseUrl = 'http://127.0.0.1:8000';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase($this->app);
    }

    protected function setUpDatabase($app)
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('confer_conversations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->boolean('is_private')->default(false);
            $table->timestamps();
        });

        Schema::create('confer_conversation_participants', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('conversation_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('confer_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('conversation_id')->unsigned();
            $table->integer('sender_id')->unsigned();
            $table->text('body');
            $table->string('type')->default('user_message');
            $table->timestamps();
        });
    }

    protected function getPackageProviders($app)
    {
        return [
            ConferServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Push' => Push::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:3sGkHB+CNmuNK666R2hWFEeEKXvnpHCr1ajYAI5pk2I=');
        $app['config']->set('auth.providers.users.model', User::class);

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        
        $app['config']->set('broadcasting.connections.pusher', [
            'driver' => 'pusher',
            'key' => 'test-key',
            'secret' => 'test-secret',
            'app_id' => 'test-id',
            'options' => [
                'cluster' => 'mt1',
                'useTLS' => true,
            ],
        ]);

        // Workaround for simulating login route, specifically needed in test
        // Tpojka\Confer\Tests\Feature\ConversationControllerTest::test_guest_cannot_access_conversations_index
        
        if (!Route::has('login')) {
            $app['router']->get('login', function () {
                return 'login';
            })->name('login');
        }
    }
}
