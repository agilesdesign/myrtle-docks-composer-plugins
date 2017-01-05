<?php

namespace Myrtle\Composer\Docks\Plugins;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Illuminate\Contracts\Console\Kernel;
use Myrtle\Docks\Facades\Docks;
use Myrtle\Permissions\Models\Ability;

class DocksInstaller implements PluginInterface, EventSubscriberInterface
{
    protected $laravel;

    protected $kernel;

    public function activate(Composer $composer, IOInterface $io)
    {
        require dirname(__DIR__, 5) . '/bootstrap/autoload.php';

        $this->laravel = require_once dirname(__DIR__, 5) . '/bootstrap/app.php';

        $this->laravel->make(Kernel::class)->bootstrap();
    }

    public static function getSubscribedEvents()
    {
        return array(
            'post-install-cmd' => 'installOrUpdate',
            'post-update-cmd' => 'installOrUpdate',
            'post-autoload-dump' => 'installOrUpdate',
        );
    }

    public function installOrUpdate($event)
    {
        Docks::all()->each(function($dock, $key)
        {
            $dock->abilitiesCoreDictionary()->each(function($description, $name){
                Ability::updateOrCreate(['name' => $name]);
            });
        });
    }
}