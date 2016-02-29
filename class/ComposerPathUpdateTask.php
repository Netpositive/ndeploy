<?php

/**
 * This file is part of the nDeploy package.
 *
 * (c) Peter Buri <peter.buri@netpositive.hu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class ComposerPathUpdaterTask
 */
class ComposerPathUpdateTask extends Task
{
    const COMPOSER_JSON = 'composer.json';
    const COMPOSER_JSON_LOCK = 'composer.lock';
    const COMPOSER_REPOSITORY_TYPE_PATH = 'path';

    /**
     * Original source repository
     *
     * @var string
     */
    private $repositorydir;

    /**
     * Current release dir
     *
     * @var string
     */
    private $releasedir;

    /**
     * @var string
     */
    private $symlinkAllowed = false;

    public function main()
    {
        // Find the composer.json and composer.lock
        $composerFile = implode('/', array($this->releasedir, self::COMPOSER_JSON));
        $composerLockFile = implode('/', array($this->releasedir, self::COMPOSER_JSON_LOCK));

        if (!file_exists($composerFile)) {
            throw new BuildException('Composer JSON not found: ' . $composerFile);
        }

        // Read composer.json file content
        $composerConfig = json_decode(file_get_contents($composerFile), true);

        if (isset($composerConfig['repositories']) && count($composerConfig['repositories'])) {
            $changed = false;
            foreach ($composerConfig['repositories'] as $key => $repository) {
                if (isset($repository['type'])
                    && self::COMPOSER_REPOSITORY_TYPE_PATH == strtolower($repository['type'])
                    && isset($repository['url'])
                    && false !== strpos($repository['url'], '..')
                ) {
                    $url = realpath(implode('/', array($this->getRepositorydir(), $repository['url'])));
                    if (!$url) {
                        throw new BuildException('Composer path repository URL not found: ' . $url);
                    }

                    // Set the new url
                    $composerConfig['repositories'][$key]['url'] = $url;

                    // Set the symlink parameter
                    if (!isset($composerConfig['repositories'][$key]['options'])) {
                        $composerConfig['repositories'][$key]['options'] = array();
                    }
                    $composerConfig['repositories'][$key]['options']['symlink'] = $this->symlinkAllowed;

                    $changed = true;
                }
            }

            // If json file change we over write it
            if ($changed) {
                file_put_contents($composerFile, json_encode($composerConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
            }
        }

        if (!file_exists($composerLockFile)) {
            return;
        }

        // Read composer.lock file content
        $composerLockConfig = json_decode(file_get_contents($composerLockFile), true);

        if (isset($composerLockConfig['packages']) && count($composerLockConfig['packages'])) {
            $changed = false;

            foreach ($composerLockConfig['packages'] as $key => $package) {

                if (isset($package['dist']['type'])
                    && self::COMPOSER_REPOSITORY_TYPE_PATH == strtolower($package['dist']['type'])
                    && isset($package['dist']['url'])
                    && false !== strpos($package['dist']['url'], '..')
                ) {


                    $url = realpath(implode('/', array($this->getRepositorydir(), $package['dist']['url'])));
                    if (!$url) {
                        throw new BuildException('Composer path repository URL not found: ' . $url);
                    }

                    // Set the new url
                    $composerLockConfig['packages'][$key]['dist']['url'] = $url;

                    // Set the symlink parameter
                    if (!isset($composerLockConfig['packages'][$key]['transport-options'])) {
                        $composerLockConfig['packages'][$key]['transport-options'] = array();
                    }
                    $composerLockConfig['packages'][$key]['transport-options']['symlink'] = $this->symlinkAllowed;

                    $changed = true;
                }
            }

            // If json file change we over write it
            if ($changed) {
                file_put_contents($composerLockFile, json_encode($composerLockConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
            }

        }
    }

    /**
     * @return mixed
     */
    public function getRepositorydir()
    {
        return $this->repositorydir;
    }

    /**
     * @param mixed $repositorydir
     */
    public function setRepositorydir($repositorydir)
    {
        $this->repositorydir = rtrim($repositorydir, '/');
    }

    /**
     * @return mixed
     */
    public function getReleasedir()
    {
        return $this->releasedir;
    }

    /**
     * @param $releasedir
     */
    public function setReleasedir($releasedir)
    {
        $this->releasedir = rtrim($releasedir, '/');
    }

    /**
     * @return string
     */
    public function getSymlinkAllowed()
    {
        return $this->symlinkAllowed;
    }

    /**
     * @param string $symlinkAllowed
     */
    public function setSymlinkAllowed($symlinkAllowed)
    {
        $this->symlinkAllowed = $symlinkAllowed;
    }

}