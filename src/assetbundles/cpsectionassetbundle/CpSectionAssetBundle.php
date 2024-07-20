<?php

namespace matthewdejager\craftmultie\assetbundles\cpsectionassetbundle;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class CpSectionAssetBundle extends AssetBundle
{

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {

        $this->sourcePath = "@matthewdejager/craftmultie/assetbundles/cpsectionassetbundle/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/index.js',
        ];

        $this->css = [
            'css/index.css',
        ];

        parent::init();
    }

}