<?php
/**
 * Clone command for producer.
 *
 * PHP version 5
 *
 * @category   ProducerCommand
 *
 * @author     Francesco Bianco <bianco@javanile.org>
 * @license    https://goo.gl/KPZ2qI  MIT License
 * @copyright  2015-2017 Javanile.org
 */

namespace Javanile\Producer\Commands;

class CloneCommand extends Command
{
    /**
     * @var boolean
     */
    protected $noMount = false;

    /**
     * @var array
     */
    protected $devPackages = [
        'javanile/producer',
    ];

    /**
     * CloneCommand constructor.
     *
     * @param $cwd
     */
    public function __construct($cwd)
    {
        parent::__construct($cwd);
    }

    /**
     * Run clone command.
     *
     * @param $args
     *
     * @return string
     */
    public function run($args)
    {
        if (!isset($args[0]) || !$args[0]) {
            return $this->error('&require-package-or-repository');
        }

        $args = $this->parseArgs($args);

        $this->noMount = in_array('--no-mount', $args);
        if ($this->noMount) {
            $args = array_values(array_diff($args, ['--no-mount']));
        }

        if (isset($args[0]) && $this->isUrl($args[0])) {
            return $this->cloneByRepositoryUrl($args);
        }

        if (isset($args[0]) && $this->isPackageName($args[0])) {
            return $this->cloneByPackageName($args);
        }

        return "> Producer: Malformed url or package name.\n";
    }

    /**
     * Clone repository by url.
     *
     * @param mixed $args
     */
    private function cloneByRepositoryUrl($args)
    {
        $repositoryUrl = $args[0];
        $projectName = isset($args[1]) ? $args[1] : $this->getProjectNameByRepositoryUrl($repositoryUrl);

        if (!$this->existsRepositoryUrl($repositoryUrl)) {
            return "Repository url not exists!";
        }

        if ($this->existsProjectName($projectName)) {
            return "> Producer: Project '{$this->projectsDir}/{$projectName}' already exists during clone.\n";
        }

        $this->info("Clone by repository url '{$repositoryUrl}'");
        $this->exec('clone', 'clone-by-repository-url', [$repositoryUrl, $projectName]);

        if ($this->noMount) {
            return;
        }

        $packageName = $this->getProjectPackageName($projectName, $repositoryUrl);

        if (!$this->existsPackageName($packageName)) {
            return $this->exec('clone', 'mount-unknown-package-as-project', [$packageName, $projectName]);
        }

        if (!$this->existsRootComposerJson()) {
            $this->createRootComposerJson();
        }

        return $this->exec('clone', 'mount-require-package-as-project', [$packageName, $projectName]);
    }

    /**
     * Clone repository by package name.
     *
     * @param mixed $args
     */
    private function cloneByPackageName($args)
    {
        $packageName = $args[0];
        if (!$this->existsPackageName($packageName)) {
            $args[0] = 'https://github.com/' . $packageName;
            return $this->cloneByRepositoryUrl($args);
        }

        $projectName = isset($args[1]) ? $args[1] : basename($packageName);
        if ($this->existsProjectName($projectName)) {
            return "> Producer: Project directory '{$this->projectsDir}/{$projectName}' already exists.\n";
        }

        if (!$this->existsRootComposerJson()) {
            $this->createRootComposerJson();
        }

        $devFlag = in_array($packageName, $this->devPackages) ? '--dev' : '';
        $this->exec('clone', 'require-package', [$packageName, $devFlag]);

        $composerJson = $this->cwd . '/vendor/' . $packageName . '/composer.json';
        if (!file_exists($composerJson)) {
            return "> Producer: Package not found.\n";
        }

        $json = json_decode(file_get_contents($composerJson));
        $repositoryUrl = null;
        if (isset($json->repositories)) {
            foreach ($json->repositories as $item) {
                if ($item->type == 'git') {
                    $repositoryUrl = $item->url;
                    break;
                }
            }
        }

        if (!$repositoryUrl) {
            return "> Producer: Repository not found on composer.json.\n";
        }

        if (!$this->existsRepositoryUrl($repositoryUrl)) {
            return "Repository url not exists!";
        }

        return $this->exec('clone', 'clone', [$repositoryUrl, $packageName, $projectName]);
    }
}
