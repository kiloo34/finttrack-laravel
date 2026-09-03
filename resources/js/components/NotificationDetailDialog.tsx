import React from 'react';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import AmountDisplay from '@/components/AmountDisplay';
import {
    Bell,
    Calendar,
    Repeat,
    FileText,
    AlertTriangle,
    ArrowRightLeft,
    ExternalLink,
    Clock,
} from 'lucide-react';
import { format } from 'date-fns';
import { router } from '@inertiajs/react';

export interface NotificationItem {
    id: string;
    type: string;
    data?: {
        title?: string;
        message?: string;
        amount?: string | number;
        type?: 'income' | 'expense' | 'transfer' | string;
        category?: 'recurring' | 'statement' | 'report' | 'budget' | 'transaction' | 'system' | string;
        action_url?: string;
        action_text?: string;
        recurring_id?: number | string;
        metadata?: Record<string, any>;
        [key: string]: any;
    };
    read_at: string | null;
    created_at: string;
}

interface NotificationDetailDialogProps {
    notification: NotificationItem | null;
    open: boolean;
    onOpenChange: (open: boolean) => void;
}

export function NotificationDetailDialog({
    notification,
    open,
    onOpenChange,
}: NotificationDetailDialogProps) {
    if (!notification) return null;

    const { data } = notification;
    const category = data?.category || (data?.recurring_id ? 'recurring' : 'system');
    const title = data?.title || 'Notification';
    const message = data?.message || '';
    const amount = data?.amount !== undefined && data?.amount !== null ? Number(data.amount) : null;
    const txType = data?.type;
    const actionUrl = data?.action_url || (data?.recurring_id ? '/recurring-transactions' : undefined);
    const actionText = data?.action_text || (data?.recurring_id ? 'Kelola Transaksi Berulang' : 'Lihat Detail');
    const metadata = data?.metadata;

    const formattedDate = (() => {
        try {
            return format(new Date(notification.created_at), 'dd MMMM yyyy, HH:mm');
        } catch {
            return notification.created_at;
        }
    })();

    const renderCategoryIcon = () => {
        switch (category) {
            case 'recurring':
                return (
                    <div className="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400">
                        <Repeat className="h-5 w-5" />
                    </div>
                );
            case 'statement':
            case 'report':
                return (
                    <div className="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400">
                        <FileText className="h-5 w-5" />
                    </div>
                );
            case 'budget':
                return (
                    <div className="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400">
                        <AlertTriangle className="h-5 w-5" />
                    </div>
                );
            case 'transaction':
                return (
                    <div className="flex h-10 w-10 items-center justify-center rounded-full bg-teal-100 text-teal-600 dark:bg-teal-950/60 dark:text-teal-400">
                        <ArrowRightLeft className="h-5 w-5" />
                    </div>
                );
            default:
                return (
                    <div className="flex h-10 w-10 items-center justify-center rounded-full bg-muted text-muted-foreground">
                        <Bell className="h-5 w-5" />
                    </div>
                );
        }
    };

    const handleActionClick = () => {
        if (!actionUrl) return;
        onOpenChange(false);
        router.visit(actionUrl);
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-[460px]">
                <DialogHeader className="flex flex-row items-start gap-3.5 space-y-0 text-left">
                    {renderCategoryIcon()}
                    <div className="flex-1 space-y-1">
                        <div className="flex items-center gap-2">
                            <Badge variant="outline" className="text-[10px] capitalize font-semibold px-2 py-0.5">
                                {category.replace('_', ' ')}
                            </Badge>
                            <span className="flex items-center text-[11px] text-muted-foreground gap-1">
                                <Clock className="h-3 w-3" />
                                {formattedDate}
                            </span>
                        </div>
                        <DialogTitle className="text-base font-semibold tracking-tight text-foreground leading-snug">
                            {title}
                        </DialogTitle>
                    </div>
                </DialogHeader>

                <div className="space-y-4 py-2 text-sm">
                    {/* Full Notification Message */}
                    {message && (
                        <DialogDescription className="text-sm text-muted-foreground whitespace-pre-wrap leading-relaxed">
                            {message}
                        </DialogDescription>
                    )}

                    {/* Financial Amount Card (if present) */}
                    {amount !== null && !isNaN(amount) && (
                        <div className="rounded-lg border border-border/70 bg-card p-3.5 flex items-center justify-between shadow-xs">
                            <div className="space-y-0.5">
                                <p className="text-xs font-medium text-muted-foreground">Nominal Transaksi</p>
                                <p className="text-xs text-muted-foreground capitalize">
                                    {txType ? `Tipe: ${txType}` : 'Jumlah'}
                                </p>
                            </div>
                            <div className={`text-base font-bold ${
                                txType === 'income' ? 'text-emerald-600 dark:text-emerald-400' :
                                txType === 'expense' ? 'text-red-600 dark:text-red-400' : ''
                            }`}>
                                {txType === 'income' ? '+ ' : txType === 'expense' ? '- ' : ''}
                                <AmountDisplay amount={amount} showSymbol={true} />
                            </div>
                        </div>
                    )}

                    {/* Dynamic Metadata Attributes */}
                    {metadata && typeof metadata === 'object' && Object.keys(metadata).length > 0 && (
                        <div className="rounded-lg border border-border/60 bg-muted/40 p-3 space-y-2 text-xs">
                            <p className="font-semibold text-foreground text-[11px] uppercase tracking-wider">
                                Informasi Tambahan
                            </p>
                            <div className="grid grid-cols-2 gap-2 pt-1">
                                {Object.entries(metadata).map(([key, value]) => (
                                    <div key={key} className="space-y-0.5">
                                        <p className="text-muted-foreground capitalize">{key.replace('_', ' ')}</p>
                                        <p className="font-medium text-foreground">{String(value)}</p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}
                </div>

                <DialogFooter className="flex flex-row justify-end items-center gap-2.5 pt-3 border-t border-border/40 mt-2">
                    <Button 
                        variant="outline" 
                        size="sm" 
                        onClick={() => onOpenChange(false)}
                        className="cursor-pointer"
                    >
                        Tutup
                    </Button>
                    {actionUrl && (
                        <Button 
                            size="sm" 
                            onClick={handleActionClick} 
                            className="gap-1.5 cursor-pointer"
                        >
                            {actionText}
                            <ExternalLink className="h-3.5 w-3.5" />
                        </Button>
                    )}
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
