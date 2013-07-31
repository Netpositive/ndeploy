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
 * Class KeepReleaseTask
 *
 * @see DeleteTask
 */
class KeepReleaseTask extends Task
{
    protected $keep = null;
    protected $filesets = array();

    public function setKeep($keep)
    {
        if ($keep < 2) {
            $keep = 2;
        }
        $this->keep = $keep;
    }

    function addFileSet(FileSet $fs) {
        $this->filesets[] = $fs;
    }

    public function main()
    {
        if ($this->keep) {

            // delete the dirs in the filesets
            foreach($this->filesets as $fs) {
                try {
                    $ds = $fs->getDirectoryScanner($this->project);
                    $dirs = $ds->getIncludedDirectories();

                    if (count($dirs) > $this->keep) {
                        sort($dirs);
                        $dirs = array_slice($dirs, 0, -$this->keep);

                        if (count($dirs) > 0) {
                            $dirCount = 0;
                            for ($j=count($dirs)-1; $j>=0; --$j) {
                                $dir = new PhingFile($fs->getDir($this->project), $dirs[$j]);

                                $dt = new DeleteTask();
                                $dt->setProject($this->getProject());
                                $dt->setTaskName($this->getTaskName());
                                $dt->setDir($dir);
                                $dt->setIncludeEmptyDirs(true);
                                $dt->main();

                                $dirCount++;
                            }
                            if ($dirCount > 0) {
                                $this->log("Deleted $dirCount director" . ($dirCount==1 ? "y" : "ies") . " from releases");
                            }
                        }
                    }

                } catch (BuildException $be) {
                    // directory doesn't exist or is not readable
                    $this->log($be->getMessage(), Project::MSG_WARN);
                }
            }
        }
    }
}