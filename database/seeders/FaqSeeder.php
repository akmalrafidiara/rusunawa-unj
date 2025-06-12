<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'What is the purpose of this FAQ?',
                'answer' => 'This FAQ is designed to provide answers to common questions about our services and policies.',
            ],
            [
                'question' => 'How can I contact customer support?',
                'answer' => 'You can contact customer support via email at',
            ],
            [
                'question' => 'What are the business hours?',
                'answer' => 'Our business hours are Monday to Friday, 9 AM to 5 PM.',
            ],
            [
                'question' => 'Where can I find the terms and conditions?',
                'answer' => 'The terms and conditions can be found on our website under the "Terms" section.',
            ],
            [
                'question' => 'How do I reset my password?',
                'answer' => 'To reset your password, go to the login page and click on "Forgot Password".',
            ],
        ];
        foreach ($faqs as $faq) {
            Faq::firstOrCreate(
                ['question' => $faq['question']],
                ['answer' => $faq['answer']]
            );
        }    
    }
}
