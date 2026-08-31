<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $projects = [
            [
                'title' => 'ErrorFlow',
                'slug' => 'errorflow',
                'tagline' => 'Post, share, and solve engineering errors together.',
                'description' => 'As a software engineer, I want to be able to post and share engineering errors that will get reviewed, shared, and eventually solved by a community of like-minded individuals. UCI Coding Bootcamp capstone project.',
                'role' => 'Team project',
                'tech_stack' => ['JavaScript', 'Node.js', 'Express', 'MySQL'],
                'category' => 'school',
                'repo_url' => 'https://github.com/UCI-Bootcamp-Project-3-ErrorFlow/ErrorFlow',
                'image_path' => 'images/projects/errorflow.gif',
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Deal Tracker',
                'slug' => 'deal-tracker',
                'tagline' => 'A simple yet powerful interface for tracking deals through the sales process.',
                'description' => 'An application that makes it easy for sales executives to track deals through the sales life cycle.',
                'role' => 'Team project',
                'tech_stack' => ['JavaScript', 'Node.js', 'Express', 'MySQL'],
                'category' => 'school',
                'repo_url' => 'https://github.com/Logan96M/Deal_Tracker',
                'image_path' => 'images/projects/deal-tracker.gif',
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Emotion Engine',
                'slug' => 'emotion-engine',
                'tagline' => 'Turns speech into insight about how it was said.',
                'description' => 'Takes in speech, converts it to text, analyzes the emotions in the text, and reports them to the user.',
                'role' => 'Team project',
                'tech_stack' => ['JavaScript', 'Node.js', 'Natural Language Processing'],
                'category' => 'school',
                'repo_url' => 'https://github.com/maximilliancharlemagne/emotion-engine',
                'is_featured' => true,
                'sort_order' => 3,
            ],
            [
                'title' => 'Google Books Explorer',
                'slug' => 'google-books-explorer',
                'tagline' => 'Search, save, and manage a personal reading list.',
                'description' => 'Allows the user to query the Google Books API with a keyword, generating a selection of books to save or view on Google Books. Saved books can be reviewed and removed from a dedicated page.',
                'role' => 'Solo project',
                'tech_stack' => ['React', 'JavaScript', 'MongoDB', 'GraphQL'],
                'category' => 'school',
                'repo_url' => 'https://github.com/acerjak/googlebooksreact',
                'is_featured' => false,
                'sort_order' => 4,
            ],
            [
                'title' => 'Employee Summary Engine',
                'slug' => 'employee-summary-engine',
                'tagline' => 'Generate a sleek, shareable employee roster in minutes.',
                'description' => 'Allows an employer to create an employee roster with a sleek UI. Add Managers, Engineers, or Interns with details like employee ID, office number, GitHub account, and contact information, then generate a finished HTML roster ready to view in the browser.',
                'role' => 'Solo project',
                'tech_stack' => ['JavaScript', 'Node.js', 'Inquirer.js'],
                'category' => 'school',
                'repo_url' => 'https://github.com/acerjak/EmployeeSummaryEngine',
                'image_path' => 'images/projects/employee-summary-engine.gif',
                'is_featured' => false,
                'sort_order' => 5,
            ],
        ];

        foreach ($projects as $project) {
            Project::updateOrCreate(['slug' => $project['slug']], $project);
        }
    }
}
