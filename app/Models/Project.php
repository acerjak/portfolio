<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $tagline
 * @property string|null $description
 * @property string|null $role
 * @property array<int, string>|null $tech_stack
 * @property string $category
 * @property string|null $repo_url
 * @property string|null $demo_url
 * @property string|null $image_path
 * @property bool $is_featured
 * @property int $sort_order
 */
#[Fillable(['title', 'slug', 'tagline', 'description', 'role', 'tech_stack', 'category', 'repo_url', 'demo_url', 'image_path', 'is_featured', 'sort_order'])]
class Project extends Model
{
    protected function casts(): array
    {
        return [
            'tech_stack' => 'array',
            'is_featured' => 'boolean',
        ];
    }
}
