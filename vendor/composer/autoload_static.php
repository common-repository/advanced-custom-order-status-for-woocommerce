<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5ddf0763c89dab523a4b19ddb5bd4927
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'StorePlugin\\CustomOrderStatus\\' => 30,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'StorePlugin\\CustomOrderStatus\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5ddf0763c89dab523a4b19ddb5bd4927::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5ddf0763c89dab523a4b19ddb5bd4927::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit5ddf0763c89dab523a4b19ddb5bd4927::$classMap;

        }, null, ClassLoader::class);
    }
}