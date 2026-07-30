<?php

namespace App\Observers;

use App\Models\CourseReview;

class CourseReviewObserver
{
    /**
     * Handle the CourseReview "created" event.
     */
    public function creating(CourseReview $courseReview): void
    {
        $courseReview->review = htmlspecialchars($courseReview->review);
    }

    /**
     * Handle the CourseReview "updated" event.
     */
    public function updating(CourseReview $courseReview): void
    {
        $courseReview->review = htmlspecialchars($courseReview->review);
    }
}
