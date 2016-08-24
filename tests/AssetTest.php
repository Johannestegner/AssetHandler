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

}
