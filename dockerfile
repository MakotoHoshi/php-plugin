FROM amazonlinux:2

RUN yum update -y
RUN yum remove -y php-*
RUN yum update -y amazon-linux-extras
RUN amazon-linux-extras install nginx1
RUN amazon-linux-extras enable php8.0
RUN amazon-linux-extras enable ansible2
RUN yum clean metadata && yum install -y php-cli-8.0.8-1.amzn2 php-pdo-8.0.8-1.amzn2 php-fpm-8.0.8-1.amzn2 php-mysqlnd-8.0.8-1.amzn2 php-xml-8.0.8-1.amzn2 php-mbstring-8.0.8-1.amzn2 mariadb-5.5.68-1.amzn2 ansible-2.9.23-1.amzn2 7.4p1-21.amzn2.0.3

# RUN composer global require laravel/installer
# ENV PATH "$PATH:$HOME/.config/composer/vendor/bin"
# RUN echo $PATH

COPY build_command/ /build_command/
RUN chmod 744 /build_command/start.sh
CMD ["/build_command/start.sh"]

WORKDIR /repo/docker/src/system

# プロビジョニング直後のデプロイ
# git clone -b [ブランチ] [リポジトリ] .
# インプレースデプロイ
# git fetch origin [ブランチ]
# git checkout [ブランチ]
# chmod 777 -R storage
# composer install