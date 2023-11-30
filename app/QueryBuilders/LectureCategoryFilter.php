<?php

namespace App\QueryBuilders;

use App\Models\Lecture\Category;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class LectureCategoryFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $category = Category::find($value);

        if (! $category) return;

        if ($category->isMain()) {
            $subCategoriesIds = Category::subCategories()
                ->where('parent_id', $category->id)
                ->pluck('id');

            $query->whereIn('category_id', $subCategoriesIds);
        } else {
            $query->where('category_id', $category->id);
        }
    }
}
