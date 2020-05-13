<?php
namespace Deployer;

require 'recipe/common.php';

set('ssh_multiplexing', false);
set('default_stage', 'prod');

host('todonime.ru')
    ->stage('prod')
    ->user('deploy')
    ->identityFile('~/.ssh/id_rsa-deploy')
    ->set('repository', 'https://github.com/grutenko/todonime-service.git')
    ->set('branch', 'master')
    ->set('deploy_path', '/var/www/todonime.ru')
    ->set('shared_files', [
        '.env',
        'client/.env',
        'composer-lock.json',
        'client/package-lock.json'
    ])
    ->set('shared_dirs', [
        'storage',
        'vendor',
        'client/node_modules'
    ]);

task('deploy:install-composer', 'cd {{release_path}} && composer install --no-dev');
task('deploy:install-npm', 'cd {{release_path}}/client && npm i && npm run build');

task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:install-composer',
    'deploy:install-npm',
    'deploy:unlock',
    'cleanup',
    'success'
]);

after('deploy:failed', 'deploy:unlock');


