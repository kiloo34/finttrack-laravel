import { Form } from '@inertiajs/react';
import { Smartphone } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import Heading from '@/components/heading';
import TwoFactorRecoveryCodes from '@/components/two-factor-recovery-codes';
import TwoFactorSetupModal from '@/components/two-factor-setup-modal';
import { Button } from '@/components/ui/button';
import { useTwoFactorAuth } from '@/hooks/use-two-factor-auth';
import { disable, enable } from '@/routes/two-factor';

export type Props = {
    canManageTwoFactor?: boolean;
    requiresConfirmation?: boolean;
    twoFactorEnabled?: boolean;
};

export default function ManageTwoFactor(props: Props) {
    const requiresConfirmation = props.requiresConfirmation ?? false;
    const twoFactorEnabled = props.twoFactorEnabled ?? false;

    const {
        qrCodeSvg,
        hasSetupData,
        manualSetupKey,
        clearSetupData,
        clearTwoFactorAuthData,
        fetchSetupData,
        recoveryCodesList,
        fetchRecoveryCodes,
        errors,
    } = useTwoFactorAuth();
    const [showSetupModal, setShowSetupModal] = useState<boolean>(false);
    const prevTwoFactorEnabled = useRef(twoFactorEnabled);

    useEffect(() => {
        if (prevTwoFactorEnabled.current && !twoFactorEnabled) {
            clearTwoFactorAuthData();
        }

        prevTwoFactorEnabled.current = twoFactorEnabled;
    }, [twoFactorEnabled, clearTwoFactorAuthData]);

    if (!(props.canManageTwoFactor ?? false)) {
        return null;
    }

    return (
        <div className="space-y-4">
            <Heading
                variant="small"
                title="Two-factor authentication"
                description="Add an extra layer of security when you sign in."
            />
            
            <div className="overflow-hidden rounded-lg border border-border bg-card p-4 sm:px-6">
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div className="flex items-center gap-4">
                        <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-muted">
                            <Smartphone className="h-5 w-5 text-muted-foreground" />
                        </div>
                        <div className="space-y-1">
                            <p className="font-medium tracking-tight">
                                Authenticator app
                            </p>
                            <p className="text-sm text-muted-foreground">
                                Use an app like 1Password or Authy to generate codes.
                            </p>
                        </div>
                    </div>

                    <div>
                        {twoFactorEnabled ? (
                            <div className="flex gap-2 items-center">
                                <Form {...disable.form()}>
                                    {({ processing }) => (
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            type="submit"
                                            disabled={processing}
                                        >
                                            Disable 2FA
                                        </Button>
                                    )}
                                </Form>
                            </div>
                        ) : (
                            <>
                                {hasSetupData ? (
                                    <Button size="sm" onClick={() => setShowSetupModal(true)} className="bg-blue-600 hover:bg-blue-700 text-white">
                                        Continue setup
                                    </Button>
                                ) : (
                                    <Form
                                        {...enable.form()}
                                        onSuccess={() => setShowSetupModal(true)}
                                    >
                                        {({ processing }) => (
                                            <Button size="sm" type="submit" disabled={processing} className="bg-blue-600 hover:bg-blue-700 text-white">
                                                Set up
                                            </Button>
                                        )}
                                    </Form>
                                )}
                            </>
                        )}
                    </div>
                </div>

                {twoFactorEnabled && (
                    <div className="mt-6 pt-6 border-t border-border">
                        <TwoFactorRecoveryCodes
                            recoveryCodesList={recoveryCodesList}
                            fetchRecoveryCodes={fetchRecoveryCodes}
                            errors={errors}
                        />
                    </div>
                )}
            </div>

            <TwoFactorSetupModal
                isOpen={showSetupModal}
                onClose={() => setShowSetupModal(false)}
                requiresConfirmation={requiresConfirmation}
                twoFactorEnabled={twoFactorEnabled}
                qrCodeSvg={qrCodeSvg}
                manualSetupKey={manualSetupKey}
                clearSetupData={clearSetupData}
                fetchSetupData={fetchSetupData}
                errors={errors}
            />
        </div>
    );
}
