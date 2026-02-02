<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        // Für Dev/CI idempotent (bei produktiven Daten bitte nicht delete() nutzen)
        Faq::query()->delete();

        $rows = [
            // Anmeldung / Meldung
            [
                'category' => 'Anmeldung',
                'sort_order' => 10,
                'question' => 'Wie melde ich ein Team an?',
                'answer_html' => '<p>Meldungen können online über die Eventhomepage getätigt werden.</p><p>Alternativ sind Meldungen per Post oder Mail mit Meldebogen und unterschriebener Einverständniserklärung möglich.</p>',
                'is_active' => true,
                'event_id' => null,
            ],
            [
                'category' => 'Anmeldung',
                'sort_order' => 20,
                'question' => 'Bis wann ist Meldeschluss?',
                'answer_html' => '<p><strong>Meldeschluss ist der 02.08.2026.</strong></p>',
                'is_active' => true,
                'event_id' => 1274,
            ],
            [
                'category' => 'Anmeldung',
                'sort_order' => 20,
                'question' => 'Wann wird meine Meldung wirksam?',
                'answer_html' => '<p>Die Meldung wird erst wirksam, wenn das Startgeld eingegangen ist.</p>',
                'is_active' => true,
                'event_id' => 1256,
            ],

            // Startgeld & Training
            [
                'category' => 'Startgeld & Training',
                'sort_order' => 10,
                'question' => 'Wie hoch ist das Startgeld?',
                'answer_html' =>  '<p>Eine Trainingseinheit ist im Startgeld enthalten. Weitere Trainingseinheiten können nach Absprache hinzugebucht werden.</p>',
                'is_active' => true,
                'event_id' => null,
            ],
            [
                'category' => 'Startgeld & Training',
                'sort_order' => 20,
                'question' => 'Gibt es Infos zu Trainingszeiten und Programm?',
                'answer_html' => '<p>Zum Thema Trainingszeiten, Teambesprechung und Programm erfolgt nach Eingang aller Meldungen rechtzeitig eine separate Information.</p>',
                'is_active' => true,
                'event_id' => null,
            ],

        ];

        $now = now();
        foreach ($rows as &$row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
            $row['eventGroup_id'] = 2;
        }

        Faq::query()->insert($rows);
    }
}
