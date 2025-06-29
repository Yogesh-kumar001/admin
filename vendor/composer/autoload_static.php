<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6daa06d829978ae8515e3bcbbe349620
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Awvisualmerchandising\\Controller\\' => 33,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Awvisualmerchandising\\Controller\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Controller',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6daa06d829978ae8515e3bcbbe349620::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6daa06d829978ae8515e3bcbbe349620::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6daa06d829978ae8515e3bcbbe349620::$classMap;

        }, null, ClassLoader::class);
    }
}
