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
 * Class LockBaseTask
 */
class LockInitTask extends LockBaseTask
{
    private $force = false;

    /**
     * @param boolean $force
     */
    public function setForce($force)
    {
        $this->force = $force;
    }

    /**
     * @return boolean
     */
    public function getForce()
    {
        return $this->force;
    }

    public function main()
    {
        $this->log("Creating lock file: ".$this->getLockFile());

        if (!file_exists($this->getLockFile()) || $this->getForce()) {
            $this->createLock();
        } else {
            $data =  $this->getLockData();

            $echo = new EchoTask();
            $echo->setProject($this->getProject());
            $echo->setTaskName($this->getTaskName());
            $echo->setLevel(Project::MSG_ERR);
            $echo->setMessage($this->formatText($this->getMessage(), $data));
            $echo->main();

            $input = new InputTask();
            $input->setProject($this->getProject());
            $input->setTaskName($this->getTaskName());
            $input->setPropertyName(self::PROPERTY_LOCK_FORCE);
            $input->setMessage('Force deploy');
            $input->setPromptChar('?');
            $input->setValidargs('y,n');
            $input->setDefaultValue('y');
            $input->main();

            $inputValue = $this->getProject()->getProperty(self::PROPERTY_LOCK_FORCE);

            if ($inputValue == 'y')
            {
                $this->createLock();
            } else {
                throw new \BuildException("Another build is running.");
            }
        }
    }
}