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

class Imgix_PurgeTask extends BaseTask
{
    /**
     * @access protected
     * @return array
     */

    protected function defineSettings ()
    {
        return array(
            'assetIds' => AttributeType::Mixed,
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
        return count($this->getSettings()->assetIds);
    }

    /**
     * @param int $step
     *
     * @return bool
     */
    public function runStep ($step)
    {
        $asset = craft()->assets->getFileById($this->getSettings()->assetIds[ $step ]);

        if ( $asset ) {
            craft()->imgix->purge($asset);
        }

        return true;
    }
}