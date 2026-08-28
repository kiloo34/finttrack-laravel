import { Link } from '@inertiajs/react';
import type { PropsWithChildren } from 'react';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useCurrentUrl } from '@/hooks/use-current-url';
import { cn, toUrl } from '@/lib/utils';
import { edit as editAppearance } from '@/routes/appearance';
import { edit } from '@/routes/profile';
import { edit as editSecurity } from '@/routes/security';
import type { NavItem } from '@/types';
import { UserCircle, Bell, Shield, AlertTriangle, LogOut } from 'lucide-react';
import { logout } from '@/routes';
import { router } from '@inertiajs/react';

const sidebarNavItems: NavItem[] = [
    {
        title: 'Profile',
        href: edit(),
        icon: UserCircle,
    },
    {
        title: 'Notifications',
        href: '/settings/notifications',
        icon: Bell,
    },
    {
        title: 'Security',
        href: editSecurity(),
        icon: Shield,
    },
    {
        title: 'Danger zone',
        href: '/settings/danger-zone',
        icon: AlertTriangle,
    },
];

export default function SettingsLayout({ children }: PropsWithChildren) {
    const { isCurrentOrParentUrl } = useCurrentUrl();

    return (
        <div className="px-4 py-6">
            <Heading
                title="Settings"
                description="Manage your profile, store, and account preferences."
            />

            <div className="flex flex-col lg:flex-row lg:space-x-12 mt-8">
                <aside className="w-full max-w-xl lg:w-48">
                    <nav
                        className="flex flex-col space-y-1 space-x-0"
                        aria-label="Settings"
                    >
                        {sidebarNavItems.map((item, index) => (
                            <Button
                                key={`${toUrl(item.href)}-${index}`}
                                size="sm"
                                variant="ghost"
                                asChild
                                className={cn('w-full justify-start gap-2 font-normal', {
                                    'bg-blue-50 text-blue-600 hover:bg-blue-50 hover:text-blue-600 dark:bg-blue-900/30 dark:text-blue-400': isCurrentOrParentUrl(item.href) && item.href !== '#',
                                    'text-muted-foreground': !isCurrentOrParentUrl(item.href) || item.href === '#',
                                })}
                            >
                                <Link href={item.href}>
                                    {item.icon && (
                                        <item.icon className="h-4 w-4" />
                                    )}
                                    {item.title}
                                </Link>
                            </Button>
                        ))}
                        
                        <Separator className="my-2" />
                        
                        <Button
                            size="sm"
                            variant="ghost"
                            className="w-full justify-start gap-2 font-normal text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30"
                            onClick={() => router.post(logout())}
                        >
                            <LogOut className="h-4 w-4" />
                            Sign out
                        </Button>
                    </nav>
                </aside>

                <Separator className="my-6 lg:hidden" />

                <div className="flex-1 md:max-w-2xl">
                    <section className="max-w-xl space-y-12">
                        {children}
                    </section>
                </div>
            </div>
        </div>
    );
}
