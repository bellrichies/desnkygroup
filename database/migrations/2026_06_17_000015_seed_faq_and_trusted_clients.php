<?php

namespace Database\Migrations;

use App\Database\Migration;

/**
 * Seeds initial FAQ entries (from the contact page) and trusted client records.
 * Idempotent: each row is inserted only when a matching question / name does not exist.
 */
class SeedFaqAndTrustedClients extends Migration
{
    public function up(): void
    {
        $this->seedFaqs();
        $this->seedTrustedClients();
    }

    public function down(): void
    {
        $questions = [
            'How quickly will I get a response?',
            'Do you handle projects outside Lagos?',
            'Can I request bulk or B2B pricing?',
        ];

        foreach ($questions as $q) {
            $this->connection->delete("DELETE FROM faqs WHERE question = ?", [$q]);
        }

        $names = [
            'Nigerian National Petroleum Corporation (NNPC)',
            'TotalEnergies Nigeria',
            'Dangote Group',
            'Julius Berger Nigeria',
            'Shell Petroleum Development Company',
            'MTN Nigeria',
            'Flour Mills of Nigeria',
            'Zenith Bank Plc',
        ];

        foreach ($names as $name) {
            $this->connection->delete("DELETE FROM trusted_clients WHERE name = ?", [$name]);
        }
    }

    private function seedFaqs(): void
    {
        $faqs = [
            [
                'question'   => 'How quickly will I get a response?',
                'answer'     => 'We aim to respond to every enquiry within one business day. For urgent matters, call or message us on WhatsApp for the fastest reply.',
                'category'   => 'General',
                'sort_order' => 10,
            ],
            [
                'question'   => 'Do you handle projects outside Lagos?',
                'answer'     => 'Yes. We deliver engineering, energy, procurement, HSE and ICT projects nationwide across Nigeria, mobilising teams and equipment to your site.',
                'category'   => 'General',
                'sort_order' => 20,
            ],
            [
                'question'   => 'Can I request bulk or B2B pricing?',
                'answer'     => 'Absolutely. Tell us your requirements in the form and select the relevant service, and our team will prepare a tailored quotation.',
                'category'   => 'General',
                'sort_order' => 30,
            ],
        ];

        foreach ($faqs as $faq) {
            $exists = $this->connection->queryOne(
                "SELECT id FROM faqs WHERE question = ?",
                [$faq['question']]
            );

            if ($exists === null) {
                $this->connection->insert(
                    "INSERT INTO faqs (question, answer, category, sort_order, is_active) VALUES (?, ?, ?, ?, 1)",
                    [$faq['question'], $faq['answer'], $faq['category'], $faq['sort_order']]
                );
            }
        }
    }

    private function seedTrustedClients(): void
    {
        $clients = [
            ['name' => 'Nigerian National Petroleum Corporation (NNPC)', 'sort_order' => 10],
            ['name' => 'TotalEnergies Nigeria',                          'sort_order' => 20],
            ['name' => 'Dangote Group',                                  'sort_order' => 30],
            ['name' => 'Julius Berger Nigeria',                          'sort_order' => 40],
            ['name' => 'Shell Petroleum Development Company',            'sort_order' => 50],
            ['name' => 'MTN Nigeria',                                    'sort_order' => 60],
            ['name' => 'Flour Mills of Nigeria',                         'sort_order' => 70],
            ['name' => 'Zenith Bank Plc',                               'sort_order' => 80],
        ];

        foreach ($clients as $client) {
            $exists = $this->connection->queryOne(
                "SELECT id FROM trusted_clients WHERE name = ?",
                [$client['name']]
            );

            if ($exists === null) {
                $this->connection->insert(
                    "INSERT INTO trusted_clients (name, logo, website_url, service_slug, sort_order, is_active)
                     VALUES (?, NULL, NULL, NULL, ?, 1)",
                    [$client['name'], $client['sort_order']]
                );
            }
        }
    }
}
