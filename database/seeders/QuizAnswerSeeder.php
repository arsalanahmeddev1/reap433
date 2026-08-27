<?php

namespace Database\Seeders;

use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class QuizAnswerSeeder extends Seeder
{
    /**
     * Seed dummy answers for each quiz question (one correct per question).
     */
    public function run(): void
    {
        $questionIds = QuizQuestion::query()
            ->pluck('id', 'slug')
            ->all();

        $rows = [
            'who-built-the-ark-as-commanded-by-god' => [
                ['answers' => 'Noah', 'bible_title' => 'Genesis 6:14', 'is_right' => 1, 'xp' => 10, 'coins' => 5, 'description' => 'Correct — Noah built the ark.'],
                ['answers' => 'Moses', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Abraham', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'David', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
            ],
            'how-many-days-and-nights-did-it-rain-during-the-flood' => [
                ['answers' => '40', 'bible_title' => 'Genesis 7:12', 'is_right' => 1, 'xp' => 15, 'coins' => 8, 'description' => 'Correct — it rained 40 days and 40 nights.'],
                ['answers' => '7', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => '12', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => '100', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
            ],
            'which-prophet-confronted-king-ahab-on-mount-carmel' => [
                ['answers' => 'Elijah', 'bible_title' => '1 Kings 18:21', 'is_right' => 1, 'xp' => 20, 'coins' => 12, 'description' => 'Correct — Elijah confronted Ahab on Mount Carmel.'],
                ['answers' => 'Elisha', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Isaiah', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Jeremiah', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
            ],
            'where-was-jesus-born' => [
                ['answers' => 'Bethlehem', 'bible_title' => 'Matthew 2:1', 'is_right' => 1, 'xp' => 10, 'coins' => 5, 'description' => 'Correct — Jesus was born in Bethlehem.'],
                ['answers' => 'Nazareth', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Jerusalem', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Capernaum', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
            ],
            'who-wrote-the-majority-of-the-new-testament-letters' => [
                ['answers' => 'Paul', 'bible_title' => 'Romans 1:1', 'is_right' => 1, 'xp' => 15, 'coins' => 8, 'description' => 'Correct — Paul wrote most of the New Testament letters.'],
                ['answers' => 'Peter', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'John', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'James', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
            ],
            'in-which-city-did-paul-preach-about-the-unknown-god' => [
                ['answers' => 'Athens', 'bible_title' => 'Acts 17:22-23', 'is_right' => 1, 'xp' => 20, 'coins' => 12, 'description' => 'Correct — Paul preached in Athens about the unknown god.'],
                ['answers' => 'Rome', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Corinth', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Ephesus', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
            ],
            'who-was-thrown-into-the-lions-den' => [
                ['answers' => 'Daniel', 'bible_title' => 'Daniel 6:16', 'is_right' => 1, 'xp' => 10, 'coins' => 5, 'description' => 'Correct — Daniel was thrown into the lions den.'],
                ['answers' => 'Joseph', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Jonah', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Samson', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
            ],
            'which-disciple-denied-jesus-three-times' => [
                ['answers' => 'Peter', 'bible_title' => 'Matthew 26:75', 'is_right' => 1, 'xp' => 15, 'coins' => 8, 'description' => 'Correct — Peter denied Jesus three times.'],
                ['answers' => 'Judas', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Thomas', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Andrew', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
            ],
            'who-was-the-mother-of-samuel-the-prophet' => [
                ['answers' => 'Hannah', 'bible_title' => '1 Samuel 1:20', 'is_right' => 1, 'xp' => 20, 'coins' => 12, 'description' => 'Correct — Hannah was the mother of Samuel.'],
                ['answers' => 'Sarah', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Rachel', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Ruth', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
            ],
            'how-many-loaves-and-fish-did-jesus-use-to-feed-the-five-thousand' => [
                ['answers' => '5 loaves and 2 fish', 'bible_title' => 'John 6:9', 'is_right' => 1, 'xp' => 10, 'coins' => 5, 'description' => 'Correct — five loaves and two fish.'],
                ['answers' => '7 loaves and 3 fish', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => '2 loaves and 5 fish', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => '12 loaves and 2 fish', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
            ],
            'which-sea-did-jesus-walk-on' => [
                ['answers' => 'Sea of Galilee', 'bible_title' => 'Matthew 14:25', 'is_right' => 1, 'xp' => 15, 'coins' => 8, 'description' => 'Correct — Jesus walked on the Sea of Galilee.'],
                ['answers' => 'Red Sea', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Dead Sea', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Mediterranean Sea', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
            ],
            'whose-shadow-is-said-to-have-healed-the-sick-in-acts' => [
                ['answers' => 'Peter', 'bible_title' => 'Acts 5:15', 'is_right' => 1, 'xp' => 20, 'coins' => 12, 'description' => 'Correct — Peter’s shadow healed the sick.'],
                ['answers' => 'Paul', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'John', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Stephen', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
            ],
            'which-prophet-is-known-for-the-vision-of-dry-bones' => [
                ['answers' => 'Ezekiel', 'bible_title' => 'Ezekiel 37:1-14', 'is_right' => 1, 'xp' => 10, 'coins' => 5, 'description' => 'Correct — Ezekiel saw the vision of dry bones.'],
                ['answers' => 'Daniel', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Hosea', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Amos', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
            ],
            'who-prophesied-about-a-virgin-giving-birth-to-a-son-called-immanuel' => [
                ['answers' => 'Isaiah', 'bible_title' => 'Isaiah 7:14', 'is_right' => 1, 'xp' => 15, 'coins' => 8, 'description' => 'Correct — Isaiah prophesied about Immanuel.'],
                ['answers' => 'Jeremiah', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Micah', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Malachi', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
            ],
            'which-prophet-spent-three-days-in-the-belly-of-a-great-fish' => [
                ['answers' => 'Jonah', 'bible_title' => 'Jonah 1:17', 'is_right' => 1, 'xp' => 20, 'coins' => 12, 'description' => 'Correct — Jonah spent three days in the great fish.'],
                ['answers' => 'Elijah', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Nahum', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Joel', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
            ],
            'how-many-books-are-in-the-bible' => [
                ['answers' => '66', 'bible_title' => 'Psalm 119:105', 'is_right' => 1, 'xp' => 10, 'coins' => 5, 'description' => 'Correct — there are 66 books in the Bible.'],
                ['answers' => '39', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => '27', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => '73', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
            ],
            'what-is-the-shortest-book-in-the-new-testament' => [
                ['answers' => '2 John', 'bible_title' => '2 John 1:1', 'is_right' => 1, 'xp' => 15, 'coins' => 8, 'description' => 'Correct — 2 John is the shortest New Testament book.'],
                ['answers' => 'Philemon', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Jude', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Titus', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
            ],
            'which-gospel-is-not-considered-a-synoptic-gospel' => [
                ['answers' => 'John', 'bible_title' => 'John 1:1', 'is_right' => 1, 'xp' => 20, 'coins' => 12, 'description' => 'Correct — John is not a Synoptic Gospel.'],
                ['answers' => 'Matthew', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Mark', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Luke', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
            ],
            'who-carried-the-cross-for-jesus-on-the-way-to-golgotha' => [
                ['answers' => 'Simon of Cyrene', 'bible_title' => 'Matthew 27:32', 'is_right' => 1, 'xp' => 10, 'coins' => 5, 'description' => 'Correct — Simon of Cyrene carried the cross.'],
                ['answers' => 'Joseph of Arimathea', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Nicodemus', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Barabbas', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
            ],
            'what-words-did-jesus-speak-from-the-cross-it-is-finished' => [
                ['answers' => 'It is finished', 'bible_title' => 'John 19:30', 'is_right' => 1, 'xp' => 15, 'coins' => 8, 'description' => 'Correct — Jesus said “It is finished.”'],
                ['answers' => 'Father, forgive them', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'My God, why have You forsaken Me?', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Into Your hands I commit My spirit', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
            ],
            'which-roman-governor-sentenced-jesus-to-be-crucified' => [
                ['answers' => 'Pontius Pilate', 'bible_title' => 'Matthew 27:26', 'is_right' => 1, 'xp' => 20, 'coins' => 12, 'description' => 'Correct — Pontius Pilate sentenced Jesus.'],
                ['answers' => 'Herod Antipas', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Caesar Augustus', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
                ['answers' => 'Felix', 'is_right' => 0, 'xp' => 0, 'coins' => 0, 'description' => null],
            ],
        ];

        foreach ($rows as $questionSlug => $answers) {
            $questionId = $questionIds[$questionSlug] ?? null;

            if (! $questionId) {
                continue;
            }

            foreach ($answers as $row) {
                $slug = Str::slug($questionSlug.'-'.$row['answers']);

                QuizAnswer::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'question_id' => $questionId,
                        'answers' => $row['answers'],
                        'bible_title' => $row['bible_title'] ?? null,
                        'description' => $row['description'],
                        'xp' => $row['xp'],
                        'coins' => $row['coins'],
                        'is_right' => $row['is_right'],
                    ]
                );
            }
        }
    }
}
