<?php

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  AssetHandlerTest.php - Part of the AssetHandler project.

  File created by Johannes Tegnér at 2016-08-09 - kl 22:45
  © - 2016
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
namespace Jite\AssetHandler;

use Jite\AssetHandler\Exceptions\AssetNotFoundException;
use Jite\AssetHandler\Exceptions\InvalidAssetTypeException;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit_Framework_TestCase;

class AssetHandlerTest extends PHPUnit_Framework_TestCase {

    /** @var AssetHandler */
    private $assetHandler;
    /** @var vfsStreamDirectory */
    private $rootFolder;

    public function setUp() {

        // Mock the filesystem using vfs.

        $fileStructure = [
            "some" => [
                "path" => [
                    "to" => [
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
                        ],
                        "private" => [
                            "assets" => [
                                "js" => ["test1.js" => "var a = 5;"],
                                "css" => ["test1.css" => ".some-class {background:blue}"]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->rootFolder   = vfsStream::setup("home", null, $fileStructure);
        $public             = $this->getDirectory("public");
        $this->assetHandler = new AssetHandler($public->url());
    }

    /**
     * @param string $name
     * @param $root vfsStreamDirectory|null
     * @returns vfsStreamDirectory|null
     */
    private function getDirectory(string $name, $root = null) {
        /** @var vfsStreamDirectory $child */
        $child = $root ?? $this->rootFolder;

        if ($child->getName() === $name) {
            return $child;
        }

        if (!is_dir($child->url())) {
            return null;
        }

        $children   = $child->getChildren();
        $childCount = count($children);

        for ($i=$childCount; $i-->0;) {
            $c = $this->getDirectory($name, $child->getChildren()[$i]);
            if ($c !== null && $c->getName() === $name) {
                return $c;
            }
        }

        return null;
    }

    //region AddScript

    public function testAddScriptBadPath() {
        $this->setExpectedException(AssetNotFoundException::class);
        $this->assetHandler->addScript("js/test.js");
    }

    public function testAddScript() {

        $this->assertTrue($this->assetHandler->addScript("assets/js/test1.js"));
    }

    public function testAddScriptDouble() {
        $this->assertTrue($this->assetHandler->addScript("assets/js/test1.js"));
        $this->assertFalse($this->assetHandler->addScript("assets/js/test1.js"));
    }

    public function testAddScriptMultiple() {
        $this->assertTrue($this->assetHandler->addScript("assets/js/test1.js"));
        $this->assertTrue($this->assetHandler->addScript("assets/js/test2.js"));
    }

    //endregion

    //region AddStyle

    public function testAddStyleBadPath() {
        $this->setExpectedException(AssetNotFoundException::class);
        $this->assetHandler->addStyleSheet("css/test.css");
    }

    public function testAddStyle() {
        $this->assertTrue($this->assetHandler->addStyleSheet("assets/css/test1.css"));
    }

    public function testAddStyleDouble() {
        $this->assertTrue($this->assetHandler->addStyleSheet("assets/css/test1.css"));
        $this->assertFalse($this->assetHandler->addStyleSheet("assets/css/test1.css"));
    }

    public function testAddStyleMultiple() {
        $this->assertTrue($this->assetHandler->addStyleSheet("assets/css/test1.css"));
        $this->assertTrue($this->assetHandler->addStyleSheet("assets/css/test2.css"));
    }

    //endregion

    //region GetAssetPath

    public function testGetAssetPathInvalidType() {
        $this->setExpectedException(InvalidAssetTypeException::class, "The asset type none does not exist.");
        $this->assetHandler->getAssetPath("", "none");
    }

    public function testGetAssetPathAllWithDifferPath() {
        // Due to allowing the "all" type when getting a asset path, this test is needed.
        // Starting off by changing the path of the scripts, cause if its changed, a "ALL" change should NOT
        // be permitted.
        $this->assetHandler->setAssetBasePath("/", AssetHandler::ASSET_TYPE_SCRIPT);
        $this->setExpectedException(InvalidAssetTypeException::class,
            "Can not fetch the asset base path: Assets base path differs.");
        $this->assetHandler->getAssetPath("assets/js/test1.js", AssetHandler::ASSET_TYPE_ALL);
    }

    public function testGetAssetPath() {
        $public = $this->getDirectory("public");
        $jsDir  = $this->getDirectory("js", $public);
        $this->assertEquals(
            $jsDir->url() . "/test1.js",
            $this->assetHandler->getAssetPath("assets/js/test1.js", AssetHandler::ASSET_TYPE_SCRIPT)
        );
    }

    //endregion

    //region SetAssetBasePath

    public function testSetAssetBasePathInvalidType() {
        $this->setExpectedException(InvalidAssetTypeException::class, "The asset type test does not exist.");
        $this->assetHandler->setAssetBasePath("/", "test");
    }

    public function testSetAssetBasePathInvalidDirectory() {

        $this->setExpectedExceptionRegExp(
            AssetNotFoundException::class,
            "/The directory \".+\" does not exist./"
        );
        $this->assetHandler->setAssetBasePath("/asdasd/", AssetHandler::ASSET_TYPE_ALL);
    }

    public function testSetAssetBasePathTypeAllFileNotExists() {
        // An asset is needed to be set.
        $this->assetHandler->addScript("assets/js/test2.js");
        $this->setExpectedExceptionRegExp(
            AssetNotFoundException::class,
            "/Asset path .+ could not be updated: The file with path .+ does not point to a valid file./"
        );
        // The private folder will contain assets in the right structure, but the "test2.js" asset
        // wont exist in it.
        $private = $this->getDirectory("private");
        $this->assetHandler->setAssetBasePath($private->url(), AssetHandler::ASSET_TYPE_ALL);
    }

    public function testSetAssetBasePathTypeAll() {
        $this->assertTrue($this->assetHandler->addScript("assets/js/test1.js"));
        $this->assertTrue($this->assetHandler->addScript("assets/css/test1.css"));

        // This should work, cause there are assets named as the above two in the "private" folder.
        // Had there not been, the change would not work.
        // The idea is that the user should only change asset part at startup in case they use another than the public
        // directory. So this should never really happen in runtime.
        $private = $this->getDirectory("private");
        $this->assertTrue($this->assetHandler->setAssetBasePath($private->url()));
    }

    public function testSetAssetBasePathTypeNotAll() {

        $this->assertTrue($this->assetHandler->addScript("assets/js/test1.js"));
        $this->assertTrue($this->assetHandler->addScript("assets/css/test1.css"));

        // This should work, cause there are assets named as the above two in the "private" folder.
        // Had there not been, the change would not work.
        // The idea is that the user should only change asset part at startup in case they use another than the public
        // directory. So this should never really happen in runtime.
        $private = $this->getDirectory("private");
        $this->assertTrue($this->assetHandler->setAssetBasePath($private->url()), AssetHandler::ASSET_TYPE_SCRIPT);
        $this->assertTrue($this->assetHandler->setAssetBasePath($private->url()), AssetHandler::ASSET_TYPE_STYLE_SHEET);

    }

    //endregion

    //region GetAssetBasePath

    public function testGetAssetBasePathInvalidType() {
        $this->setExpectedException(InvalidAssetTypeException::class, "The asset type noneexistant does not exist.");
        $this->assetHandler->getAssetBasePath("noneexistant");

    }

    public function testGetAssetBasePathTypeAllPathsDiffer() {
        $private = $this->getDirectory("private");
        $this->assetHandler->setAssetBasePath($private->url(), AssetHandler::ASSET_TYPE_STYLE_SHEET);
        $this->setExpectedException(
            InvalidAssetTypeException::class,
            "Can not fetch the asset base path: Assets base path differs."
        );
        $this->assetHandler->getAssetBasePath(AssetHandler::ASSET_TYPE_ALL);
    }

    public function testGetAssetBasePathPathsDiffer() {
        $this->assetHandler->addScript("assets/js/test1.js");
        $this->assetHandler->addScript("assets/css/test1.css");

        $private = $this->getDirectory("private");
        $public  = $this->getDirectory("public");
        $this->assetHandler->setAssetBasePath($private->url(), AssetHandler::ASSET_TYPE_STYLE_SHEET);

        $this->assertEquals(
            $public->url() . "/", // Extra slash is added by the handler.
            $this->assetHandler->getAssetBasePath(AssetHandler::ASSET_TYPE_SCRIPT)
        );
        $this->assertEquals(
            $private->url() . "/",
            $this->assetHandler->getAssetBasePath(AssetHandler::ASSET_TYPE_STYLE_SHEET)
        );
    }

    public function testGetAssetBasePathAllPathsSame() {
        $this->assetHandler->addScript("assets/js/test1.js");
        $this->assetHandler->addScript("assets/css/test1.css");

        $public = $this->getDirectory("public");

        $this->assertEquals(
            $public->url() . "/", // Extra slash is added by the handler.
            $this->assetHandler->getAssetBasePath(AssetHandler::ASSET_TYPE_ALL)
        );
    }

    //endregion

    //region GetAssets

    public function testGetAssetsInvalidAssetType() {
        $this->setExpectedException(
            InvalidAssetTypeException::class,
            "The asset type test does not exist."
        );
        $this->assetHandler->getAssets("test");
    }

    public function testGetAssetsNoResult() {
        $this->assertEmpty($this->assetHandler->getAssets());
        $this->assertEmpty($this->assetHandler->getAssets(AssetHandler::ASSET_TYPE_ALL));
        $this->assertEmpty($this->assetHandler->getAssets(AssetHandler::ASSET_TYPE_SCRIPT));
        $this->assertEmpty($this->assetHandler->getAssets(AssetHandler::ASSET_TYPE_STYLE_SHEET));
    }

    public function testGetAssetsAll() {
        $this->assetHandler->addScript("assets/js/test1.js");
        $this->assetHandler->addScript("assets/js/test2.js");
        $this->assetHandler->addStyleSheet("assets/css/test1.css");
        $this->assetHandler->addStyleSheet("assets/css/test2.css");

        $result = $this->assetHandler->getAssets();
        $this->assertCount(4, $result);
        $this->assertContains("assets/js/test1.js", $result);
        $this->assertContains("assets/js/test2.js", $result);
        $this->assertContains("assets/css/test1.css", $result);
        $this->assertContains("assets/css/test2.css", $result);

    }

    public function testGetAssetsByType() {
        $this->assetHandler->addScript("assets/js/test1.js");
        $this->assetHandler->addScript("assets/js/test2.js");
        $this->assetHandler->addStyleSheet("assets/css/test1.css");
        $this->assetHandler->addStyleSheet("assets/css/test2.css");

        $styles  = $this->assetHandler->getAssets(AssetHandler::ASSET_TYPE_STYLE_SHEET);
        $scripts = $this->assetHandler->getAssets(AssetHandler::ASSET_TYPE_SCRIPT);
        $this->assertCount(2, $styles);
        $this->assertCount(2, $scripts);
        $this->assertContains("assets/js/test1.js", $scripts);
        $this->assertContains("assets/js/test2.js", $scripts);
        $this->assertContains("assets/css/test1.css", $styles);
        $this->assertContains("assets/css/test2.css", $styles);
    }

    //endregion

    //region Scripts

    public function testScriptsNoScripts() {
        $this->assertEquals("",$this->assetHandler->scripts());
    }

    public function testScriptsOneScript() {
        $this->assetHandler->addScript("assets/js/test1.js");
        $this->assertEquals(
            "<script type=\"text/javascript\" src=\"vfs://home/some/path/to/public/assets/js/test1.js\"></script>" .
            PHP_EOL,
            $this->assetHandler->scripts()
        );
    }

    public function testScriptsMultipleScripts() {
        $this->assetHandler->addScript("assets/js/test1.js");
        $this->assetHandler->addScript("assets/js/test2.js");
        $this->assertEquals(
            '<script type="text/javascript" src="vfs://home/some/path/to/public/assets/js/test1.js"></script>' .
            PHP_EOL .
            '<script type="text/javascript" src="vfs://home/some/path/to/public/assets/js/test2.js"></script>' .
            PHP_EOL,
            $this->assetHandler->scripts()
        );
    }

    //endregion

    //region Styles

    public function testStylesNoStyles() {
        $this->assertEquals("",$this->assetHandler->styles());
    }

    public function testStylesOneStyle() {
        $this->assetHandler->addStyleSheet("assets/css/test1.css");
        $this->assertEquals(
            '<link rel="stylesheet" href="vfs://home/some/path/to/public/assets/css/test1.css">' . PHP_EOL,
            $this->assetHandler->styles()
        );
    }

    public function testStylesMultipleStyles() {
        $this->assetHandler->addStyleSheet("assets/css/test1.css");
        $this->assetHandler->addStyleSheet("assets/css/test2.css");
        $this->assertEquals(
            '<link rel="stylesheet" href="vfs://home/some/path/to/public/assets/css/test1.css">' .
            PHP_EOL .
            '<link rel="stylesheet" href="vfs://home/some/path/to/public/assets/css/test2.css">' .
            PHP_EOL,
            $this->assetHandler->styles()
        );
    }

    //endregion

    //region SetUseVersioning

    // TODO.

    //endregion
}
