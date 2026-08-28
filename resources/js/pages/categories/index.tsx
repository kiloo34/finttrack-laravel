import { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import axios from '@/lib/axios';
import { Category } from '@/types/finance';
import { Button } from '@/components/ui/button';
import { Plus, Tags, Edit2, TrendingDown, TrendingUp } from 'lucide-react';
import CategoryModal from '@/components/categories/CategoryModal';

export default function CategoriesIndex() {
    const [categories, setCategories] = useState<Category[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [selectedCategory, setSelectedCategory] = useState<Category | null>(null);

    const fetchCategories = async () => {
        setIsLoading(true);
        try {
            const response = await axios.get<{ data: Category[] }>('/categories');
            setCategories(response.data.data);
        } catch (error) {
            console.error('Failed to fetch categories', error);
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        fetchCategories();
    }, []);

    const handleOpenModal = (category?: Category) => {
        setSelectedCategory(category || null);
        setIsModalOpen(true);
    };

    const handleCloseModal = () => {
        setIsModalOpen(false);
        setSelectedCategory(null);
    };

    const incomeCategories = categories.filter(c => c.type === 'income');
    const expenseCategories = categories.filter(c => c.type === 'expense');

    return (
        <div className="flex h-full flex-1 flex-col gap-6 p-6">
            <Head title="Categories" />
            
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-semibold">Categories</h1>
                    <p className="text-sm text-muted-foreground mt-1">Organize your income and expenses.</p>
                </div>
                <Button onClick={() => handleOpenModal()}>
                    <Plus className="mr-2 h-4 w-4" /> Add Category
                </Button>
            </div>

            <div className="grid gap-6 md:grid-cols-2">
                {/* Expense Categories */}
                <div className="space-y-4">
                    <div className="flex items-center gap-2">
                        <TrendingDown className="h-5 w-5 text-red-500" />
                        <h2 className="text-xl font-medium">Expense Categories</h2>
                    </div>
                    <div className="grid gap-3">
                        {isLoading ? (
                            <p className="text-sm text-muted-foreground">Loading...</p>
                        ) : expenseCategories.length === 0 ? (
                            <div className="py-6 text-center border rounded-xl bg-card border-dashed">
                                <p className="text-sm text-muted-foreground">No expense categories yet.</p>
                            </div>
                        ) : (
                            expenseCategories.map(category => (
                                <div key={category.id} className="flex items-center justify-between rounded-xl border bg-card p-4 shadow-sm">
                                    <div className="flex items-center gap-3">
                                        <div className="bg-red-50 text-red-500 p-2 rounded-lg">
                                            <Tags className="h-4 w-4" />
                                        </div>
                                        <span className="font-medium">{category.name}</span>
                                    </div>
                                    <Button variant="ghost" size="icon" onClick={() => handleOpenModal(category)}>
                                        <Edit2 className="h-4 w-4" />
                                    </Button>
                                </div>
                            ))
                        )}
                    </div>
                </div>

                {/* Income Categories */}
                <div className="space-y-4">
                    <div className="flex items-center gap-2">
                        <TrendingUp className="h-5 w-5 text-emerald-500" />
                        <h2 className="text-xl font-medium">Income Categories</h2>
                    </div>
                    <div className="grid gap-3">
                        {isLoading ? (
                            <p className="text-sm text-muted-foreground">Loading...</p>
                        ) : incomeCategories.length === 0 ? (
                            <div className="py-6 text-center border rounded-xl bg-card border-dashed">
                                <p className="text-sm text-muted-foreground">No income categories yet.</p>
                            </div>
                        ) : (
                            incomeCategories.map(category => (
                                <div key={category.id} className="flex items-center justify-between rounded-xl border bg-card p-4 shadow-sm">
                                    <div className="flex items-center gap-3">
                                        <div className="bg-emerald-50 text-emerald-500 p-2 rounded-lg">
                                            <Tags className="h-4 w-4" />
                                        </div>
                                        <span className="font-medium">{category.name}</span>
                                    </div>
                                    <Button variant="ghost" size="icon" onClick={() => handleOpenModal(category)}>
                                        <Edit2 className="h-4 w-4" />
                                    </Button>
                                </div>
                            ))
                        )}
                    </div>
                </div>
            </div>

            <CategoryModal 
                isOpen={isModalOpen} 
                onClose={handleCloseModal} 
                onSuccess={fetchCategories}
                category={selectedCategory}
            />
        </div>
    );
}
