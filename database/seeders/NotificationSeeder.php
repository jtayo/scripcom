<?php

namespace Database\Seeders;

use App\Models\Router;
use App\Models\User;
use App\Notifications\RouterAlert;
use App\Notifications\SystemNotice;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $byEmail = function (string $email) {
            return User::where('email', $email)->first();
        };

        $admin = $byEmail('admin@wificivicplatform.com');
        $grace = $byEmail('grace@mombasa.go.ke');
        $james = $byEmail('james@mombasa.go.ke');
        $amina = $byEmail('amina@kilifi.go.ke');
        $betty = $byEmail('betty@safaricom.co.ke');

        $offlineRouter = Router::where('status', 'offline')->first();

        $seeds = [
            [$admin, new SystemNotice(
                'Welcome to the platform',
                'You are signed in as a Super Administrator with full platform access.',
                'success',
                route('admin.dashboard'),
            )],
            [$admin, $offlineRouter ? new RouterAlert(
                'Router offline',
                "Router {$offlineRouter->name} has been offline for over 5 minutes.",
                'danger',
                route('admin.device-monitoring'),
                $offlineRouter->id,
            ) : null],
            [$grace, new SystemNotice(
                'Monthly report ready',
                'The August network usage report for Mombasa County is ready to view.',
                'info',
                route('admin.reports.show', 'usage'),
            )],
            [$grace, $offlineRouter ? new RouterAlert(
                'High CPU detected',
                "Router {$offlineRouter->name} reported sustained high CPU usage during the last check.",
                'warning',
                route('admin.device-monitoring'),
                $offlineRouter->id,
            ) : null],
            [$james, new SystemNotice(
                'Welcome aboard',
                'Your viewer account is now active. You can browse network analytics and reports.',
                'info',
                route('admin.analytics'),
            )],
            [$amina, new SystemNotice(
                'Kilifi onboarding',
                'Your organization profile has been set up. Add hotspots and start monitoring.',
                'info',
                route('admin.settings'),
            )],
            [$betty, new SystemNotice(
                'Campaign summary',
                'Your active campaigns delivered 1,200 sponsored sessions this week.',
                'success',
                route('admin.campaigns.index'),
            )],
        ];

        foreach ($seeds as [$user, $notification]) {
            if (! $user || ! $notification) {
                continue;
            }

            if ($user->notifications()->where('type', get_class($notification))->exists()) {
                continue;
            }

            $user->notify($notification);
        }
    }
}
