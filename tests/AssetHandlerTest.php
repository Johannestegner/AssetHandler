<?php

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AssetHandlerTest.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-09 - kl 22:45
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jite\AssetHandler;

use Jite\AssetHandler\Exceptions\{
    AssetNameNotUniqueException, InvalidAssetException, InvalidContainerException, InvalidPathException
};

use Jite\AssetHandler\Types\AssetTypes;
use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_TestCase;

class AssetHandlerTest extends PHPUnit_Framework_TestCase {

    /** @var AssetHandler */
    protected $handler;

    public function setUp() {
        $this->handler = new AssetHandler();
        $this->handler->setBaseUrl("/assets/");
    }

    //region Helpers.

    /**
     * Set up filesystem mock and return the root dir as vfsStreamDirectory.
     *
     * @return \org\bovigo\vfs\vfsStreamDirectory
     */
    private function setUpFilesystemMock() {
        $fileStructure = [
            "project" => [
                "public" => [
                    "assets" => [
                        "js" => [
                            "test1.js" => "var a = 5;",
                            "test2.js" => "var b = 10;",
                            "test3.js" => "var d = 32;"
                        ],
                        "css" => [
                            "test1.css" => ".some-class { background: blue; }",
                            "test2.css" => ".some-class { background: blue; }",
                            "test3.css" => ".some-class { background: blue; }"
                        ]
                    ]
                ]
            ]
        ];

        $root = vfsStream::setup("www", null, $fileStructure);
        return $root;
    }

    //endregion

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
            'An asset with the name "test" already exists in the container (scripts).'
        );
        $this->handler->add("test.js", "test", AssetTypes::SCRIPT);

        // Make sure that it blows up when not passing the container, so that the Add method will place the asset right.
        $this->setExpectedException(
            AssetNameNotUniqueException::class,
            'An asset with the name test already exists in the container (scripts)'
        );
        $this->handler->add("test.js", "test");
    }

    public function testAddFailWithInvalidContainer() {
        $this->setExpectedException(
            InvalidContainerException::class,
            'Container named "test" does not exist.'
        );

        $this->handler->add("test.js", "test", "test");
    }

    public function testAddFailWithContainerNotPossibleToDetermine() {
        $this->setExpectedException(
            InvalidContainerException::class,
            "Could not determine container from the asset path (test.sdfgf)."
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
        $this->setExpectedException(
            InvalidContainerException::class,
            "Could not determine container from the asset path (test)."
        );
        $this->assertTrue($this->handler->remove("test"));
        $this->assertCount(1, $this->handler->getAssets());

    }

    public function testRemoveAssetWithBadAssetName() {

        $this->handler->add("test.css", "test", AssetTypes::STYLE_SHEET);
        $this->assertFalse($this->handler->remove("testz", AssetTypes::STYLE_SHEET));
        $this->assertFalse($this->handler->remove("testz.js", AssetTypes::STYLE_SHEET));
    }

    public function testRemoveAssetWithBadContainerName() {

        $this->setExpectedException(
            InvalidContainerException::class,
            'Container named "abcdef" does not exist.');
        $this->handler->remove("test.js", "abcdef");
    }

    public function testRemoveAssetWithUnknownFileTypeFail() {
        $this->setExpectedException(
            InvalidContainerException::class,
            'Could not determine container from the asset path (test.sds).');
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
            "<!-- Failed to fetch asset (none.js) -->" . PHP_EOL,
            $this->handler->print("none.js", AssetTypes::SCRIPT)
        );

        $this->assertEquals(
            "<!-- Failed to fetch asset (none.js) -->" . PHP_EOL,
            $this->handler->print("none.js")
        );
    }

    public function testPrintWithAssetAndContainer() {
        $this->handler->add("/js/test.js", "test", AssetTypes::SCRIPT);
        $this->assertEquals(
            '<script src="/assets/js/test.js" type="application/javascript"></script>' . PHP_EOL,
            $this->handler->print("test", AssetTypes::SCRIPT)
        );
    }

    public function testPrintWithAssetNoContainer() {
        $this->handler->add("/js/test.js", "test", AssetTypes::SCRIPT);
        $this->assertEquals(
            '<script src="/assets/js/test.js" type="application/javascript"></script>' . PHP_EOL,
            $this->handler->print("test")
        );
    }

    public function testPrintWithAssetAndContainerCustomString() {
        $this->handler->add("/js/test.js", "test", AssetTypes::SCRIPT);
        $this->assertEquals(
            '<script src="/assets/js/test.js" type="application/javascript">var a = "test";</script>' . PHP_EOL,
            $this->handler->print(
                "test",
                AssetTypes::SCRIPT,
                '<script src="{{PATH}}" type="application/javascript">var a = "{{NAME}}";</script>'
            )
        );
    }

    public function testPrintNoAssetAndCustomString() {
        $this->assertEquals(
            '<!-- Failed to fetch asset (test) -->' . PHP_EOL,
            $this->handler->print(
                "test",
                AssetTypes::SCRIPT,
                '<script src="{{PATH}}" type="application/javascript">var a = "{{NAME}}";</script>'
            )
        );

    }

    public function testPrintPredefinedImage() {
        $this->handler->add("/images/test.png", "test", AssetTypes::IMAGE);
        $this->assertEquals(
            '<img src="/assets/images/test.png">' . PHP_EOL,
            $this->handler->print("test", AssetTypes::IMAGE)
        );
    }

    public function testPrintPredefinedScript() {
        $this->handler->add("/js/test.js", "test", AssetTypes::SCRIPT);
        $this->assertEquals(
            $this->handler->print("test", AssetTypes::SCRIPT),
            '<script src="/assets/js/test.js" type="application/javascript"></script>' . PHP_EOL
        );
    }

    public function testPrintPredefinedStyle() {
        $this->handler->add("/css/test.css", "test", AssetTypes::STYLE_SHEET);
        $this->assertEquals(
            '<link rel="stylesheet" type="text/css" href="/assets/css/test.css" title="test">' . PHP_EOL,
            $this->handler->print("test", AssetTypes::STYLE_SHEET)
        );
    }

    //endregion

    //region AssetHandler::printAll

    public function testPrintAllOneAssetAndContainer() {
        $this->handler->add("/js/test.js", "test", AssetTypes::SCRIPT);
        $this->assertEquals(
            '<script src="/assets/js/test.js" type="application/javascript"></script>' . PHP_EOL,
            $this->handler->printAll(AssetTypes::SCRIPT)
        );
    }

    public function testPrintAllOneAssetNoContainer() {
        $this->handler->add("/js/test.js", "test", AssetTypes::SCRIPT);
        $this->assertEquals(
            '<script src="/assets/js/test.js" type="application/javascript"></script>' . PHP_EOL,
            $this->handler->printAll()
        );
    }

    public function testPrintAllMultiAssetAndContainer() {
        $this->handler->add("/js/test.js", "test", AssetTypes::SCRIPT);
        $this->handler->add("/js/test2.js", "test2", AssetTypes::SCRIPT);

        $this->assertEquals(
            '<script src="/assets/js/test.js" type="application/javascript"></script>' . PHP_EOL .
            '<script src="/assets/js/test2.js" type="application/javascript"></script>' . PHP_EOL,
            $this->handler->printAll(AssetTypes::SCRIPT)
        );
    }

    public function testPrintAllMultiAssetNoContainer() {
        $this->handler->add("/js/test.js", "test", AssetTypes::SCRIPT);
        $this->handler->add("/js/test2.js", "test2", AssetTypes::SCRIPT);

        $this->assertEquals(
            '<script src="/assets/js/test.js" type="application/javascript"></script>' . PHP_EOL .
            '<script src="/assets/js/test2.js" type="application/javascript"></script>' . PHP_EOL,
            $this->handler->printAll()
        );
    }

    public function testPrintAllNoAssets() {
        $this->assertEquals(
            "",
            $this->handler->printAll()
        );
    }

    //endregion

    //region AssetHandler::getAssets
    // The "getAssets" method is supposed to be internal, but it IS public, so a test should be written.

    public function testGetAssetsOneAssetNoContainer() {
        $this->handler->add("/js/test.js", "test", AssetTypes::SCRIPT);

        $this->assertCount(1, $this->handler->getAssets());
        $asset = $this->handler->getAssets()[0];

        $this->assertEquals("/js/test.js", $asset->getPath());
        $this->assertEquals("/assets/js/test.js", $asset->getFullUrl());
        $this->assertEquals("test", $asset->getName());
        $this->assertEquals(AssetTypes::SCRIPT, $asset->getType());
    }

    public function testGetAssetsOneAssetWithContainer() {
        $this->handler->add("/js/test.js", "test", AssetTypes::SCRIPT);

        $this->assertCount(1, $this->handler->getAssets(AssetTypes::SCRIPT));
        $asset = $this->handler->getAssets(AssetTypes::SCRIPT)[0];

        $this->assertEquals("/js/test.js", $asset->getPath());
        $this->assertEquals("/assets/js/test.js", $asset->getFullUrl());
        $this->assertEquals("test", $asset->getName());
        $this->assertEquals(AssetTypes::SCRIPT, $asset->getType());
    }

    public function testGetAssetsMultipleAssetNoContainer() {

        $this->handler->add("/js/test.js", "test", AssetTypes::SCRIPT);
        $this->handler->add("/js/test2.css", "test2", AssetTypes::STYLE_SHEET);

        $assets = $this->handler->getAssets();
        $this->assertCount(2, $assets);

        $css = array_first($assets, function($i, Asset $a) {
            return $a->getType() === AssetTypes::STYLE_SHEET;
        });
        $script = array_first($assets, function($i, Asset $a) {
            return $a->getType() === AssetTypes::SCRIPT;
        });

        $this->assertEquals("test2", $css->getName());
        $this->assertEquals("test", $script->getName());
    }

    public function testGetAssetsMultipleAssetWithContainer() {
        $this->handler->add("/js/test.js", "test", AssetTypes::SCRIPT);
        $this->handler->add("/js/test.js", "test2", AssetTypes::SCRIPT);
        $this->handler->add("/js/test2.css", "test2", AssetTypes::STYLE_SHEET);

        $assets = $this->handler->getAssets(AssetTypes::SCRIPT);
        $this->assertCount(2, $assets);

        $t1 = array_first($assets, function($i, Asset $a) {
            return $a->getName() === "test";
        });
        $t2 = array_first($assets, function($i, Asset $a) {
            return $a->getName() === "test2";
        });

        $this->assertNotNull($t1);
        $this->assertNotNull($t2);
    }

    public function testGetAssetsNoAssetNoContainer() {
        $this->assertEmpty($this->handler->getAssets());
    }

    public function testGetAssetsNoAssetWithContainer() {
        $this->assertEmpty($this->handler->getAssets(AssetTypes::SCRIPT));
    }

    //endregion

    //region Test AssetHandler::setBaseUrl

    public function testSetBaseUrl() {
        $this->handler->add("js/test.js", "test", AssetTypes::SCRIPT);

        $this->assertTrue($this->handler->setBaseUrl("/test/test/test"));
        $this->assertEquals(
            '<script src="/test/test/test/js/test.js" type="application/javascript"></script>' . PHP_EOL,
            $this->handler->print("test")
        );

        $this->assertTrue($this->handler->setBaseUrl("/test/scripts/"));
        $this->assertEquals(
            '<script src="/test/scripts/js/test.js" type="application/javascript"></script>' . PHP_EOL,
            $this->handler->print("test")
        );

        $this->handler->add("test.css" , "style", AssetTypes::STYLE_SHEET);

        $this->assertTrue($this->handler->setBaseUrl("/public/scripts", AssetTypes::SCRIPT));
        $this->assertTrue($this->handler->setBaseUrl("/public/styles", AssetTypes::STYLE_SHEET));

        $this->assertEquals(
            '<script src="/public/scripts/js/test.js" type="application/javascript"></script>' . PHP_EOL,
            $this->handler->print("test", AssetTypes::SCRIPT)
        );

        $this->assertEquals(
            '<link rel="stylesheet" type="text/css" href="/public/styles/test.css" title="style">' . PHP_EOL,
            $this->handler->print("style", AssetTypes::STYLE_SHEET)
        );
    }

    public function testSetBaseUrlInvalidContainer() {
        $this->setExpectedException(
            InvalidContainerException::class,
            'Container named "invalid-container" does not exist.'
        );

        $this->handler->setBaseUrl("/abc", "invalid-container");
    }

    //endregion

    //region Test AssetHandler::setBasePath

    public function testSetBasePath() {
        $fsRoot          = $this->setUpFilesystemMock();
        $projectBasePath = $fsRoot->url() . "/project/public";
        $this->assertTrue($this->handler->setBasePath($projectBasePath));
    }

    public function testSetBasePathInvalidPath() {
        $fsRoot          = $this->setUpFilesystemMock();
        $projectBasePath = $fsRoot->url() . "/pruject/public";
        $this->setExpectedException(InvalidPathException::class, 'The path "vfs://www/pruject/public" is invalid.');
        $this->handler->setBasePath($projectBasePath);
    }

    public function testSetBasePathInvalidContainer() {
        $fsRoot = $this->setUpFilesystemMock();
        $this->setExpectedException(
            InvalidContainerException::class,
            'Container named "invalid-container" does not exist.'
        );
        $this->handler->setBasePath($fsRoot->url() . "/project/public", "invalid-container");
    }

    //endregion


    //region Test AssetHandler::addContainer

    public function testAddContainer() {
        $this->assertTrue($this->handler->addContainer("Test", "<{{NAME}}>"));
    }

    public function testAddContainerFailUnique() {
        $this->assertTrue($this->handler->addContainer("Test", "<{{NAME}}>"));
        $this->setExpectedException(
            InvalidContainerException::class,
            'Container named "Test" already exist.'
        );
        $this->handler->addContainer("Test", "<{{PATH}}>");
    }

    //endregion

    //region Test AssetHandler::removeContainer

    public function testRemoveContainer() {
        $this->assertTrue($this->handler->addContainer("Test", "<{{NAME}}>"));
        $this->assertTrue($this->handler->removeContainer("Test"));

        $this->assertTrue($this->handler->addContainer("Test", "<{{NAME}}>"));
        $this->assertTrue($this->handler->addContainer("Test2", "<{{NAME}}>"));


        $this->assertTrue($this->handler->removeContainer("Test2"));
        $this->handler->getAssets("Test"); // This should blow up if the "test" container does no longer exist.
    }

    public function testRemoveContainerFailNoContainer() {
        $this->setExpectedException(
            InvalidContainerException::class,
            'Container named "Test" does not exist.'
        );
        $this->handler->removeContainer("Test");
    }

    //endregion

    //region Extra tests for custom containers.

    public function testCustomContainerAddAsset() {
        $this->handler->addContainer("test", "<{{NAME}}>");
        $this->handler->add("/", "Test", "test");

        $this->assertEquals("<Test>" . PHP_EOL, $this->handler->printAll());
    }

    public function testCustomContainerRemoveAsset() {
        $this->handler->addContainer("test", "<{{NAME}}>");
        $this->handler->add("/", "Test", "test");

        $this->assertCount(1, $this->handler->getAssets("test"));
        $this->assertTrue($this->handler->remove("Test", "test"));
        $this->assertCount(0, $this->handler->getAssets("test"));
    }

    public function testCustomContainerPrint() {
        $this->handler->addContainer("test", "<{{NAME}}>");
        $this->handler->add("/", "Test", "test");

        $this->assertEquals("<Test>" . PHP_EOL, $this->handler->print("Test", "test"));
    }

    public function testCustomContainerPrintAll() {
        $this->handler->addContainer("test", "<{{NAME}}>");
        $this->handler->add("/", "Test", "test");

        $this->assertEquals("<Test>" . PHP_EOL, $this->handler->printAll("test"));
    }

    public function testCustomContainerGetAssets() {
        $this->handler->addContainer("test", "<{{NAME}}>");
        $this->handler->add("/", "Test", "test");

        $this->assertCount(1, $this->handler->getAssets("test"));
    }

    public function testCustomContainerSetBaseUrl() {
        $this->handler->addContainer("test", "<{{PATH}}>");
        $this->handler->add("/test/test.abc", "Test", "test");

        $this->assertTrue($this->handler->setBaseUrl("/foo/bar", "test"));
        $this->assertEquals(
            "/foo/bar/test/test.abc",
            $this->handler->getAssets("test")[0]->getFullUrl()
        );

    }

    public function testCustomContainerSetBasePath() {
        $this->handler->addContainer("test", "<{{PATH}}>");
        $this->handler->add("/assets/js/test2.js", "Test", "test");

        $fsRoot = $this->setUpFilesystemMock();
        $path   = $fsRoot->url() . "/project/public";

        $this->assertTrue($this->handler->setBasePath($path, "test"));
    }

    //endregion

}
