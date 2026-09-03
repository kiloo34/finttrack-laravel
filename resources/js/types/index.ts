import type { Auth } from './auth';

export type * from './auth';
export type * from './navigation';
export type * from './ui';
export type * from './finance';

export interface PageProps {
    name: string;
    auth: Auth;
    sidebarOpen: boolean;
    constants?: any;
    [key: string]: unknown;
}

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: PageProps;
    }
}
