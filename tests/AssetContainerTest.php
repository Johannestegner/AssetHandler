<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AssetContainerTest.php - Part of the AssetHandler project.

  File created by Simius at 2016-08-20 - kl 8:29
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jite\AssetHandler;

use PHPUnit_Framework_TestCase;

class AssetContainerTest extends PHPUnit_Framework_TestCase {

    /** @var AssetContainer */
    protected $container;

    public function setUp() {
        $this->container = new AssetContainer();
    }

    public function testAddOne() {
        $this->assertTrue($this->container->add(new Asset("", "", "")));
        $this->assertCount(1, $this->container);
    }

    public function testAddMultiple() {
        $this->assertTrue($this->container->add(new Asset("1", "1", "1")));
        $this->assertTrue($this->container->add(new Asset("2", "2", "2")));
        $this->assertCount(2, $this->container);
    }

    public function testAddExisting() {
        $a1 = new Asset("1", "1", "1");
        $a2 = new Asset("1", "1", "1");

        $this->assertTrue($this->container->add($a1));
        $this->assertFalse($this->container->add($a2));
        $this->assertFalse($this->container->add($a1));
        $this->assertCount(1, $this->container);
    }

    public function testRemoveFromOne() {
        $a1 = new Asset("", "", "");

        $this->container->add($a1);
        $this->assertCount(1, $this->container);
        $this->assertTrue($this->container->remove($a1));
        $this->assertCount(0, $this->container);
    }


    public function testRemoveAllWithOne() {
        $a1 = new Asset("", "", "");

        $this->container->add($a1);
        $this->assertCount(1, $this->container);
        $this->container->removeAll();
        $this->assertCount(0, $this->container);
    }

    public function testRemoveAllWithMultiple() {
        $this->container->add(new Asset("1", "1", "1"));
        $this->container->add(new Asset("2", "2", "2"));
        $this->container->add(new Asset("3", "3", "3"));
        $this->assertCount(3, $this->container);
        $this->container->removeAll();
        $this->assertCount(0, $this->container);
    }

    public function testRemoveThenAdd() {
        $a1 = new Asset("1", "1", "1");
        $a2 = new Asset("2", "2", "2");

        $this->container->add($a1);
        $this->assertCount(1, $this->container);
        $this->assertTrue($this->container->remove($a1));
        $this->assertCount(0, $this->container);
        $this->assertTrue($this->container->add($a1));
        $this->assertTrue($this->container->add($a2));
        $this->assertCount(2, $this->container);
    }

    public function testFindWithExisting() {
        $a1 = new Asset("1", "1", "1");
        $a2 = new Asset("2", "2", "2");

        $this->container->add($a1);
        $this->container->add($a2);

        $outAsset = $this->container->find(function(Asset $a) {
            $result = $a->getType() === "1";
            return $result;
        });

        $this->assertSame($a1, $outAsset);
    }

    public function testFindWithoutExisting() {
        $a1 = new Asset("1", "1", "1");
        $a2 = new Asset("2", "2", "2");

        $this->container->add($a1);
        $this->container->add($a2);

        $outAsset = $this->container->find(function(Asset $a) {
            $result = $a->getType() === "3";
            return $result;
        });

        $this->assertNull($outAsset);
    }

    public function testCount() {
        $this->assertEmpty($this->container);
        $this->container->add(new Asset("1", "2", "3"));
        $this->assertCount(1, $this->container);
        $this->container->add(new Asset("3", "2", "1"));
        $this->assertSame(2, $this->container->count());
        $this->container->removeAll();
        $this->assertEmpty($this->container);
    }

    public function testExists() {
        $a1 = new Asset("1", "2", "3");
        $this->assertFalse($this->container->exists($a1));
        $this->container->add($a1);
        $this->assertTrue($this->container->exists($a1));
    }

    public function testForEach() {
        $this->container->add(new Asset("1", "2", "3"));
        $this->container->add(new Asset("1", "3", "2"));
        $this->container->add(new Asset("2", "3", "3"));

        $index = 0;
        foreach ($this->container as $asset) {
            $index++;
        }

        $this->assertEquals($this->container->count(), $index);
    }
}
