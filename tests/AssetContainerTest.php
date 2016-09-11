<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AssetContainerTest.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-20 - kl 8:29
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jite\AssetHandler;

use PHPUnit_Framework_TestCase;

class AssetContainerTest extends PHPUnit_Framework_TestCase {

    /** @var AssetContainer */
    protected $container;

    public function setUp() {
        $this->container = new AssetContainer("container", "/assets");
    }

    public function testAddOne() {
        $this->assertTrue($this->container->add(new Asset("", "")));
        $this->assertCount(1, $this->container);
    }

    public function testAddMultiple() {
        $this->assertTrue($this->container->add(new Asset("1", "1")));
        $this->assertTrue($this->container->add(new Asset("2", "2")));
        $this->assertCount(2, $this->container);
    }

    public function testAddExisting() {
        $a1 = new Asset("1", "1");
        $a2 = new Asset("1", "1");

        $this->assertTrue($this->container->add($a1));
        $this->assertFalse($this->container->add($a2));
        $this->assertFalse($this->container->add($a1));
        $this->assertCount(1, $this->container);
    }

    public function testRemoveFromOne() {
        $a1 = new Asset("", "");

        $this->container->add($a1);
        $this->assertCount(1, $this->container);
        $this->assertTrue($this->container->remove($a1));
        $this->assertCount(0, $this->container);
    }


    public function testRemoveAllWithOne() {
        $a1 = new Asset("", "");

        $this->container->add($a1);
        $this->assertCount(1, $this->container);
        $this->container->removeAll();
        $this->assertCount(0, $this->container);
    }

    public function testRemoveAllWithMultiple() {
        $this->container->add(new Asset("1", "1"));
        $this->container->add(new Asset("2", "2"));
        $this->container->add(new Asset("3", "3"));
        $this->assertCount(3, $this->container);
        $this->container->removeAll();
        $this->assertCount(0, $this->container);
    }

    public function testRemoveThenAdd() {
        $a1 = new Asset("1", "1");
        $a2 = new Asset("2", "2");

        $this->container->add($a1);
        $this->assertCount(1, $this->container);
        $this->assertTrue($this->container->remove($a1));
        $this->assertCount(0, $this->container);
        $this->assertTrue($this->container->add($a1));
        $this->assertTrue($this->container->add($a2));
        $this->assertCount(2, $this->container);
    }

    public function testFindWithExisting() {
        $a1 = new Asset("1", "1");
        $a2 = new Asset("2", "2");

        $this->container->add($a1);
        $this->container->add($a2);

        $outAsset = $this->container->find(function(Asset $a) {
            $result = $a->getName() === "1";
            return $result;
        });

        $this->assertSame($a1, $outAsset);
    }

    public function testFindWithoutExisting() {
        $a1 = new Asset("1", "1");
        $a2 = new Asset("2", "2");

        $this->container->add($a1);
        $this->container->add($a2);

        $outAsset = $this->container->find(function(Asset $a) {
            $result = $a->getName() === "3";
            return $result;
        });

        $this->assertNull($outAsset);
    }

    public function testCount() {
        $this->assertEmpty($this->container);
        $this->container->add(new Asset("2", "3"));
        $this->assertCount(1, $this->container);
        $this->container->add(new Asset("2", "1"));
        $this->assertSame(2, $this->container->count());
        $this->container->removeAll();
        $this->assertEmpty($this->container);
    }

    public function testExists() {
        $a1 = new Asset("2", "3");
        $this->assertFalse($this->container->exists($a1));
        $this->container->add($a1);
        $this->assertTrue($this->container->exists($a1));
    }

    public function testForEach() {
        $this->container->add(new Asset("2", "3"));
        $this->container->add(new Asset("3", "2"));
        $this->container->add(new Asset("3", "3"));

        $index = 0;
        foreach ($this->container as $asset) {
            $this->assertInstanceOf(Asset::class, $asset);
            $index++;
        }

        $this->assertEquals($this->container->count(), $index);
    }

    public function testGetBaseUrl() {
        $container = new AssetContainer("container", "/abc/def");
        $this->assertEquals("/abc/def", $container->getBaseUrl());
    }

    public function testSetBaseUrl() {
        $this->container->setBaseUrl("/passets");
        $this->assertEquals("/passets", $this->container->getBaseUrl());
    }

    public function tetSetGetBasePath() {
        $container = new AssetContainer("t", "", "/def/abc/");
        $this->assertEquals("/def/abc/", $container->getBasePath());
        $container->setBasePath("/abc/def");
        $this->assertEquals("/abc/def", $container->getBasePath());
    }

    public function testGetPrintPattern() {
        $container = new AssetContainer("", "", "", '<a href="{{URL}}">test</a>');
        $this->assertEquals('<a href="{{URL}}">test</a>', $container->getPrintPattern());
    }

    public function testGetFileRegex() {
        $container = new AssetContainer("", "", "", "", "/\\.(jpg|jpeg|tiff|gif|png|bmp|ico)$/i");
        $this->assertEquals("/\\.(jpg|jpeg|tiff|gif|png|bmp|ico)$/i", $container->getFileRegex());
    }

    public function testGetType() {
        $container = new AssetContainer("Scripts");
        $this->assertEquals("Scripts", $container->getType());
    }

}
