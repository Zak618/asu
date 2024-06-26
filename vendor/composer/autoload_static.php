<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitdfae6eae8463eeffe6b270961b725b13
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitdfae6eae8463eeffe6b270961b725b13::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitdfae6eae8463eeffe6b270961b725b13::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitdfae6eae8463eeffe6b270961b725b13::$classMap;

        }, null, ClassLoader::class);
    }
}
