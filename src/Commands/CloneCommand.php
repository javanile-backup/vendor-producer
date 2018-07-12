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
        $projectName = isset($args[1]) ? $args[1] : $this->getProjectNameByUrl($repositoryUrl);

        if ($this->existsProjectName($projectName)) {
            return "> Producer: Project 'packages/{$projectName}' already exists during clone.\n";
        }

        $this->info("Clone by repository url '{$repositoryUrl}'");
        $this->exec('clone', 'clone-by-repository-url', [$repositoryUrl, $projectName]);

        if ($this->noMount) {
            return;
        }

        if ($this->hasComposerJson($projectName)) {
            $packageName = $this->getPackageNameByComposerJson($projectName);
            if (!$this->existsPackageName($packageName)) {
                return $this->error('&package-name-not-exists', ['packageName' => $packageName]);
            }
            if (!$this->existsRootComposerJson()) {
                $this->createRootComposerJson();
            }

            return $this->exec('clone', 'mount-package-to-project', [$packageName, $projectName]);
        }

        $packageName = $this->getPackageNameByUrl($repositoryUrl);
        if (!$this->existsPackageName($packageName)) {
            return $this->error('&package-name-not-exists', ['packageName' => $packageName]);
        }

        return $this->exec('clone-mount', [$packageName, $projectName]);
    }

    /**
     * Clone repository by package name.
     *
     * @param mixed $args
     */
    private function cloneByPackageName($args)
    {
        $repo = $args[0];

        $dev = in_array($repo, $this->devPackages) ? '--dev' : '';

        $this->exec('clone-require', [$repo, $dev]);

        $comp = $this->cwd.'/vendor/'.$repo.'/composer.json';
        if (!file_exists($comp)) {
            return "> Producer: Package not found.\n";
        }

        $json = json_decode(file_get_contents($comp));
        $pack = $repo;
        $repo = null;

        if (isset($json->repositories)) {
            foreach ($json->repositories as $item) {
                if ($item->type == 'git') {
                    $repo = $item->url;
                    break;
                }
            }
        }

        if ($repo) {
            $name = isset($args[1]) ? $args[1] : basename($repo, '.git');

            //
            if (is_dir($this->cwd.'/repository/'.$name)) {
                return "> Producer: Project directory 'repository/{$name}' already exists.\n";
            }

            return $this->exec('clone-complete', [$repo, $name, $pack]);
        } else {
            return "> Producer: Repository not found on composer.json.\n";
        }
    }
}
