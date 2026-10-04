<?php

namespace App\Services\Admin;

use App\Models\Admin;
use App\Models\AdminNotification;

class AdminNotificationService
{
    // جایگزین همه‌ی foreach(Admin::all()) و Admin::first()->notify(...)
    public static function send($action, $route)
    {
        foreach (Admin::all() as $admin) {
            $notif = new AdminNotification();
            $notif->admin_id = $admin->id;
            $notif->action = $action;
            $notif->route = $route;
            $notif->unread = 1;
            $notif->save();
        }
    }

    public static function markAllRead($admin_id)
    {
        AdminNotification::where('admin_id', $admin_id)->where('unread', 1)->get()->each->unset('unread');
    }
}
