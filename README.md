# WATCHED

Bu proje, kullanıcıların film arayabilmesini, izledikleri filmleri listeleyebilmesini ve kullanıcı yorumlarını bırakabilmesini sağlayan bir film yönetim sistemidir. Ayrıca, admin paneli üzerinden kullanıcı yönetimi ve film ekleme işlemleri yapılabilir.

## Proje Yapısı

- **Frontend**: HTML, CSS, JavaScript (jQuery) kullanarak oluşturulmuştur.
- **Backend**: PHP ile geliştirilmiş ve veritabanı işlemleri MySQL kullanılarak yapılmıştır.
- **API**: TMDB (The Movie Database) API'si kullanılarak film verileri alınmaktadır.

### Önemli Dosyalar
- `index.php`: Ana sayfa, film arama ve listeleme işlemleri burada yapılır.
- `dashboard.php`: Kullanıcıların ve adminlerin film ekleme ve yönetme işlemleri yaptığı sayfa.
- `admin_panel.php`: Adminlerin kullanıcı yönetimi yaptığı panel.
- `update_movie.php`: Film ekleme işlemi.
- `remove_movie.php`: Film silme işlemi.
- `dashboard.js`: Sayfa dinamiklerini yönetmek için JavaScript kodlarını içerir.

---

## Kullanıcı Yorumları Eklenmesi

Kullanıcıların filmler hakkında yorum yapabilmesi için aşağıdaki adımlar izlenmelidir:

### 1. **Yorum Sistemi Tasarımı**
   - Film detay sayfasına bir yorum formu eklenmeli.
   - Yorum yapılacak film, API'den alınan film verisi ile ilişkili olmalı.
   - Kullanıcılar, yorumlarını yazıp gönderebilmeli.
   
### 2. **Yorum Veritabanı Yapısı**
   - `comments` tablosu, kullanıcı yorumlarını tutacak.
   - `comments` tablosu şu alanlara sahip olmalıdır:
     - `id` (otomatik artan)
     - `movie_id` (filmin id'si)
     - `user_id` (yorum yapan kullanıcı)
     - `comment` (yorum metni)
     - `created_at` (yorumun tarihi)

### 3. **Yorum Ekleme**
   - Kullanıcı yorum eklemek için, `add_comment.php` gibi bir PHP dosyası oluşturulabilir.
   - Kullanıcıların yorumlarını API veya AJAX ile backend'e gönderin ve veritabanına kaydedin.
   - Yorumlar, ilgili film detay sayfasında listelenmeli.

### 4. **Yorumları Listeleme**
   - `movie_details.php` gibi bir sayfada, filmle ilgili yorumları göstermek için bir PHP fonksiyonu kullanılabilir.
   - Yorumlar, film id'sine göre veritabanından alınmalı ve kullanıcıların görmesi için frontend'e aktarılmalıdır.

---

## Kurulum ve Çalıştırma

1. **Veritabanı Bağlantısı**
   - `config.php` dosyasına veritabanı bilgilerini girin.
   - Gerekli tabloları oluşturun (`users`, `comments`, `movies`, `admins` vb.). (Zaten xampp e göre ayarlı ama farklı bir db kullanıyorsan şifresini, adını fln girmelisin.)

2. **API Anahtarı**
   - TMDB API anahtarınızı almak için [TMDB API](https://www.themoviedb.org/settings/api) sayfasına kaydolun ve anahtarınızı api_key.txt dosyasına ekleyin.

3. **Çalıştırma**
   - Projeyi bir web sunucusuna yükleyin (örneğin, XAMPP, Apache).
   - Veritabanı ve API bağlantılarının doğru çalıştığından emin olun.

---

Bu `README.md` dosyası, frontend ve backend geliştirme için gerekli adımları ve yapmanız gereken işleri kısaca açıklamaktadır. Projenin her aşamasında sorularınız varsa, lütfen irtibata geçmekten çekinmeyin.

