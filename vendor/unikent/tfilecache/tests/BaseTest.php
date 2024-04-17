<?php

use Unikent\Cache\TaggableFileCacheServiceProvider;

abstract class BaseTest extends Orchestra\Testbench\TestCase
{

	public function tearDown() {
		\Mockery::close();
	}


	protected function getPackageProviders($app)
	{
		return [TaggableFileCacheServiceProvider::class];
	}

	/**
	 * Define environment setup.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function getEnvironmentSetUp($app)
	{
		$app['config']->set('app.key','xlhF31NeOlibJcoOW9tvZg7TkHcAZI3a');

		// Setup default database to use sqlite :memory:
		$app['config']->set('cache.default', 'tfile');
		$app['config']->set('cache.stores.tfile',
							[
								'driver' => 'tfile',
								'path'   => storage_path('framework/cache'),
								'separator' => '~#~'
							]
		);
	}

}