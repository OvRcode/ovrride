<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit049932a5882198485a0c54052e868d83
{
    public static $files = array (
        '7166494aeff09009178f278afd86c83f' => __DIR__ . '/..' . '/yahnis-elsts/plugin-update-checker/load-v4p13.php',
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit049932a5882198485a0c54052e868d83::$classMap;

        }, null, ClassLoader::class);
    }
}
