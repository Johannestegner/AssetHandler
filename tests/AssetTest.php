<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AssetTest.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-20 - kl 9:06
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jite\AssetHandler;

use PHPUnit_Framework_TestCase;

class AssetTest extends PHPUnit_Framework_TestCase {

    public function testGetPath() {
        $asset = new Asset("javascript", "/assets/js", "file.js");
        $this->assertEquals("/assets/js", $asset->getPath());
    }

    public function testGetType() {
        $asset = new Asset("javascript", "/", "file.js");
        $this->assertEquals("javascript", $asset->getType());
    }

    public function testGetName() {
        $asset = new Asset("javascript", "/assets/js", "file.js");
        $this->assertEquals("file.js", $asset->getName());

    }

    public function testGetFullUrl() {
        // To set a full URL, a container is required.
        $container = new AssetContainer("/assets");
        $asset     = new Asset("scripts", "/test.js", "test");
        $container->add($asset);

        $this->assertEquals(
            "/assets/test.js",
            $asset->getFullUrl()
        );
    }

    public function testGetContainer() {
        $container = new AssetContainer();
        $asset     = new Asset("scripts", "test.js", "test");
        $this->assertNull($asset->getContainer());
        $asset2 = new Asset("scripts", "test2.js", "test2", $container);
        $this->assertEquals($container, $asset2->getContainer());
    }

    public function testSetContainer() {
        $container = new AssetContainer();
        $asset     = new Asset("scripts", "test.js", "test");
        $this->assertNull($asset->getContainer());
        $asset->setContainer($container);
        $this->assertEquals($container, $asset->getContainer());
    }
}
