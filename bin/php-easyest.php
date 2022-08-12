<?php declare(strict_types=1);

ini_set('zend.assertions', '1');
ini_set('assert.exception', '1');

if (isset($argv[1]) && $argv[1] === 'help') {
	echo "Usage: php-easyest.php <test-directory> [<bootstrap-script>]\n";
	echo "  <test-directory> is the directory containing the tests.\n";
	echo "  <bootstrap-script> is the path to a bootstrap script.\n";
	echo "  Default test directory is './tests'.\n";
	echo "  Default bootstrap script is './vendor/autoload.php'.\n";
	exit(1);
}

require_once dirname(__DIR__) . '/vendor/autoload.php';

$argumentParser = new \PhpEasyest\ArgumentParser();
$argumentParser->parse($argv);

$runner = new \PhpEasyest\Runner();
$runner->run($argumentParser->getTestsPath(), $argumentParser->getBootstrapPath());

