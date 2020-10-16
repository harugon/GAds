<?php


namespace GAds;

use Action;
use Html;
use MediaWiki\MediaWikiServices;
use MWException;
use Parser;
use PPFrame;
use Skin;

class GAdsHooks
{

    private static array $param = [
        "class", // class
        "style", // style
        //"data-ad-client", //ユーザID
        "data-adtest",
        "data-ad-slot", //広告ID
        "data-ad-format", //形状（横長、縦長、レクタングル）
        "data-full-width-responsive" //レスポンシブ広告ユニットが全幅サイズに自動拡張
    ];

    /**
     * called when the parser initializes for the first time
     * https://www.mediawiki.org/wiki/Manual:Hooks/ParserFirstCallInit
     *
     * @param \Parser $parser
     *
     */
    public static function onParserFirstCallInit( \Parser $parser ) {
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
        $data_ad_client = $conf->get( 'GAdsClient' );


        //パラメータリストと一致したものを返す
        $tag_pram = array_filter($args, function($v, $k) {
            return in_array($k, self::$param, true);
        }, ARRAY_FILTER_USE_BOTH);

        //ユーザID
        $tag_pram['data-ad-client'] = $data_ad_client;


        return Html::rawelement(
            'ins',
            $tag_pram,""
        ).<<<TXT
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
     */
    public static function onSkinAfterBottomScripts(\Skin $skin, &$text ) {

        $conf =  MediaWikiServices::getInstance()->getMainConfig();
        $data_ad_client = $conf->get( 'GAdsClient' );

        $gads_disable_pages = $conf->get( 'GAdsDisablePages' );
        $gads_ns = $conf->get( 'GAdsNsID' );

        if ( $data_ad_client == "" ) {
            throw new MWException( "Please update your LocalSettings.php with the correct GAds configurations" );
        }

        $user = $skin->getUser();

        if ( $user && MediaWikiServices::getInstance()->getPermissionManager()->userHasRight( $user, 'noGAds' ) ) {
            return true;
        }

        //広告表示させないユーザ
        if ($skin->getUser()->isAllowed( 'noGAds' ) ) {
            return true;
        }

        //広告表示さる名前空間
        if ( !in_array( $skin->getTitle()->getNamespace(), $gads_ns, true )) {
            return true;
        }

        //広告表示させないページ
        if ( in_array( $skin->getTitle()->getPrefixedText(), $gads_disable_pages, false )){
            return true;
        }


        if (! Action::getActionName( $skin->getContext() ) == 'view') {
            return true;
        }


        $text.= <<<TAG
<script data-ad-client="{$data_ad_client}" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>

TAG;
        return true;



    }
    public static function onSkinBuildSidebar( Skin $skin, &$bar) {
        $bar['gads'][] = [
            'text'  => 'onSkinBuildSidebar <img src="https://placehold.jp/160x600.png">',
            'title' => $skin->msg( 'wikimediashoplink-link-tooltip' ),
            'id'    => 'n-shoplink',
        ];

        return true;
    }
    public static function onArticleViewHeader( &$article, &$outputDone, &$pcache ) {
        $article->getContext()->getOutput()->addHTML('<div>onArticleViewHeader<img src="https://placehold.jp/300x350.png"></div>');
    }
    public static function onArticleViewFooter( $article ) {
        $article->getContext()->getOutput()->addHTML('<div>onArticleViewFooter<img src="https://placehold.jp/300x200.png"></div>');

    }

}