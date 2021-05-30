GAds
====

MediaWikiにGoogle AdSenseを追加します


## Description
Google AdSenseを追加します、


* 広告の表示非表示はスクリプトの読み込みで制御しています

### 表示位置（例）

* wgGAdsHeader 上（320×50）
* wgGAdsFooter 下（300×250）

| Minerva | Vector |
|:------------:|:------------:|
| ![Minerva AdSense](https://raw.githubusercontent.com/harugon/GAds/master/.github/screenshots/GAds-MinervaNeue.png)      | ![Vector AdSense](https://raw.githubusercontent.com/harugon/GAds/master/.github/screenshots/GAds-Vector.png)      |


## Download

### Composer
Composer でインストールします [composer.local.json](https://www.mediawiki.org/wiki/Composer#Using_composer-merge-plugin)
```bash
COMPOSER=composer.local.json composer require harugon/GAds
```

## Install


LocalSettings.php に下記を追記
Google AdSense　のサイト運営者 IDを```$wgGAdsClient```に指定します。
$wgGAdsClientの設定だけ自動広告が利用できます
```php
wfLoadExtension( 'GAds' );
$wgGAdsClient = "";// ca-pub-123456789012345
```

記事のフッターに広告を追加したい場合生成したコードを下記のようにLocalSettings.php追加してください。

レスポンシブ広告をおすすめします　[レスポンシブ広告のタグパラメータの使用方法 \- AdSense ヘルプ](https://support.google.com/adsense/answer/9183460?hl=ja)
```php
$wgGAdsFooter = <<<TXT
<ins class="adsbygoogle"
style="display:inline-block;width:728px;height:90px"
data-ad-client="ca-pub-1234567890123456"
data-ad-slot="1234567890"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
TXT;
```
## Config

| config              | default  | Example       |                        |
|---------------------|----------|---------------|------------------------|
| $wgGAdsClient       | ""       |  ca-pub-XXXXXX    |                        |
| $wgGAdsDisablePages | []       | ["Main_Page"] | 表示させないページ     |
| $wgGAdsActions      | ["view"] |               | 表示させるアクション名 |
| $wgGAdsNsID         | [0]      |               | 表示させる名前空間     |
| $wgGAdsHeader       | ""       |               |                        |
| $wgGAdsFooter       | ""       |               |                        |

## Licence

MIT

## Author

[harugon](https://github.com/harugon)
