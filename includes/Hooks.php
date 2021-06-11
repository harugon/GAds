<?php


namespace GAds;

use Action;
use Html;
use MediaWiki\MediaWikiServices;
use Parser;
use PPFrame;
use Skin;

class Hooks
{

    /**
     * At the end of Skin::bottomScripts()
     * https://www.mediawiki.org/wiki/Manual:Hooks/SkinAfterBottomScripts
     *
     * @param Skin $skin
     * @param $text
     * @return bool
     */
    public static function onSkinAfterBottomScripts(Skin $skin, &$text ): bool
    {
        $conf = MediaWikiServices::getInstance()->getMainConfig();
        $GAdsClient = $conf->get( 'GAdsClient' );
        $GAdsDisablePages = $conf->get( 'GAdsDisablePages' );
        $GAdsNsID = $conf->get( 'GAdsNsID' );
        $GAdsActions = $conf->get( 'GAdsActions' );
        $GAdsSkins = $conf->get( 'GAdsSkins' );

        #指定したスキンが含まれるか
        if(!in_array( $skin->getSkinName(), $GAdsSkins, false )){
            return true;
        }

        //ユーザに指定した権限があるか
        //https://www.mediawiki.org/wiki/Manual:User_rights/ja
        $permission = MediaWikiServices::getInstance()->getPermissionManager()->userHasRight($skin->getUser(),'blockgads');
        if($permission){
            return true;
        }

        //指定した名前空間が含まれないか
        $namespace = $skin->getTitle()->getNamespace();
        if(!in_array($namespace,$GAdsNsID , true )){
            //含まれない場合表示させない
            return true;
        }


        //指定したページ名が含まれるか
        if(in_array( $skin->getTitle()->getPrefixedText(), $GAdsDisablePages, false )){
            //含まれる場合表示させない
            return true;

        }

        //指定したアクションが含まれないか　["view"]
        //https://www.mediawiki.org/wiki/Manual:$wgActions/ja
        $context = $skin->getContext();
        $action = Action::getActionName($context);
        if(!in_array( $action, $GAdsActions, false )){
            //含まれない場合表示させない
            return true;
        }

        //広告スクリプトタグ
        $text.= <<<TAG
<script data-ad-client="{$GAdsClient}" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
TAG;

        return true;
    }


    /**
     * ArticleViewHeader
     *
     * @param $article
     * @param $outputDone
     * @param $pcache
     */
    public static function onArticleViewHeader( &$article, &$outputDone, &$pcache ) {
        $conf = MediaWikiServices::getInstance()->getMainConfig();
        $GAdsHeader = $conf->get( 'GAdsHeader' );
        if($GAdsHeader !== ''){
            $html = Html::rawelement(
                'div',
                [
                    'id'=>'gads-header',
                    'class'=>'gads'
                ],$GAdsHeader
            );

            $article->getContext()->getOutput()->addHTML($html);
        }
    }


    /**
     * ArticleViewFooter
     *
     * @param $article
     */
    public static function onArticleViewFooter( $article ) {
        $conf = MediaWikiServices::getInstance()->getMainConfig();
        $GAdsFooter = $conf->get( 'GAdsFooter' );
        if($GAdsFooter !== ''){
            $html = Html::rawelement(
                'div',
                [
                    'id'=>'gads-footer',
                    'class'=>'gads'
                ],$GAdsFooter
            );
            $article->getContext()->getOutput()->addHTML($html);
        }
    }

}