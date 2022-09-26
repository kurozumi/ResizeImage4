# 画像リサイズプラグイン for EC-CUBE4.2

本プラグインの利用には EC-CUBE 4.0.5 以上へのアップデートが必要になります。

カスタマイズ、または他社プラグインとの競合による動作不良つきましてはサポート対象外です。

本プラグインを導入したことによる不具合や被った不利益につきましては一切責任を負いません。
ご理解の程よろしくお願いいたします。

## Example

商品一覧ページの画像を250*250pxにリサイズしたい場合。
```
<img src="{{ asset(Product.main_list_image|no_image_product, 'save_image') | imagine_filter('resize', {
    "thumbnail": {"size": [250, 250] }}
) }}">
```

商品一覧ページの画像をトリミングしてリサイズしたい場合。※デフォルトはトリミングするので省略可
```
<img src="{{ asset(Product.main_list_image|no_image_product, 'save_image') | imagine_filter('resize', {
    "thumbnail": {"size": [250, 250], "mode": "outbound" }}
) }}">
```

商品一覧ページの画像をトリミングせず、相対的なサイズに変更したい場合
```
<img src="{{ asset(Product.main_list_image|no_image_product, 'save_image') | imagine_filter('resize', {
    "thumbnail": {"size": [250, 250], "mode": "inset" }}
) }}">
```


## 独自プラグインでインストールする場合

以下のライブラリをインストールしてください。

```
composer require liip/imagine-bundle
```

## AWS 3Sを利用

独自プラグインでインストールしている場合、以下のライブラリをインストールしてください。

```
composer require aws/aws-sdk-php
```

管理画面＞画像リサイズ管理＞Amazon S3連携設定で必要な情報を設定して有効化ください。
