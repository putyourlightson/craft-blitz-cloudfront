<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\blitzcloudfront;

use craft\base\Plugin as BasePlugin;
use craft\events\RegisterComponentTypesEvent;
use putyourlightson\blitz\helpers\CachePurgerHelper;
use yii\base\Event;

class Plugin extends BasePlugin
{
    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        Event::on(CachePurgerHelper::class, CachePurgerHelper::EVENT_REGISTER_PURGER_TYPES,
            function(RegisterComponentTypesEvent $event) {
                $event->types[] = CloudFrontPurger::class;
            }
        );
    }
}
