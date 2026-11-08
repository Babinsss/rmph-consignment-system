<?php

use Illuminate\Support\Facades\Schedule;

// Automatically backup consignment database nightly at 11:00 PM (Asia/Manila)
Schedule::command('app:backup-database')->dailyAt('23:00')->timezone('Asia/Manila');
