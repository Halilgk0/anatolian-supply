<?php

namespace Database\Seeders;

use App\Catalog\ProductImages;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Demo catalog: three products drawn as SVG and one shown with real photos.
 */
class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(ProductImages $images): void
    {
        $olive = ['name' => 'Zeytin', 'hex' => '#5f6744', 'image' => null, 'cutout' => false];
        $coyote = ['name' => 'Coyote', 'hex' => '#a08259', 'image' => null, 'cutout' => false];
        $black = ['name' => 'Siyah', 'hex' => '#3a3b35', 'image' => null, 'cutout' => false];
        $grey = ['name' => 'Kurşuni', 'hex' => '#64686a', 'image' => null, 'cutout' => false];

        Product::query()->updateOrCreate(['slug' => 'toros-saha-ceketi'], [
            'code' => 'AS-101',
            'name' => 'Toros Saha Ceketi',
            'category' => 'Dış giyim',
            'tagline' => 'Rüzgârı kesen, suyu iten, kolları arma panelli dört mevsim saha ceketi.',
            'description' => 'Toros, sabah ayazından öğle güneşine kadar gün boyu üzerinde kalacak bir ceket olarak tasarlandı. Ripstop dış kumaşı yırtılmaya karşı dayanıklı, su itici kaplaması ani yağmurda kuru tutar. Yakaya gizlenen kapüşon ve cırtlı kol ağızları hava değiştiğinde saniyeler içinde ayarlanır.',
            'illustration' => 'jacket',
            'images' => [],
            'colors' => [$olive, $coyote, $black],
            'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
            'features' => [
                'Su itici kaplamalı ripstop dış kumaş',
                'Yakaya katlanıp gizlenen kapüşon',
                'İki kolda arma takmaya uygun cırt panel',
                'Dört ön cep ve bir iç güvenlik cebi',
                'Cırtla ayarlanan kol ağızları ve büzgülü etek',
            ],
            'specs' => [
                ['label' => 'Kumaş', 'value' => '%65 pamuk, %35 polyester ripstop'],
                ['label' => 'Astar', 'value' => 'Nefes alan file astar'],
                ['label' => 'Ağırlık', 'value' => 'Yaklaşık 980 g (L beden)'],
                ['label' => 'Bakım', 'value' => '30 °C’de ters çevirerek yıkayın'],
            ],
            'sort_order' => 10,
        ]);

        Product::query()->updateOrCreate(['slug' => 'kackar-kargo-pantolon'], [
            'code' => 'AS-204',
            'name' => 'Kaçkar Kargo Pantolon',
            'category' => 'Alt giyim',
            'tagline' => 'Diz pedi cepli, esneyen kasıklı, sekiz cepli arazi pantolonu.',
            'description' => 'Kaçkar, yokuş tırmanırken de çömelirken de hareketi kısıtlamayan bir kesimle dikildi. Elastan katkılı ripstop kumaş esner ama formunu kaybetmez. Çift kat diz bölümüne ped yerleştirilebilir, yan kargo cepleri kapaklı ve sessiz kapanışlıdır.',
            'illustration' => 'pants',
            'images' => [],
            'colors' => [$coyote, $olive, $grey],
            'sizes' => ['28', '30', '32', '34', '36', '38', '40'],
            'features' => [
                'Diz pedi yerleştirilebilen çift kat diz',
                'Rahat hareket için ağ (gusset) kasık',
                'Kapaklı kargo cepleri dahil toplam sekiz cep',
                '50 mm taktik kemere uygun geniş kemer köprüleri',
                'Bağcıkla daraltılabilen paçalar',
            ],
            'specs' => [
                ['label' => 'Kumaş', 'value' => '%98 pamuk, %2 elastan ripstop'],
                ['label' => 'Kesim', 'value' => 'Düz paça, normal bel'],
                ['label' => 'Ağırlık', 'value' => 'Yaklaşık 720 g (32 beden)'],
                ['label' => 'Bakım', 'value' => '30 °C’de yıkayın, ağartıcı kullanmayın'],
            ],
            'sort_order' => 20,
        ]);

        Product::query()->updateOrCreate(['slug' => 'erciyes-35l-sirt-cantasi'], [
            'code' => 'AS-310',
            'name' => 'Erciyes 35L Sırt Çantası',
            'category' => 'Ekipman',
            'tagline' => 'MOLLE panelli, su kesesi bölmeli, üç günlük arazi çantası.',
            'description' => 'Erciyes, kısa kamplar ve uzun yürüyüşler için 35 litrelik dengeli bir hacim sunar. Ana gözü bavul gibi tamamen açılır, böylece aradığınızı dibe kadar boşaltmadan bulursunuz. MOLLE şeritlerle ek cep, matara ya da ilk yardım kiti takılabilir.',
            'illustration' => 'backpack',
            'images' => [],
            'colors' => [$coyote, $olive, $black],
            'sizes' => ['Tek beden'],
            'features' => [
                'Ön ve yan yüzeylerde MOLLE/PALS şeritler',
                '3 litrelik su kesesine uygun iç bölme',
                'Bavul gibi tamamen açılan ana göz',
                'Hava kanallı, yastıklı sırt paneli',
                'Çıkarılabilir bel ve göğüs kayışı',
            ],
            'specs' => [
                ['label' => 'Hacim', 'value' => '35 litre'],
                ['label' => 'Kumaş', 'value' => '1000D Cordura naylon'],
                ['label' => 'Ölçüler', 'value' => '50 × 30 × 25 cm'],
                ['label' => 'Ağırlık', 'value' => '1,6 kg'],
            ],
            'sort_order' => 30,
        ]);

        // Public-domain photos from Wikimedia Commons:
        // File:US_Army_Issue_Bonnie_Hat-BDU_variant-circa_1990s.jpg (JPEG with background)
        // File:Boonie_wuestentarn.jpg (background removed and saved as a transparent PNG)
        $woodland = $this->storeDemoImage($images, 'agri-boonie-orman.jpg');
        $desert = $this->storeDemoImage($images, 'agri-boonie-col.png');

        Product::query()->updateOrCreate(['slug' => 'agri-boonie-sapka'], [
            'code' => 'AS-415',
            'name' => 'Ağrı Boonie Şapka',
            'category' => 'Aksesuar',
            'tagline' => 'Geniş kenarlı, havalandırma delikli, çene bağcıklı arazi şapkası.',
            'description' => 'Ağrı, güneşte ve hafif yağmurda yüzü ve enseyi koruyan klasik bir boonie şapkadır. Ripstop kumaşı katlanıp cebe sığar, açıldığında formunu korur. Taç çevresindeki şerit kamuflaj dalı ya da işaret takmak için kullanılabilir.',
            'illustration' => null,
            'images' => [$woodland, $desert],
            'colors' => [
                ['name' => 'Orman deseni', 'hex' => '#5b5a3c', 'image' => $woodland['path'], 'cutout' => $woodland['cutout']],
                ['name' => 'Çöl deseni', 'hex' => '#c9b48e', 'image' => $desert['path'], 'cutout' => $desert['cutout']],
            ],
            'sizes' => ['S', 'M', 'L', 'XL'],
            'features' => [
                'Her yönde 7 cm geniş kenar',
                'Tacın iki yanında metal havalandırma delikleri',
                'Ayarlanabilir, kilitli çene bağcığı',
                'Kamuflaj dalı takmak için taç şeridi',
            ],
            'specs' => [
                ['label' => 'Kumaş', 'value' => '%50 pamuk, %50 naylon ripstop'],
                ['label' => 'Kenar genişliği', 'value' => '7 cm'],
                ['label' => 'Bakım', 'value' => '30 °C’de yıkayın, kurutma makinesine atmayın'],
            ],
            'sort_order' => 40,
        ]);
    }

    /**
     * @return array{path: string, cutout: bool}
     */
    private function storeDemoImage(ProductImages $images, string $fileName): array
    {
        $source = database_path("seeders/images/{$fileName}");
        $path = "products/demo-{$fileName}";

        Storage::disk(config('store.media_disk'))->put($path, file_get_contents($source));

        return ['path' => $path, 'cutout' => $images->hasTransparentEdges($source)];
    }
}
