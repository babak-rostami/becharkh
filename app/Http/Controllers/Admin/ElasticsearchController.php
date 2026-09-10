<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MongoBlog;
use App\Models\MongoCategory;
use App\Models\MongoItem;
use App\Models\MongoQuestion;
use App\Services\Elasticsearch;
use Illuminate\Http\Request;

class ElasticsearchController extends Controller
{
    public function initial()
    {
        $this->createElasticIndexes();
        $this->createCategoryElasticDocuments();
        $this->createItemElasticDocuments();
        $this->createQuestionElasticDocuments();
        $this->createBlogElasticDocuments();
    }

    private function createElasticIndexes()
    {
        $elastic = new Elasticsearch();

        $category_index_name = MongoCategory::$elasticIndexName;
        $category_field = MongoCategory::$elasticField;
        $elastic->createIndex($category_index_name, $category_field);

        $item_index_name = MongoItem::$elasticIndexName;
        $item_field = MongoItem::$elasticField;
        $elastic->createIndex($item_index_name, $item_field);

        $blog_index_name = MongoBlog::$elasticIndexName;
        $blog_field = MongoBlog::$elasticField;
        $elastic->createIndex($blog_index_name, $blog_field);

        $question_index_name = MongoQuestion::$elasticIndexName;
        $question_field = MongoQuestion::$elasticField;
        $elastic->createIndex($question_index_name, $question_field);
    }

    private function createQuestionElasticDocuments()
    {
        $client = new Elasticsearch();
        MongoQuestion::chunk(200, function ($questions) use ($client) {
            foreach ($questions as $question) {
                $client->createDocument('questions', $question->id, [
                    'title' => $question->title,
                ]);
            }
        });
    }

    private function createBlogElasticDocuments()
    {
        $client = new Elasticsearch();
        MongoBlog::chunk(200, function ($blogs) use ($client) {
            foreach ($blogs as $blog) {
                $client->createDocument('blogs', $blog->id, [
                    'title' => $blog->title,
                ]);
            }
        });
    }

    private function createItemElasticDocuments()
    {
        $client = new Elasticsearch();
        MongoItem::chunk(200, function ($items) use ($client) {
            foreach ($items as $item) {
                if ($item->feature->is_in_filter_rtable == 1) {
                    $client->createDocument('items', $item->id, [
                        'similar_search' => $item->similar_search,
                    ]);
                }
            }
        });
    }

    private function createCategoryElasticDocuments()
    {
        $client = new Elasticsearch();
        MongoCategory::chunk(200, function ($categories) use ($client) {
            foreach ($categories as $category) {
                $client->createDocument('categories', $category->id, [
                    'similar_search' => $category->similar_search
                ]);
            }
        });
    }
}
