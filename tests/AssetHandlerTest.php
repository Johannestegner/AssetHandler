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
     * @returns vfsStreamDirectory|null
     */
    private function getDirectory(string $name) {
        /** @var vfsStreamDirectory $child */
        $child = $this->rootFolder;

        while ($child->getName() !== $name) {
            if (count($child->getChildren()) === 0) {
                return null;
            }
            $child = $child->getChildren()[0];
        }

        return $child;
    }

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

    public function testGetAssetPathInvalidType() {
        $this->setExpectedException(InvalidAssetTypeException::class, "The asset type none does not exist.");
        $this->assetHandler->getAssetPath("", "none");
    }

    public function testGetAssetPathAllWithDifferPath() {
        // Due to allowing the "all" type when getting a asset path, this test is needed.
        $this->setExpectedException(InvalidAssetTypeException::class,
            "Can not fetch the asset base path: Assets base path differs.");
        $this->assetHandler->setAssetBasePath("/", AssetHandler::ASSET_TYPE_SCRIPT);
        $this->assetHandler->getAssetPath("assets/js/test1.js", AssetHandler::ASSET_TYPE_ALL);
    }

    public function testGetAssetPath() {
        $jsDir = $this->getDirectory("js");
        $this->assertEquals(
            $jsDir->url() . "/test1.js",
            $this->assetHandler->getAssetPath("assets/js/test1.js", AssetHandler::ASSET_TYPE_SCRIPT)
        );
    }
}
