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
 * Class LockRemoveTask
 */
class LockRemoveTask extends LockBaseTask
{
    public function main()
    {
        $this->log("Removing lock file: ".$this->getLockFile());
        $this->removeLock();
    }
}