<?php

/*
 * This file is part of the nDeploy package.
 *
 * (c) Peter Buri <peter.buri@netpositive.hu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

include_once __DIR__.'/LockBaseTask.php';

/**
 * Class LockCheckTask
 */
class LockCheckTask extends LockBaseTask
{
    public function main()
    {
        $this->log("Checking lock file: ".$this->getLockFile());

        $currentData = $this->getCurrentData();
        $data = $this->getLockData();

        if (!is_array($data) || !isset($data['pid'])) {
            throw new \BuildException('Build is not locked!');
        } elseif ($data['pid'] != $currentData['pid']) {
            throw new \BuildException($this->formatText($this->getMessage(), $data));
        }

        return true;
    }
}
