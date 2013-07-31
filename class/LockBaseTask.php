<?php

/*
 * This file is part of the nDeploy package.
 *
 * (c) Peter Buri <peter.buri@netpositive.hu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class LockBaseTask
 */
abstract class LockBaseTask extends Task
{
    const PROPERTY_LOCK_FORCE = 'lock.force';

    /**
     * @var string
     */
    private $lockFile = '';

    /**
     * @var string
     */
    private $message = 'Project is locked by @@pid@@ process, started at @@date@@, @@diff@@ ago.';

    /**
     * @param string $lockFile
     */
    public function setLockFile($lockFile)
    {
        $this->lockFile = $lockFile;
    }

    /**
     * @return string
     */
    public function getLockFile()
    {
        return $this->lockFile;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Returns current data
     *
     * @return array
     */
    protected function getCurrentData()
    {
        return array(
            'date' => date('Y-m-d H:i:s'),
            'pid'  => getmypid()
        );
    }

    /**
     * Create lock
     *
     * @return array
     */
    protected function createLock()
    {
        $data = $this->getCurrentData();

        if (!file_exists(dirname($this->getLockFile()))) {
            mkdir(dirname($this->getLockFile()), 0777, true);
        }

        file_put_contents(
            $this->getLockFile(),
            json_encode(
                $data
            )
        );

        return $data;
    }

    /**
     * Remove lock
     */
    protected function removeLock()
    {
        if (file_exists($this->getLockFile()))
        {
            unlink($this->getLockFile());
        }
    }

    /**
     * Returns lock data
     *
     * @return array
     */
    protected function getLockData()
    {
        $data = array();
        if (file_exists($this->getLockFile()))
        {
            $data = json_decode(
                file_get_contents(
                    $this->getLockFile()
                ),
                true
            );

            // Calculate date time interval

            $startedDateTime = new \DateTime($data['date']);
            $currentDateTime = new \DateTime();
            $interval = $startedDateTime->diff($currentDateTime);

            /**
             * http://www.php.net/manual/en/dateinterval.format.php#96768
             * baptiste.place@utopiaweb.fr
             *
             */
            $format = array();
            $formatString = '0 second';
            $doPlural = function($nb,$str){return $nb>1?$str.'s':$str;}; // adds plurals

            if($interval->y !== 0) {
                $format[] = "%y ".$doPlural($interval->y, "year");
            }
            if($interval->m !== 0) {
                $format[] = "%m ".$doPlural($interval->m, "month");
            }
            if($interval->d !== 0) {
                $format[] = "%d ".$doPlural($interval->d, "day");
            }
            if($interval->h !== 0) {
                $format[] = "%h ".$doPlural($interval->h, "hour");
            }
            if($interval->i !== 0) {
                $format[] = "%i ".$doPlural($interval->i, "minute");
            }
            if($interval->s !== 0) {
                $format[] = "%s ".$doPlural($interval->s, "second");
            }

            // We use the two biggest parts
            if(count($format) > 1) {
                $formatString = array_shift($format)." and ".array_shift($format);
            } elseif (count($format)) {
                $formatString = array_pop($format);
            }

            $data['diff'] = $interval->format($formatString);

        }
        return $data;
    }

    protected function formatText($text, $tokens)
    {
        $realTokens = array();
        foreach($tokens as $key=>$value) {
            $realTokens["@@{$key}@@"] = $value;
        }
        return strtr($text, $realTokens);
    }
}