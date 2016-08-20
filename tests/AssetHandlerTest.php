<?php

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AssetHandlerTest.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-09 - kl 22:45
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jite\AssetHandler;

use Jite\AssetHandler\Exceptions\AssetNameNotUniqueException;
use Jite\AssetHandler\Exceptions\InvalidContainerException;
use Jite\AssetHandler\Types\AssetTypes;
use PHPUnit_Framework_TestCase;

class AssetHandlerTest extends PHPUnit_Framework_TestCase {

    /** @var AssetHandler */
    protected $handler;

    public function setUp() {
        $this->handler = new AssetHandler();
    }

    public function testAddWithNameAndContainer() {
        $this->assertTrue($this->handler->add("test.js", "test", AssetTypes::SCRIPT));
    }

    public function testAddWithNameNoContainer() {
        $this->assertTrue($this->handler->add("test.js", "test"));

    }

    public function testAddWithoutNameOrContainer() {
        $this->assertTrue($this->handler->add("test.js"));
    }

    public function testAddFailNoneUniqueName() {
        $this->assertTrue($this->handler->add("test.js", "test", AssetTypes::SCRIPT));
        $this->setExpectedException(
            AssetNameNotUniqueException::class,
            'An asset with the name test already exists in the container (' . AssetTypes::SCRIPT .')'
        );
        $this->handler->add("test.js", "test", AssetTypes::SCRIPT);

        // Make sure that it blows up when not passing the container, so that the Add method will place the asset right.
        $this->setExpectedException(
            AssetNameNotUniqueException::class,
            'An asset with the name test already exists in the container (' . AssetTypes::SCRIPT .')'
        );
        $this->handler->add("test.js", "test");
    }

    public function testAddFailWithInvalidContainer() {
        $this->setExpectedException(
            InvalidContainerException::class,
            'No container with name "test" found in the asset handler.'
        );

        $this->handler->add("test.js", "test", "test");
    }

    public function testAddFailWithContainerNotPossibleToDetermine() {
        $this->setExpectedException(
            InvalidContainerException::class,
            "Could not determine Container from the asset path (test.sdfgf)."
        );

        $this->handler->add("test.sdfgf", "test");
    }

    public function testAddMultiple() {
        $this->assertTrue($this->handler->add("test1.js", "test1", AssetTypes::SCRIPT));
        $this->assertTrue($this->handler->add("test2.js", "test2", AssetTypes::SCRIPT));
        $this->assertTrue($this->handler->add("test3.js", "test3", AssetTypes::SCRIPT));
        $this->assertTrue($this->handler->add("test4.js", "test4", AssetTypes::SCRIPT));
        $this->assertTrue($this->handler->add("test1.js", "test5", AssetTypes::SCRIPT));
    }

}
