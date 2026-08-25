<?php

namespace Database\Seeders;

use App\Models\QuizQuestion;
use App\Models\QuizType;
use App\Models\QuizeCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QuizQuestionSeeder extends Seeder
{
    /**
     * Seed dummy quiz questions for each category.
     */
    public function run(): void
    {
        $typeIds = QuizType::query()
            ->pluck('id', 'slug')
            ->all();

        $categoryIds = QuizeCategory::query()
            ->pluck('id', 'slug')
            ->all();

        $beginner = $typeIds['beginner'] ?? null;
        $intermediate = $typeIds['intermediate'] ?? null;
        $expert = $typeIds['expert'] ?? null;

        $rows = [
            // Old Testament
            [
                'category_slug' => 'old-testament',
                'type_id' => $beginner,
                'question' => 'Who built the ark as commanded by God?',
                'description' => 'A foundational Old Testament story about obedience and faith.',
            ],
            [
                'category_slug' => 'old-testament',
                'type_id' => $intermediate,
                'question' => 'How many days and nights did it rain during the flood?',
                'description' => 'Tests knowledge of the Genesis flood account.',
            ],
            [
                'category_slug' => 'old-testament',
                'type_id' => $expert,
                'question' => 'Which prophet confronted King Ahab on Mount Carmel?',
                'description' => 'Advanced recall of prophetic narratives in Kings.',
            ],

            // New Testament
            [
                'category_slug' => 'new-testament',
                'type_id' => $beginner,
                'question' => 'Where was Jesus born?',
                'description' => 'Basic New Testament knowledge about the birth of Christ.',
            ],
            [
                'category_slug' => 'new-testament',
                'type_id' => $intermediate,
                'question' => 'Who wrote the majority of the New Testament letters?',
                'description' => 'Focuses on apostolic authorship in the epistles.',
            ],
            [
                'category_slug' => 'new-testament',
                'type_id' => $expert,
                'question' => 'In which city did Paul preach about the unknown god?',
                'description' => 'Detailed Acts knowledge for advanced learners.',
            ],

            // Characters
            [
                'category_slug' => 'characters',
                'type_id' => $beginner,
                'question' => 'Who was thrown into the lions den?',
                'description' => 'A well-known Bible character story.',
            ],
            [
                'category_slug' => 'characters',
                'type_id' => $intermediate,
                'question' => 'Which disciple denied Jesus three times?',
                'description' => 'Identifies a key disciple from the Gospels.',
            ],
            [
                'category_slug' => 'characters',
                'type_id' => $expert,
                'question' => 'Who was the mother of Samuel the prophet?',
                'description' => 'Deeper character knowledge from 1 Samuel.',
            ],

            // Miracles
            [
                'category_slug' => 'miracles',
                'type_id' => $beginner,
                'question' => 'How many loaves and fish did Jesus use to feed the five thousand?',
                'description' => 'Introductory miracle question from the Gospels.',
            ],
            [
                'category_slug' => 'miracles',
                'type_id' => $intermediate,
                'question' => 'Which sea did Jesus walk on?',
                'description' => 'Tests recall of a major Gospel miracle.',
            ],
            [
                'category_slug' => 'miracles',
                'type_id' => $expert,
                'question' => 'Whose shadow is said to have healed the sick in Acts?',
                'description' => 'Advanced miracle question from the early church.',
            ],

            // Prophecy
            [
                'category_slug' => 'prophecy',
                'type_id' => $beginner,
                'question' => 'Which prophet is known for the vision of dry bones?',
                'description' => 'Introductory prophecy question.',
            ],
            [
                'category_slug' => 'prophecy',
                'type_id' => $intermediate,
                'question' => 'Who prophesied about a virgin giving birth to a son called Immanuel?',
                'description' => 'Messianic prophecy from Isaiah.',
            ],
            [
                'category_slug' => 'prophecy',
                'type_id' => $expert,
                'question' => 'Which prophet spent three days in the belly of a great fish?',
                'description' => 'Prophetic narrative with symbolic meaning.',
            ],

            // Bible Knowledge
            [
                'category_slug' => 'bible-knowledge',
                'type_id' => $beginner,
                'question' => 'How many books are in the Bible?',
                'description' => 'Basic Bible knowledge question.',
            ],
            [
                'category_slug' => 'bible-knowledge',
                'type_id' => $intermediate,
                'question' => 'What is the shortest book in the New Testament?',
                'description' => 'Tests familiarity with Bible structure.',
            ],
            [
                'category_slug' => 'bible-knowledge',
                'type_id' => $expert,
                'question' => 'Which Gospel is not considered a Synoptic Gospel?',
                'description' => 'Advanced structural Bible knowledge.',
            ],

            // The Cross
            [
                'category_slug' => 'the-cross',
                'type_id' => $beginner,
                'question' => 'Who carried the cross for Jesus on the way to Golgotha?',
                'description' => 'Introductory question about the crucifixion account.',
            ],
            [
                'category_slug' => 'the-cross',
                'type_id' => $intermediate,
                'question' => 'What words did Jesus speak from the cross: It is finished?',
                'description' => 'Focuses on the sayings of Jesus at the cross.',
            ],
            [
                'category_slug' => 'the-cross',
                'type_id' => $expert,
                'question' => 'Which Roman governor sentenced Jesus to be crucified?',
                'description' => 'Detailed Passion Week knowledge.',
            ],
        ];

        foreach ($rows as $row) {
            $categoryId = $categoryIds[$row['category_slug']] ?? null;
            $typeId = $row['type_id'];

            if (! $categoryId || ! $typeId) {
                continue;
            }

            $slug = Str::slug($row['question']);

            QuizQuestion::updateOrCreate(
                ['slug' => $slug],
                [
                    'quiz_category_id' => $categoryId,
                    'quiz_type_id' => $typeId,
                    'question' => $row['question'],
                    'description' => $row['description'],
                    'seo_title' => $row['question'],
                    'seo_description' => $row['description'],
                ]
            );
        }
    }
}
