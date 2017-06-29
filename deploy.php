<?php
namespace Deployer;
require 'recipe/common.php';
require 'recipe/magento.php';

date_default_timezone_set('Etc/UTC');
// Configuration

set('ssh_type', 'native');
set('ssh_multiplexing', true);

set('repository', 'https://github.com/pluckrose/deploy-tools');
set('shared_files', []);
set('shared_dirs', []);
set('writable_dirs', []);

// Servers

server('ECS Dev', 'ecsdev')
// ->user('username')
	->identityFile()
	->set('deploy_path', '/var/www/html/default/deployer-tools');

// Tasks

desc('Restart PHP-FPM service');
task('php-fpm:restart', function () {
		// The user must have rights for restart service
		// /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart php-fpm.service
		run('sudo systemctl restart php-fpm.service');
	});
after('deploy:symlink', 'php-fpm:restart');

desc('Deploy your project');
task('deploy', [
		'deploy:prepare',
		'deploy:lock',
		'deploy:release',
		'deploy:update_code',
		'deploy:shared',
		'deploy:writable',
		'deploy:vendors',
		'deploy:clear_paths',
		'deploy:symlink',
		'deploy:unlock',
		'cleanup',
		'success'
	]);

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');