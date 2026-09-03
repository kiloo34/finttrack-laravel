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
    const [isPrivacyMode, setIsPrivacyMode] = useState(() => {
        if (typeof window !== 'undefined') {
            return localStorage.getItem('privacy_mode') === 'true';
        }
        return false;
    });

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
