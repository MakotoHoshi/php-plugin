# nginx版開発環境

## 構成
amazonlinux2 + nginx1 + php8 + laravel8

## 前提条件

1. この環境では、docker及びdocker-composeがインストールされていることを前提とします。
 - [dockerの導入方法](https://docs.docker.jp/get-docker.html)
 - [docker-composeの導入方法](https://docs.docker.jp/compose/install.html)

2. 作成されるdockerコンテナと、その役割は以下の通りです。

|コンテナ名|役割|URL|
|:---|:---|:---|
|laravel_php|Webサーバー|http://localhost:8089|
|laravel_mariadb|DBサーバー|-|
|laravel_phpmyadmin|phpMyAdmin|http://localhost:8088|

## セットアップ
### コンテナを起動する
```sh
cd {your project directory}
```
```sh
docker-compose up -d --build
```
3つのコンテナが起動します。
### DBをリセットしてシーディングを行う
```sh
bash build_command/mounted.sh
```
composer install  
.envの設置  
DBの削除と作成  
マイグレーション  
シーディング  
以上が実行されます。
### 表示確認
[http://localhost:8089](http://localhost:8089)

[http://localhost:8088](http://localhost:8088)

## リリース
### リポジトリ認証情報の設定
インスタンスにcloneまたはpullするリモートリポジトリの認証情報は、  
以下のファイルに記述した後、ansible-vaultで暗号化してください。
```
/repo/docker/provision/ansible/variables.encrypt
```
- 例
```yml
repository: https://[user]:[password]@example.com/repository.git
```
### 機密ファイルの管理
ansible-vaultで機密ファイルを暗号化することで、git上で管理することができます。  
暗号化には復号化用のパスワードが必要ですが、パスワードはgitにプッシュしないでください。
### 暗号化の手順
#### ファイルの用意
暗号化する前のファイルを以下の場所に用意してください。
- SSH鍵ファイルの場合
```
/repo/docker/provision/keys/prod_key.encrypt
/repo/docker/provision/keys/dev_key.encrypt
```
- ansibleの変数ファイルの場合
```
/repo/docker/provision/ansible/variables.encrypt
```
#### コンテナにログイン
```sh
docker exec -it laravel_php /bin/bash
```
#### ansible-vaultで暗号化する
```sh
cd /repo/docker/provision/keys/
```
```sh
ansible-vault encrypt prod_key.encrypt
ansible-vault encrypt dev_key.encrypt
```
```sh
cd /repo/docker/provision/ansible/
```
```sh
ansible-vault encrypt variables.encrypt
```
復号化用のパスワードを求められます。
### インスタンスIPアドレスの設定
デプロイ先のインスタンスIPを調べて、以下のファイルに記述します。
ansible_userとansible_ssh_private_key_fileは編集する必要はありません。
```
/repo/docker/provision/ansible/hosts
```
- 例
```
[prod]
{ prod環境インスタンス1のIPアドレス }
{ prod環境インスタンス2のIPアドレス }

[prod:vars]
ansible_user=ec2-user
ansible_ssh_private_key_file=/repo/docker/provision/keys/prod_key.pem

[dev]
{ dev環境インスタンス1のIPアドレス }
{ dev環境インスタンス2のIPアドレス }

[dev:vars]
ansible_user=ec2-user
ansible_ssh_private_key_file=/repo/docker/provision/keys/dev_key.pem
```
### プロビジョニング + デプロイ
新規作成したインスタンスにデプロイする方法です。
```sh
bash build_command/deploy.sh [dev|prod] [branch]
```
### デプロイのみ
デプロイ済みのインスタンスに対して、ソースコードの変更のみを反映する方法です。
```sh
bash build_command/inplace_deploy.sh [dev|prod] [branch]
```