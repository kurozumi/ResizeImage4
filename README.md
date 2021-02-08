# 画像リサイズプラグイン for EC-CUBE4

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

.envにアクセスキーIDとシークレットアクセスキーを設定してください。

```
AWS_ACCESS_KEY_ID=*****************************
AWS_SECRET_ACCESS_KEY=*****************************
```

services.yamlを編集してください。

```
liip_imagine:
  resolvers:
    default:
      web_path:
        web_root: '%kernel.project_dir%'
        cache_prefix: 'media/cache'

    aws_s3_resolver:
      aws_s3:
        bucket: 'bucket_name' #バケット名を設定してください
        client_config:
          version: '2006-03-01'
          region: 'ap-northeast-1'
        get_options:
          Scheme: https
        put_options:
          CacheControl: "max-age=86400"

  loaders:
    default:
      filesystem:
        data_root: '%kernel.project_dir%'

  filter_sets:
    cache: ~

    # the name of the "filter set"
    resize:
      cache: aws_s3_resolver # AWS S3を利用したい場合コメントアウトしてください
```
