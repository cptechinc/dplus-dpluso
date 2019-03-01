<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitde55f17c88d493db7f2fa2056536094d
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'Dplus\\Dpluso\\UserActions\\' => 25,
            'Dplus\\Dpluso\\OrderDisplays\\' => 27,
            'Dplus\\Dpluso\\Model\\' => 19,
            'Dplus\\Dpluso\\Items\\' => 19,
            'Dplus\\Dpluso\\General\\' => 21,
            'Dplus\\Dpluso\\Customer\\' => 22,
            'Dplus\\Dpluso\\Configs\\' => 21,
            'Dplus\\Dpluso\\Bookings\\' => 22,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Dplus\\Dpluso\\UserActions\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/UserActions',
        ),
        'Dplus\\Dpluso\\OrderDisplays\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/OrderDisplays',
        ),
        'Dplus\\Dpluso\\Model\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Model',
        ),
        'Dplus\\Dpluso\\Items\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Items',
        ),
        'Dplus\\Dpluso\\General\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/General',
        ),
        'Dplus\\Dpluso\\Customer\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Customer',
        ),
        'Dplus\\Dpluso\\Configs\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Configs',
        ),
        'Dplus\\Dpluso\\Bookings\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Bookings',
        ),
    );

    public static $classMap = array (
        'Contact' => __DIR__ . '/../..' . '/src/Customer/Contact.class.php',
        'Customer' => __DIR__ . '/../..' . '/src/Customer/Customer.class.php',
        'LogmUser' => __DIR__ . '/../..' . '/src/Model/LogmUser.class.php',
        'NonExistingCustomer' => __DIR__ . '/../..' . '/src/Customer/Customer.class.php',
        'UserAction' => __DIR__ . '/../..' . '/src/UserActions/UserAction.class.php',
        'Vendor' => __DIR__ . '/../..' . '/src/Model/Vendor.class.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitde55f17c88d493db7f2fa2056536094d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitde55f17c88d493db7f2fa2056536094d::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitde55f17c88d493db7f2fa2056536094d::$classMap;

        }, null, ClassLoader::class);
    }
}
