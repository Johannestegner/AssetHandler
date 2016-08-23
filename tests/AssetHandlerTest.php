<?php

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AssetHandlerTest.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-09 - kl 22:45
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jite\AssetHandler;

use Jite\AssetHandler\Exceptions\AssetNameNotUniqueException;
use Jite\AssetHandler\Exceptions\InvalidAssetException;
use Jite\AssetHandler\Exceptions\InvalidContainerException;
use Jite\AssetHandler\Types\AssetTypes;
use PHPUnit_Framework_TestCase;

class AssetHandlerTest extends PHPUnit_Framework_TestCase {

    /** @var AssetHandler */
    protected $handler;

    public function setUp() {
        $this->handler = new AssetHandler();
        $this->handler->setBaseUrl();
        $this->handler->setBasePath();
    }

    //region AssetHandler::add

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

    //endregion

    //region AssetHandler::remove

    public function testRemoveAssetNoAsset() {
        $this->assertFalse($this->handler->remove("asset.js", AssetTypes::SCRIPT));
    }

    public function testRemoveAssetOneAssetByName() {
        $this->handler->add("test.js", "test", AssetTypes::SCRIPT);
        $this->assertCount(1, $this->handler->getAssets(AssetTypes::SCRIPT));
        $this->assertTrue($this->handler->remove("test", AssetTypes::SCRIPT));
        $this->assertCount(0, $this->handler->getAssets(AssetTypes::SCRIPT));
    }

    public function testRemoveAssetOneAssetByPath() {
        $this->handler->add("test.js", "test", AssetTypes::SCRIPT);
        $this->assertCount(1, $this->handler->getAssets(AssetTypes::SCRIPT));
        $this->assertTrue($this->handler->remove("test.js", AssetTypes::SCRIPT));
        $this->assertCount(0, $this->handler->getAssets(AssetTypes::SCRIPT));
    }

    public function testRemoveAssetMultipleAssetsByName() {
        $this->handler->add("test.js", "test", AssetTypes::SCRIPT);
        $this->handler->add("test2.js", "test2", AssetTypes::SCRIPT);
        $this->handler->add("test3.js", "test3", AssetTypes::SCRIPT);
        $this->assertCount(3, $this->handler->getAssets(AssetTypes::SCRIPT));
        $this->assertTrue($this->handler->remove("test2", AssetTypes::SCRIPT));
        $this->assertCount(2, $this->handler->getAssets(AssetTypes::SCRIPT));

        foreach ($this->handler->getAssets(AssetTypes::SCRIPT) as $asset) {
            $this->assertNotEquals($asset, "/assets/test2.js"); // the asset string is the path.
        }
    }

    public function testRemoveAssetMultipleAssetsByPath() {
        $this->handler->add("test.js", "test", AssetTypes::SCRIPT);
        $this->handler->add("test2.js", "test2", AssetTypes::SCRIPT);
        $this->handler->add("test3.js", "test3", AssetTypes::SCRIPT);
        $this->assertCount(3, $this->handler->getAssets(AssetTypes::SCRIPT));
        $this->assertTrue($this->handler->remove("test2.js", AssetTypes::SCRIPT));
        $this->assertCount(2, $this->handler->getAssets(AssetTypes::SCRIPT));

        foreach ($this->handler->getAssets(AssetTypes::SCRIPT) as $asset) {
            $this->assertNotEquals($asset, "/assets/test2.js"); // the asset string is the path.
        }
    }

    public function testRemoveAssetWithoutContainerNameOneWithName() {

        $this->handler->add("test.js", "test", AssetTypes::SCRIPT);
        $this->handler->add("test.css", "test2", AssetTypes::STYLE_SHEET);
        $this->assertCount(2, $this->handler->getAssets());
        $this->assertTrue($this->handler->remove("test"));
        $this->assertCount(1, $this->handler->getAssets());

    }

    public function testRemoveAssetWithoutContainerNameMultipleWithName() {

        $this->handler->add("test.js", "test", AssetTypes::SCRIPT);
        $this->handler->add("test.css", "test", AssetTypes::STYLE_SHEET);
        $this->assertCount(2, $this->handler->getAssets());
        $this->setExpectedException(
            AssetNameNotUniqueException::class,
            'Failed to remove asset with name "test". ' .
            'Due to none unique name, the container name is required for this operation.');
        $this->assertTrue($this->handler->remove("test"));
    }

    public function testRemoveAssetWithBadAssetName() {

        $this->handler->add("test.css", "test", AssetTypes::STYLE_SHEET);
        $this->assertFalse($this->handler->remove("testz"));
        $this->assertFalse($this->handler->remove("testz", AssetTypes::STYLE_SHEET));
        $this->assertFalse($this->handler->remove("testz.js", AssetTypes::STYLE_SHEET));
    }

    public function testRemoveAssetWithBadContainerName() {

        $this->setExpectedException(
            InvalidContainerException::class,
            'Failed to remove asset with name "test.js". The container (abcdef) does not exist.');
        $this->handler->remove("test.js", "abcdef");
    }

    public function testRemoveAssetWithUnknownFileTypeFail() {
        $this->setExpectedException(
            InvalidAssetException::class,
            'Failed to remove asset with name "test.sds". The asset container could not be determined from file type.');
        $this->handler->remove("test.sds");
    }

    public function testRemoveAssetWithUnknownFileTypeSuccess() {
        $this->handler->add("test.js", "test", AssetTypes::SCRIPT);
        $this->handler->add("test2.css", "test2", AssetTypes::STYLE_SHEET);

        $this->assertTrue($this->handler->remove("test.js"));
        $this->assertCount(1, $this->handler->getAssets());
    }

    //endregion

    //region AssetHandler::print

    public function testPrintNoAsset() {
        $this->assertEquals(
            $this->handler->print("none.js", AssetTypes::SCRIPT),
            "<!-- Failed to fetch asset (none.js) -->"
        );

        $this->assertEquals(
            $this->handler->print("none.js"),
            "<!-- Failed to fetch asset (none.js) -->"
        );
    }

    public function testPrintWithAssetAndContainer() {
        $this->handler->add("/js/test.js", "test", AssetTypes::SCRIPT);
        $this->assertEquals(
            $this->handler->print("test", AssetTypes::SCRIPT),
            '<script src="/assets/js/test.js" type="application/javascript"><script>'
        );
    }

    public function testPrintWithAssetNoContainer() {
        $this->handler->add("/js/test.js", "test", AssetTypes::SCRIPT);
        $this->assertEquals(
            $this->handler->print("test"),
            '<script src="/assets/js/test.js" type="application/javascript"><script>'
        );
    }

    public function testPrintWithAssetAndContainerCustomString() {
        $this->handler->add("/js/test.js", "test", AssetTypes::SCRIPT);
        $this->assertEquals(
            $this->handler->print(
                "test",
                AssetTypes::SCRIPT,
                '<script src="{{PATH}}" type="application/javascript">var a = "{{NAME}}";</script>'
            ),
            '<script src="/assets/js/test.js" type="application/javascript">var a = "test";</script>'
        );
    }

    public function testPrintNoAssetAndCustomString() {
        $this->handler->add("/js/test.js", "test", AssetTypes::SCRIPT);
        $this->assertEquals(
            $this->handler->print(
                "test",
                AssetTypes::SCRIPT,
                '<script src="{{PATH}}" type="application/javascript">var a = "{{NAME}}";</script>'
            ),
            '"<!-- Failed to fetch asset (/assets/js/test.js) -->"'
        );

    }

    //endregion

    //region AssetHandler::printAll

    public function testPrintAllOneAssetAndContainer() {}
    public function testPrintAllOneAssetNoContainer() {}
    public function testPrintAllMultiAssetAndContainer() {}
    public function testPrintAllMultiAssetNoContainer() {}
    public function testPrintAllNoAssets() {}

    //endregion

}
