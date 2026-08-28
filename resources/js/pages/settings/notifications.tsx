import { Head } from '@inertiajs/react';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Separator } from '@/components/ui/separator';

export default function Notifications() {
    return (
        <>
            <Head title="Notification settings" />

            <div className="space-y-6">
                <div className="mb-4">
                    <h2 className="text-xl font-bold tracking-tight">Notifications</h2>
                    <p className="text-sm text-muted-foreground mt-1">
                        Choose which alerts Fintrack sends you.
                    </p>
                </div>

                <div className="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border bg-card text-card-foreground shadow-sm">
                    <div className="flex flex-col">
                        <div className="flex flex-row items-center justify-between p-6">
                            <div className="space-y-0.5">
                                <Label className="text-base font-medium">Transaction alerts</Label>
                                <p className="text-sm text-muted-foreground">
                                    When a large transaction is recorded.
                                </p>
                            </div>
                            <Switch defaultChecked />
                        </div>
                        <Separator />
                        <div className="flex flex-row items-center justify-between p-6">
                            <div className="space-y-0.5">
                                <Label className="text-base font-medium">Budget warnings</Label>
                                <p className="text-sm text-muted-foreground">
                                    When you exceed 80% of your category budget.
                                </p>
                            </div>
                            <Switch defaultChecked />
                        </div>
                        <Separator />
                        <div className="flex flex-row items-center justify-between p-6">
                            <div className="space-y-0.5">
                                <Label className="text-base font-medium">Bill reminders</Label>
                                <p className="text-sm text-muted-foreground">
                                    When a scheduled bill is due soon.
                                </p>
                            </div>
                            <Switch />
                        </div>
                        <Separator />
                        <div className="flex flex-row items-center justify-between p-6">
                            <div className="space-y-0.5">
                                <Label className="text-base font-medium">Weekly summary</Label>
                                <p className="text-sm text-muted-foreground">
                                    A digest of your spending and income every Monday.
                                </p>
                            </div>
                            <Switch defaultChecked />
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}

import { edit } from '@/routes/profile';

Notifications.layout = {
    breadcrumbs: [
        {
            title: 'Notification settings',
            href: '/settings/notifications',
        },
    ],
};
