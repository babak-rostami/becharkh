<?php

namespace App\Http\Controllers;

use App\Models\MongoCategoryComment;
use App\Models\MongoItem;
use App\Models\MongoItemTag;
use App\Models\MongoTagItemMap;
use Illuminate\Http\Request;

class MongoItemTagController extends Controller
{
    public function index($id = null)
    {
        if ($id) {
            $selected_tag = MongoItemTag::find($id);
            $tags = MongoItemTag::where('parent_id', $id)->orderBy('priority', 'asc')->get();
            return view('item.tag.index-admin', compact('tags', 'selected_tag'));
        } else {
            $tags = MongoItemTag::where('parent_id', null)->orderBy('priority', 'asc')->get();
            return view('item.tag.index-admin', compact('tags'));
        }
    }

    public function store(Request $request)
    {
        $tag = new MongoItemTag();
        $tag->title = $request->title;
        $tag->similar_search = $request->similar_search;
        if (isset($request->parent_id)) {
            $tag->parent_id = $request->parent_id;
        }
        $tag->save();
        return back()->with('success', 'تگ با موفقیت ایجاد شد');
    }

    public function update(Request $request, $tag_id)
    {
        $tag = MongoItemTag::find($tag_id);
        if (!$tag) {
            return back()->with('error', 'تگ یافت نشد');
        }

        $parentId = $request->parent_id ?? null;
        $newPriority = (int)$request->priority;

        // اول جابه‌جا کردن اولویت‌ها
        $this->shiftTagPriorities($parentId, $newPriority, $tag_id);

        $tag->title = $request->title;
        $tag->similar_search = $request->similar_search;
        $tag->priority = $newPriority;
        $tag->parent_id = $parentId;
        $tag->update();


        if (!isset($request->parent_id)) {
            $tag->unset('parent_id');
        }

        // برای تگ آیتم هایی که انتخاب کردیم رو اول خود تگ رو به آرایه تگ های اون آیتم ها اضافه میکنه
        // بعد اگه این تگ بالاسری داشت تگ بالاسری هم به آرایه تگ های اون آیتم ها اضافه میکنه
        $newItemIds = explode(',', $request->items);
        $this->syncTagWithItems($tag, $newItemIds);
        if ($tag->parent_id) {
            $parentTag = MongoItemTag::find($tag->parent_id);
            if ($parentTag) {
                $this->syncTagWithItems($parentTag, $newItemIds);
            }
        }

        return back()->with('success', 'تگ با موفقیت به‌روزرسانی شد');
    }

    protected function shiftTagPriorities($parentId, $newPriority, $excludeId = null)
    {
        $query = MongoItemTag::where('parent_id', $parentId)
            ->where('priority', '>=', $newPriority);

        if ($excludeId) {
            $query->where('_id', '!=', $excludeId);
        }

        $conflictedTags = $query->orderBy('priority', 'asc')->get();

        $currentPriority = $newPriority;

        foreach ($conflictedTags as $tag) {
            $currentPriority++;

            // فقط اگر تغییر کنه ذخیره کن
            if ((int) $tag->priority !== $currentPriority) {
                $tag->priority = $currentPriority;
                $tag->save();
            }

            // به‌روزرسانی آیتم‌هایی که این تگ داخلشونه
            $itemIds = MongoTagItemMap::where('tag_id', $tag->_id)->pluck('item_id')->toArray();

            if (!empty($itemIds)) {
                $items = MongoItem::whereIn('_id', $itemIds)->get();

                foreach ($items as $item) {
                    $originalTagsArray = $item->tags_array ?? [];
                    $newTagsArray = [];
                    $hasChange = false;

                    foreach ($originalTagsArray as $tagObject) {
                        if (isset($tagObject['id']) && $tagObject['id'] == $tag->_id) {
                            if (!isset($tagObject['priority']) || (int)$tagObject['priority'] !== $currentPriority) {
                                $tagObject['priority'] = $currentPriority;
                                $hasChange = true;
                            }
                        }
                        $newTagsArray[] = $tagObject;
                    }

                    if ($hasChange) {
                        $item->tags_array = $newTagsArray;
                        $item->save();
                    }
                }
            }
        }
    }

    private function syncTagWithItems(MongoItemTag $tag, array $newItemIds)
    {
        $newItemIds = array_unique(array_filter($newItemIds));

        $existingMaps = MongoTagItemMap::where('tag_id', $tag->id)->get();
        $existingItemIds = $existingMaps->pluck('item_id')->toArray();

        // آیتم های قبلی که این تگ رو داشتن ارتباط هاشو پاک میکنه
        foreach ($existingMaps as $map) {
            if (!in_array($map->item_id, $newItemIds)) {
                $map->delete();

                $item = MongoItem::find($map->item_id);
                if ($item && isset($item->tags_array)) {
                    $item->tags_array = array_filter($item->tags_array, function ($t) use ($tag) {
                        return $t['id'] != $tag->id;
                    });

                    if (empty($item->tags_array)) {
                        $item->unset('tags_array');
                    } else {
                        $item->tags_array = array_values($item->tags_array);
                        $item->update();
                    }
                }
            }
        }

        // آیتم هایی که برای این تگ انتخاب شدن ثبت یا بروزرسانی میشن با اطلاعات جدید
        foreach ($newItemIds as $itemId) {
            if (!in_array($itemId, $existingItemIds)) {
                $tag_item_map = new MongoTagItemMap();
                $tag_item_map->tag_id = $tag->id;
                $tag_item_map->item_id = $itemId;
                $tag_item_map->save();
            }

            // تگ توی ارایه تگ های ایتم رو آپدیت یااضافه میکنه
            $item = MongoItem::find($itemId);
            if ($item) {
                $tags = $item->tags_array ?? [];
                $found = false;
                foreach ($tags as $i => $t) {
                    if ($t['id'] == $tag->id) {
                        $tags[$i]['parent_id'] = $tag->parent_id;
                        $tags[$i]['title'] = $tag->title;
                        $tags[$i]['similar_search'] = $tag->similar_search;
                        $tags[$i]['priority'] = (int) $tag->priority;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $tags[] = [
                        'id' => $tag->id,
                        'parent_id' => $tag->parent_id,
                        'title' => $tag->title,
                        'similar_search' => $tag->similar_search,
                        'priority' => (int) $tag->priority
                    ];
                }
                $item->tags_array = $tags;
                $item->update();
            }
        }
    }

    public function delete($tag_id)
    {
        $tag = MongoItemTag::find($tag_id);
        if (!$tag) {
            return back()->with('error', 'تگ مورد نظر یافت نشد');
        }
        $tag->delete();
        return back()->with('success', 'تگ با موفقیت حذف شد');
    }

    public function edit($tag_id)
    {
        $tags = MongoItemTag::where('id', '!=', $tag_id)->where('parent_id', null)->get();
        $tag = MongoItemTag::find($tag_id);

        $compactVars = [
            'tags',
            'tag'
        ];

        $itemIds = $tag->tagItems->pluck('item_id')->map(fn($id) => (string)$id)->toArray();
        $items = MongoItem::select(['_id', 'title'])->whereIn('_id', $itemIds)->get();

        if ($items) {
            $itemIdsArray = $items->pluck('_id')->toArray();
            $itemIds = implode(',', $itemIdsArray);
            $itemSelects = $items;

            $compactVars[] = 'itemIds';
            $compactVars[] = 'itemSelects';
        }

        return view('item.tag.edit', compact(...$compactVars));
    }

    public function commentTags($comment_id)
    {
        $comment = MongoCategoryComment::find($comment_id);
        if (!$comment) {
            abort(404, 'کامنت یافت نشد');
        }

        $com_tag_ids = $comment->tags ?? [];
        $com_tags = MongoItemTag::whereIn('_id', $com_tag_ids)->get();

        $items = $comment->getItems();

        $allTags = collect();
        foreach ($items as $item) {
            if (isset($item->tags_array) && is_array($item->tags_array)) {
                $allTags = $allTags->merge($item->tags_array);
            }
        }

        $tags = $allTags->unique('id')->values();
        $tags = $tags->reject(function ($tag) use ($com_tag_ids) {
            return in_array($tag['id'], $com_tag_ids);
        })->values();

        return view('category.comment.tags', compact('comment', 'tags', 'com_tags'));
    }

    public function comTagStore(Request $request)
    {
        $comment = MongoCategoryComment::find($request->comment_id);

        if (!$comment) {
            return back()->with('success', 'Comment not found');
        }

        $new_tag = MongoItemTag::find($request->tag_id);

        if (!$new_tag) {
            return back()->with('success', 'Tag not found');
        }

        $this->addTagToCommentIfNotExist($comment, $new_tag);

        if (isset($new_tag->parent_id)) {
            $parentTag = MongoItemTag::find($new_tag->parent_id);
            if (isset($parentTag)) {
                $this->addTagToCommentIfNotExist($comment, $parentTag);
            }
        }

        return back()->with('success', 'تگ با موفقیت اضافه شد');
    }

    private function addTagToCommentIfNotExist($comment, $new_tag)
    {
        $com_tags = $comment->tags ?? [];

        $tag_exists = false;
        foreach ($com_tags as $tag) {
            if ($tag == $new_tag->id) {
                $tag_exists = true;
                break;
            }
        }
        if (!$tag_exists) {
            $com_tags[] = $new_tag->id;
            $comment->tags = $com_tags;
            $comment->update();
        }
    }

    public function comTagDelete(Request $request)
    {
        $comment = MongoCategoryComment::find($request->comment_id);
        $tag_id = $request->tag_id;

        if (!$comment) {
            return back()->with('success', 'Comment not found');
        }

        $com_tags = $comment->tags ?? [];

        $updated_tags = array_filter($com_tags, function ($tag) use ($tag_id) {
            return $tag != $tag_id;
        });

        if (count($com_tags) !== count($updated_tags)) {
            if (empty($updated_tags)) {
                $comment->unset('tags');
            } else {
                $comment->tags = array_values($updated_tags);
                $comment->update();
            }
            return back()->with('success', 'Tag removed successfully');
        }

        return back()->with('success', 'Tag not found in the comment');
    }
}
