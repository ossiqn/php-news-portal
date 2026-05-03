-- haber.sql
CREATE DATABASE IF NOT EXISTS haberdb DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE haberdb;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    color VARCHAR(20) DEFAULT '#dc2626',
    sort_order INT DEFAULT 0
);

CREATE TABLE news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    slug VARCHAR(500) NOT NULL UNIQUE,
    content LONGTEXT,
    summary TEXT,
    category_id INT,
    image VARCHAR(500),
    status TINYINT(1) DEFAULT 1,
    is_featured TINYINT(1) DEFAULT 0,
    is_breaking TINYINT(1) DEFAULT 0,
    views INT DEFAULT 0,
    published_at DATETIME,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

INSERT INTO categories (name, slug, color, sort_order) VALUES
('Gündem', 'gundem', '#dc2626', 1),
('Spor', 'spor', '#2563eb', 2),
('Ekonomi', 'ekonomi', '#16a34a', 3),
('Teknoloji', 'teknoloji', '#9333ea', 4),
('Dünya', 'dunya', '#ea580c', 5),
('Sağlık', 'saglik', '#0891b2', 6);

INSERT INTO news (title, slug, content, summary, category_id, image, status, is_featured, is_breaking, views, published_at) VALUES
('Türkiye Ekonomisinde Yeni Gelişmeler Yaşanıyor', 'turkiye-ekonomisinde-yeni-gelismeler', '<p>Türkiye ekonomisinde son dönemde yaşanan gelişmeler piyasaları yakından etkiliyor. Uzmanlar konuya ilişkin değerlendirmelerini paylaştı.</p><p>Merkez Bankası açıkladığı son verilere göre enflasyon rakamları beklentilerin altında kaldı. Bu gelişme döviz kurlarında da etkisini gösterdi.</p>', 'Türkiye ekonomisinde son dönemde yaşanan gelişmeler piyasaları yakından etkiliyor.', 3, 'https://picsum.photos/seed/haber1/800/500', 1, 1, 0, 1520, NOW() - INTERVAL 1 HOUR),
('Süper Lig\'de Şampiyonluk Yarışı Kızışıyor', 'super-ligde-sampiyonluk-yarisi', '<p>Süper Lig\'de son haftalar büyük heyecana sahne oluyor. Şampiyonluk yarışında birden fazla takım iddia sahibi konumunda.</p><p>Bu hafta oynanan maçlarda kritik sonuçlar çıktı ve puan farkları iyice kapandı.</p>', 'Süper Lig\'de son haftalar büyük heyecana sahne oluyor.', 2, 'https://picsum.photos/seed/haber2/800/500', 1, 1, 0, 3200, NOW() - INTERVAL 2 HOUR),
('Yapay Zeka Teknolojisinde Çığır Açan Gelişme', 'yapay-zeka-teknolojisinde-cigir', '<p>Yapay zeka alanında dünyanın önde gelen teknoloji şirketleri yeni ürünlerini tanıttı. Piyasaya sürülen model insan beynine çok daha yakın sonuçlar üretiyor.</p>', 'Yapay zeka alanında çığır açan yeni gelişmeler yaşanıyor.', 4, 'https://picsum.photos/seed/haber3/800/500', 1, 1, 1, 890, NOW() - INTERVAL 3 HOUR),
('Sağlık Bakanlığı\'ndan Önemli Açıklama', 'saglik-bakanligindan-onemli-aciklama', '<p>Sağlık Bakanlığı vatandaşları ilgilendiren önemli bir açıklama yaptı. Açıklamada yeni sağlık uygulamaları ve hizmetlere dair detaylar paylaşıldı.</p>', 'Sağlık Bakanlığı vatandaşları ilgilendiren önemli açıklama yaptı.', 6, 'https://picsum.photos/seed/haber4/800/500', 1, 0, 1, 670, NOW() - INTERVAL 4 HOUR),
('Dünya Genelinde İklim Krizi Derinleşiyor', 'dunya-genelinde-iklim-krizi', '<p>Birleşmiş Milletler\'in son raporuna göre iklim krizi giderek derinleşiyor. Küresel ısınmaya karşı acil önlemler alınması gerektiği vurgulandı.</p>', 'BM raporuna göre iklim krizi giderek derinleşiyor.', 5, 'https://picsum.photos/seed/haber5/800/500', 1, 0, 0, 445, NOW() - INTERVAL 5 HOUR),
('Meclis\'te Kritik Oturum Gerçekleştirildi', 'mecliste-kritik-oturum', '<p>Türkiye Büyük Millet Meclisi\'nde bugün kritik bir oturum gerçekleştirildi. Gündemdeki yasa tasarıları uzun tartışmaların ardından kabul edildi.</p>', 'TBMM\'de kritik oturum gerçekleştirildi.', 1, 'https://picsum.photos/seed/haber6/800/500', 1, 0, 0, 1100, NOW() - INTERVAL 6 HOUR),
('Borsa İstanbul\'da Rekor Kırıldı', 'borsa-istanbulda-rekor-kirildi', '<p>Borsa İstanbul\'da bugün tarihi bir rekor kırıldı. BIST 100 endeksi tüm zamanların en yüksek seviyesini gördü.</p>', 'Borsa İstanbul\'da tarihi rekor kırıldı.', 3, 'https://picsum.photos/seed/haber7/800/500', 1, 0, 1, 2300, NOW() - INTERVAL 7 HOUR),
('Milli Takım\'da Yeni Teknik Direktör Dönemi', 'milli-takimda-yeni-teknik-direktor', '<p>Türkiye A Milli Futbol Takımı\'nda yeni teknik direktör dönemi başlıyor. Federasyon yönetimi ismi açıkladı.</p>', 'Milli Takım\'da yeni teknik direktör dönemi başlıyor.', 2, 'https://picsum.photos/seed/haber8/800/500', 1, 0, 0, 1800, NOW() - INTERVAL 8 HOUR),
('Elektrikli Araç Satışları Patladı', 'elektrikli-arac-satislari-patladi', '<p>Türkiye\'de elektrikli araç satışları geçen yıla göre yüzde yüz artış gösterdi. Togg\'un katkısıyla pazar hızla büyümeye devam ediyor.</p>', 'Türkiye\'de elektrikli araç satışları yüzde yüz arttı.', 4, 'https://picsum.photos/seed/haber9/800/500', 1, 0, 0, 990, NOW() - INTERVAL 9 HOUR),
('Orman Yangınlarıyla Mücadelede Yeni Strateji', 'orman-yanginlariyla-mucadelede-strateji', '<p>Tarım ve Orman Bakanlığı orman yangınlarıyla mücadelede yeni bir strateji belirledi. Erken uyarı sistemleri yaygınlaştırılacak.</p>', 'Bakanlık orman yangınlarıyla mücadelede yeni strateji belirledi.', 1, 'https://picsum.photos/seed/haber10/800/500', 1, 0, 0, 560, NOW() - INTERVAL 10 HOUR),
('Avrupa\'da Siyasi Çalkantı Sürüyor', 'avrupada-siyasi-calkanti-suruyor', '<p>Avrupa\'nın önde gelen ülkelerinde siyasi çalkantı sürüyor. Birçok hükümette güven bunalımı baş gösterdi.</p>', 'Avrupa\'da siyasi çalkantı sürüyor.', 5, 'https://picsum.photos/seed/haber11/800/500', 1, 0, 0, 330, NOW() - INTERVAL 11 HOUR),
('Kanser Tedavisinde Umut Veren Gelişme', 'kanser-tedavisinde-umut-veren-gelisme', '<p>Bilim insanları kanser tedavisinde umut veren yeni bir yöntem geliştirdi. Klinik deneylerde başarılı sonuçlar elde edildi.</p>', 'Kanser tedavisinde umut veren yeni yöntem geliştirildi.', 6, 'https://picsum.photos/seed/haber12/800/500', 1, 0, 0, 2100, NOW() - INTERVAL 12 HOUR);