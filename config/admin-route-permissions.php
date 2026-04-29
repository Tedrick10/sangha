<?php

return [
    // Always-available admin endpoints for authenticated users.
    'admin.dashboard' => null,
    'admin.profile.*' => null,
    'admin.notifications.*' => null,
    'admin.logout' => null,

    // Monasteries
    'admin.monasteries.index' => 'monasteries.read',
    'admin.monasteries.show' => 'monasteries.read',
    'admin.monasteries.create' => 'monasteries.create',
    'admin.monasteries.store' => 'monasteries.create',
    'admin.monasteries.edit' => 'monasteries.update',
    'admin.monasteries.update' => 'monasteries.update',
    'admin.monasteries.destroy' => 'monasteries.delete',
    'admin.monasteries.chat*' => 'monasteries.update',

    // Sanghas
    'admin.sanghas.index' => 'sanghas.read',
    'admin.sanghas.show' => 'sanghas.read',
    'admin.sanghas.create' => 'sanghas.create',
    'admin.sanghas.store' => 'sanghas.create',
    'admin.sanghas.edit' => 'sanghas.update',
    'admin.sanghas.update' => 'sanghas.update',
    'admin.sanghas.destroy' => 'sanghas.delete',
    'admin.sanghas.exam-scores' => 'sanghas.read',
    'admin.sanghas.generate-eligible-list' => 'sanghas.update',
    'admin.sanghas.custom-field-file' => 'sanghas.read',

    // Transfer Sangha requests
    'admin.monastery-requests.index' => 'monastery_requests.read',
    'admin.monastery-requests.show' => 'monastery_requests.read',
    'admin.monastery-requests.file' => 'monastery_requests.read',
    'admin.monastery-requests.update-status' => 'monastery_requests.update',
    'admin.monastery-requests.destroy' => 'monastery_requests.delete',

    // Custom fields
    'admin.custom-fields.index' => 'custom_fields.read',
    'admin.custom-fields.show' => 'custom_fields.read',
    'admin.custom-fields.create' => 'custom_fields.create',
    'admin.custom-fields.store' => 'custom_fields.create',
    'admin.custom-fields.edit' => 'custom_fields.update',
    'admin.custom-fields.update' => 'custom_fields.update',
    'admin.custom-fields.destroy' => 'custom_fields.delete',
    'admin.custom-fields.reorder' => 'custom_fields.update',

    // Subjects
    'admin.subjects.index' => 'subjects.read',
    'admin.subjects.show' => 'subjects.read',
    'admin.subjects.create' => 'subjects.create',
    'admin.subjects.store' => 'subjects.create',
    'admin.subjects.edit' => 'subjects.update',
    'admin.subjects.update' => 'subjects.update',
    'admin.subjects.destroy' => 'subjects.delete',

    // Exam types
    'admin.exam-types.index' => 'exam_types.read',
    'admin.exam-types.show' => 'exam_types.read',
    'admin.exam-types.create' => 'exam_types.create',
    'admin.exam-types.store' => 'exam_types.create',
    'admin.exam-types.edit' => 'exam_types.update',
    'admin.exam-types.update' => 'exam_types.update',
    'admin.exam-types.destroy' => 'exam_types.delete',

    // Exams
    'admin.exams.index' => 'exams.read',
    'admin.exams.show' => 'exams.read',
    'admin.exams.create' => 'exams.create',
    'admin.exams.store' => 'exams.create',
    'admin.exams.edit' => 'exams.update',
    'admin.exams.update' => 'exams.update',
    'admin.exams.destroy' => 'exams.delete',
    'admin.exams.desk-number-prefix' => 'exams.update',
    'admin.exams.generate-eligible-list' => 'exams.update',

    // Score entry suite
    'admin.mandatory-scores.index' => 'mandatory_scores.read',
    'admin.mandatory-scores.year-options' => 'mandatory_scores.read',
    'admin.mandatory-scores.exam-options' => 'mandatory_scores.read',
    'admin.mandatory-scores.desk-options' => 'mandatory_scores.read',
    'admin.mandatory-scores.grid' => 'mandatory_scores.read',
    'admin.mandatory-scores.store' => 'mandatory_scores.update',
    'admin.mandatory-scores.grid-row' => 'mandatory_scores.update',

    // Moderation
    'admin.score-moderation.index' => 'score_moderation.read',
    'admin.score-moderation.control' => 'score_moderation.update',

    // Clean pass
    'admin.clean-pass.index' => 'clean_pass.read',
    'admin.clean-pass.generate' => 'clean_pass.update',
    'admin.clean-pass.reorder' => 'clean_pass.update',

    // Legacy score endpoints (no direct sidebar menu).
    'admin.scores.*' => 'score_moderation.update',

    // Website/content
    'admin.websites.index' => 'websites.read',
    'admin.websites.edit' => 'websites.update',
    'admin.websites.update' => 'websites.update',
    'admin.site-images.edit' => 'site_images.read',
    'admin.site-images.update' => 'site_images.update',
    'admin.site-images.destroy' => 'site_images.delete',
    'admin.appearance.edit' => 'appearance.read',
    'admin.appearance.update' => 'appearance.update',

    // Administration
    'admin.languages.index' => 'languages.read',
    'admin.languages.show' => 'languages.read',
    'admin.languages.create' => 'languages.create',
    'admin.languages.store' => 'languages.create',
    'admin.languages.edit' => 'languages.update',
    'admin.languages.update' => 'languages.update',
    'admin.languages.destroy' => 'languages.delete',
    'admin.translations.edit' => 'languages.update',
    'admin.translations.update' => 'languages.update',
    'admin.roles.index' => 'roles.read',
    'admin.roles.show' => 'roles.read',
    'admin.roles.create' => 'roles.create',
    'admin.roles.store' => 'roles.create',
    'admin.roles.edit' => 'roles.update',
    'admin.roles.update' => 'roles.update',
    'admin.roles.destroy' => 'roles.delete',
    'admin.users.index' => 'users.read',
    'admin.users.show' => 'users.read',
    'admin.users.create' => 'users.create',
    'admin.users.store' => 'users.create',
    'admin.users.edit' => 'users.update',
    'admin.users.update' => 'users.update',
    'admin.users.destroy' => 'users.delete',
];
