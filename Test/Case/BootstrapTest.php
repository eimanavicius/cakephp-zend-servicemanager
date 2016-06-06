<?php

use Interop\Container\ContainerInterface;

class BootstrapTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		parent::setUp();
		Configure::delete(ContainerInterface::CLASS);
		require dirname(dirname(__DIR__)) . '/Config/bootstrap.php';
	}

	public function testSetsContainerConfigurationFilePath() {
		$this->assertStringEndsWith('/Config/container.php', Configure::read(ContainerInterface::CLASS));
	}
}
