<?php

namespace App\Traits;

trait CloudStorage
{
    /**
     * Get user photos disk name
     *
     * @return string
     */

    public function getUserDiskName(): string
    {
        return config('filesystems.disks')['disk_name']['user_photos'];
    }

    /**
     * Get student photos disk name
     *
     * @return string
     */

    public function getStudentDiskName(): string
    {
        return config('filesystems.disks')['disk_name']['student_photos'];
    }

    /**
     * Get student photos disk name
     *
     * @return string
     */

    public function getClassroomBackgroundDiskName(): string
    {
        return config('filesystems.disks')['disk_name']['classroom_backgrounds'];
    }

    /**
     * Get modals disk name
     *
     * @return string
     */

    public function getPackDiskName(): string
    {
        return config('filesystems.disks')['disk_name']['packs'];
    }
}
