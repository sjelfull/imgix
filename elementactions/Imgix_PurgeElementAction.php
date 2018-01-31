<?php
/**
 * Imgix plugin for Craft CMS
 *
 * Imgix Purge action
 *
 * @author    Fred Carlsen
 * @copyright Copyright (c) 2016 Fred Carlsen
 * @link      http://sjelfull.no
 * @package   Imgix
 * @since     1.0.0
 */

namespace Craft;

class Imgix_PurgeElementAction extends BaseElementAction
{
    public function getName ()
    {
        return Craft::t('Imgix Purge');
    }

    public function isDestructive ()
    {
        return false;
    }

    public function performAction (ElementCriteriaModel $criteria)
    {
        craft()->tasks->createTask('Imgix_Purge', 'Purging images', [ 'assetIds' => $criteria->ids() ]);
        
        $this->setMessage(Craft::t('Purging images'));

        return true;
    }
}