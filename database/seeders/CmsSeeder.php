<?php

namespace Database\Seeders;

use App\Models\Gallery;
use App\Models\Notice;
use Illuminate\Database\Seeder;

class CmsSeeder extends Seeder
{
    public function run(): void
    {
        $notices = [
            [
                'title' => 'DCA, ADCA, TYPING, TALLY समेत सभी प्रकार के कंप्यूटर कोर्स में दाखिला के लिए हमारे नजदीकी कंप्यूटर सेंटर से सम्पर्क करें।',
                'is_marquee' => true,
                'target_audience' => 'all',
                'published_at' => now(),
            ],
            [
                'title' => 'सत्र 2026-27 के लिए नए अधिकृत फ्रेंचाइज / कंप्यूटर स्टडी सेंटर हेतु ऑनलाइन आवेदन प्रारंभ हैं।',
                'is_marquee' => true,
                'target_audience' => 'centres',
                'published_at' => now(),
            ],
            [
                'title' => 'वार्षिक बोर्ड परीक्षा परिणाम घोषित कर दिए गए हैं। छात्र अपना सर्टिफिकेट एवं मार्कशीट ऑनलाइन वेरीफाई कर सकते हैं।',
                'is_marquee' => true,
                'target_audience' => 'students',
                'published_at' => now(),
            ],
        ];

        foreach ($notices as $n) {
            Notice::firstOrCreate(['title' => $n['title']], $n);
        }

        $galleries = [
            ['title' => 'Computer Practical Lab Session', 'category' => 'Lab Infrastructure', 'image_path' => 'uploads/gallery/gallery_lab_1.jpg', 'sort_order' => 1],
            ['title' => 'Smart Classroom & Presentation Hall', 'category' => 'Infrastructure', 'image_path' => 'uploads/gallery/gallery_lab_2.jpg', 'sort_order' => 2],
            ['title' => 'Annual Convocation & Certificate Ceremony', 'category' => 'Events', 'image_path' => 'uploads/gallery/gallery_cert_1.jpg', 'sort_order' => 3],
            ['title' => 'Certificate Award Distribution', 'category' => 'Events', 'image_path' => 'uploads/gallery/gallery_cert_2.jpg', 'sort_order' => 4],
            ['title' => 'Typing & Speed Test Competition', 'category' => 'Competitions', 'image_path' => 'uploads/gallery/gallery_student_1.jpg', 'sort_order' => 5],
            ['title' => 'Director & Corporate Board Conference', 'category' => 'Corporate', 'image_path' => 'uploads/gallery/gallery_student_2.jpg', 'sort_order' => 6],
            ['title' => 'Student Free Scholarship Distribution', 'category' => 'CSR & Social', 'image_path' => 'uploads/gallery/gallery_student_3.jpg', 'sort_order' => 7],
        ];

        foreach ($galleries as $g) {
            Gallery::updateOrCreate(['title' => $g['title']], $g);
        }
    }
}
