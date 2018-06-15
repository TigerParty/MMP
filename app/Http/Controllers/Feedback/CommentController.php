<?php

namespace App\Http\Controllers\Feedback;


use App\Argo\Feedback;
use App\Argo\FeedbackReply;
use App\Argo\Project;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CommentController extends Controller
{
    const COMMENT_ITEMS_PER_PAGE = 10;

    function commentIndexApi(Request $request)
    {
        $pageNumber = (integer)$request->input('page', 1);

        $comments = Feedback::comment()
            ->select([
                'id',
                'first_read_by',
                'feedbackable_type',
                'feedbackable_id',
                'created_at',
            ])
            ->withCount('feedbackReplies')
            ->orderBy('created_at', 'DESC')
            ->get();

        $totalAmount = 0;
        $respondedAmount = 0;
        $newAmount = 0;

        $pages = [];
        foreach ($comments as $index => $comment) {
            if (is_null($comment->feedbackable)) {
                $pageID = 0;
                if (!array_key_exists($pageID, $pages)) {
                    $pages[$pageID] = [
                        'id' => 0,
                        'page_title' => 'HOME PAGE',
                        'entity' => 'homepage',
                        'comment_count' => 0,
                        'unread_count' => 0,
                        'replied_count' => 0,
                        'last_created_at' => $comment->created_at,
                    ];
                }
            } else {
                if (!$comment->feedbackable->view_level_id || $comment->feedbackable->view_level_id < argo_current_permission()) {
                    continue;
                }
                $pageID = $comment->feedbackable->id;
                if (!array_key_exists($pageID, $pages)) {
                    $pages[$pageID] = [
                        'id' => $comment->feedbackable->id,
                        'page_title' => $comment->feedbackable->title,
                        'entity' => 'App\Argo\Project',
                        'comment_count' => 0,
                        'unread_count' => 0,
                        'replied_count' => 0,
                        'last_created_at' => $comment->created_at,
                    ];
                }
            }
            $pages[$pageID]['comment_count'] += 1;
            $pages[$pageID]['unread_count'] += is_null($comment->first_read_by) ? 1 : 0;
            $pages[$pageID]['replied_count'] += $comment->feedback_replies_count > 0 ? 1 : 0;

            $totalAmount += 1;
            if ($comment->feedback_replies_count > 0) {
                $respondedAmount += 1;
            }
            if (is_null($comment->first_read_by)) {
                $newAmount += 1;
            }
        }

        $arrangedPages = new Collection();
        foreach ($pages as $page) {
            $arrangedPages->push($page);
        }

        $data = $arrangedPages->forPage($pageNumber, self::COMMENT_ITEMS_PER_PAGE);

        return response()->json([
            'is_admin' => argo_is_admin_accessible(),
            'list' => $data,
            'total_amount' => $totalAmount,
            'responded_amount' => $respondedAmount,
            'new_amount' => $newAmount,
            'per_page' => self::COMMENT_ITEMS_PER_PAGE
        ]);
    }

    function commentStoreApi(Request $request)
    {
        $request->validate([
            'new_comment.name' => 'required',
            'new_comment.message' => 'required',
            'new_comment.email' => 'email|nullable',
            'entity_id' => 'integer',
            'entity_type' => [Rule::in(['App\Argo\Project', 'homepage'])]
        ]);

        if ($request->input('entity_id') > 0 &&
            $request->input('entity_type') == "App\Argo\Project") {
            $project = Project::findOrFail($request->input('entity_id'));
            if ($project->view_level_id < argo_current_permission()) {
                abort(403);
            }
            $page_title = $project->title;
        } else {
            $page_title = 'HOME PAGE';
        }

        $newComment = new Feedback;
        $newComment->type = 'comment';
        $newComment->payload = $request->input('new_comment');
        $newComment->feedbackable_id = $request->input('entity_id');
        $newComment->feedbackable_type = $request->input('entity_type') == 'homepage' ? "" : $request->input('entity_type');
        $newComment->first_read_by = null;
        $newComment->save();

        $newComment->page_title = $page_title;
        $newComment->feedback_replies = [];

        return response()->json($newComment);
    }

    function commentShowApi($pageID)
    {
        $pageComments = $pageComments = Feedback::comment()
            ->select([
                'id',
                'payload',
                'created_at',
                'feedbackable_id',
                'feedbackable_type',
            ])
            ->with([
                'feedbackReplies' => function ($query) {
                    $query->select([
                        'id',
                        'feedback_id',
                        'payload',
                        'created_at',
                    ])->orderBy('created_at', 'DESC');
                }
            ])
            ->where('feedbackable_id', '=', $pageID)
            ->orderBy('created_at', 'DESC')
            ->get();

        return response()->json([
            'page_comments' => $pageComments
        ]);
    }

    function commentMarkReadApi($pageID)
    {
        try {
            $currentUserId = \Auth::user()->id;
            Feedback::where('feedbackable_id', $pageID)
                ->whereNull('first_read_by')
                ->update([
                    'first_read_by' => $currentUserId
                ]);

            return response()->json([
                'first_read_by' => $currentUserId
            ]);
        } catch (\Exception $e) {
            \Log::error($e);

            return response()->json("Mark read for comment failed", 400);
        }
    }

    function commentReplyStoreApi(Request $request, $commentID)
    {
        $request->validate([
            'message' => 'required',
        ]);

        $newCommentReply = new FeedbackReply;
        $newCommentReply->feedback_id = $commentID;
        $newCommentReply->payload = [
            'message' => $request->input('message'),
        ];
        $newCommentReply->save();

        $feedback = $newCommentReply->feedback()
            ->withCount('feedbackReplies')
            ->first(['id, feedbackable_id']);

        return response()->json([
            'replied_entity_id' => $feedback->feedbackable_id,
            'is_first_reply' => $feedback->feedback_replies_count <= 1,
            'comment_reply' => $newCommentReply
        ]);
    }

    function commentDeleteApi($commentID)
    {
        try {
            $deletedComment = Feedback::withCount('feedbackReplies')->findOrFail($commentID);
            $deletedComment->delete();

            return response()->json([
                'page_id_of_deleted_comment' => $deletedComment->feedbackable_id,
                'has_reply' => $deletedComment->feedback_replies_count > 0,
            ]);
        } catch (\Exception $e) {
            \Log::error($e);

            return response()->json("Delete comment failed", 400);
        }
    }
}