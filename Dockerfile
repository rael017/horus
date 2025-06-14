# Use uma imagem base oficial do PHP 8.2
FROM php:8.2-cli-bookworm

# Instala as dependências do sistema necessárias para as extensões PHP
# CORREÇÃO: Adicionada a biblioteca libbrotli-dev para a compilação do Swoole
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    libbrotli-dev \
  && rm -rf /var/lib/apt/lists/*

# Instala a extensão PECL para instalar o Swoole
RUN pecl install swoole

# Ativa as extensões PHP necessárias para um framework web
RUN docker-php-ext-install pdo pdo_mysql zip
RUN docker-php-ext-enable swoole

# Define o diretório de trabalho dentro do contentor
WORKDIR /var/www/html

# Copia o seu código para dentro do contentor (isto acontecerá através do docker-compose)
COPY . .

# Expõe a porta que o servidor Nexus irá usar
EXPOSE 9501
