<?php
/**
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace putyourlightson\blitzcloudfront\events;

use craft\events\CancelableEvent;
use putyourlightson\blitz\models\SiteUriModel;

class PurgeSiteUrisEvent extends CancelableEvent
{
    /**
     * @var SiteUriModel[]|array[]
     */
    public array $siteUris = [];
}
