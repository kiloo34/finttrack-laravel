import { Head } from '@inertiajs/react';
import DeleteUser from '@/components/delete-user';

export default function DangerZone() {
    return (
        <>
            <Head title="Danger zone settings" />

            <div className="space-y-6">
                <div className="mb-4">
                    <h2 className="text-xl font-bold tracking-tight">Danger zone</h2>
                    <p className="text-sm text-muted-foreground mt-1">
                        Irreversible actions for your account. Proceed with care.
                    </p>
                </div>

                <DeleteUser />
            </div>
        </>
    );
}

DangerZone.layout = {
    breadcrumbs: [
        {
            title: 'Danger zone settings',
            href: '/settings/danger-zone',
        },
    ],
};
