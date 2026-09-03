import React, { useState, useEffect } from 'react';
import { Bell } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuGroup, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Badge } from '@/components/ui/badge';
import axios from '@/lib/axios';
import { NotificationDetailDialog, NotificationItem } from '@/components/NotificationDetailDialog';

export function NotificationDropdown() {
    const [notifications, setNotifications] = useState<NotificationItem[]>([]);
    const [unreadCount, setUnreadCount] = useState(0);
    const [selectedNotification, setSelectedNotification] = useState<NotificationItem | null>(null);
    const [isDetailOpen, setIsDetailOpen] = useState(false);

    const fetchNotifications = async () => {
        try {
            const response = await axios.get('/notifications');
            setNotifications(response.data.notifications);
            setUnreadCount(response.data.notifications.filter((n: any) => !n.read_at).length);
        } catch (error) {
            console.error('Failed to fetch notifications', error);
        }
    };

    useEffect(() => {
        fetchNotifications();

        // Check if Echo is available on the window object
        if (typeof window !== 'undefined' && (window as any).Echo) {
            const userId = (window as any).Laravel?.user?.id;
            if (userId) {
                (window as any).Echo.private(`App.Models.User.${userId}`)
                    .notification((notification: any) => {
                        console.log('New notification received:', notification);
                        fetchNotifications();
                    });
            }
        }
    }, []);

    const markAsRead = async (id: string) => {
        try {
            await axios.post(`/notifications/${id}/read`);
            fetchNotifications();
        } catch (error) {
            console.error('Failed to mark notification as read', error);
        }
    };

    const markAllAsRead = async () => {
        try {
            await axios.post('/notifications/read-all');
            fetchNotifications();
        } catch (error) {
            console.error('Failed to mark all as read', error);
        }
    };

    return (
        <>
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button variant="ghost" size="icon" className="relative h-8 w-8 text-muted-foreground hover:text-foreground">
                    <Bell className="h-4 w-4" />
                    {unreadCount > 0 && (
                        <Badge className="absolute -top-1 -right-1 h-4 w-4 flex items-center justify-center p-0 text-[10px]" variant="destructive">
                            {unreadCount > 9 ? '9+' : unreadCount}
                        </Badge>
                    )}
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent className="w-80" align="end" forceMount>
                <DropdownMenuLabel className="flex justify-between items-center">
                    <span>Notifications</span>
                    {unreadCount > 0 && (
                        <Button variant="ghost" size="sm" className="h-auto p-0 text-xs text-muted-foreground hover:text-primary" onClick={(e: React.MouseEvent) => { e.preventDefault(); markAllAsRead(); }}>
                            Mark all as read
                        </Button>
                    )}
                </DropdownMenuLabel>
                <DropdownMenuSeparator />
                <div className="max-h-80 overflow-y-auto">
                    <DropdownMenuGroup>
                        {notifications.length === 0 ? (
                            <div className="p-4 text-center text-sm text-muted-foreground">
                                No notifications
                            </div>
                        ) : (
                            notifications.map((notification: NotificationItem) => (
                                <DropdownMenuItem 
                                    key={notification.id} 
                                    className={`flex flex-col items-start p-3 cursor-pointer ${!notification.read_at ? 'bg-muted/50' : ''}`}
                                    onClick={(e: React.MouseEvent) => {
                                        e.preventDefault();
                                        setSelectedNotification(notification);
                                        setIsDetailOpen(true);
                                        if (!notification.read_at) {
                                            markAsRead(notification.id);
                                        }
                                    }}
                                >
                                    <div className="flex w-full justify-between gap-2">
                                        <span className="font-medium text-sm">{notification.data?.title || 'Notification'}</span>
                                        {!notification.read_at && <div className="h-2 w-2 mt-1.5 rounded-full bg-blue-500 flex-shrink-0" />}
                                    </div>
                                    <span className="text-xs text-muted-foreground mt-1 line-clamp-2">
                                        {notification.data?.message || 'You have a new alert.'}
                                    </span>
                                    {notification.data?.amount && (
                                        <span className="text-xs font-medium mt-1">
                                            {notification.data.amount}
                                        </span>
                                    )}
                                    <span className="text-[10px] text-muted-foreground mt-2">
                                        {new Date(notification.created_at).toLocaleString()}
                                    </span>
                                </DropdownMenuItem>
                            ))
                        )}
                    </DropdownMenuGroup>
                </div>
            </DropdownMenuContent>
        </DropdownMenu>

        <NotificationDetailDialog
            notification={selectedNotification}
            open={isDetailOpen}
            onOpenChange={setIsDetailOpen}
        />
        </>
    );
}
