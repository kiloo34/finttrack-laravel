import React, { createContext, useContext, useEffect, useState } from 'react';

interface PrivacyContextType {
    isPrivacyMode: boolean;
    togglePrivacyMode: () => void;
}

const PrivacyContext = createContext<PrivacyContextType>({
    isPrivacyMode: false,
    togglePrivacyMode: () => {},
});

export const usePrivacy = () => useContext(PrivacyContext);

export const PrivacyProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
    // Always initialize as false to match SSR output, then sync from localStorage after mount
    const [isPrivacyMode, setIsPrivacyMode] = useState(false);

    useEffect(() => {
        const stored = localStorage.getItem('privacy_mode');
        if (stored === 'true') {
            setIsPrivacyMode(true);
        }
    }, []);

    const togglePrivacyMode = () => {
        setIsPrivacyMode((prev) => {
            const next = !prev;
            localStorage.setItem('privacy_mode', String(next));
            return next;
        });
    };

    return (
        <PrivacyContext.Provider value={{ isPrivacyMode, togglePrivacyMode }}>
            {children}
        </PrivacyContext.Provider>
    );
};
