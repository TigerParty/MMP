<?php

namespace App\Services;

use App\Argo\Notification;
use Lang;
use Log;
use Mail;

class NotificationService
{
    public static function sendScheduledReportNotification($email, $reciver, $subject, $data)
    {
        $sender = config('mail.from.address');
        $from = Lang::get('notification.scheduled_report.from');

        Mail::send('emails.notification.scheduled_report', $data, function ($msg) use ($sender, $from, $email, $reciver, $subject) {
            $msg->from($sender, $from)
                ->to($email, $reciver)
                ->subject($subject);
        });
    }

    public static function sendFBCommentNotification($data)
    {
        $sender = config('mail.from.address');
        $receivers = config('argodf.notification.fb_comment.receivers');
        $subject = Lang::get('notification.fb_comment.subject');
        $from = Lang::get('notification.fb_comment.from');

        Mail::send('emails.notification.fb_comment', $data, function ($msg) use ($receivers, $subject, $from, $sender) {
            $msg->from($sender, $from)
                ->to($receivers)
                ->subject($subject);
        });
    }

    public static function sendNewReportCreatedNotification($reports = array())
    {
        $sender = config('mail.from.address');
        $receivers = config('argodf.notification.new_report_created.receivers');
        $subject = Lang::get('notification.new_report_created.subject');
        $from = Lang::get('notification.new_report_created.from');

        $urls = array();
        foreach ($reports as $report) {
            array_push($urls, url('/report/' . $report->id));
        }

        $data = array(
            'urls' => $urls
        );

        Mail::send('emails.notification.new_report_created', $data, function ($msg) use ($receivers, $subject, $from, $sender) {
            $msg->from($sender, $from)
                ->to($receivers)
                ->subject($subject);
        });
    }

    public function sendEntityUpdatedNotification($entityType, $entityId)
    {
        $notifications = Notification::where("notify_type", "=", $entityType)
            ->where("notify_id", "=", $entityId)
            ->where("schedule", "=", "by_update")
            ->get();

        foreach ($notifications as $notification) {
            $this->sendScheduledNotification(
                $notification['notify_type'],
                $notification['notify_id'],
                $notification['email'],
                $notification['receiver']
            );
        }
    }

    public function sendScheduledNotification($entityType, $entityId, $email, $receiver)
    {
        Log::info("sendScheduledNotification type: $entityType, id: $entityId, email: $email, receiver: $receiver");

        try {
            $entityData = array();

            switch ($entityType) {
                case 'App\Argo\Project':
                    $entity = $entityType::find($entityId);
                    $entityData = array(
                        'mailFrom' => trans('notification.scheduled_project.from'),
                        'subject' => trans('notification.scheduled_project.subject', [
                            'title' => $entity->title
                        ]),
                        'template' => 'emails.notification.scheduled_project',
                        'templateData' => array(
                            'url' => url("project/$entityId")
                        )
                    );
                    break;
                default:
                    Log::error("Unknown entity type: " . $entityType);
                    break;
            }

            if (!empty($entityData)) {
                $sender = config('mail.from.address');

                Mail::send($entityData['template'], $entityData['templateData'], function ($msg) use ($sender, $email, $receiver, $entityData) {
                    $msg->from($sender, $entityData['mailFrom'])
                        ->to($email, $receiver)
                        ->subject($entityData['subject']);
                });
            }
        } catch (Exception $e) {
            Log::error("Send eamil exception :" . $e->getMessage());
        }
    }

    public function setByUpdateMailNotification($entityType, $entityId, $receiver, $email)
    {
        $notifyCount = Notification::where('notify_type', '=', $entityType)
            ->where('notify_id', '=', $entityId)
            ->where('email', '=', $email)
            ->where('schedule', '=', 'by_update')
            ->count('id');

        if ($notifyCount == 0) {
            $notify = new Notification();
            $notify->receiver = $receiver;
            $notify->email = $email;
            $notify->schedule = 'by_update';
            $notify->notify_id = $entityId;
            $notify->notify_type = $entityType;
            $notify->save();
            Log::info("Notification by_update setup: $notify->id");
        } else {
            Log::info("Notification by_update not setup since it's duplicate: ($entityType - $entityId) $email");
        }
    }
}
