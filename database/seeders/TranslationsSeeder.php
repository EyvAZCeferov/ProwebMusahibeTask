<?php

namespace Database\Seeders;

use App\Models\BaseSettings\Translations;
use Illuminate\Database\Seeder;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Stichoza\GoogleTranslate\GoogleTranslate;


class TranslationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $keys = [
            'İstifadəçi uğurla qeydiyyatdan keçdi.',
            'İstifadəçi uğurla giriş etdi.',
            'İstifadəçi hesabdan çıxış etdi',
            'Daxili server xətası baş verdi.',
            'Verilən məlumatlar yanlışdır.',
            'Autentifikasiya tələb olunur.',
            'Bu əməliyyatı etmək üçün icazəniz yoxdur.',
            'Axtarılan mənbə tapılmadı.',
            'API yolu tapılmadı',
            'Silindi',
            'Bu valyuta üçün təyin etdiyiniz əskinaz artıq mövcuddur.',
            'Siz yalnız özünüz üçün hesab yarada bilərsiniz. İstifadəçi ID-si göndərilməməlidir.',
            'Başqa bir istifadəçi üçün hesab yaradarkən istifadəçi ID-si mütləqdir.',
            'Təyin edilən istifadəçi mövcud deyil.',
            'Hesabın valyutası dəyişdirilə bilməz.',
            'Hesab başqa bir istifadəçiyə təyin edilə bilməz.',
            'Bu valyuta üçün təyin etdiyiniz əskinaz artıq mövcuddur.',
            'İstifadəçi şəxs olmalıdır.',
            'start_date end_date-dən əvvəl və ya bərabər olmalıdır',
            'end_date start_date-dən sonra və ya bərabər olmalıdır.',
            'Tarix formatı yanlışdır: xx.xx.xxxx olmalıdır',
            'Siz yalnız mövcud hesab parametrlərini yeniləyə bilərsiniz. İstifadəçi ID-si göndərilməməlidir.',
            'Əməliyyat uğurla tamamlandı',
            'Məbləğ 0-dan böyük olmalıdır.',
            'Hesabda kifayət qədər vəsait yoxdur.',
            'Api sorğusu həddən artıqdır',
            'ATM-dən nağdlaşdırma uğurla tamamlandı.',
            'Nağdlaşdırma cəhdi uğursuz oldu. Səbəb kodu:',
            'Uğursuz əməliyyat jurnalını yaratmaq alınmadı',
            'Hesaba köçürmə:',
            'Hesaba mədaxil:',
            'Hesablar fərqli olmalıdır.',
            'Bu əməliyyat yalnız öz hesablarınız arasında mümkündür.',
            'Öz hesabınıza köçürmə üçün "self-transfer" endpointindən istifadə edin.',
            'Valyuta məzənnəsi tapılmadı.',
            'complete' => "Tamamlandı",
            'pending' => "Gözləmədə",
            'failed' => "Uğursuz",
            'insufficient_balance' => "Qeyri-kafi balans",
            'insufficient_funds' => "Hesabda kifayət qədər məbləğ yoxdur",
            'self_transfer' => "Öz hesabları arasında mübadilə",
            'external_transfer' => "Başqa hesaba mübadilə",
            'banknote_combination_error' => "Məbləğ mövcud əskinazlarla ödənilə bilmir",
            'amount_too_low' => "Məbləğ 0-dan böyük olmalıdır",
            'daily_limit_exceeded' => "Gündəlik nağdlaşdırma limitini aşırsınız",
        ];
        $sourceLocale = config('app.locale', 'az');
        $supportedLocales = LaravelLocalization::getSupportedLanguagesKeys();

        foreach ($keys as $key) {
            if (Translations::where('key', $key)->select("key")->exists()) {
                continue;
            }

            $value = [
                $sourceLocale => $key,
            ];
            foreach ($supportedLocales as $langkey) {
                if ($langkey === $sourceLocale) {
                    continue;
                }

                $value[$langkey] = GoogleTranslate::trans($key, $langkey, $sourceLocale);
            }


            Translations::create([
                'key' => $key,
                'value' => $value
            ]);
        }
    }
}
