<?php


namespace GAds\Test;

use RequestContext;
use Title;
use GAds\Hooks;
use GAds;

class GAdsHooksTest extends \MediaWikiTestCase
{
    private $skin;

    protected function setUp() : void {
        parent::setUp();

        $testUser = new \TestUser( 'TestUser' );
        $user = $testUser->getUser();
        $context = new RequestContext();
        $context->setTitle( Title::makeTitle( NS_MAIN, 'MAIN' ));
        $context->setUser($user);
        $this->skin = new \SkinTemplate();
        $this->skin->skinname="vector";
        $this->skin->setContext( $context );
    }


    /**
     * @dataProvider configProvider
     */
    public function testOnSkinAfterBottomScripts($RegExp,$config){
        $this->setMwGlobals($config);
        $text = "";
        Hooks::onSkinAfterBottomScripts($this->skin,$text);
        $this->assertRegExp($RegExp,$text);
    }

    public function configProvider(){
        return [
            //表示される
            [ "/ca-pub-123456789012345/",  [
                'wgGAdsClient' => "ca-pub-123456789012345",
                'wgGAdsDisablePages' => [""],
                'wgGAdsNsID' => [0,1],
                'wgGAdsActions' => ["view"],
                'wgGAdsSkins' => ["vector"],
            ]  ],
            //権限あるので表示されない
            [ "/^$/", [
                'wgGAdsClient' => "ca-pub-123456789012345",
                'wgGAdsDisablePages' => [""],
                'wgGAdsNsID' => [0,1],
                'wgGAdsActions' => ["view"],
                'wgGAdsSkins' => ["vector"],
                'wgGroupPermissions'=>[
                    '*' => [ 'blockgads' => true ],
                    ],
            ]  ],
            //page　表示されない
            [ "/^$/", [
                'wgGAdsClient' => "ca-pub-123456789012345",
                'wgGAdsDisablePages' => ["MAIN"],
                'wgGAdsNsID' => [0,1],
                'wgGAdsActions' => ["view"],
                'wgGAdsSkins' => ["vector"],
            ]  ],
            //namespace　表示されない
            ["/^$/", [
                'wgGAdsClient' => "ca-pub-123456789012345",
                'wgGAdsDisablePages' => [""],
                'wgGAdsNsID' => [1],
                'wgGAdsActions' => ["view"],
                'wgGAdsSkins' => ["vector"],
            ]  ],
            //action　表示されない
            [ "/^$/", [
                'wgGAdsClient' => "ca-pub-123456789012345",
                'wgGAdsDisablePages' => [""],
                'wgGAdsNsID' => [0,1],
                'wgGAdsActions' => ["edit"],
                'wgGAdsSkins' => ["vector"],
            ]  ],
            //Skin　表示されない
            [ "/^$/", [
                'wgGAdsClient' => "ca-pub-123456789012345",
                'wgGAdsDisablePages' => [""],
                'wgGAdsNsID' => [0,1],
                'wgGAdsActions' => ["view"],
                'wgGAdsSkins' => ["minerva"],
            ]  ]
        ];
    }


}