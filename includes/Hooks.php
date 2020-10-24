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
     * called when the parser initializes for the first time
     * https://www.mediawiki.org/wiki/Manual:Hooks/ParserFirstCallInit
     * "ParserFirstCallInit": "GAds\\GAdsHooks::onParserFirstCallInit",
     *
     * @param Parser $parser
     *
     * @throws \MWException
     */
    public static function onParserFirstCallInit( Parser $parser ) {
        $parser->setHook( 'ins', [ self::class, 'renderTagGAds' ] );
    }

    /**
     * ins タグを返す
     *
     * @param $input
     * @param array $args
     * @param Parser $parser
     * @param PPFrame $frame
     * @return string
     */
    public static function renderTagGAds( $input, array $args, Parser $parser, PPFrame $frame ) {

        $conf =  MediaWikiServices::getInstance()->getMainConfig();
        $gads_client = $conf->get( 'GAdsClient' );
        //data-ad-client 上書き
        $args['data-ad-client'] = $gads_client;

        $param_list = [
            "class", // class
            "style", // style
            "data-ad-client", //ユーザID
            "data-adtest",
            "data-ad-slot", //広告ID
            "data-ad-format", //形状（横長、縦長、レクタングル）
            "data-full-width-responsive" //レスポンシブ広告ユニットが全幅サイズに自動拡張
        ];

        $attribute = array_filter($args, function($v, $k) use ($param_list) {
            return in_array($k,$param_list, true);
        }, ARRAY_FILTER_USE_BOTH);


        $tag = Html::rawelement(
            'ins',
            $attribute,""
        );

        return $tag.<<<TXT
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
TXT;

    }


    /**
     * At the end of Skin::bottomScripts()
     * https://www.mediawiki.org/wiki/Manual:Hooks/SkinAfterBottomScripts
     *
     * @param $skin
     * @param $text
     * @return bool
     */
    public static function onSkinAfterBottomScripts(\Skin $skin, &$text ) {
        $conf = MediaWikiServices::getInstance()->getMainConfig();
        $gads_client = $conf->get( 'GAdsClient' );
        $gads_disable_pages = $conf->get( 'GAdsDisablePages' );
        $gads_namespaces = $conf->get( 'GAdsNsID' );
        $gads_actions = $conf->get( 'GAdsActions' );

        //ユーザに指定した権限があるか
        //https://www.mediawiki.org/wiki/Manual:User_rights/ja
        $user = $skin->getUser();
        $action = MediaWikiServices::getInstance()->getPermissionManager()->userHasRight( $user,'nogads');
        if($action){
            return true;
        }

        //指定した名前空間が含まれるか
        $namespace = $skin->getTitle()->getNamespace();
        if(!in_array($namespace,$gads_namespaces , true )){
            //含まれない場合表示させない
            return true;
        }


        //指定したページ名が含まれるか
        if(in_array( $skin->getTitle()->getPrefixedText(), $gads_disable_pages, false )){
            //含まれる場合表示させない
            return true;
        }

        $context = $skin->getContext();
        $action = Action::getActionName($context);
        //指定したアクションが含まれるか　["view"]
        //https://www.mediawiki.org/wiki/Manual:$wgActions/ja
        if(!in_array( $action, $gads_actions, false )){
            //含まれない場合表示させない
            return true;
        }

        $skin->getContext()->getOutput()->addModules('ext.gads');



        //広告スクリプトタグ
        $text.= <<<TAG
<script data-ad-client="{$gads_client}" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
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
        $gads_header = $conf->get( 'GAdsHeader' );
        if(!$gads_header == ''){
            $html = Html::rawelement(
                'div',
                [
                    'id'=>'gads-header',
                    'class'=>'gads',
                ],$gads_header
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
        $gads_footer = $conf->get( 'GAdsFooter' );
        if(!$gads_footer == ''){
            $html = Html::rawelement(
                'div',
                [
                    'id'=>'gads-footer',
                    'class'=>'gads',
                ],$gads_footer
            );

            $article->getContext()->getOutput()->addHTML($html);
        }

    }

}