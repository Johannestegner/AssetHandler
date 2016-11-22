<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AssetTest.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-20 - kl 9:06
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jitesoft\AssetHandler\Internal;

use PHPUnit_Framework_TestCase;

class AssetTest extends PHPUnit_Framework_TestCase {

    public function testGetPath() {
        $asset = new Asset("/assets/js", "file.js");
        $this->assertEquals("/assets/js", $asset->getPath());
    }

    public function testGetType() {
        $container = new AssetContainer("test");
        $asset     = new Asset("/", "file.js");
        $container->add($asset);
        $this->assertEquals("test", $asset->getType());
    }

    public function testGetName() {
        $asset = new Asset("/assets/js", "file.js");
        $this->assertEquals("file.js", $asset->getName());

    }

    public function testGetFullUrl() {
        // To set a full URL, a container is required.
        $container = new AssetContainer("/assets");
        $asset     = new Asset("/test.js", "test");
        $container->add($asset);

        $this->assertEquals(
            "/assets/test.js",
            $asset->getFullUrl()
        );
    }

    public function testGetContainer() {
        $container = new AssetContainer("scripts");
        $asset     = new Asset("test.js", "test");
        $this->assertNull($asset->getContainer());
        $asset2 = new Asset("test2.js", "test2", $container);
        $this->assertEquals($container, $asset2->getContainer());
    }

    public function testSetContainer() {
        $container = new AssetContainer("scripts");
        $asset     = new Asset("test.js", "test");
        $this->assertNull($asset->getContainer());
        $asset->setContainer($container);
        $this->assertEquals($container, $asset->getContainer());
    }
}
