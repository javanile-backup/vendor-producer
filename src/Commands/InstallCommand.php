<?php
/**
 * Install command for producer.
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

class InstallCommand extends Command
{
    /**
     * InitCommand constructor.
     *
     * @param $cwd
     */
    public function __construct($cwd)
    {
        parent::__construct($cwd);
    }

    /**
     * Run install command.
     *
     * @param $args
     *
     * @return string
     */
    public function run($args)
    {
        file_put_contents(
            "producer",
            "<?php global \$argv;\n".
            "require_once 'vendor/autoload.php';\n".
            "Javanile\\Producer\\Producer::cli(\$argv);\n"
        );

        return $this->info('Installation complete.");
    }
}
