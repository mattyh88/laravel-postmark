<?php

namespace Coconuts\Mail;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\ServiceProvider;

class PostmarkServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPublishing();

        if ($this->app['config']['mail.driver'] !== 'postmark') {
            return;
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'postmark');
        $this->mergeConfigFrom(__DIR__.'/../config/postmark.php', 'postmark');

        $this->app['mail.manager']->extend('postmark', function () {
            return new PostmarkTransport(
                $this->guzzle(config('postmark.guzzle', [])),
                config('postmark.secret', config('services.postmark.secret'))
            );
        });
    }

    /**
     * Register the publishable resources for this package.
     *
     * @return void
     */
    private function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/postmark.php' => config_path('postmark.php'),
            ], 'postmark-config');
        }
    }

    /**
     * Get a fresh Guzzle HTTP client instance.
     *
     * @param  array  $config
     * @return \GuzzleHttp\Client
     */
    protected function guzzle($config)
    {
        return new HttpClient($config);
    }
}
