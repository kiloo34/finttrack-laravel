import { Head, usePage, useForm } from '@inertiajs/react';
import { FormEventHandler, useState, useEffect } from 'react';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Upload } from 'lucide-react';
import InputError from '@/components/input-error';
import { edit } from '@/routes/profile';
import type { Auth } from '@/types';
import { useAppearance } from '@/hooks/use-appearance';

type PageProps = {
    auth: Auth;
    mustVerifyEmail: boolean;
    status?: string;
};

export default function Profile() {
    const { auth } = usePage<PageProps>().props;
    const { appearance, updateAppearance } = useAppearance();

    const { data, setData, patch, errors, processing } = useForm({
        name: auth.user.name,
        email: auth.user.email,
    });

    // Handle First and Last name splitting/combining
    const [firstName, setFirstName] = useState('');
    const [lastName, setLastName] = useState('');

    useEffect(() => {
        const parts = auth.user.name.split(' ');
        setFirstName(parts[0] || '');
        setLastName(parts.slice(1).join(' ') || '');
    }, [auth.user.name]);

    const handleFirstNameChange = (val: string) => {
        setFirstName(val);
        setData('name', `${val} ${lastName}`.trim());
    };

    const handleLastNameChange = (val: string) => {
        setLastName(val);
        setData('name', `${firstName} ${val}`.trim());
    };

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        patch(edit.url());
    };

    return (
        <>
            <Head title="Profile settings" />
            <h1 className="sr-only">Profile settings</h1>

            <div className="space-y-10">
                
                {/* Profile Section */}
                <section>
                    <div className="mb-4">
                        <h2 className="text-xl font-bold tracking-tight">Profile</h2>
                        <p className="text-sm text-muted-foreground mt-1">Update your photo and personal details.</p>
                    </div>

                    <Card className="shadow-sm border-sidebar-border/70 dark:border-sidebar-border">
                        <form onSubmit={submit}>
                            <CardContent className="p-6 space-y-6">
                                {/* Avatar */}
                                <div className="flex items-center gap-6">
                                    <div className="h-16 w-16 overflow-hidden rounded-full bg-muted border border-border">
                                        <img 
                                            src={`https://ui-avatars.com/api/?name=${encodeURIComponent(auth.user.name)}&background=random`} 
                                            alt="Avatar" 
                                            className="h-full w-full object-cover" 
                                        />
                                    </div>
                                    <div className="flex items-center gap-4">
                                        <Button variant="outline" type="button" size="sm">
                                            <Upload className="mr-2 h-4 w-4" /> Upload
                                        </Button>
                                        <button type="button" className="text-sm font-medium text-red-600 hover:text-red-700 transition-colors">
                                            Remove
                                        </button>
                                    </div>
                                </div>

                                {/* Name */}
                                <div>
                                    <div className="grid gap-4 sm:grid-cols-2">
                                        <div className="space-y-2">
                                            <Label htmlFor="firstName">First name</Label>
                                            <Input
                                                id="firstName"
                                                value={firstName}
                                                onChange={(e) => handleFirstNameChange(e.target.value)}
                                                required
                                            />
                                        </div>
                                        <div className="space-y-2">
                                            <Label htmlFor="lastName">Last name</Label>
                                            <Input
                                                id="lastName"
                                                value={lastName}
                                                onChange={(e) => handleLastNameChange(e.target.value)}
                                            />
                                        </div>
                                    </div>
                                    <InputError message={errors.name} className="mt-2" />
                                </div>

                                {/* Email */}
                                <div className="space-y-2">
                                    <Label htmlFor="email">Email</Label>
                                    <Input
                                        id="email"
                                        type="email"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        required
                                    />
                                    <InputError message={errors.email} className="mt-2" />
                                </div>

                                {/* Country */}
                                <div className="space-y-2">
                                    <Label htmlFor="country">Country of residence</Label>
                                    <Select defaultValue="US">
                                        <SelectTrigger id="country">
                                            <SelectValue placeholder="Select country" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="US">United States</SelectItem>
                                            <SelectItem value="UK">United Kingdom</SelectItem>
                                            <SelectItem value="CA">Canada</SelectItem>
                                            <SelectItem value="AU">Australia</SelectItem>
                                            <SelectItem value="ID">Indonesia</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>

                                {/* Submit */}
                                <div className="flex justify-end pt-4">
                                    <Button 
                                        type="submit" 
                                        disabled={processing} 
                                        className="bg-blue-600 hover:bg-blue-700 text-white"
                                    >
                                        Save Changes
                                    </Button>
                                </div>
                            </CardContent>
                        </form>
                    </Card>
                </section>

                {/* Appearance Section */}
                <section>
                    <div className="mb-4">
                        <h2 className="text-xl font-bold tracking-tight">Appearance</h2>
                    </div>

                    <Card className="shadow-sm border-sidebar-border/70 dark:border-sidebar-border">
                        <CardContent className="p-6">
                            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <h3 className="text-sm font-medium">Theme</h3>
                                    <p className="text-sm text-muted-foreground mt-1">
                                        Override your browser's preferred theme setting.
                                    </p>
                                </div>
                                <div className="w-full sm:w-48">
                                    <Select 
                                        value={appearance} 
                                        onValueChange={(val) => updateAppearance(val as any)}
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="System" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="light">Light</SelectItem>
                                            <SelectItem value="dark">Dark</SelectItem>
                                            <SelectItem value="system">System</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </section>
                
            </div>
        </>
    );
}

Profile.layout = {
    breadcrumbs: [
        {
            title: 'Profile settings',
            href: edit(),
        },
    ],
};
