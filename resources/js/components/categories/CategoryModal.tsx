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
import { Combobox } from '@/components/ui/combobox';
import axios from '@/lib/axios';
import { usePage } from '@inertiajs/react';
import { Category } from '@/types/finance';

interface CategoryModalProps {
    isOpen: boolean;
    onClose: () => void;
    onSuccess: () => void;
    category?: Category | null;
    allCategories?: Category[];
}

export default function CategoryModal({ isOpen, onClose, onSuccess, category, allCategories = [] }: CategoryModalProps) {
    const { constants } = usePage<any>().props;
    const CATEGORY_TYPES = constants?.CATEGORY_TYPES || [];
    const [isLoading, setIsLoading] = useState(false);
    
    // Form state
    const [name, setName] = useState('');
    const [type, setType] = useState<'income' | 'expense'>('expense');
    const [parentId, setParentId] = useState<string | null>(null);
    const [errors, setErrors] = useState<Record<string, string[]>>({});
    const [globalError, setGlobalError] = useState<string | null>(null);

    useEffect(() => {
        if (isOpen) {
            if (category) {
                setName(category.name);
                setType(category.type);
                setParentId(category.parent_id ? String(category.parent_id) : null);
            } else {
                resetForm();
            }
        }
    }, [isOpen, category]);

    const resetForm = () => {
        setName('');
        setType('expense');
        setParentId(null);
        setErrors({});
        setGlobalError(null);
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsLoading(true);
        setErrors({});
        setGlobalError(null);

        try {
            const payload = {
                name,
                type,
                parent_id: parentId ? parseInt(parentId) : null,
            };

            if (category) {
                await axios.put(`/categories/${category.id}`, payload);
            } else {
                await axios.post('/categories', payload);
            }
            
            onSuccess();
            onClose();
        } catch (error: any) {
            if (error.response?.data?.errors) {
                setErrors(error.response.data.errors);
            } else {
                console.error("Submission failed", error);
                setGlobalError(error.response?.data?.message || "An unexpected error occurred. Please try again.");
            }
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <Dialog open={isOpen} onOpenChange={(open) => !open && onClose()}>
            <DialogContent className="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>{category ? 'Edit Category' : 'Add Category'}</DialogTitle>
                </DialogHeader>
                <form onSubmit={handleSubmit} className="space-y-4 py-4">
                    
                    {globalError && (
                        <div className="p-3 bg-red-50 text-red-600 border border-red-200 rounded-md text-sm">
                            {globalError}
                        </div>
                    )}

                    <div className="space-y-2">
                        <Label htmlFor="name">Category Name</Label>
                        <Input 
                            id="name" 
                            value={name} 
                            onChange={(e) => setName(e.target.value)} 
                            placeholder="e.g. Salary, Groceries, Utilities"
                        />
                        {errors.name && <p className="text-sm text-red-500">{errors.name[0]}</p>}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="type">Type</Label>
                        <Combobox
                            options={CATEGORY_TYPES}
                            value={type}
                            onChange={(val) => {
                                setType(val as 'income' | 'expense');
                                setParentId(null); // Reset parent when type changes
                            }}
                            placeholder="Select type"
                        />
                        {errors.type && <p className="text-sm text-red-500">{errors.type[0]}</p>}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="parent_id">Parent Category (Optional)</Label>
                        <Combobox
                            options={[
                                { label: 'None (Top Level)', value: '' },
                                ...allCategories
                                    .filter(c => c.type === type && c.parent_id === null && c.id !== category?.id)
                                    .map(c => ({
                                        label: c.name,
                                        value: String(c.id)
                                    }))
                            ]}
                            value={parentId || ''}
                            onChange={(val) => setParentId(val === '' ? null : val)}
                            placeholder="Select parent category"
                        />
                        {errors.parent_id && <p className="text-sm text-red-500">{errors.parent_id[0]}</p>}
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" onClick={onClose}>
                            Cancel
                        </Button>
                        <Button type="submit" disabled={isLoading}>
                            {isLoading ? 'Saving...' : (category ? 'Update Category' : 'Save Category')}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
