<?php
/**
 * Imgix plugin for Craft CMS
 *
 * Imgix Task
 *
 * @author    Fred Carlsen
 * @copyright Copyright (c) 2016 Fred Carlsen
 * @link      http://sjelfull.no
 * @package   Imgix
 * @since     1.0.0
 */

namespace Craft;

class Imgix_PurgeUrlTask extends BaseTask
{
    /**
     * @access protected
     * @return array
     */

    protected function defineSettings ()
    {
        return array(
            'urls' => AttributeType::Mixed,
        );
    }

    /**
     * @return string
     */
    public function getDescription ()
    {
        return 'Imgix Purge';
    }

    /**
     * @return int
     */
    public function getTotalSteps ()
    {
        return count($this->getSettings()->urls);
    }

    /**
     * @param int $step
     *
     * @return bool
     */
    public function runStep ($step)
    {
        craft()->imgix->purgeUrl($this->getSettings()->urls[ $step ]);

        return true;
    }
}