<?php

namespace App\Listeners\Management\Surveillance;

use App\Events\Management\Surveillance\ArticleMarkReactedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ArticleMarkReactedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ArticleMarkReactedEvent  $event
     * @return void
     */
    public function handle(ArticleMarkReactedEvent $event)
    {
        //
    }
}
