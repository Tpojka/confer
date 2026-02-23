<?php

namespace Tpojka\Confer\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Tpojka\Confer\ConferServiceProvider;
use Tpojka\Confer\Facades\Push;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
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
    }
}
