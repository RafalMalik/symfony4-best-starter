<?php
//namespace Deployer;
//
//require 'recipe/symfony.php';
//
//// Project name
//set('application', 'my_project');
//
//// Project repository
//set('repository', 'https://github.com/RafalMalik/symfony4-best-starter.git');
//
//// [Optional] Allocate tty for git clone. Default value is false.
//set('git_tty', true);
//
//// Shared files/dirs between deploys
//add('shared_files', []);
//add('shared_dirs', []);
//
//// Writable dirs by web server
//add('writable_dirs', []);
//
//
//// Hosts
//
//host('project.com')
//    ->set('deploy_path', '~/{{application}}');
//
//// Tasks
//
//task('build', function () {
//    run('cd {{release_path}} && build');
//});
//
//// [Optional] if deploy fails automatically unlock.
//after('deploy:failed', 'deploy:unlock');
//
//// Migrate database before symlink new release.
//
//before('deploy:symlink', 'database:migrate');



namespace Deployer;
require_once __DIR__ . '/common.php';
set('shared_dirs', ['var/log', 'var/sessions']);
set('shared_files', ['.env']);
set('writable_dirs', ['var']);
set('migrations_config', '');
set('bin/console', function () {
    return parse('{{bin/php}} {{release_path}}/bin/console --no-interaction');
});
desc('Migrate database');
task('database:migrate', function () {
    $options = '--allow-no-migration';
    if (get('migrations_config') !== '') {
        $options = sprintf('%s --configuration={{release_path}}/{{migrations_config}}', $options);
    }
    run(sprintf('{{bin/console}} doctrine:migrations:migrate %s', $options));
});
desc('Clear cache');
task('deploy:cache:clear', function () {
    run('{{bin/console}} cache:clear --no-warmup');
});
desc('Warm up cache');
task('deploy:cache:warmup', function () {
    run('{{bin/console}} cache:warmup');
});
desc('Deploy project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'deploy:cache:clear',
    'deploy:cache:warmup',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
]);
after('deploy', 'success');
