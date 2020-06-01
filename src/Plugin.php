<?php

namespace Devin345458\Api;

use Cake\Core\BasePlugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Event\Event;

/**
 * Plugin for Api
 */
class Plugin extends BasePlugin
{

    public function bootstrap(PluginApplicationInterface $app): void
    {
        $eventManager = $app->getEventManager();
        $eventManager->on('Bake.initialize', function (Event $event) {
            /** @var \Bake\View\BakeView $view **/
            $view  = $event->getSubject();
            $view->loadHelper('Devin345458/Api.Swagger');

        });

        parent::bootstrap($app);
    }

}