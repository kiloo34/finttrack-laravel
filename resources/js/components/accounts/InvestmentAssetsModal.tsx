import { useState, useEffect } from 'react';
import { 
    Dialog, 
    DialogContent, 
    DialogHeader, 
    DialogTitle,
    DialogFooter
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import axios from '@/lib/axios';
import { Account, InvestmentAsset } from '@/types/finance';
import { RefreshCw, Plus, Save, TrendingUp } from 'lucide-react';
import AmountDisplay from '@/components/AmountDisplay';

interface InvestmentAssetsModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSuccess: () => void;
    account: Account | null;
}

const ASSET_TYPES = [
    { value: 'stock', label: 'Saham (Stock)' },
    { value: 'mutual_fund', label: 'Reksa Dana' },
    { value: 'crypto', label: 'Cryptocurrency' },
    { value: 'bond', label: 'Obligasi / Bond' },
    { value: 'other', label: 'Lainnya' },
];

export default function InvestmentAssetsModal({ isOpen, onClose, onSuccess, account }: InvestmentAssetsModalProps) {
    const [isLoading, setIsLoading] = useState(false);
    const [activeTab, setActiveTab] = useState<'list' | 'add'>('list');
    
    // Add Asset Form
    const [name, setName] = useState('');
    const [symbol, setSymbol] = useState('');
    const [type, setType] = useState('stock');
    const [lots, setLots] = useState('');         // For stocks (integer, 1 lot = 100 shares)
    const [units, setUnits] = useState('');       // For mutual funds / others (decimal)
    const [avgPrice, setAvgPrice] = useState('');
    const [marketPrice, setMarketPrice] = useState('');
    
    // Sync Prices State
    const [prices, setPrices] = useState<Record<number, string>>({});

    const isStock = type === 'stock';

    // Local assets state — keeps modal in sync without full page reload
    const [localAssets, setLocalAssets] = useState<InvestmentAsset[]>([]);

    const fetchAssets = async () => {
        if (!account) return;
        try {
            const res = await axios.get<InvestmentAsset[]>(`/accounts/${account.id}/assets`);
            const data = Array.isArray(res.data) ? res.data : (res.data as any).data ?? [];
            setLocalAssets(data);
            // Sync prices input state with new data
            const newPrices: Record<number, string> = {};
            data.forEach((a: InvestmentAsset) => {
                newPrices[a.id] = a.current_market_price.toString();
            });
            setPrices(newPrices);
        } catch (e) {
            setLocalAssets(account.investment_assets ?? []);
        }
    };

    useEffect(() => {
        if (isOpen && account) {
            const assets = account.investment_assets ?? [];
            setLocalAssets(assets);
            const initialPrices: Record<number, string> = {};
            assets.forEach(asset => {
                initialPrices[asset.id] = asset.current_market_price.toString();
            });
            setPrices(initialPrices);
            setActiveTab('list');
        }
    }, [isOpen, account]);

    const resetAddForm = () => {
        setName('');
        setSymbol('');
        setType('stock');
        setLots('');
        setUnits('');
        setAvgPrice('');
        setMarketPrice('');
    };

    const handleAddAsset = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!account) return;
        
        setIsLoading(true);
        try {
            const payload: Record<string, any> = {
                name,
                symbol: symbol || null,
                type,
                average_buy_price: parseFloat(avgPrice),
                current_market_price: parseFloat(marketPrice),
            };

            if (isStock) {
                payload.lots = parseInt(lots, 10);
            } else {
                payload.units = parseFloat(units);
            }

            await axios.post(`/accounts/${account.id}/assets`, payload);
            
            resetAddForm();
            setActiveTab('list');
            // Refresh assets locally (no full page reload needed)
            await fetchAssets();
            onSuccess(); // also refresh parent in background
        } catch (error: any) {
            console.error("Failed to add asset", error?.response?.data || error);
        } finally {
            setIsLoading(false);
        }
    };

    const handleSyncPrices = async () => {
        if (!account) return;
        
        setIsLoading(true);
        try {
            const payload = Object.entries(prices).map(([asset_id, current_market_price]) => ({
                asset_id: parseInt(asset_id),
                current_market_price: parseFloat(current_market_price)
            }));

            await axios.put(`/accounts/${account.id}/assets/sync`, { prices: payload });
            // Update prices in local state immediately
            setLocalAssets(prev => prev.map(a => ({
                ...a,
                current_market_price: prices[a.id] ?? a.current_market_price
            })));
            onSuccess(); // refresh parent in background
        } catch (error) {
            console.error("Failed to sync prices", error);
        } finally {
            setIsLoading(false);
        }
    };

    const handlePriceChange = (assetId: number, value: string) => {
        setPrices(prev => ({ ...prev, [assetId]: value }));
    };

    const getAssetDisplayQty = (asset: InvestmentAsset): string => {
        if (asset.type === 'stock' && asset.lots != null) {
            return `${asset.lots} lot (${Number(asset.lots) * 100} lembar)`;
        }
        return `${Number(asset.units).toLocaleString('id-ID')} unit`;
    };

    const getAssetValue = (asset: InvestmentAsset): number => {
        if (asset.type === 'stock' && asset.lots != null) {
            return Number(asset.lots) * 100 * Number(asset.current_market_price);
        }
        return Number(asset.units) * Number(asset.current_market_price);
    };

    if (!account) return null;

    const totalPortfolio = localAssets.reduce((acc, a) => acc + getAssetValue(a), 0);

    return (
        <Dialog open={isOpen} onOpenChange={(open) => !open && onClose()}>
            <DialogContent className="sm:max-w-[620px] max-h-[85vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle className="flex items-center gap-2">
                        <TrendingUp className="h-5 w-5 text-emerald-500" />
                        Portofolio: {account.name}
                    </DialogTitle>
                    {localAssets.length > 0 && (
                        <p className="text-sm text-muted-foreground pt-1">
                            Total Nilai: <span className="font-semibold text-foreground"><AmountDisplay amount={totalPortfolio} /></span>
                        </p>
                    )}
                </DialogHeader>

                <div className="flex gap-2 border-b pb-2 mb-4">
                    <Button 
                        variant={activeTab === 'list' ? 'default' : 'ghost'} 
                        size="sm" 
                        onClick={() => setActiveTab('list')}
                    >
                        Portofolio
                    </Button>
                    <Button 
                        variant={activeTab === 'add' ? 'default' : 'ghost'} 
                        size="sm" 
                        onClick={() => setActiveTab('add')}
                    >
                        <Plus className="h-4 w-4 mr-1" /> Tambah Aset
                    </Button>
                </div>

                {activeTab === 'list' && (
                    <div className="space-y-3">
                        {localAssets.length === 0 ? (
                            <div className="py-10 text-center">
                                <TrendingUp className="h-10 w-10 mx-auto text-muted-foreground opacity-40 mb-3" />
                                <p className="text-sm text-muted-foreground">Belum ada aset. Tambahkan saham atau reksa dana untuk mulai tracking.</p>
                                <Button variant="outline" size="sm" className="mt-3" onClick={() => setActiveTab('add')}>
                                    <Plus className="h-4 w-4 mr-1" /> Tambah Aset Pertama
                                </Button>
                            </div>
                        ) : (
                            <div className="space-y-3">
                                {localAssets.map(asset => (
                                    <div key={asset.id} className="p-4 border rounded-xl bg-muted/20 hover:bg-muted/30 transition-colors">
                                        <div className="flex justify-between items-start mb-3">
                                            <div>
                                                <div className="flex items-center gap-2">
                                                    <h4 className="font-semibold">{asset.name}</h4>
                                                    {asset.symbol && (
                                                        <span className="text-xs bg-primary/10 text-primary px-2 py-0.5 rounded-full font-mono">
                                                            {asset.symbol}
                                                        </span>
                                                    )}
                                                </div>
                                                <p className="text-xs text-muted-foreground mt-0.5">
                                                    {ASSET_TYPES.find(t => t.value === asset.type)?.label || asset.type}
                                                    {' · '}{getAssetDisplayQty(asset)}
                                                </p>
                                            </div>
                                            <div className="text-right">
                                                <div className="font-semibold text-emerald-600 dark:text-emerald-400">
                                                    <AmountDisplay amount={getAssetValue(asset)} />
                                                </div>
                                                <p className="text-xs text-muted-foreground">
                                                    Avg: <AmountDisplay amount={Number(asset.average_buy_price)} />
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div className="flex items-end gap-2 pt-3 border-t border-border/50">
                                            <div className="flex-1">
                                                <Label className="text-xs text-muted-foreground">
                                                    {asset.type === 'stock' ? 'Harga per Lembar' : 'NAB / Unit'}
                                                </Label>
                                                <Input 
                                                    type="number" 
                                                    className="h-8 text-sm mt-1" 
                                                    value={prices[asset.id] ?? ''} 
                                                    onChange={(e) => handlePriceChange(asset.id, e.target.value)}
                                                    placeholder="Update harga..."
                                                />
                                            </div>
                                            <p className="text-xs text-muted-foreground pb-2">
                                                Saat ini: <span className="font-medium"><AmountDisplay amount={Number(asset.current_market_price)} /></span>
                                            </p>
                                        </div>
                                    </div>
                                ))}
                                <Button className="w-full mt-2" onClick={handleSyncPrices} disabled={isLoading}>
                                    <RefreshCw className={`h-4 w-4 mr-2 ${isLoading ? 'animate-spin' : ''}`} /> 
                                    {isLoading ? 'Menyimpan...' : 'Update Harga Pasar'}
                                </Button>
                            </div>
                        )}
                    </div>
                )}

                {activeTab === 'add' && (
                    <form onSubmit={handleAddAsset} className="space-y-4">
                        <div className="space-y-2">
                            <Label>Jenis Aset</Label>
                            <select 
                                className="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                value={type} 
                                onChange={e => { setType(e.target.value); setLots(''); setUnits(''); }}
                            >
                                {ASSET_TYPES.map(t => (
                                    <option key={t.value} value={t.value}>{t.label}</option>
                                ))}
                            </select>
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div className="space-y-2">
                                <Label>Nama {isStock ? 'Saham' : 'Aset'}</Label>
                                <Input value={name} onChange={e => setName(e.target.value)} placeholder={isStock ? 'BCA, Telkom, GOTO...' : 'Sucorinvest MF...'} required />
                            </div>
                            <div className="space-y-2">
                                <Label>Kode / Ticker <span className="text-muted-foreground">(Opsional)</span></Label>
                                <Input value={symbol} onChange={e => setSymbol(e.target.value)} placeholder={isStock ? 'BBCA, TLKM, GOTO' : 'SUCMF01'} />
                            </div>
                        </div>

                        {isStock ? (
                            <div className="space-y-2">
                                <Label>Jumlah Lot <span className="text-xs text-muted-foreground">(1 lot = 100 lembar)</span></Label>
                                <div className="relative">
                                    <Input 
                                        type="number" 
                                        min="1" 
                                        step="1" 
                                        value={lots} 
                                        onChange={e => setLots(e.target.value)} 
                                        placeholder="Contoh: 10"
                                        required
                                    />
                                    {lots && (
                                        <span className="absolute right-3 top-2 text-xs text-muted-foreground">
                                            = {parseInt(lots) * 100} lembar
                                        </span>
                                    )}
                                </div>
                            </div>
                        ) : (
                            <div className="space-y-2">
                                <Label>Jumlah Unit</Label>
                                <Input type="number" step="0.0001" value={units} onChange={e => setUnits(e.target.value)} placeholder="Contoh: 1234.5678" required />
                            </div>
                        )}

                        <div className="grid grid-cols-2 gap-4">
                            <div className="space-y-2">
                                <Label>{isStock ? 'Harga Beli Rata-rata / Lembar' : 'NAB Beli Rata-rata / Unit'}</Label>
                                <Input type="number" step="1" value={avgPrice} onChange={e => setAvgPrice(e.target.value)} placeholder="Contoh: 9500" required />
                            </div>
                            <div className="space-y-2">
                                <Label>{isStock ? 'Harga Pasar / Lembar' : 'NAB Saat Ini / Unit'}</Label>
                                <Input type="number" step="1" value={marketPrice} onChange={e => setMarketPrice(e.target.value)} placeholder="Contoh: 9800" required />
                            </div>
                        </div>

                        {/* Preview nilai portofolio */}
                        {(isStock ? lots && avgPrice && marketPrice : units && avgPrice && marketPrice) && (
                            <div className="p-3 rounded-lg bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-sm">
                                <p className="text-muted-foreground">Estimasi nilai portofolio:</p>
                                <p className="font-semibold text-emerald-700 dark:text-emerald-400 text-lg">
                                    <AmountDisplay amount={
                                        isStock 
                                            ? parseInt(lots || '0') * 100 * parseFloat(marketPrice || '0')
                                            : parseFloat(units || '0') * parseFloat(marketPrice || '0')
                                    } />
                                </p>
                            </div>
                        )}

                        <DialogFooter className="pt-2">
                            <Button type="button" variant="outline" onClick={() => { resetAddForm(); setActiveTab('list'); }}>Batal</Button>
                            <Button type="submit" disabled={isLoading}>
                                <Save className="h-4 w-4 mr-2" /> 
                                {isLoading ? 'Menyimpan...' : `Tambah ${isStock ? 'Saham' : 'Aset'}`}
                            </Button>
                        </DialogFooter>
                    </form>
                )}
            </DialogContent>
        </Dialog>
    );
}
