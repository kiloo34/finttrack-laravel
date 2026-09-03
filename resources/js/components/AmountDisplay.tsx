import React from 'react';
import { usePrivacy } from '@/contexts/PrivacyContext';

interface AmountDisplayProps {
    amount: number | string;
    currency?: string;
    className?: string;
    showSymbol?: boolean;
}

export default function AmountDisplay({
    amount,
    currency = 'IDR',
    className = '',
    showSymbol = true,
}: AmountDisplayProps) {
    const { isPrivacyMode } = usePrivacy();

    const formattedAmount = Number(amount).toLocaleString('id-ID');
    const symbol = currency === 'IDR' ? 'Rp' : currency;

    if (isPrivacyMode) {
        return (
            <span className={className}>
                {showSymbol && `${symbol} `}••••••
            </span>
        );
    }

    return (
        <span className={className}>
            {showSymbol && `${symbol} `}{formattedAmount}
        </span>
    );
}
