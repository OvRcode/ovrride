<?php return array(
    'root' => array(
        'name' => 'facebookincubator/facebook-for-woocommerce',
        'pretty_version' => 'dev-release/3.0.24',
        'version' => 'dev-release/3.0.24',
        'reference' => '04d88256dfb22a41476b7780371c660a7c07cce0',
        'type' => 'wordpress-plugin',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => false,
    ),
    'versions' => array(
        'composer/installers' => array(
            'pretty_version' => 'v1.12.0',
            'version' => '1.12.0.0',
            'reference' => 'd20a64ed3c94748397ff5973488761b22f6d3f19',
            'type' => 'composer-plugin',
            'install_path' => __DIR__ . '/./installers',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'facebookincubator/facebook-for-woocommerce' => array(
            'pretty_version' => 'dev-release/3.0.24',
            'version' => 'dev-release/3.0.24',
            'reference' => '04d88256dfb22a41476b7780371c660a7c07cce0',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'roundcube/plugin-installer' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
        'shama/baton' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
        'woocommerce/action-scheduler-job-framework' => array(
            'pretty_version' => '2.0.0',
            'version' => '2.0.0.0',
            'reference' => 'b0b21b9cc87e476ba7f8817050b39274ea7d6732',
            'type' => 'library',
            'install_path' => __DIR__ . '/../woocommerce/action-scheduler-job-framework',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
    ),
);
