# راه‌اندازی داکر بچرخ

## ۱. یه تغییر کد ضروری (قبل از بالا آوردن)

`app/Services/Elasticsearch.php` الان این‌طوریه:

```php
$this->client = ClientBuilder::create()->build();
```

این بدون هاست مشخص، پیش‌فرض می‌ره رو `localhost:9200`. داخل داکر، Elasticsearch
یه کانتینر جداست با اسم `elasticsearch`، نه `localhost`. پس باید بشه:

```php
$this->client = ClientBuilder::create()
    ->setHosts([env('ELASTICSEARCH_HOST', 'localhost:9200')])
    ->build();
```

و توی `.env`:
```
ELASTICSEARCH_HOST=elasticsearch:9200
```

بدون این تغییر، کل جستجوی سایت (`IndexController::mainSearch`) و ایندکس‌شدن خودکار
دسته‌بندی/آیتم/سوال/بلاگ (توی `boot()` مدل‌های Mongo) کار نمی‌کنه.

## ۲. مقادیری که باید به `.env` اضافه/تنظیم کنی

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=becharkh
DB_USERNAME=becharkh
DB_PASSWORD=یک‌رمز‌قوی

DB_MONGO_HOST=mongo
DB_MONGO_PORT=27017
DB_MONGO_DATABASE=becharkh
DB_MONGO_USERNAME=becharkh
DB_MONGO_PASSWORD=یک‌رمز‌قوی

REDIS_HOST=redis
REDIS_PORT=6379
REDIS_CLIENT=predis

ELASTICSEARCH_HOST=elasticsearch:9200

FTP_HOST=dl.becharkh.com
FTP_USERNAME=...
FTP_PASSWORD=...
```

(`FTP_*` همونیه که همین الان داری — فایل‌های آپلودی رو این پروژه از یه FTP بیرونی
می‌خونه/می‌نویسه، نه از داکر، پس چیزی برای اون تو compose نساختم.)

## ۳. بالا آوردن

```bash
cp .env.example .env   # و مقادیر بالا رو پر کن
docker compose build
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate   # فقط برای جدول‌های mysql باقی‌مانده
```

سایت روی `http://localhost:8006` بالا میاد.