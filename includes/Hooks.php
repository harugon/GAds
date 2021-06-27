<?php


namespace GAds;

use Action;
use Article;
use Html;
use MediaWiki\MediaWikiServices;
use MWException;
use OutputPage;
use ParserOutput;
use Skin;

class Hooks
{

    /**
     * onBeforePageDisplay
     *
     * @param OutputPage $out
     * @param Skin $skin
     * @return bool
     * @throws MWException
     */
    public static function onBeforePageDisplay( OutputPage &$out, Skin &$skin ): bool
    {
        //CONFIG
        $conf = MediaWikiServices::getInstance()->getMainConfig();
        $GAdsClient = $conf->get( 'GAdsClient' );
        $GAdsDisablePages = $conf->get( 'GAdsDisablePages' );
        $GAdsNsID = $conf->get( 'GAdsNsID' );
        $GAdsActions = $conf->get( 'GAdsActions' );
        $GAdsSkins = $conf->get( 'GAdsSkins' );

        //指定したスキンが含まれるか
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
        $namespace = $out->getTitle()->getNamespace();
        if(!in_array($namespace,$GAdsNsID , true )){
            //含まれない場合表示させない
            return true;
        }


        //指定したページ名が含まれるか
        if(in_array( $out->getTitle()->getPrefixedText(), $GAdsDisablePages, false )){
            //含まれる場合表示させない
            return true;
        }

        //指定したアクションが含まれないか　["view"]
        //https://www.mediawiki.org/wiki/Manual:$wgActions/ja
        $context = $out->getContext();
        $action = Action::getActionName($context);
        if(!in_array( $action, $GAdsActions, false )){
            //含まれない場合表示させない
            return true;
        }


        $data_ad_client ='';
        if ($GAdsClient!==""){
            $data_ad_client = 'data-ad-client="'.$GAdsClient.'"';
        }


        $out->addModules('ext.gads.styles');
        //広告スクリプトタグ
        $out->addHeadItem('adsense','<script '.$data_ad_client.' async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>');
        return true;
    }


    /**
     * ArticleViewHeader
     *
     * @param Article $article
     * @param bool|ParserOutput	$outputDone
     * @param bool $pcache
     *
     * @return bool
     */
    public static function onArticleViewHeader(Article $article, &$outputDone, &$pcache ): bool
    {
        $conf = MediaWikiServices::getInstance()->getMainConfig();
        $GAdsHeader = $conf->get( 'GAdsHeader' );

        if($GAdsHeader === ''){
            return true;
        }
        $label = Html::rawElement('div',
            [
                'id' => 'gads-header-label',
                'class' => 'gads-label'
            ], wfMessage('gads-header-label')->parse());

        $html = Html::rawelement(
            'div',
            [
                'id' => 'gads-header',
                'class' => 'gads'
            ], $GAdsHeader
        );

        $article->getContext()->getOutput()->addHTML($html);
        return true;

    }


    /**
     * ArticleViewFooter
     *
     * @param Article $article
     * @param bool $patrolFooterShown
     * @return bool
     */
    public static function onArticleViewFooter(Article $article,bool $patrolFooterShown): bool
    {
        $conf = MediaWikiServices::getInstance()->getMainConfig();
        $GAdsFooter = $conf->get( 'GAdsFooter' );

        if($GAdsFooter === '') {
            return true;
        }

        $label = Html::rawElement('div',
            [
                'id' => 'gads-footer-label',
                'class' => 'gads-label'
            ], wfMessage('gads-footer-label')->parse());

        $html = Html::rawelement(
            'div',
            [
                'id' => 'gads-footer',
                'class' => 'gads'
            ], $GAdsFooter
        );

        $article->getContext()->getOutput()->addHTML($html);
        return true;
    }

}