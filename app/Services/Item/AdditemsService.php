<?php

namespace App\Services\Item;

use App\Models\MongoItem;
use Illuminate\Support\Str;

class AdditemsService
{
    public function addForCreate($category, $request)
    {
        $items = [];
        $items_title = [];
        $changeStatus = 0;
        if ($category->status) {
            $cfeatures = $category->features()->where('is_in_filter_rtable', 1);
            $citems = MongoItem::where('category_id', $category->id)->get();
            $cfeatures = $cfeatures->where('parent_id', null);
            while (count($cfeatures) > 0) {
                foreach ($cfeatures as $key => $fea) {
                    //new items for this fea
                    $new_i_inp_name = $fea->id . '-';
                    $nfiInputs = collect($request->all())->filter(function ($value, $key) use ($new_i_inp_name) {
                        return str_starts_with($key, $new_i_inp_name);
                    });
                    if ($fea->select_items_count == 1) {
                        $newItemAdded = 0;
                        // if new item added handle it here
                        if (isset($nfiInputs)) {
                            foreach ($nfiInputs as $nfi) {
                                $nifid = explode('-', $nfi)[1];
                                $nifTitle = explode('-', $nfi)[0];
                                if ($request[$fea->slug] == $nifid) {
                                    $newItem = new MongoItem();
                                    $newItem->title = $nifTitle;
                                    $newItem->feature_id = $fea->id;
                                    $newItem->slug = $nifTitle . rand(100000, 999999);
                                    $newItem->status = 0;
                                    $newItem->save();
                                    $changeStatus = 1;

                                    $items[] = $newItem->id;

                                    $newItemAdded = 1;
                                }
                            }
                        }
                        if ($newItemAdded == 0) {
                            $it = $citems->where('id', $request[$fea->slug])->first();
                            if (isset($it)) {
                                $iparent = $it->parent_id ?? null;
                                $addItem = 0;
                                if ($iparent != null) {
                                    if (in_array($iparent, $items)) {
                                        $addItem = 1;
                                    }
                                } else {
                                    $addItem = 1;
                                }
                                if ($addItem) {
                                    $items[] = $it->id;
                                    $items_title[] = $it->full_title ?? $it->title;
                                }
                            }
                        }
                    } else {
                        $fisArray = $request[$fea->slug];
                        if ($fisArray) {
                            $fisArrays = json_decode("[$fisArray]")[0];
                            if (count($fisArrays) > $fea->select_items_count) {
                                break;
                            }

                            if (count($nfiInputs) > 0) {
                                foreach ($nfiInputs as $nfi) {
                                    $nifid = explode('-', $nfi)[1];
                                    $nifTitle = explode('-', $nfi)[0];
                                    $nikey = array_search($nifid, $fisArrays);
                                    if ($nikey !== false) {
                                        $newItem = new MongoItem();
                                        $newItem->title = $nifTitle;
                                        $newItem->feature_id = $fea->id;
                                        $newItem->slug = $nifTitle . rand(100000, 999999);
                                        $newItem->status = 0;
                                        $newItem->save();
                                        $changeStatus = 1;

                                        $items[] = $newItem->id;
                                    }
                                }
                            }

                            foreach ($fisArrays as $fi_id) {
                                $it = $citems->where('id', $fi_id)->first();
                                if (isset($it)) {
                                    $iparent = $it->parent_id ?? null;
                                    $addItem = 0;
                                    if ($iparent != null) {
                                        if (in_array($iparent, $items)) {
                                            $addItem = 1;
                                        }
                                    } else {
                                        $addItem = 1;
                                    }
                                    if ($addItem) {
                                        $items[] = $it->id;
                                        $items_title[] = $it->full_title ?? $it->title;
                                    }
                                }
                            }
                        }
                    }
                    //now do it for children features
                    $cfeatures->forget($key);
                    $fchildren = $fea->children;
                    if (count($fchildren) > 0) {
                        foreach ($fchildren as $key => $chf) {
                            $cfeatures->add($chf);
                        }
                    }
                }
            }
        }
        return ['items' => array_reverse($items), 'items_title' => array_reverse($items_title), 'changeStatus' => $changeStatus];
    }

    public function addForUpdate($category, $last_items, $request)
    {
        $items = [];
        $items_title = [];
        $changeStatus = 0;
        if ($category->status) {
            $cfeatures = $category->features()->where('is_in_filter_rtable', 1);
            $citems = MongoItem::where('category_id', $category->id)->get();
            $cfeatures = $cfeatures->where('parent_id', null);
            while (count($cfeatures) > 0) {
                foreach ($cfeatures as $key => $fea) {
                    //new items for this fea
                    $new_i_inp_name = $fea->id . '-';
                    $nfiInputs = collect($request->all())->filter(function ($value, $key) use ($new_i_inp_name) {
                        return Str::startsWith($key, $new_i_inp_name);
                    });
                    if ($fea->select_items_count == 1) {
                        $newItemAdded = 0;
                        // if new item added handle it here
                        if (isset($nfiInputs)) {
                            foreach ($nfiInputs as $nfi) {
                                $nifid = explode('-', $nfi)[1];
                                $nifTitle = explode('-', $nfi)[0];
                                if ($request[$fea->slug] == $nifid) {
                                    $newItem = new MongoItem();
                                    $newItem->title = $nifTitle;
                                    $newItem->feature_id = $fea->id;
                                    $newItem->slug = $nifTitle . rand(100000, 999999);
                                    $newItem->status = 0;
                                    $newItem->save();
                                    $changeStatus = 1;

                                    $items[] = $newItem->id;

                                    $newItemAdded = 1;
                                }
                            }
                        }
                        if ($newItemAdded == 0) {
                            $it = $citems->where('id', $request[$fea->slug])->first();
                            if (!in_array($request[$fea->slug], $last_items)) {
                                if (isset($it)) {
                                    $iparent = $it->parent_id ?? null;
                                    $addItem = 0;
                                    if ($iparent != null) {
                                        if (in_array($iparent, $items)) {
                                            $addItem = 1;
                                        }
                                    } else {
                                        $addItem = 1;
                                    }
                                    if ($addItem) {
                                        $items[] = $it->id;
                                        $items_title[] = $it->full_title ?? $it->title;
                                    }
                                }
                            } else {
                                $items[] = $it->id;
                                $items_title[] = $it->full_title ?? $it->title;
                            }
                        }
                    } else {
                        $fisArray = $request[$fea->slug];
                        if ($fisArray) {
                            $fisArrays = json_decode("[$fisArray]")[0];
                            if (count($fisArrays) > $fea->select_items_count) {
                                break;
                            }

                            if (isset($nfiInputs)) {
                                foreach ($nfiInputs as $nfi) {
                                    $nifid = explode('-', $nfi)[1];
                                    $nifTitle = explode('-', $nfi)[0];
                                    $nikey = array_search($nifid, $fisArrays);
                                    if ($nikey !== false) {
                                        $newItem = new MongoItem();
                                        $newItem->title = $nifTitle;
                                        $newItem->feature_id = $fea->id;
                                        $newItem->slug = $nifTitle . rand(100000, 999999);
                                        $newItem->status = 0;
                                        $newItem->save();
                                        $changeStatus = 1;

                                        $items[] = $newItem->id;
                                    }
                                }
                            }

                            foreach ($fisArrays as $fi_id) {
                                $it = $citems->where('id', $fi_id)->first();
                                if (!in_array($fi_id, $last_items)) {
                                    if (isset($it)) {
                                        $iparent = $it->parent_id ?? null;
                                        $addItem = 0;
                                        if ($iparent != null) {
                                            if (in_array($iparent, $items)) {
                                                $addItem = 1;
                                            }
                                        } else {
                                            $addItem = 1;
                                        }
                                        if ($addItem) {
                                            $items[] = $it->id;
                                            $items_title[] = $it->full_title ?? $it->title;
                                        }
                                    }
                                } else {
                                    $items[] = $it->id;
                                    $items_title[] = $it->full_title ?? $it->title;
                                }
                            }
                        }
                    }
                    //now do it for children features
                    $cfeatures->forget($key);
                    $fchildren = $fea->children;
                    if (count($fchildren) > 0) {
                        foreach ($fchildren as $key => $chf) {
                            $cfeatures->add($chf);
                        }
                    }
                }
            }
        }
        return ['items' => array_reverse($items), 'items_title' => array_reverse($items_title), 'changeStatus' => $changeStatus];
    }

    public function addForCreateAd($category, $request)
    {
        $items = [];
        $items_title = [];
        $changeStatus = 0;
        $typeTextFeatures = null;
        if ($category->status) {
            $cfeatures = $category->features()->where('is_in_filter_ad', 1);
            $typeTextFeatures = $cfeatures->where('input_type', 1);
            $cfeatures = $cfeatures->where('input_type', 0);

            $item_cat_ids = [];
            foreach ($cfeatures as $f) {
                $item_cat_ids[] = $f->category_id;
            }
            $cat_ids = array_merge($item_cat_ids, [$category->id]);
            $cat_ids = array_unique($cat_ids);
            
            $citems = MongoItem::whereIn('category_id', $cat_ids)->get();
            $cfeatures = $cfeatures->where('parent_id', null);
            while (count($cfeatures) > 0) {
                foreach ($cfeatures as $key => $fea) {
                    //new items for this fea
                    $new_i_inp_name = $fea->id . '-';
                    $nfiInputs = collect($request->all())->filter(function ($value, $key) use ($new_i_inp_name) {
                        return str_starts_with($key, $new_i_inp_name);
                    });
                    $newItemAdded = 0;
                    // if new item added handle it here
                    if (isset($nfiInputs)) {
                        foreach ($nfiInputs as $nfi) {
                            $nifid = explode('-', $nfi)[1];
                            $nifTitle = explode('-', $nfi)[0];
                            if ($request[$fea->slug] == $nifid) {
                                $newItem = new MongoItem();
                                $newItem->title = $nifTitle;
                                $newItem->feature_id = $fea->id;
                                $newItem->slug = $nifTitle . rand(100000, 999999);
                                $newItem->status = 0;
                                $newItem->save();
                                $changeStatus = 1;
                                $items[] = $newItem->id;
                                $newItemAdded = 1;
                            }
                        }
                    }
                    if ($newItemAdded == 0) {
                        $it = $citems->where('id', $request[$fea->slug])->first();
                        if (isset($it)) {
                            $iparent = $it->parent_id ?? null;
                            $addItem = 0;
                            if ($iparent != null) {
                                if (in_array($iparent, $items)) {
                                    $addItem = 1;
                                }
                            } else {
                                $addItem = 1;
                            }
                            if ($addItem) {
                                $items[] = $it->id;
                                $items_title[] = $it->full_title ?? $it->title;
                            }
                        }
                    }

                    //now do it for children features
                    $cfeatures->forget($key);
                    $fchildren = $fea->children;
                    if (count($fchildren) > 0) {
                        foreach ($fchildren as $key => $chf) {
                            $cfeatures->add($chf);
                        }
                    }
                }
            }
        }
        return ['items' => array_reverse($items), 'items_title' => array_reverse($items_title), 'changeStatus' => $changeStatus, 'typeTextFeatures' => $typeTextFeatures];
    }

    public function addForUpdateAd($category, $last_items, $request)
    {
        $items = [];
        $items_title = [];
        $changeStatus = 0;
        $typeTextFeatures = null;
        if ($category->status) {
            $cfeatures = $category->features()->where('is_in_filter_ad', 1);
            $typeTextFeatures = $cfeatures->where('input_type', 1);
            $cfeatures = $cfeatures->where('input_type', 0);

            $item_cat_ids = [];
            foreach ($cfeatures as $f) {
                $item_cat_ids[] = $f->category_id;
            }
            $cat_ids = array_merge($item_cat_ids, [$category->id]);
            $cat_ids = array_unique($cat_ids);

            $citems = MongoItem::whereIn('category_id', $cat_ids)->get();
            $cfeatures = $cfeatures->where('parent_id', null);
            while (count($cfeatures) > 0) {
                foreach ($cfeatures as $key => $fea) {
                    //new items for this fea
                    $new_i_inp_name = $fea->id . '-';
                    $nfiInputs = collect($request->all())->filter(function ($value, $key) use ($new_i_inp_name) {
                        return Str::startsWith($key, $new_i_inp_name);
                    });
                    $newItemAdded = 0;
                    // if new item added handle it here
                    if (isset($nfiInputs)) {
                        foreach ($nfiInputs as $nfi) {
                            $nifid = explode('-', $nfi)[1];
                            $nifTitle = explode('-', $nfi)[0];
                            if ($request[$fea->slug] == $nifid) {
                                $newItem = new MongoItem();
                                $newItem->title = $nifTitle;
                                $newItem->feature_id = $fea->id;
                                $newItem->slug = $nifTitle . rand(100000, 999999);
                                $newItem->status = 0;
                                $newItem->save();
                                $changeStatus = 1;
                                $items[] = $newItem->id;
                                $newItemAdded = 1;
                            }
                        }
                    }
                    if ($newItemAdded == 0) {
                        $it = $citems->where('id', $request[$fea->slug])->first();
                        if (!in_array($request[$fea->slug], $last_items)) {
                            if (isset($it)) {
                                $iparent = $it->parent_id ?? null;
                                $addItem = 0;
                                if ($iparent != null) {
                                    if (in_array($iparent, $items)) {
                                        $addItem = 1;
                                    }
                                } else {
                                    $addItem = 1;
                                }
                                if ($addItem) {
                                    $items[] = $it->id;
                                    $items_title[] = $it->full_title ?? $it->title;
                                }
                            }
                        } else {
                            $items[] = $it->id;
                            $items_title[] = $it->full_title ?? $it->title;
                        }
                    }

                    //now do it for children features
                    $cfeatures->forget($key);
                    $fchildren = $fea->children;
                    if (count($fchildren) > 0) {
                        foreach ($fchildren as $key => $chf) {
                            $cfeatures->add($chf);
                        }
                    }
                }
            }
        }
        return ['items' => array_reverse($items), 'items_title' => array_reverse($items_title), 'changeStatus' => $changeStatus, 'typeTextFeatures' => $typeTextFeatures];
    }
}
