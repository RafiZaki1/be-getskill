<?php

namespace App\Enums;

enum UploadDiskEnum: string
{
    case USERS = 'users';
    case COURSES = 'courses';
    case EVENTS = 'events';
    case BLOGS = 'blogs';
    case IMAGE = 'image';
    case REWARDS = 'rewards';
    case SCHOOL = 'schools';
    case FILE = 'file';
    case JOURNAL = 'journal';
    case PAYMENTS = 'payments';
    case CV = 'cv';
}
