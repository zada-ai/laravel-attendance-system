<?php

return [
    'labels' => [
        'admin' => 'Admin',
        'student' => 'Student',
        'teacher' => 'Teacher',
        'hr' => 'HR',
    ],

    'list' => [
        'admin' => 'Admin',
        'student' => 'Student',
        'teacher' => 'Teacher',
        'hr' => 'HR',
    ],

    'permissions' => [
        'admin' => ['*'],
        'student' => [
            'mark-attendance',
            'submit-leave',
            'view-attendance',
            'view-tasks',
            'submit-task',
            'view-reports',
        ],
        'teacher' => [
            'mark-attendance',
            'assign-task',
            'approve-leave',
            'approve-task',
            'view-reports',
        ],
        'hr' => [
            'approve-leave',
            'view-reports',
        ],
    ],
];
