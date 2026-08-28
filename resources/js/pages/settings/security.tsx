import { Head, usePage, router } from '@inertiajs/react';
import { useState } from 'react';
import Heading from '@/components/heading';
import type { Props as ManagePasskeysProps } from '@/components/manage-passkeys';
import type { Props as ManageTwoFactorProps } from '@/components/manage-two-factor';
import ManageTwoFactor from '@/components/manage-two-factor';
import PasskeyItem from '@/components/passkey-item';
import PasskeyRegistration from '@/components/passkey-register';
import { Button } from '@/components/ui/button';
import { Mail, Lock, KeyRound } from 'lucide-react';
import { edit } from '@/routes/security';
import UpdatePasswordModal from '@/components/settings/UpdatePasswordModal';
import { destroy } from '@/actions/Laravel/Passkeys/Http/Controllers/PasskeyRegistrationController';
import type { PageProps } from '@/types';

type Props = {
    passwordRules: string;
} & ManagePasskeysProps &
    ManageTwoFactorProps;

export default function Security(props: Props) {
    const user = usePage<PageProps>().props.auth.user;
    const [isPasswordModalOpen, setIsPasswordModalOpen] = useState(false);
    const passkeys = props.passkeys ?? [];

    const handleDeletePasskey = (id: number, onError: () => void) => {
        router.delete(destroy.url(id), {
            preserveScroll: true,
            onError,
        });
    };

    const handleRegisterSuccess = () => {
        router.reload();
    };

    return (
        <>
            <Head title="Security settings" />

            <div className="space-y-10">
                <div className="space-y-4">
                    <Heading
                        variant="small"
                        title="Login methods"
                        description="Ways you can sign in to your account."
                    />

                    <div className="overflow-hidden rounded-lg border border-border bg-card">
                        {/* Email Row */}
                        <div className="flex items-center justify-between border-b p-4 sm:px-6">
                            <div className="flex items-center gap-4">
                                <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-muted">
                                    <Mail className="h-5 w-5 text-muted-foreground" />
                                </div>
                                <div className="space-y-1">
                                    <p className="font-medium tracking-tight">
                                        {user.email}
                                    </p>
                                    <p className="text-sm text-muted-foreground">
                                        Sign in with one-time codes sent to your email.
                                    </p>
                                </div>
                            </div>
                            <Button variant="outline" size="sm">
                                Change email
                            </Button>
                        </div>

                        {/* Password Row */}
                        <div className="flex items-center justify-between border-b p-4 sm:px-6">
                            <div className="flex items-center gap-4">
                                <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-muted">
                                    <Lock className="h-5 w-5 text-muted-foreground" />
                                </div>
                                <div className="space-y-1">
                                    <p className="font-medium tracking-tight">
                                        Password
                                    </p>
                                    <p className="text-sm text-muted-foreground">
                                        Update your password to stay secure.
                                    </p>
                                </div>
                            </div>
                            <Button variant="outline" size="sm" onClick={() => setIsPasswordModalOpen(true)}>
                                Change
                            </Button>
                        </div>

                        {/* Passkeys Row */}
                        {props.canManagePasskeys && (
                            <div className="flex flex-col p-4 sm:px-6">
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-4">
                                        <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-muted">
                                            <KeyRound className="h-5 w-5 text-muted-foreground" />
                                        </div>
                                        <div className="space-y-1">
                                            <p className="font-medium tracking-tight">
                                                Passkey
                                            </p>
                                            <p className="text-sm text-muted-foreground">
                                                Use Face ID, Touch ID, or a security key.
                                            </p>
                                        </div>
                                    </div>
                                    <PasskeyRegistration onSuccess={handleRegisterSuccess} />
                                </div>
                                
                                {passkeys.length > 0 && (
                                    <div className="mt-4 rounded-md border border-border bg-muted/30">
                                        {passkeys.map((passkey) => (
                                            <PasskeyItem
                                                key={passkey.id}
                                                passkey={passkey}
                                                onDelete={handleDeletePasskey}
                                            />
                                        ))}
                                    </div>
                                )}
                            </div>
                        )}
                    </div>
                </div>

                <ManageTwoFactor
                    canManageTwoFactor={props.canManageTwoFactor}
                    requiresConfirmation={props.requiresConfirmation}
                    twoFactorEnabled={props.twoFactorEnabled}
                />
            </div>

            <UpdatePasswordModal
                isOpen={isPasswordModalOpen}
                onClose={() => setIsPasswordModalOpen(false)}
                passwordRules={props.passwordRules}
            />
        </>
    );
}

Security.layout = {
    breadcrumbs: [
        {
            title: 'Security settings',
            href: edit(),
        },
    ],
};
