# Anatolian Supply Co.

Askeri giyim ve taktik ekipman tanıtım sitesi. Sitede satış yok: ürün sayfaları Instagram hesabına yönlendirir ve ziyaretçinin ürün bilgileriyle birlikte e-posta ile bilgi istemesini sağlar.

## Ürün yönetimi paneli

Panelin giriş ekranı ya da şifresi yok; uzun bir bağlantı var ve bu bağlantının kendisi anahtar görevi görüyor. Sitenin hiçbir yerinde bu bağlantıya giden bir düğme yok ve arama motorlarına kapalı. Bağlantıyı bilen herkes ürün ekleyip silebilir, o yüzden kimseyle paylaşmıyorum.

**Şu anki panel bağlantım:**

```
http://192.168.1.105:8000/yonetim/4V6gbiGGabzYCMxSfu0hWcax5zLKpcPDbHCx5Ovd
```

Bağlantı iki parçadan oluşur:

- **Baş kısmı** (`http://192.168.1.105:8000`): siteyi hangi adresten açıyorsam o. Bilgisayarımın adresi değişirse sadece bu kısmı değiştiriyorum.
- **Son kısmı** (`/yonetim/4V6gb…`): gizli kod. `.env` dosyasındaki `STORE_ADMIN_KEY` değeridir.

### Panele nasıl girerim?

1. Proje klasöründe terminali açıyorum (VS Code’da üst menüden **Terminal > New Terminal**).
2. Siteyi başlatıyorum:
   ```bash
   php artisan serve --host=192.168.1.105 --port=8000
   ```
3. Tarayıcıda yukarıdaki panel bağlantısını açıyorum.
4. Ürün listesi açılıyor. Bir dahaki sefere kolay girmek için bağlantıyı yer imlerine ekliyorum.

Aynı Wi-Fi’ye bağlı telefonumdan da aynı bağlantıyla panele girebilirim.

Kodda tek harf bile yanlış olursa “sayfa bulunamadı” çıkar. Bu bir hata değil: yanlış kodla gelen biri panelin var olduğunu bile anlamasın diye böyle.

### Panel bağlantısını nasıl değiştiririm?

Bağlantıyı biriyle paylaştıysam, yanlışlıkla bir yere yapıştırdıysam ya da siteyi yayına almadan önce bağlantıyı değiştiriyorum.

1. Proje klasöründe terminali açıyorum.
2. Şu komutu yazıp Enter’a basıyorum (adresi siteyi açtığım adresle değiştiriyorum):
   ```bash
   php artisan yonetim:yeni-anahtar --adres=http://192.168.1.105:8000
   ```
3. Komut ekrana yeni panel bağlantısını yazıyor. O bağlantıyı kopyalıyorum.
4. Site açıksa başka bir şey yapmama gerek yok; `php artisan serve` değişikliği fark edip kendini yeniden başlatıyor.
5. Tarayıcıdaki eski yer imini silip yeni bağlantıyı yer imlerine ekliyorum.
6. Bu dosyanın en üstündeki “Şu anki panel bağlantım” kısmına yeni bağlantıyı yazıyorum.
7. Kontrol ediyorum: eski bağlantı “sayfa bulunamadı” göstermeli, yenisi ürün listesini açmalı.

**Komut çalışmazsa elle değiştirmek için:**

1. Proje klasöründeki `.env` dosyasını açıyorum.
2. `STORE_ADMIN_KEY=` ile başlayan satırı buluyorum.
3. Eşittirden sonraki kodu silip yerine en az 30 karakterlik rastgele harf ve rakam yazıyorum. Boşluk, Türkçe karakter ve işaret kullanmıyorum.
4. Dosyayı kaydediyorum.
5. Yeni bağlantım şu oluyor: `http://192.168.1.105:8000/yonetim/` + yazdığım kod.
6. Yukarıdaki 5., 6. ve 7. adımları uyguluyorum.

### Paneli tamamen nasıl kapatırım?

1. `.env` dosyasını açıyorum.
2. `STORE_ADMIN_KEY=` satırındaki kodu silip satırı sadece `STORE_ADMIN_KEY=` olarak bırakıyorum.
3. Dosyayı kaydediyorum. Artık hiçbir bağlantı paneli açmıyor.
4. Paneli tekrar açmak istediğimde `php artisan yonetim:yeni-anahtar` komutunu çalıştırıyorum.

> Bu dosya projeyle birlikte paylaşılır. Projeyi GitHub’da herkese açık bir depoya koyarsam buradaki kod da görünür olur. Canlı sitede mutlaka farklı bir kod kullanıyorum ve o kodu buraya yazmıyorum.

### Yeni ürünü nasıl eklerim?

1. Panel bağlantısını açıp **Yeni ürün ekle** düğmesine basıyorum.
2. **Fotoğraflar:** Kutunun üstüne dokunuyorum ya da fotoğrafları kutuya sürüklüyorum. Birden fazla fotoğrafı aynı anda seçebiliyorum (en fazla 8). Telefonla çektiğim büyük fotoğraflar yüklenmeden önce otomatik küçültülüyor. “Ana fotoğraf” olarak seçtiğim fotoğraf ürün kartında görünüyor.
3. **Ürün bilgileri:** Ürün adını, kategorisini, tek cümlelik kısa açıklamasını ve detaylı açıklamasını yazıyorum. Ürün kodu otomatik öneriliyor; istersem değiştiriyorum.
4. **Renkler:** “Hızlı ekle” düğmeleriyle hazır renkleri tek dokunuşla ekliyorum ya da kendi rengimi yazıyorum. Bir renge fotoğraf eklersem, ziyaretçi o rengi seçtiğinde ürün fotoğrafı da değişiyor.
5. **Bedenler:** Satılan bedenlere dokunarak seçiyorum. Listede olmayan bir beden için alttaki kutuya yazıp **Ekle**’ye basıyorum.
6. **Öne çıkanlar:** Her satıra bir özellik yazıyorum.
7. **Teknik bilgiler:** Sol kutuya bilgi adını (Kumaş, Ağırlık…), sağ kutuya değerini yazıyorum. Boş satırlar kaydedilmiyor.
8. **Yayın:** “Sitede yayınla” kapalıysa ürün kaydediliyor ama sitede görünmüyor. “Sıra” küçük olan önce görünüyor; 10, 20, 30… diye verirsem araya ürün eklemek kolay oluyor.
9. Sağdaki (telefonda en alttaki) **Önizleme**’de ürün kartının ana sayfada nasıl görüneceğini canlı görüyorum. Bitince **Ürünü ekle**’ye basıyorum.

Ürün listesinde **Düzenle** ile her şeyi sonradan değiştirebiliyorum, **Sil** ile ürünü fotoğraflarıyla birlikte kalıcı olarak siliyorum. Bir ürünü geçici olarak gizlemek istersem silmek yerine “Sitede yayınla”yı kapatıyorum.

### Fotoğraflar sitede nasıl görünür?

- **Arka planı şeffaf PNG (dekupe):** Ürün doğrudan zemine oturur, altında gölgesiyle görünür. En şık sonuç budur.
- **Normal fotoğraf (JPEG ya da arka planlı PNG):** Bantla tutturulmuş bir fotoğraf baskısı gibi gösterilir.
- Kaydırırken oluşan hareketler, ana sayfada askıdaki etiketlerin sallanması ve ürün sayfasında fareyle eğilme efekti fotoğraflı ürünlerde de aynen çalışır.

Örnek olarak eklenen **Ağrı Boonie Şapka** ürünü iki türü de gösterir: “Orman deseni” normal bir JPEG fotoğraf, “Çöl deseni” arka planı silinmiş bir PNG’dir. Bu fotoğraflar Wikimedia Commons’taki kamu malı (public domain) görsellerdir.

## Bilgisayarda çalıştırma

İlk kurulum:

```bash
composer install
npm install
cp .env.example .env          # yalnızca .env yoksa
php artisan key:generate      # yalnızca yeni .env için
php artisan migrate --seed    # veritabanı tablolarını ve örnek ürünleri oluşturur
php artisan storage:link      # yüklenen fotoğrafların sitede görünmesi için
npm run build
```

Sonra her seferinde:

```bash
php artisan serve --host=192.168.1.105 --port=8000
```

Site `http://192.168.1.105:8000` adresinde açılır ve aynı Wi-Fi’deki telefonlardan da görülebilir. Bu komutla `localhost:8000` adresi çalışmaz; hem `localhost` hem de bu adres çalışsın istersem `--host=0.0.0.0` kullanıyorum. Bilgisayarımın adresini öğrenmek için terminale `ipconfig` yazıp “IPv4 Address” satırına bakıyorum.

Tasarımda değişiklik yaparken `composer run dev` komutu değişiklikleri anında yansıtır.

`php artisan db:seed` komutu dört örnek ürünü baştan yazar; panelden bu ürünlerde yaptığım değişiklikler kaybolur. Panelden eklediğim ürünlere dokunmaz.

## E-posta ile bilgi talebi

Ürün sayfasındaki “E-postayla bilgi al” formu, ziyaretçinin mesajını ürün bilgileriyle birlikte `anatoliansupplyco@gmail.com` adresine gönderir. Gelen e-postaya “Yanıtla” dediğimde cevap doğrudan ziyaretçiye gider.

E-postaların gerçekten gelmesi için `.env` içinde Gmail ayarlarını giriyorum. Hesapta iki adımlı doğrulama açık olmalı ve şifre olarak Google hesabından oluşturulan 16 haneli **Uygulama şifresi** kullanılmalı:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=anatoliansupplyco@gmail.com
MAIL_PASSWORD=uygulama-sifresi
```

Bu ayar yapılmadan önce e-postalar gönderilmez, `storage/logs/laravel.log` dosyasına yazılır.

## Önemli ayarlar (`.env`)

| Ayar | Ne işe yarar |
| --- | --- |
| `STORE_ADMIN_KEY` | Panel bağlantısındaki gizli kod. Boşsa panel kapalıdır. |
| `STORE_MEDIA_DISK` | Ürün fotoğraflarının saklandığı yer. Bilgisayarda `public`. |
| `STORE_INQUIRY_EMAIL` | Bilgi taleplerinin gideceği adres. Varsayılan `anatoliansupplyco@gmail.com`. |
| `STORE_INSTAGRAM_URL` | Sitedeki tüm Instagram düğmelerinin gittiği adres. |

## Vercel’e taşımadan önce

Site şu an bilgisayardaki bir SQLite dosyasını ve `storage` klasörünü kullanıyor. Vercel’de sunucunun dosyaları kalıcı olmadığı için yayına almadan önce şunlar ayarlanmalı:

- **Veritabanı:** Barındırılan bir veritabanı (örneğin Vercel Marketplace’teki Neon Postgres). Bağlantı bilgileri `DB_*` ayarlarıyla verilir; kodda değişiklik gerekmez.
- **Fotoğraflar:** S3 uyumlu bir depolama alanı (Cloudflare R2, AWS S3 gibi). `STORE_MEDIA_DISK=s3` yapılıp depolama bilgileri girilir; bunun için `league/flysystem-aws-s3-v3` paketi eklenir.
- **Oturum:** `SESSION_DRIVER=cookie`.
- **Panel kodu:** Canlı site için bilgisayardakinden farklı yeni bir kod belirlenir ve Vercel’in ortam değişkenlerine `STORE_ADMIN_KEY` olarak girilir.
- **PHP çalıştırma:** Laravel Vercel’de topluluk tarafından geliştirilen PHP çalışma ortamıyla çalışır; `vercel.json` ve küçük bir giriş dosyası eklenir.

Fotoğraflar tarayıcıda zaten küçültüldüğü için Vercel’in istek boyutu sınırına takılmaz.
