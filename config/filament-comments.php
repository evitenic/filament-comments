<?php

return [
    /*
     * Whether or not user avatars should be displayed next to comments.
     */
    'display_avatars' => true,

    /*
     * The icons that are used in the comments component.
     */
    'icons' => [
        'action' => 'heroicon-s-chat-bubble-left-right',
        'delete' => 'heroicon-s-trash',
        'empty' => 'heroicon-s-chat-bubble-left-right',
        'edit' => 'heroicon-s-pencil-square',
    ],

    /*
     * The comment model to be used
     */
    'comment_model' => \Evitenic\FilamentComments\Models\FilamentComment::class,

    /*
     * The policy that will be used to authorize actions against comments.
     */
    'model_policy' => \Evitenic\FilamentComments\Policies\FilamentCommentPolicy::class,

    /*
     * The number of days after which soft-deleted comments should be deleted.
     *
     * Set to null if no comments should be deleted.
     */
    'prune_after_days' => 30,

    /*
     * Options: 'rich', 'markdown'
     */
    'editor' => 'rich',

    // null or html
    'comment_type' => 'html',

    /*
     * The rich editor toolbar buttons that are available to users.
     */
    'toolbar_buttons' => [
        'blockquote',
        'bold',
        'bulletList',
        'codeBlock',
        'italic',
        'link',
        'orderedList',
        'redo',
        'strike',
        'underline',
        'undo',
        'attachFiles',
    ],

    /*
     * The attribute used to display the user's name.
     */
    'user_name_attribute' => 'name',

    /*
     * Authenticatable model class
     */
    'authenticatable' => \App\Models\User::class,

    /*
     * The name of the table where the comments are stored.
     */
    'table_name' => 'filament_comments',

    /*
     * Expired edit time in minutes.
     * This is the time after which a comment can no longer be edited.
     * Set to null or 0 to disable the edit time limit.
     * For example, if you want to allow editing for 15 minutes after a comment is posted, set this to 15.
     */
    'edit_time_limit' => 15,
];
